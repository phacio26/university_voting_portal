<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function candidates()
    {
        return $this->hasMany(Candidate::class)->where('is_disqualified', false);
    }

    public function allCandidates()
    {
        return $this->hasMany(Candidate::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function getVoteCount($electionPeriodId)
    {
        return $this->votes()->where('election_period_id', $electionPeriodId)->count();
    }
}