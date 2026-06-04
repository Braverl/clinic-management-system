@extends('layouts.app')

@section('title', 'Doctor Dashboard')

@section('content')
<div class="container-fluid mt-4">
    <div class="welcome-banner">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>Welcome, Dr. {{ auth()->user()->name }}!</h2>
                <p>Here's your schedule and appointments for today.</p>
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
                <div class="stat-icon"><i class="fas fa-calendar-today"></i></div>
                <h3 class="stat-number">{{ \App\Models\Appointment::where('doctor_id', auth()->user()->doctor->id)->whereDate('appointment_date', today())->count() }}</h3>
                <p class="stat-label">Today's Appointments</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <h3 class="stat-number">{{ \App\Models\Appointment::where('doctor_id', auth()->user()->doctor->id)->where('status', 'pending')->count() }}</h3>
                <p class="stat-label">Pending Approvals</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <h3 class="stat-number">{{ \App\Models\Appointment::where('doctor_id', auth()->user()->doctor->id)->where('status', 'completed')->count() }}</h3>
                <p class="stat-label">Total Patients Seen</p>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Session Information</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <small>Logged in as: <strong>Dr. {{ auth()->user()->name }}</strong></small><br>
                        <small>Role: <strong>Doctor</strong></small><br>
                        <small>Login Time: <strong>{{ session('login_time') ? date('Y-m-d H:i:s', strtotime(session('login_time'))) : 'N/A' }}</strong></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection