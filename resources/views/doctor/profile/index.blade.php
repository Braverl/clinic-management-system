@extends('layouts.app')

@section('title', 'Doctor Profile')

@section('content')
<div class="container-fluid mt-4">
    <h2><i class="fas fa-user-md me-2"></i>Doctor Profile</h2>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    @if($doctor->profile_image)
                        <img src="{{ Storage::url($doctor->profile_image) }}" class="profile-image mb-3">
                    @else
                        <div class="profile-avatar-placeholder mx-auto">
                            <i class="fas fa-user-md"></i>
                        </div>
                    @endif
                    <h4 class="mt-3">Dr. {{ $doctor->name }}</h4>
                    <p class="text-muted">{{ $doctor->email }}</p>
                    <span class="badge bg-success">{{ $doctorDetails->specialization ?? 'General Physician' }}</span>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Professional Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Full Name</label>
                            <p class="fw-bold">Dr. {{ $doctor->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Email Address</label>
                            <p class="fw-bold">{{ $doctor->email }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Phone Number</label>
                            <p class="fw-bold">{{ $doctor->phone ?? 'Not provided' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Specialization</label>
                            <p class="fw-bold">{{ $doctorDetails->specialization ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Qualification</label>
                            <p class="fw-bold">{{ $doctorDetails->qualification ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Experience</label>
                            <p class="fw-bold">{{ $doctorDetails->experience_years ?? 0 }} years</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Consultation Fee</label>
                            <p class="fw-bold text-success">${{ number_format($doctorDetails->consultation_fee ?? 0, 2) }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">License Number</label>
                            <p class="fw-bold">{{ $doctorDetails->license_number ?? 'N/A' }}</p>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="text-muted">Bio</label>
                            <p class="fw-bold">{{ $doctorDetails->bio ?? 'Not provided' }}</p>
                        </div>
                    </div>
                    <a href="{{ route('doctor.profile.edit') }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection