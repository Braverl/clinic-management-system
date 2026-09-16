@extends('layouts.app')

@section('title', 'My Medical Records')

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-notes-medical me-2"></i>Medical Records</h2>
        <a href="{{ route('doctor.appointments.index') }}" class="btn btn-primary">
            <i class="fas fa-calendar-plus me-2"></i>View Appointments
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if($medicalRecords->count())
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Record #</th>
                            <th>Patient</th>
                            <th>Diagnosis</th>
                            <th>Appointment</th>
                            <th>Prescriptions</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($medicalRecords as $record)
                        <tr>
                            <td><strong>#{{ $record->id }}</strong></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-2">
                                        @if($record->patient->user->profile_image)
                                        <img src="{{ Storage::url($record->patient->user->profile_image) }}" width="36" height="36" class="rounded-circle object-fit-cover">
                                        @else
                                        <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center text-white" style="width: 36px; height: 36px;">
                                            <i class="fas fa-user-sm"></i>
                                        </div>
                                        @endif
                                    </div>
                                    <div>
                                        <strong>{{ $record->patient->user->name }}</strong>
                                        <small class="d-block text-muted">{{ $record->patient->blood_group ?? '' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="fw-bold">{{ \Illuminate\Support\Str::limit($record->diagnosis, 40) }}</td>
                            <td>{{ $record->appointment->appointment_number ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-info">{{ $record->prescriptions->count() }} rx</span>
                            </td>
                            <td>{{ $record->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('doctor.medical-records.show', $record) }}" class="btn btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('doctor.medical-records.edit', $record) }}" class="btn btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $medicalRecords->links() }}
            @else
            <div class="text-center py-5">
                <i class="fas fa-notes-medical fa-4x text-muted mb-3"></i>
                <h5>No Medical Records Yet</h5>
                <p class="text-muted">Medical records are created when you complete a patient appointment.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection