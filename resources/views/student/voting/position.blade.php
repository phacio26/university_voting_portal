@extends('layouts.student-portal')

@section('title', 'Vote - ' . $position->title)

@push('styles')
<style>
    .candidate-choice {
        border: 2px solid var(--portal-border);
        border-radius: 18px;
        transition: all .3s ease;
        cursor: pointer;
        background: var(--portal-card);
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
        position: relative;
        overflow: hidden;
    }
    
    .candidate-choice:hover {
        border-color: #a0c4ff;
        box-shadow: 0 8px 20px rgba(10, 77, 145, 0.15);
        transform: translateY(-2px);
    }
    
    .candidate-choice.selected {
        border-color: var(--portal-brand);
        box-shadow: 0 0 0 4px rgba(10, 77, 145, 0.15), 0 8px 20px rgba(10, 77, 145, 0.2);
        background: #f0f8ff;
    }
    
    .candidate-choice.selected::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--portal-brand), var(--portal-brand-2));
    }

    .candidate-choice.validation-error {
        border-color: #dc3545;
        box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.16);
        background: #fff7f8;
    }

    .vote-required-hint {
        margin-top: -0.25rem;
        margin-bottom: 0.75rem;
    }
    
    .candidate-info h2 {
        font-size: 1.35rem;
        font-weight: 700;
        letter-spacing: -0.01em;
        color: var(--portal-ink);
    }
    
    .candidate-bio {
        font-size: 0.95rem;
        line-height: 1.5;
        color: var(--portal-muted);
    }
    
    .candidate-radio {
        width: 1.5rem;
        height: 1.5rem;
        cursor: pointer;
    }
    
    .avatar-img {
        display: block;
        width: 96px;
        height: 96px;
        min-width: 96px;
        min-height: 96px;
        aspect-ratio: 1 / 1;
        border-radius: 16px;
        object-fit: cover;
        object-position: center 30%;
        border: 3px solid var(--portal-border);
        background: #eef3f9;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.10);
        image-rendering: auto;
    }
    
    .avatar-fallback {
        display: inline-flex;
        width: 96px;
        height: 96px;
        min-width: 96px;
        min-height: 96px;
        aspect-ratio: 1 / 1;
        border-radius: 16px;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #e6eef8, #dbe7f5);
        color: #2f4f73;
        font-weight: 800;
        font-size: 2.5rem;
        border: 3px solid var(--portal-border);
    }

    @media (min-width: 992px) {
        .candidate-choice .d-flex {
            align-items: center !important;
        }

        .avatar-img,
        .avatar-fallback {
            width: 112px;
            height: 132px;
            min-width: 112px;
            min-height: 132px;
            border-radius: 14px;
            aspect-ratio: 112 / 132;
        }

        .avatar-img {
            object-position: center 26%;
        }
    }

    @media (max-width: 576px) {
        .portal-card {
            padding: 0.85rem !important;
        }

        .portal-card h1.h4 {
            font-size: 1.05rem;
        }

        .candidate-choice {
            border-radius: 14px;
        }

        .candidate-choice.p-4 {
            padding: 0.75rem !important;
        }

        .candidate-choice .d-flex {
            gap: 0.75rem !important;
        }

        .candidate-info h2 {
            font-size: 1rem;
            font-weight: 700;
        }

        .candidate-bio {
            font-size: 0.82rem;
            line-height: 1.35;
        }

        .avatar-img,
        .avatar-fallback {
            width: 72px;
            height: 72px;
            border-radius: 10px;
        }

        .avatar-fallback {
            font-size: 1.35rem;
        }

        .candidate-radio {
            width: 1.2rem;
            height: 1.2rem;
        }

        .badge.bg-primary {
            font-size: 0.72rem;
            font-weight: 700;
        }

        #voteButton,
        .btn.btn-outline-secondary {
            font-size: 0.84rem;
            padding: 0.42rem 0.62rem;
            min-height: 2rem;
        }
    }
</style>
@endpush

@section('content')
@php
    $totalPositions = $positions->count();
    $currentPosition = $currentPositionIndex + 1;
    $sessionVotes = session('votes', []);
    $positionIds = $positions->pluck('id')->all();
    $completedCount = collect($positionIds)->filter(function ($id) use ($sessionVotes) {
        return array_key_exists($id, $sessionVotes);
    })->count();
    $progressPercentage = ($completedCount / max($totalPositions, 1)) * 100;
@endphp

<div class="portal-card p-3 p-md-4 mb-3">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
        <div>
            <h1 class="h4 portal-heading mb-1">{{ $position->title }}</h1>
            @if($position->description)
                <p class="portal-muted mb-0">{{ $position->description }}</p>
            @endif
        </div>
        <span class="badge bg-primary">Step {{ $currentPosition }} / {{ $totalPositions }}</span>
    </div>
    <div class="progress" style="height: 10px;">
        <div class="progress-bar bg-success" style="width: {{ $progressPercentage }}%"></div>
    </div>
</div>

<div class="portal-card p-3 p-md-4">
    @if($hasVotedForPosition)
        <div class="alert alert-success">
            Your choice for this position is saved. You can still update it before submitting.
        </div>
    @endif

    @if($candidates->isEmpty())
        <div class="alert alert-warning mb-0">No candidates available for this position.</div>
    @else
        <form id="voteForm" method="POST" action="{{ route('student.voting.vote.position', $position) }}">
            @csrf
            <div id="voteRequiredHint" class="alert alert-danger d-none vote-required-hint" role="alert">
                Please select one candidate to continue.
            </div>
            <div class="row g-4 mb-4">
                @foreach($candidates as $candidate)
                    <div class="col-md-6">
                        <label class="candidate-choice p-4 d-block {{ (int) $selectedCandidateId === (int) $candidate->id ? 'selected' : '' }}" for="candidate_{{ $candidate->id }}">
                            <div class="d-flex align-items-stretch gap-4">
                                @if($candidate->photo)
                                    <img src="{{ asset('storage/' . $candidate->photo) }}" alt="{{ $candidate->name }}" class="avatar-img flex-shrink-0">
                                @else
                                    <div class="avatar-fallback flex-shrink-0">{{ strtoupper(substr($candidate->name, 0, 2)) }}</div>
                                @endif
                                <div class="candidate-info flex-grow-1 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                                            <h2 class="mb-0">{{ $candidate->name }}</h2>
                                            <div class="form-check mt-0">
                                                <input class="form-check-input candidate-radio"
                                                    type="radio"
                                                    name="candidate_id"
                                                    id="candidate_{{ $candidate->id }}"
                                                    value="{{ $candidate->id }}"
                                                    {{ (int) $selectedCandidateId === (int) $candidate->id ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                        @if($candidate->bio)
                                            <p class="candidate-bio mb-0">{{ $candidate->bio }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-between flex-wrap gap-2">
                @if($currentPositionIndex > 0)
                    @php($prevPosition = $positions[$currentPositionIndex - 1])
                    <a href="{{ route('student.voting.position', $prevPosition) }}" class="btn btn-outline-secondary">Previous</a>
                @else
                    <a href="{{ route('student.voting.start') }}" class="btn btn-outline-secondary">Restart</a>
                @endif

                <button type="submit" class="btn btn-primary" id="voteButton">{{ $hasVotedForPosition ? ($currentPosition == $totalPositions ? 'Update and Review' : 'Update and Continue') : ($currentPosition == $totalPositions ? 'Save and Review' : 'Save and Continue') }}</button>
            </div>
        </form>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.candidate-radio').forEach((radio) => {
        radio.addEventListener('change', () => {
            const voteRequiredHint = document.getElementById('voteRequiredHint');
            document.querySelectorAll('.candidate-choice').forEach((card) => card.classList.remove('selected'));
            document.querySelectorAll('.candidate-choice').forEach((card) => card.classList.remove('validation-error'));
            if (voteRequiredHint) {
                voteRequiredHint.classList.add('d-none');
            }
            radio.closest('.candidate-choice').classList.add('selected');
        });
    });

    const form = document.getElementById('voteForm');
    if (form) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            const selected = form.querySelector('input[name="candidate_id"]:checked');
            const voteRequiredHint = document.getElementById('voteRequiredHint');
            if (!selected) {
                document.querySelectorAll('.candidate-choice').forEach((card) => {
                    card.classList.add('validation-error');
                });
                if (voteRequiredHint) {
                    voteRequiredHint.classList.remove('d-none');
                }
                return;
            }

            const voteButton = document.getElementById('voteButton');
            voteButton.disabled = true;
            const originalLabel = voteButton.innerHTML;
            voteButton.innerHTML = 'Saving...';

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                    },
                    body: new URLSearchParams(new FormData(form))
                });

                const data = await response.json();
                if (response.ok && data.success && data.next_url) {
                    window.location.href = data.next_url;
                    return;
                }

                alert(data.error || 'Could not save your choice right now.');
            } catch (error) {
                alert('Connection issue. Please try again.');
            }

            voteButton.disabled = false;
            voteButton.innerHTML = originalLabel;
        });
    }
</script>
@endpush
