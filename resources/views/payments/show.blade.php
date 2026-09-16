@extends('layouts.app')

@section('title', 'Payment Details')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-lg">
                <div class="card-header {{ $payment->status === 'completed' ? 'bg-success' : ($payment->status === 'refunded' ? 'bg-warning' : 'bg-info') }} text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-receipt me-2"></i>Payment Details</h4>
                        <span class="badge bg-light text-dark">{{ $payment->payment_number }}</span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-4 text-center">
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 80px; height: 80px;">
                                <h2 class="text-{{ $payment->status === 'completed' ? 'success' : 'warning' }} mb-0">
                                    ${{ number_format($payment->amount, 2) }}
                                </h2>
                            </div>
                            <div>
                                <span class="badge bg-{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'refunded' ? 'warning' : 'info') }} p-2 text-capitalize">
                                    {{ $payment->status }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="text-muted small">Payment Method</label>
                                    <p class="fw-bold text-capitalize">
                                        <i class="fas fa-{{ $payment->payment_method === 'card' ? 'credit-card' : ($payment->payment_method === 'insurance' ? 'shield-alt' : 'globe') }} me-1"></i>
                                        {{ $payment->payment_method }}
                                    </p>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="text-muted small">Transaction ID</label>
                                    <p class="fw-bold">{{ $payment->transaction_id ?? 'N/A' }}</p>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="text-muted small">Payment Date</label>
                                    <p class="fw-bold">{{ $payment->created_at->format('M d, Y \a\t g:i A') }}</p>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="text-muted small">Patient</label>
                                    <p class="fw-bold">{{ $payment->patient->user->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h6 class="mb-3"><i class="fas fa-calendar-alt me-2"></i>Appointment Information</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Appointment #</label>
                            <p class="fw-bold">
                                <a href="{{ auth()->user()->isAdmin() ? route('admin.appointments.show', $payment->appointment_id) : route('patient.appointments.show', $payment->appointment_id) }}">
                                    {{ $payment->appointment->appointment_number ?? 'N/A' }}
                                </a>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Doctor</label>
                            <p class="fw-bold">Dr. {{ $payment->appointment->doctor->user->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Appointment Date</label>
                            <p class="fw-bold">{{ $payment->appointment ? \Carbon\Carbon::parse($payment->appointment->appointment_date)->format('l, F d, Y') : 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Status</label>
                            <p class="fw-bold">
                                <span class="badge bg-{{ $payment->appointment->status === 'completed' ? 'success' : 'info' }} text-capitalize">
                                    {{ $payment->appointment->status ?? 'N/A' }}
                                </span>
                            </p>
                        </div>
                    </div>

                    @if($payment->invoice)
                    <hr>
                    <h6 class="mb-3"><i class="fas fa-file-invoice me-2"></i>Invoice Information</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Invoice #</label>
                            <p class="fw-bold">{{ $payment->invoice->invoice_number }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Total</label>
                            <p class="fw-bold text-success">${{ number_format($payment->invoice->total, 2) }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Due Date</label>
                            <p class="fw-bold">{{ $payment->invoice->due_date->format('M d, Y') }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ auth()->user()->isAdmin() ? route('admin.payments.index') : route('patient.payments.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Payments
                        </a>
                        <div class="d-flex gap-2">
                            @if($payment->status === 'completed' && $payment->invoice)
                            <a href="{{ auth()->user()->isAdmin() ? route('admin.payments.invoice', $payment) : route('patient.payments.invoice', $payment) }}" class="btn btn-success">
                                <i class="fas fa-download me-2"></i>Download Invoice
                            </a>
                            @endif

                            @if(auth()->user()->isAdmin() && $payment->status === 'completed')
                            <form action="{{ route('admin.payments.refund', $payment) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure you want to refund this payment?')">
                                    <i class="fas fa-undo me-2"></i>Refund Payment
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection