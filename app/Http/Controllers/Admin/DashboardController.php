<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\ElectionPeriod;
use App\Models\Position;
use App\Models\Student;
use App\Models\Vote;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private function buildRaceMonitorAndPulse(ElectionPeriod $activeElection, int $totalStudents): array
    {
        $positionsQuery = $activeElection->positions()->where('positions.is_active', true)->orderBy('order');
        $positions = $positionsQuery->exists()
            ? $positionsQuery->get()
            : Position::where('is_active', true)->orderBy('order')->get();

        $positionCount = $positions->count();
        $votersParticipated = Vote::where('election_period_id', $activeElection->id)
            ->distinct('student_id')
            ->count('student_id');
        $ballotsCast = Vote::where('election_period_id', $activeElection->id)->count();
        $ballotsExpected = $totalStudents * $positionCount;

        $votingPulse = [
            'voters_participated' => $votersParticipated,
            'participation_rate' => $totalStudents > 0 ? ($votersParticipated / $totalStudents) * 100 : 0,
            'position_count' => $positionCount,
            'ballots_expected' => $ballotsExpected,
            'ballots_cast' => $ballotsCast,
            'ballot_progress' => $ballotsExpected > 0 ? ($ballotsCast / $ballotsExpected) * 100 : 0,
        ];

        $raceMonitor = $positions->map(function ($position) use ($activeElection) {
            $candidates = $position->candidates()
                ->where('is_disqualified', false)
                ->withCount(['votes' => function ($query) use ($activeElection) {
                    $query->where('election_period_id', $activeElection->id);
                }])
                ->orderByDesc('votes_count')
                ->get();

            $leader = $candidates->first();
            $runnerUp = $candidates->skip(1)->first();
            $topVotes = $leader ? $leader->votes_count : 0;
            $isTie = $topVotes > 0 && $candidates->where('votes_count', $topVotes)->count() > 1;

            return [
                'position' => $position->title,
                'total_votes' => $candidates->sum('votes_count'),
                'status' => $candidates->isEmpty()
                    ? 'no_candidates'
                    : ($topVotes === 0 ? 'no_votes' : ($isTie ? 'tie' : 'leading')),
                'leader_name' => $leader ? $leader->name : null,
                'leader_votes' => $leader ? $leader->votes_count : 0,
                'runner_up_name' => $runnerUp ? $runnerUp->name : null,
                'runner_up_votes' => $runnerUp ? $runnerUp->votes_count : 0,
                'margin' => $leader && $runnerUp ? max($leader->votes_count - $runnerUp->votes_count, 0) : 0,
            ];
        });

        return [$raceMonitor, $votingPulse];
    }

    public function index()
    {
        $stats = [
            'total_students' => Student::count(),
            'total_positions' => Position::where('is_active', true)->count(),
            'total_candidates' => Candidate::where('is_disqualified', false)->count(),
            'active_elections' => ElectionPeriod::where('is_active', true)->count(),
        ];

        $activeElection = ElectionPeriod::where('is_active', true)->first();
        
        if ($activeElection) {
            $stats['total_votes'] = $activeElection->getTotalVotesCount();
            $stats['voter_turnout'] = $activeElection->getVoterTurnout($stats['total_students']);
        }

        $raceMonitor = collect();
        $votingPulse = [
            'voters_participated' => 0,
            'participation_rate' => 0,
            'position_count' => 0,
            'ballots_expected' => 0,
            'ballots_cast' => 0,
            'ballot_progress' => 0,
        ];

        if ($activeElection) {
            [$raceMonitor, $votingPulse] = $this->buildRaceMonitorAndPulse($activeElection, $stats['total_students']);
        }

        return view('admin.dashboard', compact('stats', 'activeElection', 'raceMonitor', 'votingPulse'));
    }

    public function getLiveStatus()
    {
        $totalStudents = Student::count();
        $activeElection = ElectionPeriod::where('is_active', true)->first();

        if (!$activeElection) {
            return response()->json([
                'active_election' => false,
            ]);
        }

        [$raceMonitor, $votingPulse] = $this->buildRaceMonitorAndPulse($activeElection, $totalStudents);
        $totalVotes = $activeElection->getTotalVotesCount();
        $turnout = $activeElection->getVoterTurnout($totalStudents);

        return response()->json([
            'active_election' => true,
            'stats' => [
                'total_votes' => $totalVotes,
                'voter_turnout' => $turnout,
                'remaining_students' => max($totalStudents - $totalVotes, 0),
                'total_students' => $totalStudents,
            ],
            'voting_pulse' => $votingPulse,
            'race_monitor' => $raceMonitor->values(),
        ]);
    }

    public function getVotingStats()
    {
        $activeElection = ElectionPeriod::where('is_active', true)->first();
        
        if (!$activeElection) {
            return response()->json(['error' => 'No active election'], 404);
        }

        $positions = Position::where('is_active', true)
            ->with(['candidates' => function($query) use ($activeElection) {
                $query->where('is_disqualified', false)
                    ->withCount(['votes as vote_count' => function($query) use ($activeElection) {
                        $query->where('election_period_id', $activeElection->id);
                    }]);
            }])
            ->get();

        $data = [
            'positions' => [],
            'overall' => [
                'total_votes' => $activeElection->getTotalVotesCount(),
                'total_students' => Student::count(),
                'voter_turnout' => $activeElection->getVoterTurnout(Student::count()),
            ]
        ];

        foreach ($positions as $position) {
            $positionData = [
                'title' => $position->title,
                'candidates' => []
            ];

            foreach ($position->candidates as $candidate) {
                $positionData['candidates'][] = [
                    'name' => $candidate->name,
                    'votes' => $candidate->vote_count,
                ];
            }

            $data['positions'][] = $positionData;
        }

        return response()->json($data);
    }
}
