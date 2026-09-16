@extends('layouts.app')

@section('title', 'Reschedule Appointment')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Reschedule Appointment</h4>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-light border mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="text-muted small">Current Doctor</label>
                                <p class="fw-bold mb-0">Dr. {{ $appointment->doctor->user->name }}</p>
                                <small class="text-muted">{{ $appointment->doctor->specialization }}</small>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Current Schedule</label>
                                <p class="fw-bold mb-0">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('l, F d, Y') }} at {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</p>
                                <span class="badge bg-info">{{ $appointment->appointment_number }}</span>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('patient.appointments.update-reschedule', $appointment) }}" method="POST" id="rescheduleForm">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-bold">New Appointment Date</label>
                            <input type="date" name="appointment_date" id="appointment_date"
                                class="form-control @error('appointment_date') is-invalid @enderror"
                                min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                            @error('appointment_date')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">New Preferred Time</label>
                            <select name="appointment_time" id="appointment_time" class="form-control @error('appointment_time') is-invalid @enderror" required>
                                <option value="">Select a date first</option>
                            </select>
                            <small class="text-muted">Available slots based on doctor's schedule (30 min intervals)</small>
                            @error('appointment_time')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex align-items-center gap-3 mt-4">
                            <a href="{{ route('patient.appointments.show', $appointment) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check-circle me-2"></i>Confirm Reschedule
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    let doctorId = {{ $appointment->doctor_id }};

    $('#appointment_date').on('change', function() {
        const date = $(this).val();

        if (date) {
            $('#appointment_time').html('<option>Loading available slots...</option>');

            $.ajax({
                url: `/get-doctor-schedule/${doctorId}/${date}`,
                method: 'GET',
                success: function(response) {
                    let slotsHtml = '<option value="">Select Time</option>';
                    if (response.available_slots && response.available_slots.length > 0) {
                        response.available_slots.forEach(slot => {
                            slotsHtml += `<option value="${slot.time}">${slot.display}</option>`;
                        });
                    } else {
                        slotsHtml = '<option value="">No slots available on this date</option>';
                    }
                    $('#appointment_time').html(slotsHtml);
                },
                error: function() {
                    $('#appointment_time').html('<option value="">Error loading slots. Please try again.</option>');
                }
            });
        }
    });
});
</script>
@endpush
@endsection