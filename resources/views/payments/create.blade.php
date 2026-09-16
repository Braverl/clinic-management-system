@extends('layouts.app')

@section('title', 'Pay for Appointment')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-credit-card me-2"></i>Complete Payment</h4>
                </div>
                <div class="card-body p-4">
                    <div class="bg-light rounded p-3 mb-4">
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted">Appointment</small>
                                <p class="fw-bold mb-1">{{ $appointment->appointment_number }}</p>
                            </div>
                            <div class="col-6 text-end">
                                <small class="text-muted">Doctor</small>
                                <p class="fw-bold mb-1">Dr. {{ $appointment->doctor->user->name }}</p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Date</small>
                                <p class="mb-0">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}</p>
                            </div>
                            <div class="col-6 text-end">
                                <small class="text-muted">Time</small>
                                <p class="mb-0">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mb-4">
                        <small class="text-muted">Consultation Fee</small>
                        <h2 class="text-success mb-0">${{ number_format($amount, 2) }}</h2>
                    </div>

                    <form action="{{ route('patient.payments.store') }}" method="POST" id="paymentForm">
                        @csrf
                        <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">

                        <div class="mb-4">
                            <label class="form-label fw-bold">Payment Method</label>
                            <div class="row g-2">
                                <div class="col-4">
                                    <input type="radio" class="btn-check" name="payment_method" value="card" id="methodCard" required>
                                    <label class="btn btn-outline-primary w-100 py-3" for="methodCard">
                                        <i class="fas fa-credit-card d-block mb-1"></i>
                                        <small>Card</small>
                                    </label>
                                </div>
                                <div class="col-4">
                                    <input type="radio" class="btn-check" name="payment_method" value="online" id="methodOnline">
                                    <label class="btn btn-outline-primary w-100 py-3" for="methodOnline">
                                        <i class="fas fa-globe d-block mb-1"></i>
                                        <small>Online</small>
                                    </label>
                                </div>
                                <div class="col-4">
                                    <input type="radio" class="btn-check" name="payment_method" value="insurance" id="methodInsurance">
                                    <label class="btn btn-outline-primary w-100 py-3" for="methodInsurance">
                                        <i class="fas fa-shield-alt d-block mb-1"></i>
                                        <small>Insurance</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg" id="payBtn">
                                <i class="fas fa-lock me-2"></i>Pay ${{ number_format($amount, 2) }}
                            </button>
                            <a href="{{ route('patient.appointments.show', $appointment) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Cancel
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
$('#paymentForm').on('submit', function() {
    $('#payBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Processing...');
});
</script>
@endpush
@endsection