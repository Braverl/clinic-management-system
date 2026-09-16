@extends('layouts.app')

@section('title', 'Appointment Details - ' . $patient->user->name)

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Appointment Details</h4>
                        <span class="badge bg-light text-dark">{{ $appointment->appointment_number }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-{{ $appointment->status === 'pending' ? 'warning' : ($appointment->status === 'confirmed' ? 'info' : ($appointment->status === 'completed' ? 'success' : 'danger')) }}"
                                 style="width: {{ $appointment->status === 'pending' ? '25' : ($appointment->status === 'confirmed' ? '50' : ($appointment->status === 'completed' ? '100' : '0')) }}%">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <small>Pending</small>
                            <small>Confirmed</small>
                            <small>Completed</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Patient</label>
                            <p class="fw-bold">{{ $patient->user->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Doctor</label>
                            <p class="fw-bold">Dr. {{ $appointment->doctor->user->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Specialization</label>
                            <p class="fw-bold">{{ $appointment->doctor->specialization }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Consultation Fee</label>
                            <p class="fw-bold text-success">${{ number_format($appointment->doctor->consultation_fee, 2) }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Date</label>
                            <p class="fw-bold">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('l, F d, Y') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Time</label>
                            <p class="fw-bold">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</p>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="text-muted">Symptoms</label>
                            <p class="fw-bold">{{ $appointment->symptoms ?? 'Not specified' }}</p>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="text-muted">Status</label>
                            <p>
                                <span class="badge bg-{{ $appointment->status === 'pending' ? 'warning' : ($appointment->status === 'confirmed' ? 'info' : ($appointment->status === 'completed' ? 'success' : 'danger')) }} p-2 text-capitalize">
                                    {{ $appointment->status }}
                                </span>
                            </p>
                        </div>
                        @if($appointment->cancellation_reason)
                        <div class="col-12 mb-3">
                            <label class="text-muted text-danger">Cancellation Reason</label>
                            <p class="text-danger">{{ $appointment->cancellation_reason }}</p>
                        </div>
                        @endif
                    </div>

                    @if($appointment->medicalRecord)
                    <div class="alert alert-info mt-3">
                        <h6><i class="fas fa-file-medical me-2"></i>Medical Record</h6>
                        <p><strong>Diagnosis:</strong> {{ $appointment->medicalRecord->diagnosis }}</p>
                        <p><strong>Treatment Plan:</strong> {{ $appointment->medicalRecord->treatment_plan ?? 'N/A' }}</p>
                        @foreach($appointment->medicalRecord->prescriptions as $prescription)
                        <div class="border-top mt-2 pt-2">
                            <strong>Prescription:</strong> {{ $prescription->medication_name }}<br>
                            <small>{{ $prescription->dosage }} - {{ $prescription->frequency }} for {{ $prescription->duration }}</small>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <div class="alert {{ $appointment->payment ? 'alert-success' : 'alert-secondary' }} border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1"><i class="fas fa-credit-card me-2"></i>Payment Status</h6>
                                @if($appointment->payment)
                                <span class="badge bg-{{ $appointment->payment->status === 'completed' ? 'success' : 'warning' }} text-capitalize">{{ $appointment->payment->status }}</span>
                                <span class="ms-2"><strong>${{ number_format($appointment->payment->amount, 2) }}</strong></span>
                                @else
                                <span class="badge bg-secondary">Not Paid Yet</span>
                                @endif
                            </div>
                            @if($appointment->payment && $appointment->payment->status === 'completed')
                            <a href="{{ route('admin.payments.invoice', $appointment->payment) }}" class="btn btn-sm btn-success">
                                <i class="fas fa-download me-1"></i>Invoice
                            </a>
                            @endif
                        </div>
                    </div>

                    @if($appointment->status === 'pending')
                    <form action="{{ route('admin.appointments.update-status', $appointment) }}" method="POST" class="d-flex gap-2 mt-3">
                        @csrf
                        <input type="hidden" name="status" value="confirmed">
                        <button type="submit" class="btn btn-success flex-fill">
                            <i class="fas fa-check me-2"></i>Confirm Appointment
                        </button>
                    </form>
                    @endif

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('admin.patients.appointments.index', $patient) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </a>
                        <a href="{{ route('admin.patients.show', $patient) }}" class="btn btn-info">
                            <i class="fas fa-user me-2"></i>View Patient
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection