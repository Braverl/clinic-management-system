@extends('layouts.app')

@section('title', 'Appointments - ' . $patient->user->name)

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-calendar-check me-2"></i>Appointments of {{ $patient->user->name }}</h2>
            <p class="text-muted mb-0">{{ $patient->user->email }} | {{ $patient->blood_group ? 'Blood: ' . $patient->blood_group : '' }}</p>
        </div>
        <div>
            <a href="{{ route('admin.patients.appointments.create', $patient) }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Book Appointment
            </a>
            <a href="{{ route('admin.patients.show', $patient) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Patient
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Appointment #</th>
                            <th>Doctor</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($appointments as $apt)
                        <tr>
                            <td><strong>{{ $apt->appointment_number }}</strong></td>
                            <td>Dr. {{ $apt->doctor->user->name ?? 'N/A' }}<br>
                                <small class="text-muted">{{ $apt->doctor->specialization ?? '' }}</small>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($apt->appointment_date)->format('M d, Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($apt->appointment_time)->format('g:i A') }}</td>
                            <td>
                                <span class="badge bg-{{ $apt->status === 'pending' ? 'warning' : ($apt->status === 'confirmed' ? 'info' : ($apt->status === 'completed' ? 'success' : 'danger')) }} text-capitalize">
                                    {{ $apt->status }}
                                </span>
                            </td>
                            <td>
                                @if($apt->payment)
                                <span class="badge bg-{{ $apt->payment->status === 'completed' ? 'success' : 'warning' }} text-capitalize">{{ $apt->payment->status }}</span>
                                @else
                                <span class="badge bg-secondary">Not Paid</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.patients.appointments.show', [$patient, $apt]) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-calendar-times fa-3x text-muted mb-3 d-block"></i>
                                <p>No appointments found for this patient.</p>
                                <a href="{{ route('admin.patients.appointments.create', $patient) }}" class="btn btn-primary">Book First Appointment</a>
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