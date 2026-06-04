<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Appointment Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .report-title { font-size: 24px; color: #333; }
        .report-date { color: #666; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #667eea; color: white; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="report-title">Appointment Report</h1>
        <p class="report-date">Period: {{ $request->start_date }} to {{ $request->end_date }}</p>
        <p>Generated on: {{ date('F d, Y') }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Appointment #</th>
                <th>Patient Name</th>
                <th>Doctor Name</th>
                <th>Department</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($appointments as $apt)
            <tr>
                <td>{{ $apt->appointment_number }}</td>
                <td>{{ $apt->patient->user->name ?? 'N/A' }}</td>
                <td>Dr. {{ $apt->doctor->user->name ?? 'N/A' }}</td>
                <td>{{ $apt->doctor->department->name ?? 'N/A' }}</td>
                <td>{{ \Carbon\Carbon::parse($apt->appointment_date)->format('Y-m-d') }}</td>
                <td>{{ \Carbon\Carbon::parse($apt->appointment_time)->format('g:i A') }}</td>
                <td>
                    @if($apt->status == 'pending')
                        <span style="color: orange;">Pending</span>
                    @elseif($apt->status == 'confirmed')
                        <span style="color: blue;">Confirmed</span>
                    @elseif($apt->status == 'completed')
                        <span style="color: green;">Completed</span>
                    @else
                        <span style="color: red;">Cancelled</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <p>Total Appointments: {{ $appointments->count() }}</p>
        <p>This is a system generated report. © Clinic Management System</p>
    </div>
</body>
</html>