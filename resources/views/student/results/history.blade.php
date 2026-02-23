@extends('layouts.student-portal')

@section('title', 'Election History')

@push('styles')
<style>
    .history-card {
        border: 1px solid var(--portal-border);
        border-radius: 14px;
        background: #fff;
    }
    .winner-pill {
        border-radius: 999px;
        background: #ecf8f1;
        color: #0f5132;
        border: 1px solid #c9ebda;
        padding: 0.2rem 0.55rem;
        font-size: 0.78rem;
        font-weight: 700;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
    <div>
        <h1 class="portal-heading h3 mb-1">Election History</h1>
        <p class="portal-muted mb-0">Browse completed elections and published outcomes.</p>
    </div>
    <a href="{{ route('student.results.index') }}" class="btn btn-outline-primary">
        <i class="fas fa-chart-line me-2"></i>Current Results
    </a>
</div>

@if($pastElections->count() > 0)
    <div class="row g-3">
        @foreach($electionsWithStats as $item)
            @php
                $election = $item['election'];
                $positions = $item['positions'];
                $winners = $positions->filter(fn ($p) => isset($p->winner));
            @endphp
            <div class="col-12">
                <div class="history-card p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                        <div>
                            <h2 class="h5 mb-1">{{ $election->title }}</h2>
                            <div class="small text-muted">
                                {{ $election->start_time->format('M j, Y g:i A') }} to {{ $election->end_time->format('M j, Y g:i A') }}
                            </div>
                        </div>
                        <span class="badge bg-success">Completed</span>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6 col-md-3"><div class="portal-card p-2 text-center"><small class="d-block text-muted">Positions</small><strong>{{ $positions->count() }}</strong></div></div>
                        <div class="col-6 col-md-3"><div class="portal-card p-2 text-center"><small class="d-block text-muted">Votes</small><strong>{{ number_format($item['total_votes']) }}</strong></div></div>
                        <div class="col-6 col-md-3">
                            <div class="portal-card p-2 text-center">
                                <small class="d-block text-muted">Turnout</small>
                                <strong>{{ (int) $item['total_votes'] === 0 ? 'No turnout yet' : number_format($item['voter_turnout'], 1) . '%' }}</strong>
                            </div>
                        </div>
                        <div class="col-6 col-md-3"><div class="portal-card p-2 text-center"><small class="d-block text-muted">Registered</small><strong>{{ number_format($totalStudents) }}</strong></div></div>
                    </div>

                    @if($winners->count() > 0)
                        <div class="mb-3">
                            <h3 class="h6 mb-2">Winners</h3>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($winners as $position)
                                    <span class="winner-pill">
                                        {{ $position->title }}: {{ $position->winner->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <a href="{{ route('student.results.show', ['election' => $election->id]) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-eye me-1"></i>View Full Results
                    </a>
                    <button
                        type="button"
                        class="btn btn-outline-danger btn-sm ms-2 js-open-delete-history"
                        data-action="{{ route('student.results.history.destroy', ['election' => $election->id]) }}"
                        data-title="{{ $election->title }}"
                    >
                        <i class="fas fa-trash me-1"></i>Delete
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    @if($pastElections->hasPages())
        <div class="mt-3">{{ $pastElections->links() }}</div>
    @endif
@else
    <div class="portal-card p-4 text-center">
        <i class="fas fa-clock-rotate-left fa-2x text-muted mb-2"></i>
        <h2 class="h5">No election history available</h2>
        <p class="portal-muted mb-3">Published results will appear here when elections are completed.</p>
        <a href="{{ route('student.dashboard') }}" class="btn btn-outline-primary">Back to Dashboard</a>
    </div>
@endif

<div class="modal fade" id="deleteHistoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Remove History Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2">Are you sure you want to remove this election from your history?</p>
                <p class="small text-muted mb-0" id="historyDeleteLabel"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="historyDeleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const modalEl = document.getElementById('deleteHistoryModal');
        const formEl = document.getElementById('historyDeleteForm');
        const labelEl = document.getElementById('historyDeleteLabel');
        if (!modalEl || !formEl || !labelEl) return;

        const modal = new bootstrap.Modal(modalEl);
        document.querySelectorAll('.js-open-delete-history').forEach((button) => {
            button.addEventListener('click', () => {
                formEl.action = button.getAttribute('data-action');
                labelEl.textContent = button.getAttribute('data-title') || '';
                modal.show();
            });
        });
    })();
</script>
@endpush
