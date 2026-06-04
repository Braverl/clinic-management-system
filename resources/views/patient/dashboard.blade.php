@extends('layouts.app')

@section('title', 'Patient Dashboard')

@section('content')
<div class="container-fluid mt-4">
    <div class="welcome-banner">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>Welcome, {{ auth()->user()->name }}!</h2>
                <p>Manage your appointments and medical records.</p>
            </div>
            <div>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                <h3 class="stat-number">{{ \App\Models\Appointment::where('patient_id', auth()->user()->patient->id)->count() }}</h3>
                <p class="stat-label">Total Appointments</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <h3 class="stat-number">{{ \App\Models\Appointment::where('patient_id', auth()->user()->patient->id)->whereIn('status', ['pending', 'confirmed'])->count() }}</h3>
                <p class="stat-label">Upcoming</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <h3 class="stat-number">{{ \App\Models\Appointment::where('patient_id', auth()->user()->patient->id)->where('status', 'completed')->count() }}</h3>
                <p class="stat-label">Completed</p>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('patient.appointments.book') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-2"></i>Book New Appointment
                    </a>
                    <a href="{{ route('patient.medical-history') }}" class="btn btn-info">
                        <i class="fas fa-file-medical me-2"></i>View Medical History
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Session Information</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <small>Logged in as: <strong>{{ auth()->user()->name }}</strong></small><br>
                        <small>Role: <strong>Patient</strong></small><br>
                        <small>Login Time: <strong>{{ session('login_time') ? date('Y-m-d H:i:s', strtotime(session('login_time'))) : 'N/A' }}</strong></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection