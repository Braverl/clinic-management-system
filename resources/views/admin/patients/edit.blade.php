@extends('layouts.app')

@section('title', 'Edit Patient')

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-user me-2"></i>Edit Patient: {{ $patient->user->name }}</h2>
        <a href="{{ route('admin.patients.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back
        </a>
    </div>
    
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('admin.patients.update', $patient) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $patient->user->name) }}" required>
                        @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="{{ $patient->user->email }}" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone *</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $patient->user->phone) }}" required>
                        @error('phone') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="dob" class="form-control" value="{{ old('dob', $patient->user->dob) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-control">
                            <option value="">Select</option>
                            <option value="male" {{ $patient->user->gender == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ $patient->user->gender == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ $patient->user->gender == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Blood Group</label>
                        <select name="blood_group" class="form-control">
                            <option value="">Select</option>
                            <option value="A+" {{ $patient->blood_group == 'A+' ? 'selected' : '' }}>A+</option>
                            <option value="A-" {{ $patient->blood_group == 'A-' ? 'selected' : '' }}>A-</option>
                            <option value="B+" {{ $patient->blood_group == 'B+' ? 'selected' : '' }}>B+</option>
                            <option value="B-" {{ $patient->blood_group == 'B-' ? 'selected' : '' }}>B-</option>
                            <option value="O+" {{ $patient->blood_group == 'O+' ? 'selected' : '' }}>O+</option>
                            <option value="O-" {{ $patient->blood_group == 'O-' ? 'selected' : '' }}>O-</option>
                            <option value="AB+" {{ $patient->blood_group == 'AB+' ? 'selected' : '' }}>AB+</option>
                            <option value="AB-" {{ $patient->blood_group == 'AB-' ? 'selected' : '' }}>AB-</option>
                        </select>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" rows="2" class="form-control">{{ old('address', $patient->user->address) }}</textarea>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Allergies</label>
                        <textarea name="allergies" rows="2" class="form-control">{{ old('allergies', $patient->allergies) }}</textarea>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Medical History</label>
                        <textarea name="medical_history" rows="2" class="form-control">{{ old('medical_history', $patient->medical_history) }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Emergency Contact Name</label>
                        <input type="text" name="emergency_contact_name" class="form-control" value="{{ old('emergency_contact_name', $patient->emergency_contact_name) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Emergency Contact Number</label>
                        <input type="text" name="emergency_contact" class="form-control" value="{{ old('emergency_contact', $patient->emergency_contact) }}">
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Profile Image</label>
                        <input type="file" name="profile_image" class="form-control" accept="image/*">
                        @if($patient->user->profile_image)
                            <div class="mt-2">
                                <img src="{{ Storage::url($patient->user->profile_image) }}" width="100" class="rounded">
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i>Update Patient
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection