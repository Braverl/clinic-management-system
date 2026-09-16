@extends('layouts.app')

@section('title', 'Medical Record Details')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-file-medical me-2"></i>Medical Record #{{ $medicalRecord->id }}</h4>
                        <div>
                            <a href="{{ route('doctor.medical-records.edit', $medicalRecord) }}" class="btn btn-sm btn-light">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-light border mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">Patient</small>
                                <h6 class="mb-1">{{ $medicalRecord->patient->user->name }}</h6>
                                <small class="text-muted">{{ $medicalRecord->patient->user->email }}</small>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <small class="text-muted">Date</small>
                                <h6 class="mb-1">{{ $medicalRecord->created_at->format('l, F d, Y \a\t g:i A') }}</h6>
                                <small class="text-muted">Appointment: {{ $medicalRecord->appointment->appointment_number ?? 'N/A' }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Diagnosis</label>
                            <p class="fw-bold">{{ $medicalRecord->diagnosis }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Symptoms</label>
                            <p class="fw-bold">{{ $medicalRecord->symptoms }}</p>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="text-muted">Treatment Plan</label>
                            <p class="fw-bold">{{ $medicalRecord->treatment_plan ?? 'Not specified' }}</p>
                        </div>
                        @if($medicalRecord->notes)
                        <div class="col-12 mb-3">
                            <label class="text-muted">Notes</label>
                            <p class="fst-italic">{{ $medicalRecord->notes }}</p>
                        </div>
                        @endif
                    </div>

                    <hr>
                    <div class="row text-center">
                        <div class="col">
                            <label class="text-muted small d-block">Weight</label>
                            <h6>{{ $medicalRecord->weight ? $medicalRecord->weight . ' kg' : 'N/A' }}</h6>
                        </div>
                        <div class="col">
                            <label class="text-muted small d-block">Height</label>
                            <h6>{{ $medicalRecord->height ? $medicalRecord->height . ' cm' : 'N/A' }}</h6>
                        </div>
                        <div class="col">
                            <label class="text-muted small d-block">BMI</label>
                            <h6>{{ $medicalRecord->bmi ?? 'N/A' }}</h6>
                        </div>
                        <div class="col">
                            <label class="text-muted small d-block">Blood Pressure</label>
                            <h6>{{ $medicalRecord->blood_pressure ?? 'N/A' }}</h6>
                        </div>
                        <div class="col">
                            <label class="text-muted small d-block">Temperature</label>
                            <h6>{{ $medicalRecord->temperature ?? 'N/A' }}</h6>
                        </div>
                    </div>

                    @if($medicalRecord->prescriptions->count())
                    <hr>
                    <h6 class="mb-3"><i class="fas fa-pills me-2"></i>Prescriptions</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>RX #</th>
                                    <th>Medication</th>
                                    <th>Dosage</th>
                                    <th>Frequency</th>
                                    <th>Duration</th>
                                    <th>Instructions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($medicalRecord->prescriptions as $rx)
                                <tr>
                                    <td>{{ $rx->prescription_number }}</td>
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

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('doctor.medical-records.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </a>
                        <a href="{{ route('doctor.medical-records.edit', $medicalRecord) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Edit Record
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection