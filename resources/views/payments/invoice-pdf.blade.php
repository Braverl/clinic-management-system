<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $payment->invoice->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1e293b; font-size: 12px; line-height: 1.6; }
        .invoice-container { padding: 40px; max-width: 800px; margin: 0 auto; }

        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px; border-bottom: 3px solid #6366f1; padding-bottom: 20px; }
        .header-left h1 { font-size: 28px; color: #6366f1; margin-bottom: 4px; }
        .header-left p { color: #64748b; }
        .header-right { text-align: right; }
        .header-right .invoice-label { font-size: 18px; font-weight: 700; color: #6366f1; }
        .header-right .invoice-number { font-size: 14px; color: #475569; margin-top: 4px; }

        .parties { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .party-box { width: 48%; }
        .party-box h3 { font-size: 11px; text-transform: uppercase; color: #94a3b8; letter-spacing: 1px; margin-bottom: 6px; }
        .party-box p { margin: 2px 0; }
        .party-box .name { font-weight: 700; font-size: 14px; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        thead th { background: #6366f1; color: #fff; padding: 10px 12px; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; }
        tbody td { padding: 10px 12px; border-bottom: 1px solid #e2e8f0; }
        tbody tr:nth-child(even) { background: #f8fafc; }

        .totals { display: flex; justify-content: flex-end; margin-top: 20px; }
        .totals-table { width: 280px; }
        .totals-table tr td { padding: 6px 12px; }
        .totals-table tr td:first-child { color: #64748b; }
        .totals-table tr td:last-child { text-align: right; font-weight: 600; }
        .totals-table .grand-total td { border-top: 2px solid #6366f1; font-size: 16px; font-weight: 800; color: #6366f1; padding-top: 10px; }

        .footer { margin-top: 40px; border-top: 1px solid #e2e8f0; padding-top: 16px; text-align: center; color: #94a3b8; font-size: 10px; }
        .footer strong { color: #6366f1; }

        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 4px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .status-paid { background: #d1fae5; color: #065f46; }
        .status-unpaid { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="header">
            <div class="header-left">
                <h1>ClinicSystem</h1>
                <p>Healthcare Management</p>
                <p>support@clinicsystem.com</p>
            </div>
            <div class="header-right">
                <div class="invoice-label">INVOICE</div>
                <div class="invoice-number">{{ $payment->invoice->invoice_number }}</div>
                <div style="margin-top: 8px;">
                    <span class="status-badge {{ $payment->invoice->status === 'paid' ? 'status-paid' : 'status-unpaid' }}">
                        {{ strtoupper($payment->invoice->status) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="parties">
            <div class="party-box">
                <h3>Bill To</h3>
                <p class="name">{{ $payment->patient->user->name ?? 'N/A' }}</p>
                <p>{{ $payment->patient->user->email ?? '' }}</p>
                <p>{{ $payment->patient->user->phone ?? '' }}</p>
                <p>{{ $payment->patient->user->address ?? '' }}</p>
            </div>
            <div class="party-box">
                <h3>Details</h3>
                <p><strong>Invoice Date:</strong> {{ $payment->invoice->invoice_date->format('M d, Y') }}</p>
                <p><strong>Due Date:</strong> {{ $payment->invoice->due_date->format('M d, Y') }}</p>
                <p><strong>Payment #:</strong> {{ $payment->payment_number }}</p>
                <p><strong>Transaction #:</strong> {{ $payment->transaction_id ?? 'N/A' }}</p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Details</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>Consultation Fee</strong><br>
                        <small>Dr. {{ $payment->appointment->doctor->user->name ?? 'N/A' }}</small>
                    </td>
                    <td>
                        Appointment: {{ $payment->appointment->appointment_number ?? 'N/A' }}<br>
                        <small>{{ $payment->appointment ? \Carbon\Carbon::parse($payment->appointment->appointment_date)->format('M d, Y') : '' }}</small>
                    </td>
                    <td style="text-align: right;">${{ number_format($payment->amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="totals">
            <table class="totals-table">
                <tr>
                    <td>Subtotal</td>
                    <td>${{ number_format($payment->invoice->subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td>Tax</td>
                    <td>${{ number_format($payment->invoice->tax, 2) }}</td>
                </tr>
                <tr class="grand-total">
                    <td>Total</td>
                    <td>${{ number_format($payment->invoice->total, 2) }}</td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p>Thank you for choosing <strong>ClinicSystem</strong>. This invoice was generated automatically.</p>
            <p style="margin-top: 4px;">For questions, contact support@clinicsystem.com</p>
        </div>
    </div>
</body>
</html>
