<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'registration_number',
        'email',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Find student by registration number for authentication
     */
    public function findForPassport($username)
    {
        return $this->where('registration_number', $username)->first();
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function hasVotedInPeriod($electionPeriodId)
    {
        return $this->votes()->where('election_period_id', $electionPeriodId)->exists();
    }

    public function getVoteForPosition($positionId, $electionPeriodId)
    {
        return $this->votes()
            ->where('position_id', $positionId)
            ->where('election_period_id', $electionPeriodId)
            ->first();
    }
}