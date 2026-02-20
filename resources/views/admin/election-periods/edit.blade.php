@extends('layouts.admin')

@section('title', 'Edit Election Period')
@section('page-title', 'Edit Election Period')
@section('page-icon', 'fa-edit')

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
        <form method="POST" action="{{ route('admin.election-periods.update', $electionPeriod) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="title" class="form-label">Election Title *</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $electionPeriod->title) }}" required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $electionPeriod->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="start_time" class="form-label">Start Time *</label>
                        <input type="datetime-local" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" value="{{ old('start_time', $electionPeriod->start_time->format('Y-m-d\TH:i')) }}" required>
                        @error('start_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="end_time" class="form-label">End Time *</label>
                        <input type="datetime-local" class="form-control @error('end_time') is-invalid @enderror" id="end_time" name="end_time" value="{{ old('end_time', $electionPeriod->end_time->format('Y-m-d\TH:i')) }}" required>
                        @error('end_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 flex-wrap election-form-actions">
                <a href="{{ route('admin.election-periods.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Election Period
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

        startInput.addEventListener('change', function () {
            endInput.min = this.value;
            if (endInput.value && endInput.value < this.value) {
                endInput.value = this.value;
            }
        });
    })();
</script>
@endsection
