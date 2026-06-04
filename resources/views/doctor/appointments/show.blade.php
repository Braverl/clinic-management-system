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
                        <!-- Patient Image Section - ADDED HERE -->
                        <div class="col-md-6 text-center">
                            @if($appointment->patient->user->profile_image)
                                <img src="{{ Storage::url($appointment->patient->user->profile_image) }}" width="120" height="120" class="rounded-circle mb-3 object-fit-cover" style="object-fit: cover;">
                            @else
                                <div class="bg-info rounded-circle d-inline-flex align-items-center justify-content-center text-white mb-3" style="width: 120px; height: 120px;">
                                    <i class="fas fa-user fa-3x"></i>
                                </div>
                            @endif
                            <h5>{{ $appointment->patient->user->name }}</h5>
                            <p class="text-muted">{{ $appointment->patient->user->email }}</p>
                        </div>
                        <!-- End Patient Image Section -->
                        
                        <div class="col-md-6">
                            <p><strong>Patient Name:</strong> {{ $appointment->patient->user->name }}</p>
                            <p><strong>Patient Email:</strong> {{ $appointment->patient->user->email }}</p>
                            <p><strong>Patient Phone:</strong> {{ $appointment->patient->user->phone }}</p>
                            <p><strong>Blood Group:</strong> {{ $appointment->patient->blood_group ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Appointment Date:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('F d, Y') }}</p>
                            <p><strong>Appointment Time:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</p>
                            <p><strong>Status:</strong> 
                                <span class="badge bg-{{ $appointment->status == 'pending' ? 'warning' : ($appointment->status == 'confirmed' ? 'info' : 'success') }}">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </p>
                            <p><strong>Emergency:</strong> {{ $appointment->is_emergency ? 'Yes' : 'No' }}</p>
                        </div>
                        <div class="col-12">
                            <p><strong>Symptoms:</strong></p>
                            <p class="border p-3 rounded bg-light">{{ $appointment->symptoms ?? 'No symptoms reported' }}</p>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label class="fw-bold">Update Status</label>
                        <div class="btn-group">
                            @if($appointment->status == 'pending')
                            <button class="btn btn-success update-status" data-status="confirmed" data-url="{{ route('doctor.appointments.update-status', $appointment) }}">
                                <i class="fas fa-check me-1"></i> Confirm
                            </button>
                            <button class="btn btn-danger update-status" data-status="cancelled" data-url="{{ route('doctor.appointments.update-status', $appointment) }}">
                                <i class="fas fa-times me-1"></i> Cancel
                            </button>
                            @elseif($appointment->status == 'confirmed')
                            <button class="btn btn-primary update-status" data-status="completed" data-url="{{ route('doctor.appointments.update-status', $appointment) }}">
                                <i class="fas fa-flag-checkered me-1"></i> Mark Completed
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Medical Record Form -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-file-medical me-2"></i>Medical Record</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('doctor.appointments.add-medical-record', $appointment) }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <label>Diagnosis</label>
                            <textarea name="diagnosis" class="form-control" rows="2" required>{{ $appointment->medicalRecord->diagnosis ?? '' }}</textarea>
                        </div>
                        <div class="mb-2">
                            <label>Treatment Plan</label>
                            <textarea name="treatment_plan" class="form-control" rows="2">{{ $appointment->medicalRecord->treatment_plan ?? '' }}</textarea>
                        </div>
                        <div class="mb-2">
                            <label>Notes</label>
                            <textarea name="notes" class="form-control" rows="2">{{ $appointment->medicalRecord->notes ?? '' }}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <label>Weight (kg)</label>
                                <input type="number" step="0.1" name="weight" class="form-control" value="{{ $appointment->medicalRecord->weight ?? '' }}">
                            </div>
                            <div class="col-6">
                                <label>Height (cm)</label>
                                <input type="number" step="0.1" name="height" class="form-control" value="{{ $appointment->medicalRecord->height ?? '' }}">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-6">
                                <label>Blood Pressure</label>
                                <input type="text" name="blood_pressure" class="form-control" placeholder="120/80" value="{{ $appointment->medicalRecord->blood_pressure ?? '' }}">
                            </div>
                            <div class="col-6">
                                <label>Temperature</label>
                                <input type="text" name="temperature" class="form-control" placeholder="98.6" value="{{ $appointment->medicalRecord->temperature ?? '' }}">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mt-3">Save Medical Record</button>
                    </form>
                </div>
            </div>
            
            <!-- Prescriptions -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-prescription me-2"></i>Prescriptions</h5>
                </div>
                <div class="card-body">
                    @if($appointment->medicalRecord && $appointment->medicalRecord->prescriptions->count() > 0)
                        @foreach($appointment->medicalRecord->prescriptions as $prescription)
                        <div class="border-bottom mb-2 pb-2">
                            <strong>{{ $prescription->medication_name }}</strong><br>
                            <small>{{ $prescription->dosage }} - {{ $prescription->frequency }} for {{ $prescription->duration }}</small>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted">No prescriptions yet.</p>
                    @endif
                    
                    @if($appointment->medicalRecord)
                    <form action="{{ route('doctor.appointments.add-prescription', $appointment) }}" method="POST" class="mt-3">
                        @csrf
                        <input type="hidden" name="medical_record_id" value="{{ $appointment->medicalRecord->id }}">
                        <div class="mb-2">
                            <label>Medication</label>
                            <input type="text" name="medication_name" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <label>Dosage</label>
                                <input type="text" name="dosage" class="form-control" placeholder="10mg" required>
                            </div>
                            <div class="col-4">
                                <label>Frequency</label>
                                <input type="text" name="frequency" class="form-control" placeholder="Once daily" required>
                            </div>
                            <div class="col-4">
                                <label>Duration</label>
                                <input type="text" name="duration" class="form-control" placeholder="30 days" required>
                            </div>
                        </div>
                        <div class="mb-2 mt-2">
                            <label>Instructions</label>
                            <textarea name="instructions" class="form-control" rows="2" placeholder="Take with food"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Add Prescription</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('.update-status').on('click', function() {
        const status = $(this).data('status');
        const url = $(this).data('url');
        
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
    });
});
</script>
@endpush
@endsection