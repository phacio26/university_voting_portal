<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ElectionPeriod;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $student = Auth::guard('student')->user();
        $activeElection = ElectionPeriod::where('is_active', true)->first();
        
        $hasVoted = $activeElection ? $student->hasVotedInPeriod($activeElection->id) : false;
        $canVote = $activeElection && $activeElection->isVotingOpen() && !$hasVoted;
        $showResults = $activeElection && ($activeElection->hasEnded() || $activeElection->results_available);

        return view('student.dashboard', compact(
            'student', 
            'activeElection', 
            'canVote', 
            'hasVoted',
            'showResults'
        ));
    }
}