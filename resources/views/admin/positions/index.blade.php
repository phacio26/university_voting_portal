@extends('layouts.admin')

@section('title', 'Positions')
@section('page-title', 'Positions')
@section('page-icon', 'fa-bullseye')

@section('styles')
<style>
    .position-mobile-card {
        border: 1px solid var(--neutral-border);
        border-radius: var(--radius-md);
        background: #fff;
    }

    .position-mobile-actions {
        display: flex;
        flex-wrap: nowrap;
        gap: 0.5rem;
        align-items: center;
    }

    .position-mobile-actions .btn {
        min-width: 0;
        padding: 0.35rem 0.55rem;
        font-size: 0.78rem;
        min-height: 2rem;
        white-space: nowrap;
    }

    @media (max-width: 767.98px) {
        .position-mobile-actions {
            gap: 0.35rem;
        }

        .position-mobile-actions form {
            margin: 0;
            flex: 1 1 auto;
        }

        .position-mobile-actions .btn {
            width: 100%;
        }

        .position-mobile-actions > a,
        .position-mobile-actions > button {
            flex: 1 1 auto;
        }
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('admin.positions.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> New Position
    </a>
</div>

<div class="card">
    <div class="card-body">
        @if($positions->count() > 0)
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Candidates</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($positions as $position)
                            <tr>
                                <td><span class="badge bg-secondary">{{ $position->order }}</span></td>
                                <td class="fw-semibold">{{ $position->title }}</td>
                                <td>
                                    @if($position->description)
                                        <small class="text-muted">{{ Str::limit($position->description, 70) }}</small>
                                    @else
                                        <span class="text-muted">No description</span>
                                    @endif
                                </td>
                                <td>
                                    @if($position->is_active)
                                        <span class="badge bg-success status-badge">Active</span>
                                    @else
                                        <span class="badge bg-secondary status-badge">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $position->candidates_count }}</span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group" aria-label="Position actions">
                                        <form method="POST" action="{{ route('admin.positions.toggle-status', $position) }}" class="d-inline">
                                            @csrf
                                            <button
                                                type="submit"
                                                class="btn btn-outline-{{ $position->is_active ? 'warning' : 'success' }}"
                                                title="{{ $position->is_active ? 'Deactivate' : 'Activate' }}"
                                                aria-label="{{ $position->is_active ? 'Deactivate' : 'Activate' }} {{ $position->title }}"
                                            >
                                                <i class="fas fa-{{ $position->is_active ? 'pause' : 'play' }} me-1"></i>
                                                {{ $position->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>

                                        <a
                                            href="{{ route('admin.positions.edit', $position) }}"
                                            class="btn btn-outline-primary"
                                            title="Edit"
                                            aria-label="Edit {{ $position->title }}"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <button
                                            type="button"
                                            class="btn btn-outline-danger js-open-delete"
                                            data-action="{{ route('admin.positions.destroy', $position) }}"
                                            data-label="{{ $position->title }}"
                                            title="Delete"
                                            aria-label="Delete {{ $position->title }}"
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
                    @foreach($positions as $position)
                        <div class="position-mobile-card p-3">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <div>
                                    <h6 class="mb-1">{{ $position->title }}</h6>
                                    <small class="text-muted">Order: {{ $position->order }}</small>
                                </div>
                                @if($position->is_active)
                                    <span class="badge bg-success status-badge">Active</span>
                                @else
                                    <span class="badge bg-secondary status-badge">Inactive</span>
                                @endif
                            </div>

                            <div class="small mb-2">
                                @if($position->description)
                                    <span class="text-muted">{{ Str::limit($position->description, 110) }}</span>
                                @else
                                    <span class="text-muted">No description</span>
                                @endif
                            </div>

                            <div class="mb-3">
                                <span class="small text-muted">Candidates:</span>
                                <span class="badge bg-info ms-1">{{ $position->candidates_count }}</span>
                            </div>

                            <div class="position-mobile-actions">
                                <form method="POST" action="{{ route('admin.positions.toggle-status', $position) }}">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="btn btn-outline-{{ $position->is_active ? 'warning' : 'success' }} btn-sm"
                                        aria-label="{{ $position->is_active ? 'Deactivate' : 'Activate' }} {{ $position->title }}"
                                    >
                                        <i class="fas fa-{{ $position->is_active ? 'pause' : 'play' }}"></i>
                                        {{ $position->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>

                                <a
                                    href="{{ route('admin.positions.edit', $position) }}"
                                    class="btn btn-outline-primary btn-sm"
                                    aria-label="Edit {{ $position->title }}"
                                >
                                    <i class="fas fa-edit"></i> Edit
                                </a>

                                <button
                                    type="button"
                                    class="btn btn-outline-danger btn-sm js-open-delete"
                                    data-action="{{ route('admin.positions.destroy', $position) }}"
                                    data-label="{{ $position->title }}"
                                    aria-label="Delete {{ $position->title }}"
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
                <i class="fas fa-bullseye fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Positions</h5>
                <p class="text-muted">Create your first position to start configuring elections.</p>
                <a href="{{ route('admin.positions.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Position
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
                <p class="mb-2">Are you sure you want to delete this position? This cannot be undone.</p>
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
