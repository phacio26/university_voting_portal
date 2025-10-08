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

        // Recent activity
        $recentVotes = Vote::with(['student', 'candidate', 'position'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentVotes', 'activeElection'));
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