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
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chartData['labels'][] = $date->format('M d');
            $chartData['appointments'][] = Appointment::whereDate('appointment_date', $date)->count();
            $chartData['revenue'][] = Payment::whereDate('created_at', $date)
                ->where('status', 'completed')
                ->sum('amount');
        }
        
        return view('admin.dashboard', compact(
            'totalDoctors', 'totalPatients', 'totalAppointments', 'totalDepartments',
            'pendingAppointments', 'completedAppointments', 'cancelledAppointments',
            'todayAppointments', 'todayCompleted', 'totalRevenue', 'monthlyRevenue',
            'recentAppointments', 'recentPatients', 'chartData'
        ));
    }
}