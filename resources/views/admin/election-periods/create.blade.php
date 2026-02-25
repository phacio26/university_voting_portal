@extends('layouts.admin')

@section('title', 'Create Election Period')
@section('page-title', 'Create Election Period')
@section('page-icon', 'fa-plus-circle')

@section('styles')
<style>
    @media (max-width: 767.98px) {
        .election-form-actions {
            flex-direction: column;
        }

        .election-form-actions .btn {
            width: 100%;
        }
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <a href="{{ route('admin.election-periods.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back to List
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.election-periods.store') }}">
            @csrf

            <div class="mb-3">
                <label for="title" class="form-label">Election Title *</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

             

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="start_time" class="form-label">Start Time *</label>
                        <input type="datetime-local" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" value="{{ old('start_time') }}" required>
                        @error('start_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Malawi time (Africa/Blantyre).</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="end_time" class="form-label">End Time *</label>
                        <input type="datetime-local" class="form-control @error('end_time') is-invalid @enderror" id="end_time" name="end_time" value="{{ old('end_time') }}" required>
                        @error('end_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text"><b>Must be later than start time.<b></div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 flex-wrap election-form-actions">
                <a href="{{ route('admin.election-periods.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Election Period
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    (function () {
        const startInput = document.getElementById('start_time');
        const endInput = document.getElementById('end_time');
        if (!startInput || !endInput) return;

        const now = new Date();
        const timezoneOffset = now.getTimezoneOffset() * 60000;
        const localTime = new Date(now - timezoneOffset).toISOString().slice(0, 16);

        startInput.min = localTime;
        endInput.min = localTime;

        startInput.addEventListener('change', function () {
            endInput.min = this.value;
            if (endInput.value && endInput.value < this.value) {
                endInput.value = this.value;
            }
        });
    })();
</script>
@endsection
