@extends('layouts.app')

@section('title', 'Patient Medical History - ' . $patient->user->name)

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-notes-medical me-2"></i>Patient Medical History</h2>
            <p class="text-muted mb-0">
                {{ $patient->user->name }} | {{ $patient->user->email ?? '' }}
                @if($patient->blood_group) | Blood: {{ $patient->blood_group }}@endif
            </p>
        </div>
        <a href="{{ route('doctor.appointments.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Appointments
        </a>
    </div>

    @if($patient->allergies && $patient->allergies !== 'None')
    <div class="alert alert-warning border-0 shadow-sm mb-4">
        <h6 class="mb-1"><i class="fas fa-exclamation-triangle me-2 text-warning"></i>Allergies</h6>
        <p class="mb-0">{{ $patient->allergies }}</p>
    </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-3">
                    <div class="border-end">
                        <h4 class="text-primary mb-1">{{ $medicalRecords->count() }}</h4>
                        <small class="text-muted">Total Visits</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border-end">
                        <h4 class="text-success mb-1">{{ $patient->appointments()->where('status','completed')->count() }}</h4>
                        <small class="text-muted">Completed</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border-end">
                        <h4 class="text-info mb-1">{{ $patient->emergency_contact ?? 'N/A' }}</h4>
                        <small class="text-muted">Emergency Contact</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <h4 class="text-warning mb-1">{{ $patient->insurance_number ?? 'N/A' }}</h4>
                    <small class="text-muted">Insurance #</small>
                </div>
            </div>
        </div>
    </div>

    @forelse($medicalRecords as $record)
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-0"><i class="fas fa-stethoscope me-2 text-primary"></i>{{ \Carbon\Carbon::parse($record->created_at)->format('l, F d, Y \a\t g:i A') }}</h6>
                <small class="text-muted">Appointment: {{ $record->appointment->appointment_number ?? 'N/A' }}</small>
            </div>
            <span class="badge bg-secondary">Diagnosis Recorded</span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="text-muted small">Diagnosis</label>
                    <p class="fw-bold mb-0">{{ $record->diagnosis }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="text-muted small">Symptoms</label>
                    <p class="mb-0">{{ $record->symptoms }}</p>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="text-muted small">Treatment Plan</label>
                    <p class="mb-0">{{ $record->treatment_plan ?? 'Not specified' }}</p>
                </div>
                @if($record->notes)
                <div class="col-md-12 mb-3">
                    <label class="text-muted small">Doctor's Notes</label>
                    <p class="mb-0 fst-italic">{{ $record->notes }}</p>
                </div>
                @endif
                <div class="col-md-12">
                    <div class="row text-center bg-light rounded p-2">
                        <div class="col">
                            <small class="text-muted d-block">Weight</small>
                            <strong>{{ $record->weight ? $record->weight . ' kg' : 'N/A' }}</strong>
                        </div>
                        <div class="col">
                            <small class="text-muted d-block">Height</small>
                            <strong>{{ $record->height ? $record->height . ' cm' : 'N/A' }}</strong>
                        </div>
                        <div class="col">
                            <small class="text-muted d-block">BMI</small>
                            <strong>{{ $record->bmi ? $record->bmi : 'N/A' }}</strong>
                        </div>
                        <div class="col">
                            <small class="text-muted d-block">Blood Pressure</small>
                            <strong>{{ $record->blood_pressure ?? 'N/A' }}</strong>
                        </div>
                        <div class="col">
                            <small class="text-muted d-block">Temperature</small>
                            <strong>{{ $record->temperature ?? 'N/A' }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            @if($record->prescriptions && $record->prescriptions->count())
            <hr>
            <h6 class="mb-2"><i class="fas fa-pills me-2"></i>Prescriptions</h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Medication</th>
                            <th>Dosage</th>
                            <th>Frequency</th>
                            <th>Duration</th>
                            <th>Instructions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($record->prescriptions as $rx)
                        <tr>
                            <td class="fw-bold">{{ $rx->medication_name }}</td>
                            <td>{{ $rx->dosage }}</td>
                            <td>{{ $rx->frequency }}</td>
                            <td>{{ $rx->duration }}</td>
                            <td>{{ $rx->instructions ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
    @empty
    <div class="text-center py-5">
        <i class="fas fa-notes-medical fa-4x text-muted mb-3"></i>
        <h5>No Medical Records Found</h5>
        <p class="text-muted">No medical history recorded for this patient yet.</p>
    </div>
    @endforelse
</div>
@endsection