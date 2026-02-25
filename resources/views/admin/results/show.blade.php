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
            padding: 1rem !important;
        }

        .result-card h5 {
            font-size: 1rem;
        }

        .table-responsive {
            margin: 0 -0.5rem;
        }

        .table-sm {
            font-size: 0.85rem;
        }

        .table-sm td,
        .table-sm th {
            padding: 0.5rem;
            vertical-align: top;
        }

        .mobile-candidate-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.25rem;
        }

        .mobile-votes-info {
            font-size: 0.8rem;
            color: #6c757d;
        }

        .desktop-table {
            display: none;
        }

        .mobile-cards {
            display: block;
        }
    }

    @media (min-width: 768px) {
        .desktop-table {
            display: table;
        }

        .mobile-cards {
            display: none;
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
    <div class="alert alert-warning" role="alert">
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
            <strong>{{ (int) $totalBallots === 0 ? 'No turnout yet' : ($studentsCount > 0 ? number_format(($totalBallots / $studentsCount) * 100, 1) . '%' : '0.0%') }}</strong>
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
                    <span class="badge bg-secondary">Lost Elections</span>
                @endif
            </div>

            @if($position->winner)
                <div class="alert alert-success py-2 mb-2" role="alert">
                    <strong>{{ $position->winner->name }}</strong> won with
                    {{ number_format($position->winner->votes_count) }} votes
                    ({{ number_format($position->winner->vote_percentage, 1) }}%).
                </div>
            @elseif($position->is_tie)
                <div class="alert alert-warning py-2 mb-2" role="alert">
                    This position is tied. Re-vote is required or still pending for final winner declaration.
                </div>
            @endif

            <!-- Desktop Table View -->
            <div class="table-responsive desktop-table">
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

            <!-- Mobile Card View -->
            <div class="mobile-cards">
                @forelse($position->candidates as $candidate)
                    <div class="border rounded p-2 mb-2 bg-light">
                        <div class="mobile-candidate-info">
                            <strong>{{ $candidate->name }}</strong>
                            @if($position->winner && $position->winner->id === $candidate->id)
                                <span class="badge bg-success">Winner</span>
                            @endif
                        </div>
                        <div class="mobile-votes-info">
                            <span>{{ number_format($candidate->votes_count) }} votes</span>
                            <span class="ms-3">{{ number_format($candidate->vote_percentage, 1) }}%</span>
                        </div>
                    </div>
                @empty
                    <div class="text-muted text-center py-2">No candidates for this position.</div>
                @endforelse
            </div>
        </div>
    @endforeach
</div>
@endsection
