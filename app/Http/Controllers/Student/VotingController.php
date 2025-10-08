<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\ElectionPeriod;
use App\Models\Position;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VotingController extends Controller
{
    public function index()
    {
        $activeElection = ElectionPeriod::where('is_active', true)->first();
        $positions = Position::where('is_active', true)
            ->orderBy('order')
            ->with(['candidates' => function($query) {
                $query->where('is_disqualified', false);
            }])
            ->get();

        return view('student.voting.index', compact('positions', 'activeElection'));
    }

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

    public function submitAllVotes()
    {
        $student = Auth::guard('student')->user();
        $activeElection = ElectionPeriod::where('is_active', true)->first();

        // Check if student has voted for all positions
        $positionsCount = Position::where('is_active', true)->count();
        $studentVotesCount = Vote::where('student_id', $student->id)
            ->where('election_period_id', $activeElection->id)
            ->count();

        if ($positionsCount !== $studentVotesCount) {
            return redirect()->route('student.voting.index')
                ->with('error', 'Please vote for all positions before submitting.');
        }

        return view('student.voting.submit');
    }

    public function finalizeVotes()
    {
        $student = Auth::guard('student')->user();
        $activeElection = ElectionPeriod::where('is_active', true)->first();

        // Additional validation can be added here

        return redirect()->route('student.dashboard')
            ->with('success', 'Thank you for voting! Your votes have been submitted successfully.');
    }
}