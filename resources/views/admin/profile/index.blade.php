@extends('layouts.app')

@section('title', 'Admin Profile')

@section('content')
<div class="container-fluid mt-4">
    <h2><i class="fas fa-user-shield me-2"></i>Admin Profile</h2>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    @if($admin->profile_image)
                        <img src="{{ Storage::url($admin->profile_image) }}" class="profile-image mb-3">
                    @else
                        <div class="profile-avatar-placeholder mx-auto">
                            <i class="fas fa-user-shield"></i>
                        </div>
                    @endif
                    <h4 class="mt-3">{{ $admin->name }}</h4>
                    <p class="text-muted">{{ $admin->email }}</p>
                    <span class="badge bg-primary">Administrator</span>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Personal Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Full Name</label>
                            <p class="fw-bold">{{ $admin->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Email Address</label>
                            <p class="fw-bold">{{ $admin->email }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Phone Number</label>
                            <p class="fw-bold">{{ $admin->phone ?? 'Not provided' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Role</label>
                            <p class="fw-bold"><span class="badge bg-danger">System Administrator</span></p>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="text-muted">Address</label>
                            <p class="fw-bold">{{ $admin->address ?? 'Not provided' }}</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.profile.edit') }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection