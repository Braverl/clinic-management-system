@extends('layouts.app')

@section('title', 'My Appointments')

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-calendar-check me-2"></i>My Appointments</h2>
        <a href="{{ route('patient.appointments.book') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Book New Appointment
        </a>
    </div>
    
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Appointment #</th>
                            <th>Doctor</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($appointments as $apt)
                        <tr>
                            <td>{{ $apt->appointment_number }}</td>
                            <td>Dr. {{ $apt->doctor->user->name }}<br>
                                <small class="text-muted">{{ $apt->doctor->specialization }}</small>
                            </td>
                            <td>{{ $apt->appointment_date }}</td>
                            <td>{{ \Carbon\Carbon::parse($apt->appointment_time)->format('g:i A') }}</td>
                            <td>
                                <span class="badge bg-{{ $apt->status == 'pending' ? 'warning' : ($apt->status == 'confirmed' ? 'info' : ($apt->status == 'completed' ? 'success' : 'danger')) }}">
                                    {{ ucfirst($apt->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('patient.appointments.show', $apt) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                @if(in_array($apt->status, ['pending', 'confirmed']))
                                <a href="{{ route('patient.appointments.reschedule', $apt->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-calendar-alt"></i> Reschedule
                                </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="fas fa-calendar-times fa-3x text-muted mb-3 d-block"></i>
                                <p>No appointments found.</p>
                                <a href="{{ route('patient.appointments.book') }}" class="btn btn-primary">Book Your First Appointment</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $appointments->links() }}
        </div>
    </div>
</div>
@endsection