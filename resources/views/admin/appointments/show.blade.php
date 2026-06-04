@extends('layouts.app')

@section('title', 'Appointment Details')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h4 class="mb-0">Appointment Details: {{ $appointment->appointment_number }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Patient Name:</strong> {{ $appointment->patient->user->name ?? 'N/A' }}</p>
                            <p><strong>Patient Email:</strong> {{ $appointment->patient->user->email ?? 'N/A' }}</p>
                            <p><strong>Patient Phone:</strong> {{ $appointment->patient->user->phone ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Doctor Name:</strong> Dr. {{ $appointment->doctor->user->name ?? 'N/A' }}</p>
                            <p><strong>Specialization:</strong> {{ $appointment->doctor->specialization ?? 'N/A' }}</p>
                            <p><strong>Consultation Fee:</strong> ${{ number_format($appointment->doctor->consultation_fee ?? 0, 2) }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Appointment Date:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('F d, Y') }}</p>
                            <p><strong>Appointment Time:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Status:</strong> 
                                <span class="badge bg-{{ $appointment->status == 'pending' ? 'warning' : ($appointment->status == 'confirmed' ? 'info' : ($appointment->status == 'completed' ? 'success' : 'danger')) }}">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </p>
                            <p><strong>Emergency:</strong> {{ $appointment->is_emergency ? 'Yes' : 'No' }}</p>
                        </div>
                        <div class="col-12">
                            <p><strong>Symptoms:</strong></p>
                            <p class="border p-3 rounded bg-light">{{ $appointment->symptoms ?? 'No symptoms reported' }}</p>
                        </div>
                        @if($appointment->cancellation_reason)
                        <div class="col-12">
                            <p><strong>Cancellation Reason:</strong></p>
                            <p class="border p-3 rounded bg-danger text-white">{{ $appointment->cancellation_reason }}</p>
                        </div>
                        @endif
                    </div>
                    
                    <div class="mt-4">
                        <label class="fw-bold">Update Status</label>
                        <div class="btn-group">
                            @if($appointment->status == 'pending')
                            <button class="btn btn-success update-status" data-status="confirmed" data-url="{{ route('admin.appointments.update-status', $appointment) }}">
                                <i class="fas fa-check me-1"></i> Confirm
                            </button>
                            <button class="btn btn-danger update-status" data-status="cancelled" data-url="{{ route('admin.appointments.update-status', $appointment) }}">
                                <i class="fas fa-times me-1"></i> Cancel
                            </button>
                            <button class="btn btn-warning update-status" data-status="rejected" data-url="{{ route('admin.appointments.update-status', $appointment) }}">
                                <i class="fas fa-ban me-1"></i> Reject
                            </button>
                            @elseif($appointment->status == 'confirmed')
                            <button class="btn btn-primary update-status" data-status="completed" data-url="{{ route('admin.appointments.update-status', $appointment) }}">
                                <i class="fas fa-flag-checkered me-1"></i> Mark Completed
                            </button>
                            <button class="btn btn-danger update-status" data-status="cancelled" data-url="{{ route('admin.appointments.update-status', $appointment) }}">
                                <i class="fas fa-times me-1"></i> Cancel
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-file-medical me-2"></i>Medical Record</h5>
                </div>
                <div class="card-body">
                    @if($appointment->medicalRecord)
                        <p><strong>Diagnosis:</strong> {{ $appointment->medicalRecord->diagnosis }}</p>
                        <p><strong>Treatment Plan:</strong> {{ $appointment->medicalRecord->treatment_plan ?? 'N/A' }}</p>
                        <p><strong>Notes:</strong> {{ $appointment->medicalRecord->notes ?? 'N/A' }}</p>
                        <hr>
                        <p><strong>Vitals:</strong></p>
                        <ul>
                            <li>Weight: {{ $appointment->medicalRecord->weight ?? 'N/A' }} kg</li>
                            <li>Height: {{ $appointment->medicalRecord->height ?? 'N/A' }} cm</li>
                            <li>Blood Pressure: {{ $appointment->medicalRecord->blood_pressure ?? 'N/A' }}</li>
                            <li>Temperature: {{ $appointment->medicalRecord->temperature ?? 'N/A' }} °F</li>
                        </ul>
                    @else
                        <p class="text-muted">No medical record yet.</p>
                    @endif
                </div>
            </div>
            
            @if($appointment->payment)
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i>Payment</h5>
                </div>
                <div class="card-body">
                    <p><strong>Amount:</strong> ${{ number_format($appointment->payment->amount, 2) }}</p>
                    <p><strong>Method:</strong> {{ ucfirst($appointment->payment->payment_method) }}</p>
                    <p><strong>Status:</strong> 
                        <span class="badge bg-{{ $appointment->payment->status == 'completed' ? 'success' : 'warning' }}">
                            {{ ucfirst($appointment->payment->status) }}
                        </span>
                    </p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('.update-status').on('click', function() {
        const status = $(this).data('status');
        const url = $(this).data('url');
        
        if (status === 'cancelled' || status === 'rejected') {
            Swal.fire({
                title: `Provide Reason for ${status}`,
                input: 'textarea',
                inputPlaceholder: 'Enter cancellation/rejection reason...',
                showCancelButton: true,
                confirmButtonText: 'Submit',
                preConfirm: (reason) => {
                    if (!reason) {
                        Swal.showValidationMessage('Reason is required');
                        return false;
                    }
                    return reason;
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    $('<form>', {
                        method: 'POST',
                        action: url
                    }).append($('<input>', {
                        name: 'status',
                        value: status,
                        type: 'hidden'
                    })).append($('<input>', {
                        name: 'cancellation_reason',
                        value: result.value,
                        type: 'hidden'
                    })).append($('<input>', {
                        name: '_token',
                        value: '{{ csrf_token() }}',
                        type: 'hidden'
                    })).appendTo('body').submit();
                }
            });
        } else {
            Swal.fire({
                title: `Confirm ${status}`,
                text: `Are you sure you want to mark this appointment as ${status}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: `Yes, ${status} it!`
            }).then((result) => {
                if (result.isConfirmed) {
                    $('<form>', {
                        method: 'POST',
                        action: url
                    }).append($('<input>', {
                        name: 'status',
                        value: status,
                        type: 'hidden'
                    })).append($('<input>', {
                        name: '_token',
                        value: '{{ csrf_token() }}',
                        type: 'hidden'
                    })).appendTo('body').submit();
                }
            });
        }
    });
});
</script>
@endpush
@endsection