<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $doctor = auth()->user()->doctor;
        
        $todayAppointments = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', Carbon::today())
            ->with('patient.user')
            ->orderBy('appointment_time')
            ->get();
        
        $pendingAppointments = Appointment::where('doctor_id', $doctor->id)
            ->where('status', 'pending')
            ->count();
        
        $totalAppointments = Appointment::where('doctor_id', $doctor->id)->count();
        $completedAppointments = Appointment::where('doctor_id', $doctor->id)
            ->where('status', 'completed')
            ->count();
        
        $upcomingAppointments = Appointment::where('doctor_id', $doctor->id)
            ->where('appointment_date', '>', Carbon::today())
            ->where('status', 'confirmed')
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->limit(5)
            ->get();
        
        $recentPatients = MedicalRecord::where('doctor_id', $doctor->id)
            ->with('patient.user')
            ->latest()
            ->limit(10)
            ->get()
            ->unique('patient_id');
        
        // Weekly appointment chart data
        $chartData = [];
        $startDate = Carbon::now()->subDays(6)->startOfDay();

        $appointmentsByDay = Appointment::where('doctor_id', $doctor->id)
            ->where('appointment_date', '>=', $startDate->toDateString())
            ->selectRaw('appointment_date as date, COUNT(*) as total')
            ->groupBy('appointment_date')
            ->pluck('total', 'date');

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chartData['labels'][] = $date->format('D, M d');
            $chartData['appointments'][] = (int) ($appointmentsByDay[$date->format('Y-m-d')] ?? 0);
        }
        
        return view('doctor.dashboard', compact(
            'todayAppointments', 'pendingAppointments', 'totalAppointments',
            'completedAppointments', 'upcomingAppointments', 'recentPatients', 'chartData'
        ));
    }
}