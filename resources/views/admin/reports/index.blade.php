@extends('layouts.app')

@section('title', 'Generate Reports')

@section('content')
<div class="container-fluid mt-4">
    <h2><i class="fas fa-chart-line me-2"></i>Generate Reports</h2>
    
    <div class="row">
        <!-- Appointment Report -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Appointment Report</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.reports.appointments') }}" method="POST" target="_blank">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-download me-2"></i>Generate PDF
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Revenue Report -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-dollar-sign me-2"></i>Revenue Report</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.reports.revenue') }}" method="POST" target="_blank">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Select Year</label>
                            <select name="year" class="form-control" required>
                                <option value="">Select Year</option>
                                @for($i = 2023; $i <= date('Y'); $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Select Month (Optional)</label>
                            <select name="month" class="form-control">
                                <option value="">All Months</option>
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}">{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                                @endfor
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-download me-2"></i>Generate Revenue Report
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Doctor Performance Report -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-user-md me-2"></i>Doctor Performance Report</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.reports.doctors') }}" method="POST" target="_blank">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-info w-100">
                            <i class="fas fa-download me-2"></i>Generate Doctor Report
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Stats -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Quick Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <h3 class="text-primary">{{ \App\Models\Appointment::count() }}</h3>
                            <p>Total Appointments</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <h3 class="text-success">{{ \App\Models\Appointment::where('status', 'completed')->count() }}</h3>
                            <p>Completed</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <h3 class="text-warning">{{ \App\Models\Appointment::where('status', 'pending')->count() }}</h3>
                            <p>Pending</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <h3 class="text-info">{{ \App\Models\Doctor::count() }}</h3>
                            <p>Active Doctors</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection