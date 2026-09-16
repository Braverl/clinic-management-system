@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-user-edit me-2"></i>Edit Profile</h4>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('patient.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" value="{{ $user->email }}" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" name="dob" class="form-control" value="{{ old('dob', $user->dob) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-control">
                                    <option value="">Select</option>
                                    <option value="male" {{ $user->gender == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ $user->gender == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ $user->gender == 'other' ? 'selected' : '' }}>Other</option>
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
                                <textarea name="address" rows="2" class="form-control">{{ old('address', $user->address) }}</textarea>
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
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Insurance Provider</label>
                                <input type="text" name="insurance_provider" class="form-control" value="{{ old('insurance_provider', $patient->insurance_provider) }}" placeholder="e.g. BlueCross, Aetna">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Insurance Number</label>
                                <input type="text" name="insurance_number" class="form-control" value="{{ old('insurance_number', $patient->insurance_number) }}">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Profile Image</label>
                                <input type="file" name="profile_image" class="form-control" accept="image/*">
                                @if($user->profile_image)
                                    <div class="mt-2">
                                        <img src="{{ Storage::url($user->profile_image) }}" width="100" class="rounded">
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Profile
                            </button>
                            <a href="{{ route('patient.profile.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                    
                    <hr class="my-4">
                    
                    <h5>Change Password</h5>
                    <form action="{{ route('patient.profile.change-password') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label>Current Password</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>New Password</label>
                                <input type="password" name="new_password" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Confirm Password</label>
                                <input type="password" name="new_password_confirmation" class="form-control" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-warning">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection