<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Doctor Performance Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .report-title { font-size: 24px; color: #333; }
        .report-date { color: #666; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #667eea; color: white; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #666; }
        .summary { margin-top: 20px; padding: 10px; background-color: #f5f5f5; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="report-title">Doctor Performance Report</h1>
        <p class="report-date">Period: {{ $request->start_date }} to {{ $request->end_date }}</p>
        <p>Generated on: {{ date('F d, Y') }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Doctor Name</th>
                <th>Department</th>
                <th>Specialization</th>
                <th>Experience</th>
                <th>Appointments</th>
                <th>Completed</th>
                <th>Cancelled</th>
                <th>Revenue ($)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalAppointments = 0;
                $totalCompleted = 0;
                $totalRevenue = 0;
            @endphp
            @foreach($doctors as $doctor)
            @php
                $aptCount = $doctor->appointments->count();
                $completedCount = $doctor->appointments->where('status', 'completed')->count();
                $cancelledCount = $doctor->appointments->where('status', 'cancelled')->count();
                $revenue = $completedCount * $doctor->consultation_fee;
                $totalAppointments += $aptCount;
                $totalCompleted += $completedCount;
                $totalRevenue += $revenue;
            @endphp
            <tr>
                <td>Dr. {{ $doctor->user->name }}</td>
                <td>{{ $doctor->department->name ?? 'N/A' }}</td>
                <td>{{ $doctor->specialization }}</td>
                <td>{{ $doctor->experience_years }} years</td>
                <td>{{ $aptCount }} </td>
                <td>{{ $completedCount }} </td>
                <td>{{ $cancelledCount }} </td>
                <td>${{ number_format($revenue, 2) }} </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f0f0f0; font-weight: bold;">
                <td colspan="4" style="text-align: right;">TOTAL:</td>
                <td>{{ $totalAppointments }}</td>
                <td>{{ $totalCompleted }}</td>
                <td>-</td>
                <td>${{ number_format($totalRevenue, 2) }}</td>
            </tr>
        </tfoot>
    </table>
    
    <div class="summary">
        <h4>Performance Summary</h4>
        <p>Total Doctors: {{ $doctors->count() }}</p>
        <p>Total Appointments: {{ $totalAppointments }}</p>
        <p>Completion Rate: {{ $totalAppointments > 0 ? round(($totalCompleted / $totalAppointments) * 100, 2) : 0 }}%</p>
        <p>Total Revenue Generated: ${{ number_format($totalRevenue, 2) }}</p>
    </div>
    
    <div class="footer">
        <p>This is a system generated report. © Clinic Management System</p>
    </div>
</body>
</html>