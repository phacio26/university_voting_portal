<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ElectionPeriod;
use App\Models\Position;
use App\Models\Vote;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ResultsController extends Controller
{
    private function getEffectiveElectionForPosition(ElectionPeriod $rootElection, Position $position, $descendants)
    {
        $endedDescendants = $descendants->filter(function ($descendant) use ($position) {
            return $descendant->end_time->lt(now()) &&
                $descendant->is_revote &&
                $descendant->positions()->where('positions.id', $position->id)->exists();
        })->sortBy('end_time')->values();

        return $endedDescendants->last() ?? $rootElection;
    }

    private function buildPositionCandidates(ElectionPeriod $sourceElection, Position $position)
    {
        $query = $position->candidates()
            ->where('is_disqualified', false)
            ->withCount(['votes' => function ($q) use ($sourceElection) {
                $q->where('election_period_id', $sourceElection->id);
            }]);

        return $query->orderByDesc('votes_count')->get();
    }

    private function preparePositionResults($positions, ElectionPeriod $election)
    {
        $rootElection = ElectionPeriod::getRootElection($election);
        $descendants = ElectionPeriod::getDescendantsForRoot($rootElection);

        foreach ($positions as $position) {
            $sourceElection = $this->getEffectiveElectionForPosition($rootElection, $position, $descendants);
            $candidates = $this->buildPositionCandidates($sourceElection, $position);

            $candidates = $candidates->values();
            $position->setRelation('candidates', $candidates);
            $position->winner = null;
            $position->is_tie = false;
            $position->total_position_votes = $candidates->sum('votes_count');
            $position->result_source_election_id = $sourceElection->id;

            foreach ($position->candidates as $candidate) {
                $candidate->vote_percentage = $position->total_position_votes > 0
                    ? ($candidate->votes_count / $position->total_position_votes) * 100
                    : 0;
            }

            if ($candidates->isEmpty()) {
                continue;
            }

            $topVotes = $candidates->first()->votes_count;
            $topCount = $candidates->where('votes_count', $topVotes)->count();

            if ($topVotes > 0 && $topCount > 1) {
                $position->is_tie = true;
                continue;
            }

            if ($topVotes > 0 && $topCount === 1) {
                $position->winner = $candidates->first();
            }
        }
    }

    public function index(Request $request)
    {
        ElectionPeriod::finalizeEndedElections(now());

        $activeOrUpcoming = ElectionPeriod::where('end_time', '>=', now())->exists();
        if ($activeOrUpcoming) {
            $pendingRevote = ElectionPeriod::where('is_revote', true)
                ->where('results_available', false)
                ->where('end_time', '>=', now())
                ->orderBy('start_time')
                ->with('positions')
                ->first();

            return view('student.results.no-results', compact('pendingRevote'));
        }

        $election = ElectionPeriod::where('results_available', true)
            ->where('is_revote', false)
            ->orderBy('end_time', 'desc')
            ->first();

        if (!$election) {
            return view('student.results.no-results');
        }

        $positions = $this->getPositionsForElection($election);
        $studentsCount = Student::count();

        $this->preparePositionResults($positions, $election);

        return view('student.results.index', [
            'election' => $election,
            'positions' => $positions,
            'studentsCount' => $studentsCount,
        ]);
    }

    public function history(Request $request)
    {
        $student = Auth::guard('student')->user();
        $hiddenRootIds = DB::table('student_hidden_election_results')
            ->where('student_id', $student->id)
            ->pluck('election_period_id');

        $pastElections = ElectionPeriod::where('end_time', '<', now())
            ->where('results_available', true)
            ->where('is_revote', false)
            ->when($hiddenRootIds->isNotEmpty(), function ($query) use ($hiddenRootIds) {
                $query->whereNotIn('id', $hiddenRootIds);
            })
            ->orderBy('end_time', 'desc')
            ->paginate(10);

        $totalStudents = Student::count();

        $electionsWithStats = [];
        foreach ($pastElections as $election) {
            $positions = $this->getPositionsForElection($election);
            $totalVotes = Vote::where('election_period_id', $election->id)->count();

            $this->preparePositionResults($positions, $election);

            $electionsWithStats[] = [
                'election' => $election,
                'positions' => $positions,
                'total_votes' => $totalVotes,
                'voter_turnout' => $totalStudents > 0 ? ($totalVotes / $totalStudents) * 100 : 0,
            ];
        }

        return view('student.results.history', [
            'pastElections' => $pastElections,
            'electionsWithStats' => $electionsWithStats,
            'totalStudents' => $totalStudents,
        ]);
    }

    public function destroyHistory(Request $request, ElectionPeriod $election)
    {
        if ($election->end_time->gte(now())) {
            return back()->with('error', 'Only completed election records can be removed.');
        }

        $student = Auth::guard('student')->user();
        $rootElection = ElectionPeriod::getRootElection($election);

        DB::table('student_hidden_election_results')->updateOrInsert(
            [
                'student_id' => $student->id,
                'election_period_id' => $rootElection->id,
            ],
            [
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return back()->with('success', 'This election record has been removed from your history.');
    }

    public function show(Request $request, $electionId)
    {
        $election = ElectionPeriod::findOrFail($electionId);
        $rootElection = ElectionPeriod::getRootElection($election);

        if (!$rootElection->results_available || $rootElection->is_revote) {
            return redirect()->route('student.results.index')
                ->with('error', 'Results for this election are not available yet.');
        }

        $positions = $this->getPositionsForElection($rootElection);
        $totalVotes = Vote::where('election_period_id', $rootElection->id)->count();
        $totalStudents = Student::count();

        $this->preparePositionResults($positions, $rootElection);

        return view('student.results.show', [
            'election' => $rootElection,
            'positions' => $positions,
            'totalVotes' => $totalVotes,
            'studentsCount' => $totalStudents,
        ]);
    }

    public function showPosition(Request $request, Position $position)
    {
        $election = ElectionPeriod::where('results_available', true)
            ->where('is_revote', false)
            ->orderBy('end_time', 'desc')
            ->first();

        if (!$election) {
            return redirect()->route('student.results.index')
                ->with('error', 'There are no published results yet.');
        }

        $positions = $this->getPositionsForElection($election);
        if (!$positions->contains('id', $position->id)) {
            return redirect()->route('student.results.index')
                ->with('error', 'This position does not have results for the selected election.');
        }

        $rootElection = ElectionPeriod::getRootElection($election);
        $descendants = ElectionPeriod::getDescendantsForRoot($rootElection);
        $sourceElection = $this->getEffectiveElectionForPosition($rootElection, $position, $descendants);
        $candidates = $this->buildPositionCandidates($sourceElection, $position);

        $totalPositionVotes = $candidates->sum('votes_count');

        foreach ($candidates as $candidate) {
            $candidate->vote_percentage = $totalPositionVotes > 0
                ? ($candidate->votes_count / $totalPositionVotes) * 100
                : 0;
        }

        $position->is_tie = false;
        $position->winner = null;
        if ($candidates->isNotEmpty()) {
            $topVotes = $candidates->first()->votes_count;
            $topCount = $candidates->where('votes_count', $topVotes)->count();
            if ($topVotes > 0 && $topCount > 1) {
                $position->is_tie = true;
            } elseif ($topVotes > 0 && $topCount === 1) {
                $position->winner = $candidates->first();
            }
        }

        return view('student.results.position', [
            'election' => $election,
            'position' => $position,
            'candidates' => $candidates,
            'totalPositionVotes' => $totalPositionVotes,
        ]);
    }

    public function downloadPdf(Request $request)
    {
        $election = ElectionPeriod::where('results_available', true)
            ->where('is_revote', false)
            ->orderBy('end_time', 'desc')
            ->first();

        if (!$election) {
            return redirect()->route('student.results.index')
                ->with('error', 'There are no published results yet.');
        }

        $positions = $this->getPositionsForElection($election);
        $this->preparePositionResults($positions, $election);

        $studentsCount = Student::count();
        $totalVotes = Vote::where('election_period_id', $election->id)->count();

        $pdf = Pdf::loadView('student.results.pdf', [
            'election' => $election,
            'positions' => $positions,
            'studentsCount' => $studentsCount,
            'totalVotes' => $totalVotes,
        ]);

        return $pdf->download('election-results-' . $election->id . '.pdf');
    }

    private function getPositionsForElection(ElectionPeriod $election)
    {
        $positionsQuery = $election->positions()->where('positions.is_active', true)->orderBy('order');
        if ($positionsQuery->exists()) {
            return $positionsQuery->get();
        }

        return Position::where('is_active', true)->orderBy('order')->get();
    }
}
