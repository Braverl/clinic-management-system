@extends('layouts.app')

@section('title', 'Manage Appointments')

@section('content')
<div class="container-fluid mt-4">
    <h2><i class="fas fa-calendar-check me-2"></i>Manage Appointments</h2>
    
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card border-0 shadow-sm bg-warning text-white">
                <div class="card-body">
                    <h6>Pending</h6>
                    <h3>{{ $stats['pending'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm bg-info text-white">
                <div class="card-body">
                    <h6>Confirmed</h6>
                    <h3>{{ $stats['confirmed'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body">
                    <h6>Completed</h6>
                    <h3>{{ $stats['completed'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm bg-danger text-white">
                <div class="card-body">
                    <h6>Cancelled</h6>
                    <h3>{{ $stats['cancelled'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body">
                    <h6>Today</h6>
                    <h3>{{ $stats['today'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Patient</th>
                            <th>Doctor</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($appointments as $apt)
                        <tr>
                            <td>{{ $apt->appointment_number }}</td>
                            <td>{{ $apt->patient->user->name ?? 'N/A' }}</td>
                            <td>Dr. {{ $apt->doctor->user->name ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($apt->appointment_date)->format('M d, Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($apt->appointment_time)->format('g:i A') }}</td>
                            <td>
                                <span class="badge bg-{{ $apt->status == 'pending' ? 'warning' : ($apt->status == 'confirmed' ? 'info' : ($apt->status == 'completed' ? 'success' : 'danger')) }}">
                                    {{ ucfirst($apt->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.appointments.show', $apt) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    @if($apt->status == 'pending')
                                    <button class="btn btn-sm btn-success update-status" data-status="confirmed" data-url="{{ route('admin.appointments.update-status', $apt) }}">
                                        <i class="fas fa-check"></i> Confirm
                                    </button>
                                    <button class="btn btn-sm btn-danger update-status" data-status="cancelled" data-url="{{ route('admin.appointments.update-status', $apt) }}">
                                        <i class="fas fa-times"></i> Cancel
                                    </button>
                                    @elseif($apt->status == 'confirmed')
                                    <button class="btn btn-sm btn-primary update-status" data-status="completed" data-url="{{ route('admin.appointments.update-status', $apt) }}">
                                        <i class="fas fa-flag-checkered"></i> Complete
                                    </button>
                                    <button class="btn btn-sm btn-danger update-status" data-status="cancelled" data-url="{{ route('admin.appointments.update-status', $apt) }}">
                                        <i class="fas fa-times"></i> Cancel
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7">
                                @include('partials.empty-state', [
                                    'icon' => 'calendar-times',
                                    'title' => 'No Appointments Found',
                                    'message' => 'There are no appointments matching your criteria. Try adjusting the filters or check back later.',
                                ])
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $appointments->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('.update-status').on('click', function() {
        const status = $(this).data('status');
        const url = $(this).data('url');
        
        if (status === 'cancelled') {
            Swal.fire({
                title: 'Cancel Appointment',
                input: 'textarea',
                inputPlaceholder: 'Enter cancellation reason...',
                showCancelButton: true,
                confirmButtonText: 'Submit',
                preConfirm: (reason) => {
                    if (!reason) {
                        Swal.showValidationMessage('Reason is required');
                        return false;
                    }
                    return reason;
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    $('<form>', {
                        method: 'POST',
                        action: url
                    }).append($('<input>', {
                        name: 'status',
                        value: status,
                        type: 'hidden'
                    })).append($('<input>', {
                        name: 'cancellation_reason',
                        value: result.value,
                        type: 'hidden'
                    })).append($('<input>', {
                        name: '_token',
                        value: '{{ csrf_token() }}',
                        type: 'hidden'
                    })).appendTo('body').submit();
                }
            });
        } else {
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
        }
    });
});
</script>
@endpush
@endsection