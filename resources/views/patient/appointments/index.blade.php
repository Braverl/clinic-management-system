@extends('layouts.app')

@section('title', 'My Appointments')

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-calendar-check me-2"></i>My Appointments</h2>
        <a href="{{ route('patient.appointments.book') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Book New Appointment
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if($appointments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <!-- Add doctor image column in the table -->
<thead>
    <tr>
        <th>Appointment #</th>
        <th>Doctor Photo</th>
        <th>Doctor</th>
        <th>Specialization</th>
        <th>Date</th>
        <th>Time</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
</thead>
<tbody>
    @foreach($appointments as $apt)
    <tr>
        <td>{{ $apt->appointment_number }}</td>
        <td class="text-center">
            @if($apt->doctor->user->profile_image)
                <img src="{{ Storage::url($apt->doctor->user->profile_image) }}" width="45" height="45" class="rounded-circle object-fit-cover">
            @else
                <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center text-white" style="width: 45px; height: 45px;">
                    <i class="fas fa-user-md"></i>
                </div>
            @endif
        </td>
        <td>Dr. {{ $apt->doctor->user->name ?? 'N/A' }}</td>
        <td>{{ $apt->doctor->specialization ?? 'N/A' }}</td>
        <td>{{ \Carbon\Carbon::parse($apt->appointment_date)->format('M d, Y') }}</td>
        <td>{{ \Carbon\Carbon::parse($apt->appointment_time)->format('g:i A') }}</td>
        <td>
            <span class="badge bg-{{ $apt->status == 'pending' ? 'warning' : ($apt->status == 'confirmed' ? 'info' : ($apt->status == 'completed' ? 'success' : 'danger')) }}">
                {{ ucfirst($apt->status) }}
            </span>
        </td>
        <td>
            <a href="{{ route('patient.appointments.show', $apt) }}" class="btn btn-sm btn-info">
                <i class="fas fa-eye"></i> View
            </a>
            @if(in_array($apt->status, ['pending', 'confirmed']))
            <button type="button" class="btn btn-sm btn-danger cancel-appointment" data-id="{{ $apt->id }}" data-number="{{ $apt->appointment_number }}">
                <i class="fas fa-times"></i> Cancel
            </button>
            @endif
        </td>
    </tr>
    @endforeach
</tbody>
                    </table>
                </div>
                {{ $appointments->links() }}
            @else
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                    <h5>No Appointments Found</h5>
                    <p>You haven't booked any appointments yet.</p>
                    <a href="{{ route('patient.appointments.book') }}" class="btn btn-primary">Book Your First Appointment</a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('.cancel-appointment').on('click', function() {
            const appointmentId = $(this).data('id');
            const appointmentNumber = $(this).data('number');
            
            Swal.fire({
                title: 'Cancel Appointment',
                text: `Are you sure you want to cancel appointment ${appointmentNumber}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, cancel it!',
                input: 'textarea',
                inputPlaceholder: 'Reason for cancellation (optional)'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/patient/appointments/${appointmentId}/cancel`,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            cancellation_reason: result.value
                        },
                        success: function(response) {
                            Swal.fire('Cancelled!', 'Appointment has been cancelled.', 'success');
                            location.reload();
                        },
                        error: function() {
                            Swal.fire('Error!', 'Failed to cancel appointment.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
@endsection