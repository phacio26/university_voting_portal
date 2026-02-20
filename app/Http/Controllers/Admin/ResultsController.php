<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ElectionPeriod;
use App\Models\Position;
use App\Models\Student;
use App\Models\Vote;
use Carbon\Carbon;

class ResultsController extends Controller
{
    public function index()
    {
        $now = Carbon::now('Africa/Blantyre');
        ElectionPeriod::finalizeEndedElections($now);

        $elections = ElectionPeriod::where('is_revote', false)
            ->where('end_time', '<', $now)
            ->orderByDesc('end_time')
            ->paginate(10);

        $elections->getCollection()->transform(function ($election) use ($now) {
            $election->pending_resolution = ElectionPeriod::rootHasPendingRevoteOrTie($election, $now);
            $election->results_visible_to_students = (bool) $election->results_available;
            return $election;
        });

        return view('admin.results.index', compact('elections'));
    }

    public function show(ElectionPeriod $election)
    {
        $now = Carbon::now('Africa/Blantyre');
        $rootElection = ElectionPeriod::getRootElection($election);

        if ($rootElection->end_time->gte($now)) {
            return redirect()->route('admin.results.index')
                ->with('error', 'This election is still in progress. Results are available after it closes.');
        }

        $positions = $this->getPositionsForElection($rootElection);
        $this->preparePositionResults($positions, $rootElection);

        $studentsCount = Student::count();
        $totalBallots = Vote::where('election_period_id', $rootElection->id)
            ->distinct('student_id')
            ->count('student_id');
        $pendingResolution = ElectionPeriod::rootHasPendingRevoteOrTie($rootElection, $now);

        return view('admin.results.show', [
            'election' => $rootElection,
            'positions' => $positions,
            'studentsCount' => $studentsCount,
            'totalBallots' => $totalBallots,
            'pendingResolution' => $pendingResolution,
        ]);
    }

    private function getPositionsForElection(ElectionPeriod $election)
    {
        $positionsQuery = $election->positions()->where('positions.is_active', true)->orderBy('order');
        if ($positionsQuery->exists()) {
            return $positionsQuery->get();
        }

        return Position::where('is_active', true)->orderBy('order')->get();
    }

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

    private function preparePositionResults($positions, ElectionPeriod $election): void
    {
        $rootElection = ElectionPeriod::getRootElection($election);
        $descendants = ElectionPeriod::getDescendantsForRoot($rootElection);

        foreach ($positions as $position) {
            $sourceElection = $this->getEffectiveElectionForPosition($rootElection, $position, $descendants);
            $candidates = $this->buildPositionCandidates($sourceElection, $position)->values();

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
}
