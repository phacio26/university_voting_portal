@extends('layouts.student-portal')

@section('title', 'Review Votes')

@push('styles')
<style>
    @media (max-width: 576px) {
        .portal-card {
            padding: 0.85rem !important;
        }

        .portal-card h1.h4 {
            font-size: 1.05rem;
        }

        .portal-card .fw-semibold {
            font-size: 0.95rem;
        }

        .portal-card .btn {
            font-size: 0.84rem;
            padding: 0.42rem 0.62rem;
            min-height: 2rem;
        }
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
    <div>
        <h1 class="h4 portal-heading mb-1">Review Your Vote</h1>
        <p class="portal-muted mb-0">Review your choices before submitting your vote.</p>
    </div>
</div>

<div class="portal-card p-3 p-md-4">
    @if(empty($voteDetails))
        <div class="alert alert-warning mb-0">
            No choices saved yet. <a href="{{ route('student.voting.start') }}">Start voting</a>.
        </div>
    @else
        <div class="row g-3 mb-3">
            @foreach($voteDetails as $vote)
                <div class="col-md-6">
                    <div class="portal-card p-3 h-100">
                        <small class="text-muted d-block mb-1">{{ $vote['position']->title }}</small>
                        <div class="fw-semibold mb-2">{{ $vote['candidate']->name ?? 'Unknown candidate' }}</div>
                        <a href="{{ route('student.voting.position', $vote['position']) }}" class="btn btn-outline-primary btn-sm">
                            Change Choice
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-between flex-wrap gap-2">
            <a href="{{ route('student.voting.position', $positions->first()) }}" class="btn btn-outline-secondary">
                Back to Voting
            </a>
            @if(count($voteDetails) == $positions->count())
                <form action="{{ route('student.voting.submit') }}" method="POST">
                    @csrf
                    <button class="btn btn-success">
                        <i class="fas fa-paper-plane me-1"></i>Submit Vote
                    </button>
                </form>
            @else
                <span class="text-muted">Complete every position before submitting.</span>
            @endif
        </div>
    @endif
</div>
@endsection
