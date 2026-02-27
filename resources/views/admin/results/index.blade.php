@extends('layouts.admin')

@section('title', 'Election Results')
@section('page-title', 'Election Results')
@section('page-icon', 'fa-graduation-cap')

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
                            <th>Election Title</th>
                            <th>Completion Date</th>
                            <th>Status</th>
                            <th>Visibility</th>
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
                                        <span class="badge bg-warning text-dark">Tie-break Required</span>
                                    @else
                                        <span class="badge bg-success">Finalized</span>
                                    @endif
                                </td>
                                <td>
                                    @if($election->results_visible_to_students)
                                        <span class="badge bg-info">Public</span>
                                    @else
                                        <span class="badge bg-secondary">Private</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.results.show', $election) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-trophy me-1"></i>Results
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
                                    <span class="badge bg-warning text-dark">Tie-break</span>
                                @else
                                    <span class="badge bg-success">Finalized</span>
                                @endif
                            </div>

                            <div class="small mb-2">
                                @if($election->results_visible_to_students)
                                    <span class="badge bg-info">Public</span>
                                @else
                                    <span class="badge bg-secondary">Private</span>
                                @endif
                            </div>

                            @if($election->description)
                                <p class="small text-muted mb-2">{{ Str::limit($election->description, 120) }}</p>
                            @endif

                            <a href="{{ route('admin.results.show', $election) }}" class="btn btn-outline-primary btn-sm w-100">
                                <i class="fas fa-trophy me-1"></i>Results
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-3">
                {{ $elections->links('vendor.pagination.simple') }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Results to Display</h5>
                <p class="text-muted mb-0">Completed elections will appear here.</p>
            </div>
        @endif
    </div>
</div>
@endsection
