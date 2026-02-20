@extends('layouts.admin')

@section('title', 'Edit Candidate')
@section('page-title', 'Edit Candidate')
@section('page-icon', 'fa-edit')

@section('styles')
<style>
    .photo-preview-wrap {
        width: 160px;
        height: 160px;
        margin: 0 auto;
        position: relative;
    }

    .photo-preview {
        width: 160px;
        height: 160px;
        object-fit: cover;
        border-radius: 999px;
        border: 3px solid #dfe6f0;
        background: #f7f9fc;
        display: none;
        position: absolute;
        top: 0;
        left: 0;
    }

    .photo-placeholder {
        width: 160px;
        height: 160px;
        border-radius: 999px;
        border: 3px solid #dfe6f0;
        background: #f7f9fc;
        color: #5b6470;
        font-weight: 600;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 0.5rem;
        margin: 0 auto;
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('admin.candidates.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back to List
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.candidates.update', $candidate) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-4">
                <div class="col-md-4 text-center">
                    <label for="photo" class="form-label">Candidate Photo</label>
                    <div class="mb-3">
                        <div class="photo-preview-wrap">
                            <div id="photo-placeholder" class="photo-placeholder" @if($candidate->photo) style="display: none;" @endif>Candidate Photo</div>
                            <img
                                id="photo-preview"
                                src="{{ $candidate->photo ? asset('storage/' . $candidate->photo) : '' }}"
                                class="photo-preview"
                                alt="Candidate photo preview"
                                @if($candidate->photo) style="display: block;" @endif
                            >
                        </div>
                    </div>
                    <input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo" name="photo" accept="image/*">
                    @error('photo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Minimum size is 300x300 (JPG, PNG, WEBP).</div>
                </div>

                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="name" class="form-label">Candidate Name *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $candidate->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="position_id" class="form-label">Position *</label>
                        <select class="form-select @error('position_id') is-invalid @enderror" id="position_id" name="position_id" required>
                            <option value="">Select a position</option>
                            @foreach($positions as $position)
                                <option value="{{ $position->id }}" {{ old('position_id', $candidate->position_id) == $position->id ? 'selected' : '' }}>
                                    {{ $position->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('position_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="bio" class="form-label">Biography</label>
                        <textarea class="form-control @error('bio') is-invalid @enderror" id="bio" name="bio" rows="4" placeholder="Brief description about the candidate">{{ old('bio', $candidate->bio) }}</textarea>
                        @error('bio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 flex-wrap">
                <a href="{{ route('admin.candidates.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Candidate
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    (function () {
        const input = document.getElementById('photo');
        const preview = document.getElementById('photo-preview');
        const placeholder = document.getElementById('photo-placeholder');
        const originalSrc = "{{ $candidate->photo ? asset('storage/' . $candidate->photo) : '' }}";
        if (!input || !preview || !placeholder) return;

        input.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (!file) {
                preview.src = originalSrc;
                if (originalSrc) {
                    preview.style.display = 'block';
                    placeholder.style.display = 'none';
                } else {
                    preview.style.display = 'none';
                    placeholder.style.display = 'flex';
                }
                return;
            }

            const reader = new FileReader();
            reader.onload = function (event) {
                preview.src = event.target.result;
                preview.style.display = 'block';
                placeholder.style.display = 'none';
            };
            reader.readAsDataURL(file);
        });
    })();
</script>
@endsection
