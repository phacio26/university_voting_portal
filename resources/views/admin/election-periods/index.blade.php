@extends('layouts.admin')

@section('title', 'Election Periods')
@section('page-title', 'Election Periods')
@section('page-icon', 'fa-calendar-alt')

@section('styles')
<style>
    .election-mobile-card {
        border: 1px solid var(--neutral-border);
        border-radius: var(--radius-md);
        background: #fff;
    }

    .election-mobile-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
        align-items: center;
    }

    .election-mobile-actions form {
        margin: 0;
        flex: 1 1 calc(50% - 0.35rem);
    }

    .election-mobile-actions .btn,
    .election-mobile-actions > a,
    .election-mobile-actions > button {
        flex: 1 1 calc(50% - 0.35rem);
        min-width: 0;
        width: 100%;
        padding: 0.35rem 0.5rem;
        font-size: 0.78rem;
        min-height: 2rem;
        white-space: normal;
        line-height: 1.25;
    }

    @media (max-width: 575.98px) {
        .election-mobile-actions form,
        .election-mobile-actions .btn,
        .election-mobile-actions > a,
        .election-mobile-actions > button {
            flex: 1 1 100%;
        }

        .election-mobile-card h6 {
            font-size: 0.95rem;
        }

        .election-mobile-card .small,
        .election-mobile-card small {
            font-size: 0.78rem;
        }
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <a href="{{ route('admin.election-periods.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> New Election Period
    </a>
</div>

<div class="card">
    <div class="card-body">
        @if($electionPeriods->count() > 0)
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Status</th>
                            <th>Results</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($electionPeriods as $period)
                            <tr>
                                <td>
                                    <strong>{{ $period->title }}</strong>
                                    @if($period->description)
                                        <br><small class="text-muted">{{ Str::limit($period->description, 70) }}</small>
                                    @endif
                                </td>
                                <td>{{ $period->start_time->format('M j, Y g:i A') }}</td>
                                <td>{{ $period->end_time->format('M j, Y g:i A') }}</td>
                                <td>
                                    @if($period->is_active)
                                        <span class="badge bg-success status-badge">Active</span>
                                    @else
                                        <span class="badge bg-secondary status-badge">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    @if($period->results_available)
                                        <span class="badge bg-info status-badge">Available</span>
                                    @else
                                        <span class="badge bg-warning status-badge">Hidden</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group" aria-label="Election period actions">
                                        @if(!$period->is_active)
                                            <form method="POST" action="{{ route('admin.election-periods.activate', $period) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-success" title="Activate" aria-label="Activate {{ $period->title }}">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.election-periods.deactivate', $period) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-warning" title="Deactivate" aria-label="Deactivate {{ $period->title }}">
                                                    <i class="fas fa-pause"></i>
                                                </button>
                                            </form>
                                        @endif

                                        @if(!$period->results_available)
                                            <form method="POST" action="{{ route('admin.election-periods.results-available', $period) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-info" title="Publish results" aria-label="Publish results for {{ $period->title }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </form>
                                        @endif

                                        @if(!$period->is_revote && $period->end_time->lt(now()))
                                            <a href="{{ route('admin.results.show', $period) }}" class="btn btn-outline-success" title="View results" aria-label="View results for {{ $period->title }}">
                                                <i class="fas fa-graduation-cap"></i>
                                            </a>
                                        @endif

                                        <a href="{{ route('admin.election-periods.edit', $period) }}" class="btn btn-outline-primary" title="Edit" aria-label="Edit {{ $period->title }}">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <button
                                            type="button"
                                            class="btn btn-outline-danger js-open-delete"
                                            data-action="{{ route('admin.election-periods.destroy', $period) }}"
                                            data-label="{{ $period->title }}"
                                            title="Delete"
                                            aria-label="Delete {{ $period->title }}"
                                        >
                                            <i class="fas fa-trash me-1"></i>Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-md-none">
                <div class="d-grid gap-3">
                    @foreach($electionPeriods as $period)
                        <div class="election-mobile-card p-3">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <div>
                                    <h6 class="mb-1">{{ $period->title }}</h6>
                                    <small class="text-muted d-block">{{ $period->start_time->format('M j, Y g:i A') }}</small>
                                    <small class="text-muted d-block">{{ $period->end_time->format('M j, Y g:i A') }}</small>
                                </div>
                                @if($period->is_active)
                                    <span class="badge bg-success status-badge">Active</span>
                                @else
                                    <span class="badge bg-secondary status-badge">Inactive</span>
                                @endif
                            </div>

                            <div class="mb-3">
                                @if($period->results_available)
                                    <span class="badge bg-info status-badge">Results Available</span>
                                @else
                                    <span class="badge bg-warning status-badge">Results Hidden</span>
                                @endif
                                @if($period->description)
                                    <p class="small text-muted mt-2 mb-0">{{ Str::limit($period->description, 110) }}</p>
                                @endif
                            </div>

                            <div class="election-mobile-actions">
                                @if(!$period->is_active)
                                    <form method="POST" action="{{ route('admin.election-periods.activate', $period) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success btn-sm" aria-label="Activate {{ $period->title }}">
                                            <i class="fas fa-play"></i> Activate
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.election-periods.deactivate', $period) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-warning btn-sm" aria-label="Deactivate {{ $period->title }}">
                                            <i class="fas fa-pause"></i> Deactivate
                                        </button>
                                    </form>
                                @endif

                                @if(!$period->results_available)
                                    <form method="POST" action="{{ route('admin.election-periods.results-available', $period) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-info btn-sm" aria-label="Publish results for {{ $period->title }}">
                                            <i class="fas fa-eye"></i> Publish
                                        </button>
                                    </form>
                                @endif

                                @if(!$period->is_revote && $period->end_time->lt(now()))
                                    <a href="{{ route('admin.results.show', $period) }}" class="btn btn-outline-success btn-sm" aria-label="View results for {{ $period->title }}">
                                        <i class="fas fa-trophy"></i> Results
                                    </a>
                                @endif

                                <a href="{{ route('admin.election-periods.edit', $period) }}" class="btn btn-outline-primary btn-sm" aria-label="Edit {{ $period->title }}">
                                    <i class="fas fa-edit"></i> Edit
                                </a>

                                <button
                                    type="button"
                                    class="btn btn-outline-danger btn-sm js-open-delete"
                                    data-action="{{ route('admin.election-periods.destroy', $period) }}"
                                    data-label="{{ $period->title }}"
                                    aria-label="Delete {{ $period->title }}"
                                >
                                    <i class="fas fa-trash me-1"></i>Delete
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Election Periods</h5>
                <p class="text-muted">Create your first election period to open voting windows.</p>
                <a href="{{ route('admin.election-periods.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Election Period
                </a>
            </div>
        @endif
    </div>
</div>

<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2">Are you sure you want to delete this election period? This cannot be undone.</p>
                <p class="small text-muted mb-0" id="deleteEntityLabel"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    (function () {
        const modalEl = document.getElementById('confirmDeleteModal');
        if (!modalEl) return;

        const modal = new bootstrap.Modal(modalEl);
        const formEl = document.getElementById('deleteForm');
        const labelEl = document.getElementById('deleteEntityLabel');
        const cancelBtn = modalEl.querySelector('[data-bs-dismiss="modal"]');

        document.querySelectorAll('.js-open-delete').forEach((button) => {
            button.addEventListener('click', () => {
                formEl.action = button.getAttribute('data-action');
                labelEl.textContent = button.getAttribute('data-label') || '';
                modal.show();
            });
        });

        modalEl.addEventListener('hidden.bs.modal', () => {
            formEl.action = '';
            labelEl.textContent = '';
        });

        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => {
                formEl.action = '';
                labelEl.textContent = '';
            });
        }
    })();
</script>
@endsection
