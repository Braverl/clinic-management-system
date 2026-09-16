@extends('layouts.app')

@section('title', 'Edit Medical Record')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Medical Record #{{ $medicalRecord->id }}</h4>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-light border mb-4">
                        <strong>{{ $medicalRecord->patient->user->name }}</strong>
                        <span class="text-muted">| {{ $medicalRecord->created_at->format('M d, Y') }}</span>
                    </div>

                    <form action="{{ route('doctor.medical-records.update', $medicalRecord) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Diagnosis *</label>
                                <textarea name="diagnosis" rows="2" class="form-control @error('diagnosis') is-invalid @enderror" required>{{ old('diagnosis', $medicalRecord->diagnosis) }}</textarea>
                                @error('diagnosis') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Treatment Plan</label>
                                <textarea name="treatment_plan" rows="3" class="form-control">{{ old('treatment_plan', $medicalRecord->treatment_plan) }}</textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Notes</label>
                                <textarea name="notes" rows="3" class="form-control">{{ old('notes', $medicalRecord->notes) }}</textarea>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Weight (kg)</label>
                                <input type="number" step="0.01" name="weight" class="form-control" value="{{ old('weight', $medicalRecord->weight) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Height (cm)</label>
                                <input type="number" step="0.01" name="height" class="form-control" value="{{ old('height', $medicalRecord->height) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Blood Pressure</label>
                                <input type="text" name="blood_pressure" class="form-control" placeholder="120/80" value="{{ old('blood_pressure', $medicalRecord->blood_pressure) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Temperature</label>
                                <input type="text" name="temperature" class="form-control" placeholder="98.6" value="{{ old('temperature', $medicalRecord->temperature) }}">
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Changes
                            </button>
                            <a href="{{ route('doctor.medical-records.show', $medicalRecord) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection