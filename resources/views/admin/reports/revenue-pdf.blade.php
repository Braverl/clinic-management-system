<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Revenue Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .report-title { font-size: 24px; color: #333; }
        .report-date { color: #666; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #667eea; color: white; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #666; }
        .total { font-size: 18px; font-weight: bold; margin-top: 20px; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="report-title">Revenue Report</h1>
        @if($request->month)
            <p class="report-date">Period: {{ date('F', mktime(0,0,0,$request->month,1)) }} {{ $request->year }}</p>
        @else
            <p class="report-date">Period: Year {{ $request->year }}</p>
        @endif
        <p>Generated on: {{ date('F d, Y') }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Payment #</th>
                <th>Patient Name</th>
                <th>Doctor Name</th>
                <th>Appointment Date</th>
                <th>Amount ($)</th>
                <th>Payment Method</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
            <tr>
                <td>{{ $payment->payment_number }}</td>
                <td>{{ $payment->patient->user->name ?? 'N/A' }}</td>
                <td>Dr. {{ $payment->appointment->doctor->user->name ?? 'N/A' }}</td>
                <td>{{ $payment->appointment->appointment_date ?? 'N/A' }}</td>
                <td>${{ number_format($payment->amount, 2) }}</td>
                <td>{{ ucfirst($payment->payment_method) }}</td>
                <td>{{ $payment->created_at->format('Y-m-d') }}</td>
            </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">No payment records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="total">
        <p>Total Revenue: ${{ number_format($totalRevenue, 2) }}</p>
        <hr>
        <p><strong>Summary:</strong></p>
        <p>Total Transactions: {{ $payments->count() }}</p>
        <p>Average Transaction: ${{ number_format($payments->avg('amount') ?? 0, 2) }}</p>
    </div>
    
    <div class="footer">
        <p>This is a system generated report. © Clinic Management System</p>
    </div>
</body>
</html>