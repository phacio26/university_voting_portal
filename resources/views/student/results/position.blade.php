@extends('layouts.student-portal')

@section('title', $position->title . ' Results')

@push('styles')
<style>
    .position-summary {
        border: 1px solid var(--portal-border);
        border-radius: 16px;
        background: #fff;
        padding: 1.25rem;
        box-shadow: 0 10px 22px rgba(15, 23, 42, 0.06);
    }
    .candidate-row {
        border: 1px solid var(--portal-border);
        border-radius: 14px;
        padding: 0.85rem;
        background: #fff;
    }
    .candidate-row.top {
        border-color: #b6dcc8;
        background: #f7fcf9;
    }
    .result-bar {
        height: 8px;
        border-radius: 999px;
        background: #edf2f8;
        overflow: hidden;
    }
    .result-fill {
        height: 100%;
        background: linear-gradient(90deg, #0a4d91, #0f766e);
    }
    .rank-chip {
        border-radius: 999px;
        padding: 0.2rem 0.6rem;
        font-size: 0.75rem;
        font-weight: 700;
        background: #e8eff8;
        color: #1f3b5a;
    }
    .rank-chip.top {
        background: #e7f6ee;
        color: #0f5132;
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
    .candidate-row {
        max-width: 420px;
        margin: 0 auto;
    }
    .candidate-metrics {
        font-size: 1rem;
        font-weight: 700;
        color: #0a4d91;
    }
    .candidate-row.top .candidate-metrics {
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
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
    <div>
        <h1 class="h4 portal-heading mb-1">{{ $position->title }} Results</h1>
        <p class="portal-muted mb-0">{{ $election->title }}</p>
    </div>
    <a href="{{ route('student.results.index') }}" class="btn btn-outline-primary btn-sm">All Results</a>
</div>

<div class="position-summary mb-3">
    <div class="d-flex flex-wrap gap-3 align-items-center">
        <div>
            <small class="text-muted d-block">Total Votes</small>
            <strong>{{ number_format($totalPositionVotes) }}</strong>
        </div>
        <div>
            <small class="text-muted d-block">Candidates</small>
            <strong>{{ $candidates->count() }}</strong>
        </div>
        <div class="ms-auto">
            <span class="badge bg-success">Published Results</span>
        </div>
    </div>
</div>

<div class="portal-card p-3 p-md-4">
    @if($candidates->isEmpty())
        <p class="text-muted mb-0">No candidates found for this position.</p>
    @else
        <div class="d-grid gap-2">
            @foreach($candidates as $index => $candidate)
                <div class="candidate-row {{ isset($position->winner) && $candidate->id === $position->winner->id ? 'top' : '' }} text-center">
                    @if($candidate->photo)
                        <img src="{{ asset('storage/' . $candidate->photo) }}" alt="{{ $candidate->name }}" class="results-avatar">
                    @else
                        <div class="results-avatar-fallback mx-auto">{{ strtoupper(substr($candidate->name, 0, 2)) }}</div>
                    @endif
                    <div class="fw-bold mt-2">{{ $candidate->name }}</div>
                    <div class="candidate-metrics">
                        {{ number_format($candidate->votes_count) }} votes - {{ number_format($candidate->vote_percentage, 1) }}%
                    </div>
                    @if(isset($position->winner) && $candidate->id === $position->winner->id && $totalPositionVotes > 0 && empty($position->is_tie))
                        <div class="winner-declare">{{ $candidate->name }} has won as {{ $position->title }}</div>
                    @endif
                    <div class="result-bar mt-2">
                        <div class="result-fill" style="width: {{ min(100, $candidate->vote_percentage) }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
