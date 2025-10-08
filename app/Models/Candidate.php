<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'bio',
        'photo',
        'position_id',
        'is_disqualified',
        'disqualification_reason',
    ];

    protected $casts = [
        'is_disqualified' => 'boolean',
    ];

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function getVoteCount($electionPeriodId)
    {
        return $this->votes()->where('election_period_id', $electionPeriodId)->count();
    }

    public function getVotePercentage($electionPeriodId, $totalVotes)
    {
        if ($totalVotes == 0) return 0;
        return ($this->getVoteCount($electionPeriodId) / $totalVotes) * 100;
    }
}