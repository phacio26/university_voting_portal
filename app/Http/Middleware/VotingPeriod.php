<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ElectionPeriod;

class VotingPeriod
{
    public function handle(Request $request, Closure $next)
    {
        $activeElection = ElectionPeriod::where('is_active', true)->first();
        
        if (!$activeElection || !$activeElection->isVotingOpen()) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Voting is currently closed.');
        }

        return $next($request);
    }
}