<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\ElectionPeriod;
use App\Models\Position;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class VotingController extends Controller
{
    public function index()
    {
        // This will be called when user visits /student/voting
        return $this->checkVotingStatus();
    }

    public function checkVotingStatus()
    {
        $student = Auth::guard('student')->user();
        $activeElection = ElectionPeriod::where('is_active', true)->first();

        if (!$activeElection) {
            return redirect()->route('student.dashboard')
                ->with('error', 'No active election period found.');
        }

        // Check if student has already voted
        $hasVoted = Vote::where('student_id', $student->id)
            ->where('election_period_id', $activeElection->id)
            ->exists();

        if ($hasVoted) {
            return redirect()->route('student.dashboard')
                ->with('info', 'You have already voted in this election.');
        }

        // Check if voting period is active
        if (!$activeElection->isVotingOpen()) {
            if (now()->lt($activeElection->start_time)) {
                return redirect()->route('student.dashboard')
                    ->with('warning', 'Voting will start on ' . $activeElection->start_time->format('M d, Y h:i A'));
            } else {
                return redirect()->route('student.dashboard')
                    ->with('error', 'Voting period has ended.');
            }
        }

        // All checks passed - proceed to start voting
        return redirect()->route('student.voting.start');
    }

    public function start()
    {
        $student = Auth::guard('student')->user();
        $activeElection = ElectionPeriod::where('is_active', true)->first();

        if (!$activeElection || !$activeElection->isVotingOpen()) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Voting period is not active.');
        }

        // Check if student has already voted
        $hasVoted = Vote::where('student_id', $student->id)
            ->where('election_period_id', $activeElection->id)
            ->exists();

        if ($hasVoted) {
            return redirect()->route('student.dashboard')
                ->with('info', 'You have already voted in this election.');
        }

        // Get positions for the active election (revote only uses tied positions)
        $positions = $this->getElectionPositions($activeElection);

        if ($positions->isEmpty()) {
            return redirect()->route('student.dashboard')
                ->with('error', 'No positions available for voting.');
        }

        $votes = Session::get('votes', []);
        $hasActiveSession = Session::get('voting_session') && is_array($votes);

        // Resume an active in-progress session instead of resetting it.
        if ($hasActiveSession) {
            $positionIds = $positions->pluck('id')->toArray();
            $firstIncompleteIndex = null;

            foreach ($positionIds as $index => $positionId) {
                if (!array_key_exists($positionId, $votes)) {
                    $firstIncompleteIndex = $index;
                    break;
                }
            }

            if ($firstIncompleteIndex === null) {
                Session::put('current_position_index', max(count($positionIds) - 1, 0));
                return redirect()->route('student.voting.review');
            }

            Session::put('current_position_index', $firstIncompleteIndex);
            return redirect()->route('student.voting.position', ['position' => $positionIds[$firstIncompleteIndex]]);
        }

        // Start a new voting session.
        Session::put('voting_session', true);
        Session::put('current_position_index', 0);
        Session::put('votes', []);

        $firstPosition = $positions->first();
        return redirect()->route('student.voting.position', ['position' => $firstPosition->id]);
    }

    public function showPosition(Position $position)
    {
        // Check if voting session is active
        if (!Session::get('voting_session')) {
            return redirect()->route('student.voting.check');
        }

        $activeElection = ElectionPeriod::where('is_active', true)->first();
        $student = Auth::guard('student')->user();

        // Check if voting period is still open
        if (!$activeElection || !$activeElection->isVotingOpen()) {
            Session::forget('voting_session');
            return redirect()->route('student.dashboard')
                ->with('error', 'Voting period has ended.');
        }

        // Ensure position is part of this election
        $positions = $this->getElectionPositions($activeElection);
        if (!$positions->contains('id', $position->id)) {
            return redirect()->route('student.dashboard')
                ->with('error', 'This position is not part of the current election.');
        }

        // Check if student has voted for this position already in this session
        $votes = Session::get('votes', []);
        $selectedCandidateId = $votes[$position->id] ?? null;
        $hasVotedForPosition = $selectedCandidateId !== null;

        // FIXED: Removed ->with('student') from the query
        $candidates = Candidate::where('position_id', $position->id)
            ->where('is_disqualified', false);

        $candidates = $candidates->get(); // Removed ->with('student')

        // Get all positions to show progress
        $positions = $this->getElectionPositions($activeElection);

        $currentPositionIndex = Session::get('current_position_index', 0);

        return view('student.voting.position', compact(
            'position',
            'candidates',
            'positions',
            'currentPositionIndex',
            'hasVotedForPosition',
            'selectedCandidateId'
        ));
    }

    public function voteForPosition(Request $request, Position $position)
    {
        // Check if voting session is active
        if (!Session::get('voting_session')) {
            return response()->json(['error' => 'Voting session expired. Please start again.'], 400);
        }

        $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
        ]);

        $candidate = Candidate::where('id', $request->candidate_id)
            ->where('position_id', $position->id)
            ->where('is_disqualified', false)
            ->first();

        if (!$candidate) {
            return response()->json([
                'error' => 'Invalid candidate selection for this position.'
            ], 422);
        }

        $activeElection = ElectionPeriod::where('is_active', true)->first();
        $student = Auth::guard('student')->user();

        // Validate voting period
        if (!$activeElection || !$activeElection->isVotingOpen()) {
            Session::forget('voting_session');
            return response()->json(['error' => 'Voting period has ended.'], 400);
        }

        // Store vote in session
        $votes = Session::get('votes', []);
        $votes[$position->id] = $request->candidate_id;
        Session::put('votes', $votes);

        // Update current position index
        $positions = $this->getElectionPositions($activeElection);
        $currentIndex = array_search($position->id, $positions->pluck('id')->toArray());
        
        if ($currentIndex !== false && isset($positions[$currentIndex + 1])) {
            // Move to next position
            Session::put('current_position_index', $currentIndex + 1);
            $nextPosition = $positions[$currentIndex + 1];
            return response()->json([
                'success' => true,
                'next_url' => route('student.voting.position', ['position' => $nextPosition->id])
            ]);
        } else {
            // Last position completed, go to review
            Session::put('current_position_index', $currentIndex);
            return response()->json([
                'success' => true,
                'next_url' => route('student.voting.review')
            ]);
        }
    }

    public function review()
    {
        // Check if voting session is active
        if (!Session::get('voting_session')) {
            return redirect()->route('student.voting.check');
        }

        $votes = Session::get('votes', []);
        
        if (empty($votes)) {
            return redirect()->route('student.voting.start');
        }

        // Get all positions and candidates for review
        $activeElection = ElectionPeriod::where('is_active', true)->first();
        if (!$activeElection) {
            return redirect()->route('student.dashboard')
                ->with('error', 'No active election period found.');
        }

        $positions = $this->getElectionPositions($activeElection)->load([
            'candidates' => function($query) {
                $query->where('is_disqualified', false);
            }
        ]);

        // Prepare vote data for review
        $voteDetails = [];
        foreach ($positions as $position) {
            if (isset($votes[$position->id])) {
                $candidate = Candidate::find($votes[$position->id]);
                if ($candidate) {
                    $voteDetails[] = [
                        'position' => $position,
                        'candidate' => $candidate
                    ];
                }
            }
        }

        return view('student.voting.review', compact('voteDetails', 'positions'));
    }

    public function submitAllVotes()
    {
        // Check if voting session is active
        if (!Session::get('voting_session')) {
            return redirect()->route('student.voting.check');
        }

        $votes = Session::get('votes', []);
        $activeElection = ElectionPeriod::where('is_active', true)->first();
        $positions = $activeElection ? $this->getElectionPositions($activeElection) : collect();
        
        if (count($votes) !== $positions->count()) {
            return redirect()->route('student.voting.start')
                ->with('error', 'Please vote for all positions before submitting.');
        }

        $student = Auth::guard('student')->user();

        if (!$activeElection || !$activeElection->isVotingOpen()) {
            Session::forget(['voting_session', 'votes', 'current_position_index']);
            return redirect()->route('student.dashboard')
                ->with('error', 'Voting period is no longer active.');
        }

        // Check if student has already voted in database
        $hasVoted = Vote::where('student_id', $student->id)
            ->where('election_period_id', $activeElection->id)
            ->exists();

        if ($hasVoted) {
            Session::forget('voting_session');
            return redirect()->route('student.dashboard')
                ->with('error', 'You have already voted in this election.');
        }

        $validPositionIds = $positions->pluck('id')->toArray();

        try {
            DB::transaction(function () use ($votes, $validPositionIds, $student, $activeElection) {
                foreach ($votes as $positionId => $candidateId) {
                    if (!in_array((int) $positionId, $validPositionIds, true)) {
                        throw new \RuntimeException('Invalid position submitted.');
                    }

                    $candidate = Candidate::where('id', $candidateId)
                        ->where('position_id', $positionId)
                        ->where('is_disqualified', false)
                        ->first();

                    if (!$candidate) {
                        throw new \RuntimeException('One or more selected candidates are invalid.');
                    }

                    Vote::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'position_id' => $positionId,
                            'election_period_id' => $activeElection->id,
                        ],
                        [
                            'candidate_id' => $candidateId,
                        ]
                    );
                }
            });
        } catch (\Throwable $e) {
            return redirect()->route('student.voting.review')
                ->with('error', $e->getMessage());
        }

        // Clear voting session
        Session::forget(['voting_session', 'votes', 'current_position_index']);

        return redirect()->route('student.dashboard')
            ->with('success', 'Vote submitted. Your ballot has been recorded successfully.');
    }

    public function cancelVotingSession()
    {
        // Clear voting session
        Session::forget('voting_session');
        Session::forget('votes');
        Session::forget('current_position_index');

        return redirect()->route('student.dashboard')
            ->with('info', 'Voting session cancelled.');
    }

    // You can keep your original vote method as an alternative API endpoint
    public function vote(Request $request)
    {
        $student = Auth::guard('student')->user();
        $activeElection = ElectionPeriod::where('is_active', true)->first();

        if (!$activeElection || !$activeElection->isVotingOpen()) {
            return response()->json(['error' => 'Voting period has ended.'], 400);
        }

        if ($student->hasVotedInPeriod($activeElection->id)) {
            return response()->json(['error' => 'You have already voted in this election.'], 400);
        }

        $request->validate([
            'position_id' => 'required|exists:positions,id',
            'candidate_id' => 'required|exists:candidates,id',
        ]);

        // Check if student has already voted for this position
        $existingVote = Vote::where('student_id', $student->id)
            ->where('position_id', $request->position_id)
            ->where('election_period_id', $activeElection->id)
            ->first();

        if ($existingVote) {
            $existingVote->update(['candidate_id' => $request->candidate_id]);
        } else {
            Vote::create([
                'student_id' => $student->id,
                'candidate_id' => $request->candidate_id,
                'position_id' => $request->position_id,
                'election_period_id' => $activeElection->id,
            ]);
        }

        return response()->json(['success' => 'Vote recorded successfully.']);
    }

    // You can keep finalizeVotes if needed, but submitAllVotes already handles it
    public function finalizeVotes()
    {
        return $this->submitAllVotes();
    }

    private function getElectionPositions(ElectionPeriod $election)
    {
        $positionsQuery = $election->positions()->where('positions.is_active', true)->orderBy('order');
        if ($positionsQuery->exists()) {
            return $positionsQuery->get();
        }

        // Re-vote must stay limited to explicitly attached tied positions.
        if ($election->is_revote) {
            return collect();
        }

        return Position::where('is_active', true)->orderBy('order')->get();
    }
}
