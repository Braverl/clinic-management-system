@extends('layouts.app')

@section('title', 'Edit Doctor')

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-user-md me-2"></i>Edit Doctor: Dr. {{ $doctor->user->name }}</h2>
        <a href="{{ route('admin.doctors.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back
        </a>
    </div>
    
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('admin.doctors.update', $doctor) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $doctor->user->name) }}" required>
                        @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="{{ $doctor->user->email }}" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone *</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $doctor->user->phone) }}" required>
                        @error('phone') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">License Number</label>
                        <input type="text" class="form-control" value="{{ $doctor->license_number }}" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Department *</label>
                        <select name="department_id" class="form-control @error('department_id') is-invalid @enderror" required>
                            <option value="">Select Department</option>
                            @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id', $doctor->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                        @error('department_id') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Specialization *</label>
                        <input type="text" name="specialization" class="form-control @error('specialization') is-invalid @enderror" value="{{ old('specialization', $doctor->specialization) }}" required>
                        @error('specialization') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Qualification *</label>
                        <input type="text" name="qualification" class="form-control @error('qualification') is-invalid @enderror" value="{{ old('qualification', $doctor->qualification) }}" required>
                        @error('qualification') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Experience (Years) *</label>
                        <input type="number" name="experience_years" class="form-control @error('experience_years') is-invalid @enderror" value="{{ old('experience_years', $doctor->experience_years) }}" required>
                        @error('experience_years') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Consultation Fee ($) *</label>
                        <input type="number" step="0.01" name="consultation_fee" class="form-control @error('consultation_fee') is-invalid @enderror" value="{{ old('consultation_fee', $doctor->consultation_fee) }}" required>
                        @error('consultation_fee') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Bio</label>
                        <textarea name="bio" rows="3" class="form-control">{{ old('bio', $doctor->bio) }}</textarea>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Availability Schedule</label>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Available Days</label>
                        <div class="row">
                            @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                            <div class="col-auto">
                                <div class="form-check">
                                    <input type="checkbox" name="available_days[]" value="{{ $day }}" class="form-check-input" id="day{{ $day }}" {{ in_array($day, old('available_days', $availableDays)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="day{{ $day }}">{{ $day }}</label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Available From (time)</label>
                        <input type="time" name="available_from" class="form-control" value="{{ old('available_from', $doctor->available_from ? \Carbon\Carbon::parse($doctor->available_from)->format('H:i') : '09:00') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Available Until (time)</label>
                        <input type="time" name="available_to" class="form-control" value="{{ old('available_to', $doctor->available_to ? \Carbon\Carbon::parse($doctor->available_to)->format('H:i') : '17:00') }}">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Profile Image</label>
                        <input type="file" name="profile_image" class="form-control" accept="image/*">
                        @if($doctor->user->profile_image)
                            <div class="mt-2">
                                <img src="{{ Storage::url($doctor->user->profile_image) }}" width="100" class="rounded">
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i>Update Doctor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection