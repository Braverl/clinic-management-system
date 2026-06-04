@extends('layouts.app')

@section('title', 'Medical History')

@section('content')
<div class="container-fluid mt-4">
    <h2><i class="fas fa-file-medical me-2"></i>My Medical History</h2>
    
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-body">
            @php
                // Get medical records directly in the view if needed
                $patientId = auth()->user()->patient->id ?? null;
                $medicalRecordsList = $medicalRecords ?? \App\Models\MedicalRecord::where('patient_id', $patientId)->with(['doctor.user'])->get();
            @endphp
            
            @if(isset($medicalRecordsList) && $medicalRecordsList->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Doctor</th>
                                <th>Diagnosis</th>
                                <th>Treatment</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($medicalRecordsList as $record)
                            <tr>
                                <td>{{ $record->created_at ? $record->created_at->format('Y-m-d') : 'N/A' }}</td>
                                <td>Dr. {{ $record->doctor && $record->doctor->user ? $record->doctor->user->name : 'N/A' }}</td>
                                <td>{{ $record->diagnosis ?? 'N/A' }}</td>
                                <td>{{ Str::limit($record->treatment_plan ?? 'N/A', 50) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle me-2"></i>
                    No medical records found. Your medical history will appear here after your appointments.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection