<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_active' => 'boolean',
        'results_available' => 'boolean',
    ];

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