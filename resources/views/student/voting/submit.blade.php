@extends('layouts.student-portal')

@section('title', 'Submit Votes')

@section('content')
<div class="portal-card p-4 text-center">
    <h1 class="h4 portal-heading mb-2">Finalize Your Vote</h1>
    <p class="portal-muted mb-3">Review your choices before submitting your vote.</p>
    <a href="{{ route('student.voting.review') }}" class="btn btn-primary">
        <i class="fas fa-list-check me-1"></i>Go to Review
    </a>
</div>
@endsection
