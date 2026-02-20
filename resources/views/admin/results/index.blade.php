@extends('layouts.admin')

@section('title', 'Election Results')
@section('page-title', 'Election Results')
@section('page-icon', 'fa-trophy')

@section('styles')
<style>
    .result-mobile-card {
        border: 1px solid var(--neutral-border);
        border-radius: var(--radius-md);
        background: #fff;
    }
</style>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        @if($elections->count() > 0)
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Election</th>
                            <th>Ended</th>
                            <th>Resolution Status</th>
                            <th>Student Visibility</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($elections as $election)
                            <tr>
                                <td>
                                    <strong>{{ $election->title }}</strong>
                                    @if($election->description)
                                        <div class="small text-muted">{{ Str::limit($election->description, 90) }}</div>
                                    @endif
                                </td>
                                <td>{{ $election->end_time->format('M j, Y g:i A') }}</td>
                                <td>
                                    @if($election->pending_resolution)
                                        <span class="badge bg-warning text-dark">Pending Re-vote/Tie</span>
                                    @else
                                        <span class="badge bg-success">Resolved</span>
                                    @endif
                                </td>
                                <td>
                                    @if($election->results_visible_to_students)
                                        <span class="badge bg-info">Published</span>
                                    @else
                                        <span class="badge bg-secondary">Admin Only</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.results.show', $election) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye me-1"></i>View Winners
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-md-none">
                <div class="d-grid gap-3">
                    @foreach($elections as $election)
                        <div class="result-mobile-card p-3">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <div>
                                    <h6 class="mb-1">{{ $election->title }}</h6>
                                    <div class="small text-muted">Ended: {{ $election->end_time->format('M j, Y g:i A') }}</div>
                                </div>
                                @if($election->pending_resolution)
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @else
                                    <span class="badge bg-success">Resolved</span>
                                @endif
                            </div>

                            <div class="small mb-2">
                                @if($election->results_visible_to_students)
                                    <span class="badge bg-info">Published</span>
                                @else
                                    <span class="badge bg-secondary">Admin Only</span>
                                @endif
                            </div>

                            @if($election->description)
                                <p class="small text-muted mb-2">{{ Str::limit($election->description, 120) }}</p>
                            @endif

                            <a href="{{ route('admin.results.show', $election) }}" class="btn btn-outline-primary btn-sm w-100">
                                <i class="fas fa-eye me-1"></i>View Winners
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-3">
                {{ $elections->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Closed Elections Yet</h5>
                <p class="text-muted mb-0">Results will appear here after an election closes.</p>
            </div>
        @endif
    </div>
</div>
@endsection
