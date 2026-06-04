@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container-fluid mt-4">
    <h2><i class="fas fa-user-circle me-2"></i>My Profile</h2>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    @if(auth()->user()->profile_image)
                        <img src="{{ Storage::url(auth()->user()->profile_image) }}" class="profile-image mb-3">
                    @else
                        <div class="bg-secondary rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 120px; height: 120px;">
                            <i class="fas fa-user fa-3x text-white"></i>
                        </div>
                    @endif
                    <h4 class="mt-3">{{ auth()->user()->name }}</h4>
                    <p class="text-muted">{{ auth()->user()->email }}</p>
                    <span class="badge bg-primary">{{ ucfirst(auth()->user()->role) }}</span>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Personal Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Full Name</label>
                            <p class="fw-bold">{{ auth()->user()->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Email</label>
                            <p class="fw-bold">{{ auth()->user()->email }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Phone</label>
                            <p class="fw-bold">{{ auth()->user()->phone ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Blood Group</label>
                            <p class="fw-bold">{{ auth()->user()->patient->blood_group ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <a href="{{ route('patient.profile.edit') }}" class="btn btn-primary">Edit Profile</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection