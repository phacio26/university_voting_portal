@extends('layouts.student-portal')

@section('title', 'Election Results')

@push('styles')
<style>
    .results-hero {
        border-radius: 18px;
        padding: 1.5rem;
        color: #0f172a;
        background:
            linear-gradient(135deg, rgba(10, 77, 145, 0.15), rgba(15, 118, 110, 0.08)),
            #ffffff;
        border: 1px solid rgba(10, 77, 145, 0.15);
        box-shadow: 0 18px 30px rgba(15, 23, 42, 0.08);
    }
    .hero-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: rgba(10, 77, 145, 0.1);
        color: #0a4d91;
        border-radius: 999px;
        padding: 0.25rem 0.75rem;
        font-size: 0.78rem;
        font-weight: 700;
    }
    .stats-card {
        border: 1px solid var(--portal-border);
        border-radius: 14px;
        background: #ffffff;
        padding: 0.85rem;
        height: 100%;
    }
    .position-panel {
        border: 1px solid var(--portal-border);
        border-radius: 14px;
        background: #fff;
    }
    .position-meta {
        font-size: 0.85rem;
        color: var(--portal-muted);
    }
    .winner-box {
        border: 1px solid #c8e6d4;
        background: #f4fbf7;
        border-radius: 12px;
        padding: 0.9rem;
        margin-bottom: 1rem;
    }
    .candidate-card {
        border: 1px solid var(--portal-border);
        border-radius: 14px;
        padding: 0.75rem;
        background: #fff;
        max-width: 420px;
        margin: 0 auto;
    }
    .candidate-card.winner {
        border-color: #b6dcc8;
        background: #f7fcf9;
    }
    .results-avatar {
        width: 200px;
        height: 200px;
        border-radius: 18px;
        object-fit: cover;
        border: 3px solid #dbe7f5;
    }
    .results-avatar-fallback {
        width: 200px;
        height: 200px;
        border-radius: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #e6eef8;
        color: #2f4f73;
        font-weight: 800;
        font-size: 3.2rem;
        border: 3px solid #dbe7f5;
    }
    .candidate-name {
        font-size: 1.05rem;
        font-weight: 800;
        margin-top: 0.6rem;
    }
    .candidate-metrics {
        font-size: 1rem;
        font-weight: 700;
        color: #0a4d91;
    }
    .candidate-card.winner .candidate-metrics {
        color: #0f5132;
    }
    .winner-declare {
        font-size: 0.85rem;
        color: #14532d;
        background: linear-gradient(135deg, #dcfce7, #bbf7d0);
        border: 1px solid #86efac;
        border-radius: 999px;
        padding: 0.28rem 0.75rem;
        font-weight: 800;
        display: inline-block;
        margin-top: 0.5rem;
        box-shadow: 0 6px 14px rgba(20, 83, 45, 0.12);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        line-height: 1.2;
    }
    .rank-badge {
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.2rem 0.6rem;
        background: #e8eff8;
        color: #1f3b5a;
    }
    .rank-badge.top {
        background: #e7f6ee;
        color: #0f5132;
    }
    .candidate-name {
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 0.2rem;
    }
    .candidate-bio {
        font-size: 0.88rem;
        color: #5b6780;
        line-height: 1.4;
    }
    .bar-wrap {
        background: #edf2f8;
        border-radius: 999px;
        overflow: hidden;
        height: 8px;
    }
    .bar-fill {
        background: linear-gradient(90deg, #0a4d91, #0f766e);
        height: 100%;
    }
    .tie-tag {
        background: #fff6e6;
        color: #8a5b00;
        border: 1px solid #ffe0b2;
        font-size: 0.72rem;
        font-weight: 700;
        border-radius: 999px;
        padding: 0.18rem 0.55rem;
    }
</style>
@endpush

@section('content')
@php
    $studentsCount = $studentsCount ?? \App\Models\Student::count();
    $totalBallots = $election->votes()->distinct('student_id')->count('student_id');
    $turnout = $studentsCount > 0 ? ($totalBallots / $studentsCount) * 100 : 0;
@endphp

<div class="results-hero mb-3">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <span class="hero-pill"><i class="fas fa-check-circle"></i>Results Published</span>
            <h1 class="portal-heading h3 mb-1 mt-2">{{ $election->title }}</h1>
            <p class="portal-muted mb-0">{{ $election->start_time->format('M j, Y g:i A') }} to {{ $election->end_time->format('M j, Y g:i A') }}</p>
        </div>
        <div>
            <a href="{{ route('student.results.download.pdf') }}" class="btn btn-primary btn-sm">Download PDF</a>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-6 col-lg-3"><div class="stats-card text-center"><small class="text-muted d-block">Positions</small><strong>{{ $positions->count() }}</strong></div></div>
    <div class="col-6 col-lg-3"><div class="stats-card text-center"><small class="text-muted d-block">Ballots Cast</small><strong>{{ number_format($totalBallots) }}</strong></div></div>
    <div class="col-6 col-lg-3"><div class="stats-card text-center"><small class="text-muted d-block">Registered</small><strong>{{ number_format($studentsCount) }}</strong></div></div>
    <div class="col-6 col-lg-3"><div class="stats-card text-center"><small class="text-muted d-block">Turnout</small><strong>{{ number_format($turnout, 1) }}%</strong></div></div>
</div>

@foreach($positions as $position)
    @php
        $positionTotalVotes = isset($position->total_position_votes)
            ? $position->total_position_votes
            : $position->candidates->sum(function ($c) {
                return $c->votes_count ?? $c->vote_count ?? 0;
            });
    @endphp
    <div class="position-panel p-3 p-md-4 mb-3">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
            <div>
                <h2 class="h5 mb-1">{{ $position->title }}</h2>
                @if($position->description)
                    <p class="portal-muted mb-0">{{ $position->description }}</p>
                @endif
            </div>
            <div class="position-meta">
                {{ $position->candidates->count() }} candidates
                @if($positionTotalVotes > 0)
                    - {{ number_format($positionTotalVotes) }} votes
                @endif
            </div>
        </div>

        @if(isset($position->winner) && $positionTotalVotes > 0 && empty($position->is_tie))
            <div class="winner-box text-center">
                @if($position->winner->photo)
                    <img src="{{ asset('storage/' . $position->winner->photo) }}" alt="{{ $position->winner->name }}" class="results-avatar">
                @else
                    <div class="results-avatar-fallback mx-auto">{{ strtoupper(substr($position->winner->name, 0, 2)) }}</div>
                @endif
                <div class="candidate-name">{{ $position->winner->name }}</div>
                <div class="candidate-metrics">{{ number_format($position->winner->votes_count ?? $position->winner->vote_count ?? 0) }} votes - {{ number_format($position->winner->vote_percentage ?? 0, 1) }}%</div>
                <div class="winner-declare">{{ $position->winner->name }} has won as {{ $position->title }}</div>
            </div>
        @endif

        @if($position->candidates->count() > 0)
            <div class="d-grid gap-3">
                @foreach($position->candidates as $index => $candidate)
                    @if(isset($position->winner) && $candidate->id === $position->winner->id)
                        @continue
                    @endif
                    <div class="candidate-card text-center">
                        @if($candidate->photo)
                            <img src="{{ asset('storage/' . $candidate->photo) }}" alt="{{ $candidate->name }}" class="results-avatar">
                        @else
                            <div class="results-avatar-fallback mx-auto">{{ strtoupper(substr($candidate->name, 0, 2)) }}</div>
                        @endif
                            <div class="candidate-name">{{ $candidate->name }}</div>
                            <div class="candidate-metrics">
                                {{ number_format($candidate->votes_count ?? $candidate->vote_count ?? 0) }} votes - {{ number_format($candidate->vote_percentage ?? 0, 1) }}%
                            </div>
                            @if(isset($position->winner) && $candidate->id === $position->winner->id && $positionTotalVotes > 0 && empty($position->is_tie))
                                <div class="winner-declare">{{ $candidate->name }} has won as {{ $position->title }}</div>
                            @endif
                            @if($candidate->bio)
                                <div class="candidate-bio mt-2">{{ Str::limit($candidate->bio, 120) }}</div>
                            @endif
                            <div class="bar-wrap mt-2">
                                <div class="bar-fill" style="width: {{ min(100, $candidate->vote_percentage) }}%"></div>
                            </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-muted mb-0">No candidates for this position.</p>
        @endif
    </div>
@endforeach

<div class="text-center mt-4">
    <a href="{{ route('student.dashboard') }}" class="btn btn-outline-primary">Back to Dashboard</a>
</div>
@endsection
