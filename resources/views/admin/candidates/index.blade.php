@extends('layouts.admin')

@section('title', 'Candidates')
@section('page-title', 'Candidates')
@section('page-icon', 'fa-users')

@section('styles')
<style>
    .admin-candidate-card {
        border: 1px solid var(--neutral-border);
        border-radius: var(--radius-lg);
        height: 100%;
        background: #fff;
        overflow: hidden;
    }

    .admin-candidate-media {
        width: 100%;
        aspect-ratio: 4 / 3;
        background: #eef2f7;
        border-bottom: 1px solid var(--neutral-border);
    }

    .admin-candidate-photo {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center center;
        display: block;
        background: #eef2f7;
        filter: saturate(1.14) contrast(1.08) brightness(1.03);
        transition: transform 0.28s ease, filter 0.28s ease;
    }

    .admin-candidate-photo:hover {
        transform: scale(1.02);
        filter: saturate(1.22) contrast(1.12) brightness(1.05);
    }

    .admin-candidate-avatar {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #5b6470;
        font-size: 2rem;
    }

    .admin-candidate-actions .btn {
        white-space: nowrap;
    }

    .admin-candidate-actions-row {
        border-bottom: 1px solid var(--neutral-border);
        background: #f8fafc;
    }

    .admin-candidate-actions-row .btn {
        width: 100%;
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('admin.candidates.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> New Candidate
    </a>
</div>

@if($candidates->count() > 0)
    <div class="row g-4">
        @foreach($candidates as $candidate)
            <div class="col-md-6 col-lg-4">
                <div class="admin-candidate-card">
                    <div class="admin-candidate-media">
                        @if($candidate->photo)
                            <img
                                src="{{ asset('storage/' . $candidate->photo) }}"
                                class="admin-candidate-photo"
                                alt="{{ $candidate->name }}"
                                loading="lazy"
                                onerror="this.style.display='none'; this.nextElementSibling.classList.remove('d-none');"
                            >
                            <div class="admin-candidate-avatar d-none" aria-hidden="true">
                                <i class="fas fa-user"></i>
                            </div>
                        @else
                            <div class="admin-candidate-avatar" aria-hidden="true">
                                <i class="fas fa-user"></i>
                            </div>
                        @endif
                    </div>

                    <div class="p-3 admin-candidate-actions-row">
                        <div class="row g-2">
                            <div class="col-12 col-md-4">
                                @if($candidate->is_disqualified)
                                    <form method="POST" action="{{ route('admin.candidates.reinstate', $candidate) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" aria-label="Reinstate {{ $candidate->name }}">
                                            <i class="fas fa-undo"></i> Reinstate
                                        </button>
                                    </form>
                                @else
                                    <button
                                        type="button"
                                        class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#disqualifyModal{{ $candidate->id }}"
                                        aria-label="Disqualify {{ $candidate->name }}"
                                    >
                                        <i class="fas fa-ban"></i> Disqualify
                                    </button>
                                @endif
                            </div>
                            <div class="col-6 col-md-4">
                                <a href="{{ route('admin.candidates.edit', $candidate) }}" class="btn btn-outline-primary btn-sm" aria-label="Edit {{ $candidate->name }}">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </div>
                            <div class="col-6 col-md-4">
                                <button
                                    type="button"
                                    class="btn btn-outline-danger btn-sm js-open-delete"
                                    data-action="{{ route('admin.candidates.destroy', $candidate) }}"
                                    data-label="{{ $candidate->name }}"
                                    aria-label="Delete {{ $candidate->name }}"
                                >
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 d-flex flex-column h-100">
                        <h3 class="h5 mb-1 text-capitalize">{{ $candidate->name }}</h3>
                        <p class="mb-2"><span class="badge bg-primary">{{ $candidate->position->title }}</span></p>

                        @if($candidate->bio)
                            <p class="text-muted small mb-3">{{ Str::limit($candidate->bio, 120) }}</p>
                        @else
                            <p class="text-muted small fst-italic mb-3">No bio provided</p>
                        @endif

                        <div class="mb-3 mt-auto">
                            @if($candidate->is_disqualified)
                                <span class="badge bg-danger">Disqualified</span>
                                @if($candidate->disqualification_reason)
                                    <div class="small text-muted mt-1">
                                        Reason: {{ Str::limit($candidate->disqualification_reason, 90) }}
                                    </div>
                                @endif
                            @else
                                <span class="badge bg-success">Active</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="disqualifyModal{{ $candidate->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Disqualify Candidate</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="POST" action="{{ route('admin.candidates.disqualify', $candidate) }}">
                            @csrf
                            <div class="modal-body">
                                <p>Disqualify <strong>{{ $candidate->name }}</strong>?</p>
                                <div class="mb-3">
                                    <label for="disqualification_reason_{{ $candidate->id }}" class="form-label">Reason *</label>
                                    <textarea
                                        class="form-control"
                                        id="disqualification_reason_{{ $candidate->id }}"
                                        name="disqualification_reason"
                                        rows="3"
                                        required
                                    ></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-danger">Disqualify</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-users fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">No Candidates Yet</h5>
            <p class="text-muted">Add the first candidate to begin voting setup.</p>
            <a href="{{ route('admin.candidates.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Candidate
            </a>
        </div>
    </div>
@endif

<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2">Are you sure you want to delete this candidate? This cannot be undone.</p>
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
