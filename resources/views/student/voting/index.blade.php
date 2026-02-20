@extends('layouts.student-portal')

@section('title', 'Voting')

@section('content')
<div class="portal-card p-4 text-center">
    <h1 class="h4 portal-heading mb-2">Voting Session</h1>
    <p class="portal-muted mb-3">Start or continue your voting process.</p>
    <a href="{{ route('student.voting.start') }}" class="btn btn-primary">
        <i class="fas fa-arrow-right me-1"></i>Continue Voting
    </a>
</div>
@endsection
