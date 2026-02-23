<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ElectionPeriod;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $student = Auth::guard('student')->user();
        $now = Carbon::now('Africa/Blantyre');

        $this->syncElectionState($now);

        $activeElection = ElectionPeriod::where('is_active', true)->first();
        $revoteElection = ElectionPeriod::where('is_revote', true)
            ->where('results_available', false)
            ->where('end_time', '>=', $now)
            ->with('positions')
            ->orderBy('start_time')
            ->first();
        $nextElection = ElectionPeriod::where('start_time', '>', $now)
            ->orderBy('start_time')
            ->first();
        $activeOrUpcoming = ElectionPeriod::where('end_time', '>=', $now)->exists();
        
        $hasVoted = $activeElection ? $student->hasVotedInPeriod($activeElection->id) : false;
        $canVote = $activeElection && $activeElection->isVotingOpen() && !$hasVoted;
        $revoteCanVote = $revoteElection
            ? ($revoteElection->isVotingOpen() && !$student->hasVotedInPeriod($revoteElection->id))
            : false;
        $showResults = !$activeOrUpcoming && ElectionPeriod::where('results_available', true)
            ->where('is_revote', false)
            ->exists();
        ['registered_students' => $registeredStudents, 'ballots_cast' => $ballotsCast, 'turnout_percent' => $turnoutPercent] =
            $this->getParticipationMetrics($activeElection);

        return view('student.dashboard', compact(
            'student', 
            'activeElection', 
            'revoteElection',
            'revoteCanVote',
            'nextElection',
            'canVote', 
            'hasVoted',
            'showResults',
            'registeredStudents',
            'ballotsCast',
            'turnoutPercent'
        ));
    }

    public function votingStatus(Request $request)
    {
        $student = Auth::guard('student')->user();
        $now = Carbon::now('Africa/Blantyre');

        $this->syncElectionState($now);

        $activeElection = ElectionPeriod::where('is_active', true)
            ->with('positions')
            ->first();
        $revoteElection = ElectionPeriod::where('is_revote', true)
            ->where('results_available', false)
            ->where('end_time', '>=', $now)
            ->with('positions')
            ->orderBy('start_time')
            ->first();
        $nextElection = ElectionPeriod::where('start_time', '>', $now)
            ->orderBy('start_time')
            ->first();
        $hasVoted = $activeElection ? $student->hasVotedInPeriod($activeElection->id) : false;
        $canVote = $activeElection && $activeElection->isVotingOpen() && !$hasVoted;
        $revoteCanVote = $revoteElection
            ? ($revoteElection->isVotingOpen() && !$student->hasVotedInPeriod($revoteElection->id))
            : false;
        $activeOrUpcoming = ElectionPeriod::where('end_time', '>=', $now)->exists();
        $showResults = !$activeOrUpcoming && ElectionPeriod::where('results_available', true)
            ->where('is_revote', false)
            ->exists();
        ['registered_students' => $registeredStudents, 'ballots_cast' => $ballotsCast, 'turnout_percent' => $turnoutPercent] =
            $this->getParticipationMetrics($activeElection);

        return response()->json([
            'now_iso' => $now->format('c'),
            'can_vote' => (bool) $canVote,
            'has_voted' => (bool) $hasVoted,
            'active_election_title' => $activeElection ? $activeElection->title : null,
            'active_election_description' => $activeElection ? ($activeElection->description ?: 'No description provided.') : null,
            'active_election_id' => $activeElection ? $activeElection->id : null,
            'is_revote' => $activeElection ? (bool) $activeElection->is_revote : false,
            'active_results_available' => $activeElection ? (bool) $activeElection->results_available : false,
            'active_has_ended' => $activeElection ? (bool) $activeElection->hasEnded() : false,
            'active_start_iso' => $activeElection ? $activeElection->start_time->format('c') : null,
            'active_end_iso' => $activeElection ? $activeElection->end_time->format('c') : null,
            'active_start_display' => $activeElection ? $activeElection->start_time->format('M j, Y g:i A') : null,
            'active_end_display' => $activeElection ? $activeElection->end_time->format('M j, Y g:i A') : null,
            'active_revote_positions_text' => $activeElection && $activeElection->is_revote
                ? ($activeElection->positions->pluck('title')->implode(', ') ?: 'selected positions')
                : null,
            'revote_can_vote' => (bool) $revoteCanVote,
            'revote_election_id' => $revoteElection ? $revoteElection->id : null,
            'revote_positions_text' => $revoteElection
                ? ($revoteElection->positions->pluck('title')->implode(', ') ?: 'selected positions')
                : null,
            'revote_start_display' => $revoteElection ? $revoteElection->start_time->format('M j, Y g:i A') : null,
            'next_election_id' => $nextElection ? $nextElection->id : null,
            'next_election_title' => $nextElection ? $nextElection->title : null,
            'next_election_start_iso' => $nextElection ? $nextElection->start_time->format('c') : null,
            'next_election_start_display' => $nextElection ? $nextElection->start_time->format('M j, Y g:i A') : null,
            'registered_students' => $registeredStudents,
            'ballots_cast' => $ballotsCast,
            'turnout_percent' => $turnoutPercent,
            'show_results' => $showResults,
        ]);
    }

    private function syncElectionState(Carbon $now): void
    {
        $cacheKey = 'election_state_sync:' . intdiv($now->timestamp, 10);
        Cache::remember($cacheKey, now()->addSeconds(12), function () use ($now) {
            $activeCandidate = ElectionPeriod::where('start_time', '<=', $now)
                ->where('end_time', '>=', $now)
                ->orderByDesc('start_time')
                ->first();

            if ($activeCandidate) {
                ElectionPeriod::where('is_active', true)
                    ->where('id', '!=', $activeCandidate->id)
                    ->update(['is_active' => false]);

                if (!$activeCandidate->is_active) {
                    $activeCandidate->update(['is_active' => true]);
                }
            } else {
                ElectionPeriod::where('is_active', true)->update(['is_active' => false]);
            }

            ElectionPeriod::finalizeEndedElections($now);
            return true;
        });
    }

    private function getParticipationMetrics(?ElectionPeriod $activeElection): array
    {
        $cacheKey = 'dashboard_participation_metrics:' . ($activeElection?->id ?? 'none');

        return Cache::remember($cacheKey, now()->addSeconds(10), function () use ($activeElection) {
            $registeredStudents = Student::count();
            $ballotsCast = $activeElection
                ? $activeElection->votes()->distinct('student_id')->count('student_id')
                : 0;

            return [
                'registered_students' => $registeredStudents,
                'ballots_cast' => $ballotsCast,
                'turnout_percent' => $registeredStudents > 0
                    ? round(($ballotsCast / $registeredStudents) * 100, 1)
                    : 0,
            ];
        });
    }
}
