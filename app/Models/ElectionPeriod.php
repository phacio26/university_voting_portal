<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Candidate;
use Illuminate\Support\Collection;

class ElectionPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'start_time',
        'end_time',
        'is_active',
        'results_available',
        'is_revote',
        'parent_id',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_active' => 'boolean',
        'results_available' => 'boolean',
        'is_revote' => 'boolean',
    ];

    public function positions()
    {
        return $this->belongsToMany(Position::class, 'election_period_positions');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function revotes()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public static function finalizeEndedElections(Carbon $now)
    {
        $ended = self::where('end_time', '<', $now)
            ->where('results_available', false)
            ->get();

        foreach ($ended as $election) {
            $tiePositionIds = self::getTiePositionIds($election);

            if (!empty($tiePositionIds)) {
                $election->update([
                    'results_available' => false,
                    'is_active' => false,
                ]);

                $exists = self::where('parent_id', $election->id)
                    ->where('is_revote', true)
                    ->exists();

                if (!$exists) {
                    $durationSeconds = $election->end_time->diffInSeconds($election->start_time);
                    $revoteStart = $election->end_time->copy();
                    $revoteEnd = $revoteStart->copy()->addSeconds($durationSeconds);

                    $revote = self::create([
                        'title' => $election->title . ' (Re-vote)',
                        'description' => 'Re-vote for positions with equal votes.',
                        'start_time' => $revoteStart,
                        'end_time' => $revoteEnd,
                        'is_active' => false,
                        'results_available' => false,
                        'is_revote' => true,
                        'parent_id' => $election->id,
                    ]);

                    $revote->positions()->sync($tiePositionIds);
                }
            } else {
                $election->update([
                    'results_available' => true,
                    'is_active' => false,
                ]);
            }
        }

        $roots = self::whereNull('parent_id')
            ->where('end_time', '<', $now)
            ->where('results_available', false)
            ->get();

        foreach ($roots as $root) {
            if (!self::rootHasPendingRevoteOrTie($root, $now)) {
                $root->update([
                    'results_available' => true,
                    'is_active' => false,
                ]);
            }
        }
    }

    public static function getDescendantsForRoot(self $root): Collection
    {
        $all = collect();
        $queue = collect([$root->id]);

        while ($queue->isNotEmpty()) {
            $children = self::whereIn('parent_id', $queue->all())->get();
            if ($children->isEmpty()) {
                break;
            }

            $all = $all->merge($children);
            $queue = $children->pluck('id');
        }

        return $all;
    }

    public static function getRootElection(self $election): self
    {
        $current = $election;
        while ($current->parent_id) {
            $parent = self::find($current->parent_id);
            if (!$parent || $parent->id === $current->id) {
                break;
            }
            $current = $parent;
        }

        return $current;
    }

    public static function rootHasPendingRevoteOrTie(self $root, Carbon $now): bool
    {
        $descendants = self::getDescendantsForRoot($root);
        $endedDescendants = $descendants->filter(fn ($e) => $e->end_time->lt($now));
        $openDescendants = $descendants->filter(fn ($e) => $e->end_time->gte($now));

        if ($openDescendants->isNotEmpty()) {
            return true;
        }

        $positionsQuery = $root->positions()->where('positions.is_active', true)->orderBy('order');
        $positions = $positionsQuery->exists()
            ? $positionsQuery->get()
            : Position::where('is_active', true)->orderBy('order')->get();

        foreach ($positions as $position) {
            $positionElections = collect([$root])->merge(
                $endedDescendants->filter(function ($election) use ($position) {
                    return $election->is_revote &&
                        $election->positions()->where('positions.id', $position->id)->exists();
                })
            )->sortBy('end_time')->values();

            $latestForPosition = $positionElections->last();
            if (!$latestForPosition) {
                continue;
            }

            $tieIds = self::getTieCandidateIdsForPosition($latestForPosition, $position->id);
            if (!empty($tieIds)) {
                return true;
            }
        }

        return false;
    }

    private static function getTiePositionIds(self $election)
    {
        $positionsQuery = $election->positions()->where('positions.is_active', true)->orderBy('order');
        $positions = $positionsQuery->exists()
            ? $positionsQuery->get()
            : Position::where('is_active', true)->orderBy('order')->get();

        $tiePositionIds = [];
        foreach ($positions as $position) {
            $candidates = $position->candidates()
                ->where('is_disqualified', false)
                ->withCount(['votes' => function ($q) use ($election) {
                    $q->where('election_period_id', $election->id);
                }])
                ->orderByDesc('votes_count')
                ->get();

            if ($candidates->count() > 1) {
                $first = $candidates->get(0)->votes_count;
                $second = $candidates->get(1)->votes_count;
                if ($first > 0 && $first === $second) {
                    $tiePositionIds[] = $position->id;
                }
            }
        }

        return $tiePositionIds;
    }

    public static function getTieCandidateIdsForPosition(self $election, int $positionId)
    {
        $candidates = Candidate::where('position_id', $positionId)
            ->where('is_disqualified', false)
            ->withCount(['votes' => function ($q) use ($election) {
                $q->where('election_period_id', $election->id);
            }])
            ->orderByDesc('votes_count')
            ->get();

        if ($candidates->count() < 2) {
            return [];
        }

        $topVotes = $candidates->first()->votes_count;
        if ($topVotes <= 0) {
            return [];
        }

        $topCandidates = $candidates->where('votes_count', $topVotes);
        return $topCandidates->count() > 1 ? $topCandidates->pluck('id')->all() : [];
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function isVotingOpen()
    {
        $now = Carbon::now('Africa/Blantyre');
        return $this->is_active && 
               $now->between($this->start_time, $this->end_time);
    }

    public function hasEnded()
    {
        $now = Carbon::now('Africa/Blantyre');
        return $now->greaterThan($this->end_time);
    }

    public function getTotalVotesCount()
    {
        return $this->votes()->count();
    }

    public function getVoterTurnout($totalStudents)
    {
        if ($totalStudents == 0) return 0;
        return ($this->getTotalVotesCount() / $totalStudents) * 100;
    }
}
