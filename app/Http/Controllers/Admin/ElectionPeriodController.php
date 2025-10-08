<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ElectionPeriod;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ElectionPeriodController extends Controller
{
    public function index()
    {
        $electionPeriods = ElectionPeriod::latest()->get();
        return view('admin.election-periods.index', compact('electionPeriods'));
    }

    public function create()
    {
        return view('admin.election-periods.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
        ]);

        // Convert to Malawi time
        $startTime = Carbon::parse($request->start_time, 'Africa/Blantyre');
        $endTime = Carbon::parse($request->end_time, 'Africa/Blantyre');

        ElectionPeriod::create([
            'title' => $request->title,
            'description' => $request->description,
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]);

        return redirect()->route('admin.election-periods.index')
            ->with('success', 'Election period created successfully.');
    }

    public function edit(ElectionPeriod $electionPeriod)
    {
        return view('admin.election-periods.edit', compact('electionPeriod'));
    }

    public function update(Request $request, ElectionPeriod $electionPeriod)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $startTime = Carbon::parse($request->start_time, 'Africa/Blantyre');
        $endTime = Carbon::parse($request->end_time, 'Africa/Blantyre');

        $electionPeriod->update([
            'title' => $request->title,
            'description' => $request->description,
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]);

        return redirect()->route('admin.election-periods.index')
            ->with('success', 'Election period updated successfully.');
    }

    public function destroy(ElectionPeriod $electionPeriod)
    {
        $electionPeriod->delete();
        return redirect()->route('admin.election-periods.index')
            ->with('success', 'Election period deleted successfully.');
    }

    public function activate(ElectionPeriod $electionPeriod)
    {
        // Deactivate all other election periods
        ElectionPeriod::where('id', '!=', $electionPeriod->id)->update(['is_active' => false]);
        
        $electionPeriod->update(['is_active' => true]);

        return redirect()->back()->with('success', 'Election period activated successfully.');
    }

    public function deactivate(ElectionPeriod $electionPeriod)
    {
        $electionPeriod->update(['is_active' => false]);
        return redirect()->back()->with('success', 'Election period deactivated successfully.');
    }

    public function makeResultsAvailable(ElectionPeriod $electionPeriod)
    {
        $electionPeriod->update(['results_available' => true]);
        return redirect()->back()->with('success', 'Results made available to students.');
    }
}