@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="welcome-banner">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2><i class="fas fa-tachometer-alt me-2"></i>Welcome back, {{ auth()->user()->name }}!</h2>
                        <p>Here's what's happening with your clinic today.</p>
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
        </div>
    </div>
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-user-md"></i></div>
            <h3 class="stat-number">{{ $totalDoctors ?? \App\Models\Doctor::count() }}</h3>
            <p class="stat-label">Total Doctors</p>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <h3 class="stat-number">{{ $totalPatients ?? \App\Models\Patient::count() }}</h3>
            <p class="stat-label">Total Patients</p>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
            <h3 class="stat-number">{{ $totalAppointments ?? \App\Models\Appointment::count() }}</h3>
            <p class="stat-label">Total Appointments</p>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-dollar-sign"></i></div>
            <h3 class="stat-number">${{ number_format($totalRevenue ?? \App\Models\Payment::where('status', 'completed')->sum('amount'), 2) }}</h3>
            <p class="stat-label">Total Revenue</p>
        </div>
    </div>
    
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6>Pending Approvals</h6>
                    <h3 class="text-warning">{{ $pendingAppointments ?? \App\Models\Appointment::where('status', 'pending')->count() }}</h3>
                    <small>Appointments waiting for confirmation</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6>Today's Appointments</h6>
                    <h3 class="text-info">{{ $todayAppointments ?? \App\Models\Appointment::whereDate('appointment_date', now())->count() }}</h3>
                    <small>Appointments scheduled today</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6>Monthly Revenue</h6>
                    <h3 class="text-success">${{ number_format($monthlyRevenue ?? 0, 2) }}</h3>
                    <small>This month's earnings</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="chart-wrapper">
                <canvas id="appointmentsChart"></canvas>
            </div>
        </div>
        <div class="col-md-4">
            <div class="chart-wrapper">
                <h5>Quick Actions</h5>
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.doctors.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add New Doctor
                    </a>
                    <a href="{{ route('admin.patients.create') }}" class="btn btn-success">
                        <i class="fas fa-user-plus me-2"></i>Add New Patient
                    </a>
                    <a href="{{ route('admin.appointments.index') }}" class="btn btn-info">
                        <i class="fas fa-calendar me-2"></i>View Appointments
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-warning">
                        <i class="fas fa-chart-line me-2"></i>Generate Reports
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('appointmentsChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['labels'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']) !!},
            datasets: [{
                label: 'Appointments',
                data: {!! json_encode($chartData['appointments'] ?? [65, 59, 80, 81, 56, 95]) !!},
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true
        }
    });
</script>
@endpush
@endsection