@extends('layouts.admin')

@section('title', 'Election Winners')
@section('page-title', 'Election Winners')
@section('page-icon', 'fa-award')

@section('styles')
<style>
    .result-card {
        border: 1px solid var(--neutral-border);
        border-radius: var(--radius-md);
        background: #fff;
    }

    @media (max-width: 767.98px) {
        .result-card {
            padding: 0.8rem !important;
        }

        .result-card h5 {
            font-size: 1rem;
        }

        .table-sm td,
        .table-sm th {
            white-space: nowrap;
            font-size: 0.82rem;
        }
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
    <div>
        <h4 class="mb-1">{{ $election->title }}</h4>
        <p class="text-muted mb-0">{{ $election->start_time->format('M j, Y g:i A') }} to {{ $election->end_time->format('M j, Y g:i A') }}</p>
    </div>
    <a href="{{ route('admin.results.index') }}" class="btn btn-outline-secondary btn-sm">Back to Results</a>
</div>

@if($pendingResolution)
    <div class="alert alert-warning">
        <strong>Finalization still pending:</strong>
        At least one position is still tied or awaiting a re-vote. Current leaders are shown below.
    </div>
@endif

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="result-card p-3 text-center">
            <small class="text-muted d-block">Positions</small>
            <strong>{{ $positions->count() }}</strong>
        </div>
    </div>
    <div class="col-md-4">
        <div class="result-card p-3 text-center">
            <small class="text-muted d-block">Participants</small>
            <strong>{{ number_format($totalBallots) }} / {{ number_format($studentsCount) }}</strong>
        </div>
    </div>
    <div class="col-md-4">
        <div class="result-card p-3 text-center">
            <small class="text-muted d-block">Turnout</small>
            <strong>{{ $studentsCount > 0 ? number_format(($totalBallots / $studentsCount) * 100, 1) : '0.0' }}%</strong>
        </div>
    </div>
</div>

<div class="d-grid gap-3">
    @foreach($positions as $position)
        <div class="result-card p-3">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                <h5 class="mb-0">{{ $position->title }}</h5>
                @if($position->winner)
                    <span class="badge bg-success">Winner Declared</span>
                @elseif($position->is_tie)
                    <span class="badge bg-warning text-dark">Tie</span>
                @else
                    <span class="badge bg-secondary">No Winner Yet</span>
                @endif
            </div>

            @if($position->winner)
                <div class="alert alert-success py-2 mb-2">
                    <strong>{{ $position->winner->name }}</strong> won with
                    {{ number_format($position->winner->votes_count) }} votes
                    ({{ number_format($position->winner->vote_percentage, 1) }}%).
                </div>
            @elseif($position->is_tie)
                <div class="alert alert-warning py-2 mb-2">
                    This position is tied. Re-vote is required or still pending for final winner declaration.
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Candidate</th>
                            <th>Votes</th>
                            <th>Percent</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($position->candidates as $candidate)
                            <tr>
                                <td>{{ $candidate->name }}</td>
                                <td>{{ number_format($candidate->votes_count) }}</td>
                                <td>{{ number_format($candidate->vote_percentage, 1) }}%</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-muted">No candidates for this position.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</div>
@endsection
