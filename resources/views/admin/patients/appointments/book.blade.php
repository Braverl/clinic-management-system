@extends('layouts.app')

@section('title', 'Book Appointment for ' . $patient->user->name)

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-calendar-plus me-2"></i>Book Appointment for {{ $patient->user->name }}</h4>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-light border mb-4">
                        <div class="d-flex justify-content-between">
                            <div>
                                <label class="text-muted small">Patient</label>
                                <p class="fw-bold mb-1">{{ $patient->user->name }}</p>
                                <small class="text-muted">{{ $patient->user->email }}</small>
                            </div>
                            <div class="text-end">
                                <label class="text-muted small">Contact</label>
                                <p class="fw-bold mb-1">{{ $patient->user->phone ?? 'N/A' }}</p>
                                @if($patient->blood_group)<small class="text-muted">Blood: {{ $patient->blood_group }}</small>@endif
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('admin.patients.appointments.store', $patient) }}" method="POST" id="bookingForm">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-bold">Select Doctor</label>
                            <select name="doctor_id" id="doctor_id" class="form-control @error('doctor_id') is-invalid @enderror" required>
                                <option value="">-- Choose Doctor --</option>
                                @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" data-fee="{{ $doctor->consultation_fee }}" data-days="{{ $doctor->available_days }}">
                                    Dr. {{ $doctor->user->name }} - {{ $doctor->specialization }}
                                    (Fee: ${{ number_format($doctor->consultation_fee, 2) }})
                                </option>
                                @endforeach
                            </select>
                            @error('doctor_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Available Days</label>
                            <div id="availableDays" class="text-muted">
                                <small>Select a doctor to see their available days.</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Appointment Date</label>
                            <input type="date" name="appointment_date" id="appointment_date"
                                class="form-control @error('appointment_date') is-invalid @enderror"
                                min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                            @error('appointment_date')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Preferred Time</label>
                            <select name="appointment_time" id="appointment_time" class="form-control @error('appointment_time') is-invalid @enderror" required>
                                <option value="">Select doctor and date first</option>
                            </select>
                            <small class="text-muted">Slots are based on the doctor's working hours (30 min intervals)</small>
                            @error('appointment_time')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3" id="fee_display" style="display: none;">
                            <label class="form-label fw-bold">Consultation Fee</label>
                            <h4 class="text-success">$<span id="consultation_fee">0</span>.00</h4>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Symptoms / Reason for Visit</label>
                            <textarea name="symptoms" rows="4" class="form-control" placeholder="Describe the reason for the visit..."></textarea>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" name="is_emergency" class="form-check-input" id="emergency">
                            <label class="form-check-label text-danger" for="emergency">
                                <i class="fas fa-ambulance me-1"></i> Emergency appointment
                            </label>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-check-circle me-2"></i>Book Appointment
                            </button>
                            <a href="{{ route('admin.patients.appointments.index', $patient) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
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
    const patient = {{ $patient->id }};

    $('#doctor_id').on('change', function() {
        const selected = $(this).find('option:selected');
        const doctorId = $(this).val();
        const days = selected.data('days');

        if (doctorId) {
            $('#availableDays').html('<span class="badge bg-primary me-1">' + (days || 'No fixed days') + '</span>');
        } else {
            $('#availableDays').html('<small>Select a doctor to see their available days.</small>');
        }
    });

    $('#doctor_id, #appointment_date').on('change', function() {
        const doctorId = $('#doctor_id').val();
        const date = $('#appointment_date').val();

        if (doctorId && date) {
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
                        $('#fee_display').show();
                        $('#consultation_fee').text(response.fee);
                    } else {
                        slotsHtml = '<option value="">No slots available on this date</option>';
                        $('#fee_display').hide();
                    }
                    $('#appointment_time').html(slotsHtml);
                },
                error: function() {
                    $('#appointment_time').html('<option value="">Error loading slots</option>');
                }
            });
        }
    });
});
</script>
@endpush
@endsection