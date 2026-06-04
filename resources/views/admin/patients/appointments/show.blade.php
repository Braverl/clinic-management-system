@extends('layouts.app')

@section('title', 'Appointment Details')

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
                    <!-- Status Bar -->
                    <div class="mb-4">
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-{{ $appointment->status == 'pending' ? 'warning' : ($appointment->status == 'confirmed' ? 'info' : ($appointment->status == 'completed' ? 'success' : 'danger')) }}" 
                                 style="width: {{ $appointment->status == 'pending' ? '25' : ($appointment->status == 'confirmed' ? '50' : ($appointment->status == 'completed' ? '100' : '0')) }}%">
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
                            <label class="text-muted">Doctor Name</label>
                            <p class="fw-bold">Dr. {{ $appointment->doctor->user->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Specialization</label>
                            <p class="fw-bold">{{ $appointment->doctor->specialization }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Appointment Date</label>
                            <p class="fw-bold">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('l, F d, Y') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Appointment Time</label>
                            <p class="fw-bold">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</p>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="text-muted">Symptoms</label>
                            <p class="fw-bold">{{ $appointment->symptoms ?? 'Not specified' }}</p>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="text-muted">Status</label>
                            <p>
                                <span class="badge bg-{{ $appointment->status == 'pending' ? 'warning' : ($appointment->status == 'confirmed' ? 'info' : ($appointment->status == 'completed' ? 'success' : 'danger')) }} p-2">
                                    {{ ucfirst($appointment->status) }}
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
                    
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('patient.appointments.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </a>
                        <div>
                            @if(in_array($appointment->status, ['pending', 'confirmed']))
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                <i class="fas fa-times me-2"></i>Cancel Appointment
                            </button>
                            @endif
                            @if(!$appointment->payment && $appointment->status == 'confirmed')
                            <a href="{{ route('payments.create', $appointment) }}" class="btn btn-success">
                                <i class="fas fa-credit-card me-2"></i>Pay Now
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('patient.appointments.cancel', $appointment) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Cancel Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to cancel this appointment?</p>
                    <div class="mb-3">
                        <label>Reason for cancellation (optional)</label>
                        <textarea name="cancellation_reason" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Confirm Cancellation</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection