@extends('layouts.student-portal')

@section('title', 'No Results Yet')

@section('content')
<div class="portal-card p-4 p-md-5 text-center">
    <i class="fas fa-signal fa-3x text-muted mb-3"></i>
    <h1 class="h4 portal-heading mb-2">No Published Results Yet</h1>
    <p class="portal-muted mb-4">
        Results will be visible only after the election process is fully completed and finalized.
        Check your dashboard for the current voting status.
    </p>
    <a href="{{ route('student.dashboard') }}" class="btn btn-primary">
        <i class="fas fa-house me-2"></i>Back to Dashboard
    </a>
</div>
@endsection
