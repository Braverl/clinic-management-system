@extends('layouts.app')

@section('title', 'Book Appointment')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-calendar-plus me-2"></i>Book New Appointment</h4>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('patient.appointments.store') }}" method="POST" id="bookingForm">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Select Doctor</label>
                            <select name="doctor_id" id="doctor_id" class="form-control" required>
                                <option value="">-- Choose Doctor --</option>
                                @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" data-image="{{ $doctor->user->profile_image ? Storage::url($doctor->user->profile_image) : '' }}" data-fee="{{ $doctor->consultation_fee }}" data-days="{{ $doctor->available_days }}">
                                    Dr. {{ $doctor->user->name }} - {{ $doctor->specialization }} 
                                    (Fee: ${{ number_format($doctor->consultation_fee, 2) }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Doctor Preview Card -->
                        <div id="doctorPreview" class="mb-3" style="display: none;">
                            <div class="card border-0 shadow-sm bg-light">
                                <div class="card-body d-flex align-items-center">
                                    <div id="doctorPreviewImage" class="me-3"></div>
                                    <div>
                                        <h5 id="doctorPreviewName" class="mb-1"></h5>
                                        <p id="doctorPreviewSpecialization" class="mb-0 text-muted"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Available Days</label>
                            <div id="availableDays" class="text-muted">
                                <small>Select a doctor to see their available days.</small>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Appointment Date</label>
                            <input type="date" name="appointment_date" id="appointment_date" class="form-control" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Preferred Time</label>
                            <select name="appointment_time" id="appointment_time" class="form-control" required>
                                <option value="">Select doctor and date first</option>
                            </select>
                            <small class="text-muted">Available slots are based on the doctor's working hours (30 min intervals)</small>
                        </div>
                        
                        <div class="mb-3" id="fee_display" style="display: none;">
                            <label class="form-label fw-bold">Consultation Fee</label>
                            <h4 class="text-success">$<span id="consultation_fee">0</span>.00</h4>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Symptoms / Reason for Visit</label>
                            <textarea name="symptoms" rows="4" class="form-control" placeholder="Describe your symptoms or reason for consultation..."></textarea>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="is_emergency" class="form-check-input" id="emergency" value="1">
                            <label class="form-check-label text-danger" for="emergency">
                                <i class="fas fa-ambulance me-1"></i> This is an emergency
                            </label>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-check-circle me-2"></i>Book Appointment
                            </button>
                            <a href="{{ route('patient.appointments.index') }}" class="btn btn-secondary">
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
    // Doctor selection preview
    $('#doctor_id').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const doctorId = $(this).val();
        const doctorName = selectedOption.text().split(' - ')[0];
        const doctorSpecialization = selectedOption.text().split(' - ')[1]?.split(' (Fee')[0] || '';
        const doctorImage = selectedOption.data('image');
        const doctorFee = selectedOption.data('fee');
        const doctorDays = selectedOption.data('days');
        
        if (doctorId) {
            $('#doctorPreviewName').text(doctorName);
            $('#doctorPreviewSpecialization').text(doctorSpecialization);
            
            if (doctorDays) {
                $('#availableDays').html(doctorDays.split(',').map(day => `<span class="badge bg-primary me-1">${day}</span>`).join(''));
            } else {
                $('#availableDays').html('<small class="text-muted">No fixed days set</small>');
            }
            
            if (doctorImage) {
                $('#doctorPreviewImage').html(`<img src="${doctorImage}" width="60" height="60" class="rounded-circle object-fit-cover">`);
            } else {
                $('#doctorPreviewImage').html(`<div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center text-white" style="width: 60px; height: 60px;"><i class="fas fa-user-md fa-2x"></i></div>`);
            }
            
            $('#doctorPreview').show();
            $('#consultation_fee').text(doctorFee);
            $('#fee_display').show();
        } else {
            $('#doctorPreview').hide();
            $('#fee_display').hide();
        }
    });
    
    // Schedule loading
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