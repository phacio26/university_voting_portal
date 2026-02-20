@extends('layouts.admin')

@section('title', 'Edit Position')
@section('page-title', 'Edit Position')
@section('page-icon', 'fa-edit')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('admin.positions.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back to List
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.positions.update', $position) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="title" class="form-label">Position Title *</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $position->title) }}" required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $position->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="order" class="form-label">Display Order *</label>
                <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', $position->order) }}" min="0" required>
                @error('order')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Lower numbers appear earlier in the ballot.</div>
            </div>

            <div class="d-flex justify-content-end gap-2 flex-wrap">
                <a href="{{ route('admin.positions.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Position
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
