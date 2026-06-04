@extends('layouts.app')

@section('title', 'Manage Appointments')

@section('content')
<div class="container-fluid mt-4">
    <h2 class="mb-4"><i class="fas fa-calendar-check me-2"></i>My Appointments</h2>
    
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-warning">
                <div class="card-body text-white">
                    <h6>Pending Approvals</h6>
                    <h2 class="mb-0">{{ $stats['pending'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-info">
                <div class="card-body text-white">
                    <h6>Today's Appointments</h6>
                    <h2 class="mb-0">{{ $stats['today'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-success">
                <div class="card-body text-white">
                    <h6>Upcoming</h6>
                    <h2 class="mb-0">{{ $stats['upcoming'] }}</h2>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <div class="row">
                <div class="col-md-4">
                    <select id="statusFilter" class="form-control">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="date" id="dateFilter" class="form-control">
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="appointmentsTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Patient</th>
                            <th>Photo</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Symptoms</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($appointments as $apt)
                        <tr>
                            <td>{{ $apt->appointment_number }}</td>
                            <td>
                                <strong>{{ $apt->patient->user->name }}</strong><br>
                                <small>{{ $apt->patient->user->phone }}</small>
                            </td>
                            <td class="text-center">
                                @if($apt->patient->user->profile_image)
                                    <img src="{{ Storage::url($apt->patient->user->profile_image) }}" width="45" height="45" class="rounded-circle object-fit-cover">
                                @else
                                    <div class="bg-info rounded-circle d-inline-flex align-items-center justify-content-center text-white" style="width: 45px; height: 45px;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                @endif
                            </td>
                            <td>{{ $apt->appointment_date }}</td>
                            <td>{{ \Carbon\Carbon::parse($apt->appointment_time)->format('g:i A') }}</td>
                            <td>{{ Str::limit($apt->symptoms, 50) }}</td>
                            <td>
                                <span class="badge bg-{{ $apt->status == 'pending' ? 'warning' : ($apt->status == 'confirmed' ? 'info' : ($apt->status == 'completed' ? 'success' : 'danger')) }}">
                                    {{ ucfirst($apt->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('doctor.appointments.show', $apt) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $appointments->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    $('#statusFilter, #dateFilter').on('change', function() {
        const status = $('#statusFilter').val();
        const date = $('#dateFilter').val();
        window.location.href = '{{ route("doctor.appointments.index") }}?status=' + status + '&date=' + date;
    });
</script>
@endpush
@endsection