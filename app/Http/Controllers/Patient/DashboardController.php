<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $patient = auth()->user()->patient;
        
        $upcomingAppointments = Appointment::where('patient_id', $patient->id)
            ->where('appointment_date', '>=', Carbon::today())
            ->whereIn('status', ['pending', 'confirmed'])
            ->with('doctor.user')
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();
        
        $pastAppointments = Appointment::where('patient_id', $patient->id)
            ->where('appointment_date', '<', Carbon::today())
            ->orWhere('status', 'completed')
            ->with('doctor.user')
            ->orderBy('appointment_date', 'desc')
            ->limit(5)
            ->get();
        
        $totalAppointments = Appointment::where('patient_id', $patient->id)->count();
        $completedAppointments = Appointment::where('patient_id', $patient->id)->where('status', 'completed')->count();
        $pendingAppointments = Appointment::where('patient_id', $patient->id)->where('status', 'pending')->count();
        
        return view('patient.dashboard', compact(
            'upcomingAppointments', 'pastAppointments',
            'totalAppointments', 'completedAppointments', 'pendingAppointments'
        ));
    }
}