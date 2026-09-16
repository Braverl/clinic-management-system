<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Department;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalDoctors = Doctor::count();
        $totalPatients = Patient::count();
        $totalAppointments = Appointment::count();
        $totalDepartments = Department::count();
        $pendingAppointments = Appointment::where('status', 'pending')->count();
        $completedAppointments = Appointment::where('status', 'completed')->count();
        $cancelledAppointments = Appointment::where('status', 'cancelled')->count();
        
        $todayAppointments = Appointment::whereDate('appointment_date', Carbon::today())->count();
        $todayCompleted = Appointment::whereDate('appointment_date', Carbon::today())
            ->where('status', 'completed')->count();
        
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        $monthlyRevenue = Payment::where('status', 'completed')
            ->whereMonth('created_at', Carbon::now()->month)
            ->sum('amount');
        
        $recentAppointments = Appointment::with(['patient.user', 'doctor.user'])
            ->latest()
            ->take(10)
            ->get();
        
        $recentPatients = Patient::with('user')
            ->latest()
            ->take(10)
            ->get();
        
        // Chart data - last 7 days appointments
        $chartData = [];
        $startDate = Carbon::now()->subDays(6)->startOfDay();

        $appointmentsByDay = Appointment::where('appointment_date', '>=', $startDate->toDateString())
            ->selectRaw('appointment_date as date, COUNT(*) as total')
            ->groupBy('appointment_date')
            ->pluck('total', 'date');

        $revenueByDay = Payment::where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupByRaw('DATE(created_at)')
            ->pluck('total', 'date');

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $key = $date->format('Y-m-d');
            $chartData['labels'][] = $date->format('M d');
            $chartData['appointments'][] = (int) ($appointmentsByDay[$key] ?? 0);
            $chartData['revenue'][] = (float) ($revenueByDay[$key] ?? 0);
        }
        
        return view('admin.dashboard', compact(
            'totalDoctors', 'totalPatients', 'totalAppointments', 'totalDepartments',
            'pendingAppointments', 'completedAppointments', 'cancelledAppointments',
            'todayAppointments', 'todayCompleted', 'totalRevenue', 'monthlyRevenue',
            'recentAppointments', 'recentPatients', 'chartData'
        ));
    }
}