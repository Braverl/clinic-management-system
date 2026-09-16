@extends('layouts.app')

@section('title', auth()->user()->isAdmin() ? 'Manage Payments' : 'My Payments')

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-credit-card me-2"></i>{{ auth()->user()->isAdmin() ? 'All Payments' : 'My Payments' }}</h2>
    </div>

    <div class="row g-3 mb-4">
        @if(auth()->user()->isAdmin())
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="bg-success rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fas fa-dollar-sign text-white"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="mb-0 text-success">${{ number_format($stats['total_collected'], 2) }}</h4>
                            <small class="text-muted">Total Collected</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fas fa-calendar text-white"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="mb-0 text-primary">${{ number_format($stats['monthly_revenue'], 2) }}</h4>
                            <small class="text-muted">This Month</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fas fa-undo text-white"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="mb-0 text-warning">${{ number_format($stats['total_refunded'], 2) }}</h4>
                            <small class="text-muted">Refunded</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="bg-info rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fas fa-receipt text-white"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="mb-0 text-info">{{ $stats['total_count'] }}</h4>
                            <small class="text-muted">Total Transactions</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-success mb-1">${{ number_format($stats['total_paid'], 2) }}</h3>
                    <small class="text-muted">Total Paid</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h3 class="text-info mb-1">{{ $stats['total_count'] }}</h3>
                    <small class="text-muted">Total Transactions</small>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if($payments->count())
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Payment #</th>
                            @if(auth()->user()->isAdmin())
                            <th>Patient</th>
                            @endif
                            <th>Appointment</th>
                            <th>Doctor</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $pmt)
                        <tr>
                            <td><strong>{{ $pmt->payment_number }}</strong></td>
                            @if(auth()->user()->isAdmin())
                            <td>{{ $pmt->patient->user->name ?? 'N/A' }}</td>
                            @endif
                            <td>
                                <a href="{{ auth()->user()->isAdmin() ? route('admin.appointments.show', $pmt->appointment_id) : route('patient.appointments.show', $pmt->appointment_id) }}">
                                    {{ $pmt->appointment->appointment_number ?? 'N/A' }}
                                </a>
                            </td>
                            <td>Dr. {{ $pmt->appointment->doctor->user->name ?? 'N/A' }}</td>
                            <td><strong class="text-success">${{ number_format($pmt->amount, 2) }}</strong></td>
                            <td><span class="badge bg-secondary text-capitalize">{{ $pmt->payment_method }}</span></td>
                            <td>
                                <span class="badge bg-{{ $pmt->status === 'completed' ? 'success' : ($pmt->status === 'refunded' ? 'warning' : 'info') }} text-capitalize">
                                    {{ $pmt->status }}
                                </span>
                            </td>
                            <td>{{ $pmt->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ auth()->user()->isAdmin() ? route('admin.payments.show', $pmt) : route('patient.payments.show', $pmt) }}" class="btn btn-outline-primary" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($pmt->invoice && $pmt->status === 'completed')
                                    <a href="{{ auth()->user()->isAdmin() ? route('admin.payments.invoice', $pmt) : route('patient.payments.invoice', $pmt) }}" class="btn btn-outline-success" title="Download Invoice">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    @endif
                                    @if(auth()->user()->isAdmin() && $pmt->status === 'completed')
                                    <form action="{{ route('admin.payments.refund', $pmt) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-warning confirm-refund" title="Refund" onclick="return confirm('Are you sure you want to refund this payment?')">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $payments->links() }}
            @else
            <div class="text-center py-5">
                <i class="fas fa-credit-card fa-4x text-muted mb-3"></i>
                <h5>No Payments Found</h5>
                <p class="text-muted">
                    @if(auth()->user()->isAdmin()) No payment records found yet. @else You have no payment records yet. @endif
                </p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection