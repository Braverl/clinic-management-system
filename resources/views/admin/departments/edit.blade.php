@extends('layouts.app')

@section('title', 'Edit Department')

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-building me-2"></i>Edit Department: {{ $department->name }}</h2>
        <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back
        </a>
    </div>
    
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('admin.departments.update', $department) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label class="form-label">Department Name *</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $department->name) }}" required>
                    @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="4" class="form-control">{{ old('description', $department->description) }}</textarea>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Department Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    @if($department->image)
                        <div class="mt-2">
                            <img src="{{ Storage::url($department->image) }}" width="150" class="rounded">
                        </div>
                    @endif
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" name="is_active" class="form-check-input" id="is_active" {{ $department->is_active ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i>Update Department
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection