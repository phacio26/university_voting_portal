<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PositionController extends Controller
{
    public function index()
    {
        $positions = Position::withCount('candidates')
            ->orderBy('order')
            ->get();
        return view('admin.positions.index', compact('positions'));
    }

    public function create()
    {
        return view('admin.positions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:positions',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
        ]);

        Position::create($request->all());

        return redirect()->route('admin.positions.index')
            ->with('success', 'Position created successfully.');
    }

    public function edit(Position $position)
    {
        return view('admin.positions.edit', compact('position'));
    }

    public function update(Request $request, Position $position)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:positions,title,' . $position->id,
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
        ]);

        $position->update($request->all());

        return redirect()->route('admin.positions.index')
            ->with('success', 'Position updated successfully.');
    }

    public function destroy(Position $position)
    {
        DB::transaction(function () use ($position) {
            $candidatePhotos = Candidate::where('position_id', $position->id)
                ->whereNotNull('photo')
                ->pluck('photo');

            if ($candidatePhotos->isNotEmpty()) {
                foreach ($candidatePhotos as $photo) {
                    Storage::disk('public')->delete($photo);
                }
            }

            Candidate::where('position_id', $position->id)->delete();
            $position->delete();
        });

        return redirect()->route('admin.positions.index')
            ->with('success', 'Position deleted successfully.');
    }

    public function toggleStatus(Position $position)
    {
        $position->update(['is_active' => !$position->is_active]);
        
        $status = $position->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Position {$status} successfully.");
    }
}
