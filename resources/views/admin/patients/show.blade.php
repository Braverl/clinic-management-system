@extends('layouts.app')

@section('title', 'Patient Details')

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-user me-2"></i>Patient Details</h2>
        <div>
            <a href="{{ route('admin.patients.appointments.create', $patient) }}" class="btn btn-primary">
                <i class="fas fa-calendar-plus me-2"></i>Book Appointment
            </a>
            <a href="{{ route('admin.patients.appointments.index', $patient) }}" class="btn btn-info">
                <i class="fas fa-calendar-check me-2"></i>Appointments
            </a>
            <a href="{{ route('admin.patients.edit', $patient) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('admin.patients.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <!-- UPDATED PROFILE SECTION WITH IMAGE -->
                <div class="card-body text-center">
                    @if($patient->user->profile_image)
                        <img src="{{ Storage::url($patient->user->profile_image) }}" class="profile-image mb-3" style="width: 150px; height: 150px; object-fit: cover; border-radius: 50%;">
                    @else
                        <div class="bg-secondary rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 150px; height: 150px;">
                            <i class="fas fa-user fa-5x text-white"></i>
                        </div>
                    @endif
                    <h4 class="mt-3">{{ $patient->user->name }}</h4>
                    <p class="text-muted">{{ $patient->user->email }}</p>
                    <div class="mt-3">
                        <span class="badge bg-{{ $patient->user->is_active ? 'success' : 'danger' }} p-2">
                            {{ $patient->user->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
                <!-- END UPDATED PROFILE SECTION -->
            </div>
            
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted">Total Appointments</label>
                        <h3>{{ $totalAppointments }}</h3>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted">Completed Appointments</label>
                        <h3>{{ $completedAppointments }}</h3>
                    </div>
                    <div>
                        <label class="text-muted">Total Spent</label>
                        <h3 class="text-success">${{ number_format($totalSpent, 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Personal Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Full Name</label>
                            <p class="fw-bold">{{ $patient->user->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Email Address</label>
                            <p class="fw-bold">{{ $patient->user->email }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Phone Number</label>
                            <p class="fw-bold">{{ $patient->user->phone ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Date of Birth</label>
                            <p class="fw-bold">{{ $patient->user->dob ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Gender</label>
                            <p class="fw-bold">{{ ucfirst($patient->user->gender ?? 'N/A') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Blood Group</label>
                            <p class="fw-bold">{{ $patient->blood_group ?? 'N/A' }}</p>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="text-muted">Address</label>
                            <p class="fw-bold">{{ $patient->user->address ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Emergency Contact Name</label>
                            <p class="fw-bold">{{ $patient->emergency_contact_name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Emergency Contact Number</label>
                            <p class="fw-bold">{{ $patient->emergency_contact ?? 'N/A' }}</p>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="text-muted">Allergies</label>
                            <p class="fw-bold">{{ $patient->allergies ?? 'None' }}</p>
                        </div>
                        <div class="col-12">
                            <label class="text-muted">Medical History</label>
                            <p class="fw-bold">{{ $patient->medical_history ?? 'None' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Recent Appointments</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Doctor</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th>Fee</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($patient->appointments()->latest()->limit(10)->get() as $apt)
                                <tr>
                                    <td>{{ $apt->appointment_date }}</td>
                                    <td>Dr. {{ $apt->doctor->user->name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($apt->appointment_time)->format('g:i A') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $apt->status == 'pending' ? 'warning' : ($apt->status == 'completed' ? 'success' : 'info') }}">
                                            {{ ucfirst($apt->status) }}
                                        </span>
                                    </td>
                                    <td>${{ number_format($apt->doctor->consultation_fee, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No appointments found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection