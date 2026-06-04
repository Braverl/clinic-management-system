@extends('layouts.app')

@section('title', 'Edit Doctor Profile')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-user-edit me-2"></i>Edit Doctor Profile</h4>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('doctor.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $doctor->name) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" value="{{ $doctor->email }}" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $doctor->phone) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Profile Image</label>
                                <input type="file" name="profile_image" class="form-control" accept="image/*">
                                @if($doctor->profile_image)
                                    <div class="mt-2">
                                        <img src="{{ Storage::url($doctor->profile_image) }}" width="80" class="rounded-circle">
                                    </div>
                                @endif
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Bio / About</label>
                                <textarea name="bio" rows="4" class="form-control">{{ old('bio', $doctorDetails->bio) }}</textarea>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Profile
                            </button>
                            <a href="{{ route('doctor.profile.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                    
                    <hr class="my-4">
                    
                    <h5>Change Password</h5>
                    <form action="{{ route('doctor.profile.change-password') }}" method="POST">
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