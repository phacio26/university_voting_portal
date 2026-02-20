<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CandidateController extends Controller
{
    public function index()
    {
        $candidates = Candidate::with('position')->latest()->get();
        return view('admin.candidates.index', compact('candidates'));
    }

    public function create()
    {
        $positions = Position::where('is_active', true)->get();
        return view('admin.candidates.create', compact('positions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'position_id' => 'required|exists:positions,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072|dimensions:min_width=300,min_height=300,max_width=4000,max_height=4000',
        ]);

        $data = $request->only(['name', 'bio', 'position_id']);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('candidates', 'public');
        }

        Candidate::create($data);

        return redirect()->route('admin.candidates.index')
            ->with('success', 'Candidate created successfully.');
    }

    public function edit(Candidate $candidate)
    {
        $positions = Position::where('is_active', true)->get();
        return view('admin.candidates.edit', compact('candidate', 'positions'));
    }

    public function update(Request $request, Candidate $candidate)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'position_id' => 'required|exists:positions,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072|dimensions:min_width=300,min_height=300,max_width=4000,max_height=4000',
        ]);

        $data = $request->only(['name', 'bio', 'position_id']);

        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($candidate->photo) {
                Storage::disk('public')->delete($candidate->photo);
            }
            $data['photo'] = $request->file('photo')->store('candidates', 'public');
        }

        $candidate->update($data);

        return redirect()->route('admin.candidates.index')
            ->with('success', 'Candidate updated successfully.');
    }

    public function destroy(Candidate $candidate)
    {
        if ($candidate->photo) {
            Storage::disk('public')->delete($candidate->photo);
        }

        $candidate->delete();
        return redirect()->route('admin.candidates.index')
            ->with('success', 'Candidate deleted successfully.');
    }

    public function disqualify(Request $request, Candidate $candidate)
    {
        $request->validate([
            'disqualification_reason' => 'required|string',
        ]);

        $candidate->update([
            'is_disqualified' => true,
            'disqualification_reason' => $request->disqualification_reason,
        ]);

        return redirect()->back()->with('success', 'Candidate disqualified successfully.');
    }

    public function reinstate(Candidate $candidate)
    {
        $candidate->update([
            'is_disqualified' => false,
            'disqualification_reason' => null,
        ]);

        return redirect()->back()->with('success', 'Candidate reinstated successfully.');
    }
}
