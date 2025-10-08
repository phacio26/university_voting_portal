<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ElectionPeriod;
use App\Models\Position;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ResultsController extends Controller
{
    public function index()
    {
        $election = ElectionPeriod::where('results_available', true)
            ->orWhereHas('votes')
            ->latest()
            ->first();

        if (!$election) {
            return redirect()->route('student.dashboard')
                ->with('error', 'No election results available yet.');
        }

        $positions = Position::where('is_active', true)
            ->with(['candidates' => function($query) use ($election) {
                $query->where('is_disqualified', false)
                    ->withCount(['votes as vote_count' => function($query) use ($election) {
                        $query->where('election_period_id', $election->id);
                    }])
                    ->orderBy('vote_count', 'desc');
            }])
            ->orderBy('order')
            ->get();

        // Calculate percentages and determine winners
        foreach ($positions as $position) {
            $totalVotes = $position->candidates->sum('vote_count');
            
            foreach ($position->candidates as $candidate) {
                $candidate->vote_percentage = $totalVotes > 0 ? 
                    round(($candidate->vote_count / $totalVotes) * 100, 2) : 0;
            }

            // Determine winner (handle ties)
            if ($position->candidates->count() > 0) {
                $maxVotes = $position->candidates->max('vote_count');
                $winners = $position->candidates->where('vote_count', $maxVotes);
                
                if ($winners->count() === 1) {
                    $position->winner = $winners->first();
                } else {
                    $position->is_tie = true;
                    $position->winners = $winners;
                }
            }
        }

        return view('student.results.index', compact('election', 'positions'));
    }

    public function downloadPdf()
    {
        $election = ElectionPeriod::where('results_available', true)
            ->orWhereHas('votes')
            ->latest()
            ->first();

        if (!$election) {
            return redirect()->back()->with('error', 'No results available for download.');
        }

        $positions = Position::where('is_active', true)
            ->with(['candidates' => function($query) use ($election) {
                $query->where('is_disqualified', false)
                    ->withCount(['votes as vote_count' => function($query) use ($election) {
                        $query->where('election_period_id', $election->id);
                    }])
                    ->orderBy('vote_count', 'desc');
            }])
            ->orderBy('order')
            ->get();

        // Calculate percentages
        foreach ($positions as $position) {
            $totalVotes = $position->candidates->sum('vote_count');
            foreach ($position->candidates as $candidate) {
                $candidate->vote_percentage = $totalVotes > 0 ? 
                    round(($candidate->vote_count / $totalVotes) * 100, 2) : 0;
            }
        }

        $pdf = Pdf::loadView('student.results.pdf', compact('election', 'positions'));
        
        return $pdf->download('election-results-' . $election->id . '.pdf');
    }
}