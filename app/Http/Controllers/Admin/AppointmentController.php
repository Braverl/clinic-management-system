<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::with(['patient.user', 'doctor.user']);
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('date') && $request->date) {
            $query->whereDate('appointment_date', $request->date);
        }
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('patient.user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('doctor.user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }
        
        $appointments = $query->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->paginate(20);
        
        $stats = [
            'total' => Appointment::count(),
            'pending' => Appointment::where('status', 'pending')->count(),
            'confirmed' => Appointment::where('status', 'confirmed')->count(),
            'completed' => Appointment::where('status', 'completed')->count(),
            'cancelled' => Appointment::where('status', 'cancelled')->count(),
            'today' => Appointment::whereDate('appointment_date', Carbon::today())->count(),
        ];
        
        return view('admin.appointments.index', compact('appointments', 'stats'));
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['patient.user', 'doctor.user', 'medicalRecord']);
        return view('admin.appointments.show', compact('appointment'));
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled,rejected',
            'cancellation_reason' => 'required_if:status,cancelled,rejected|nullable|string',
        ]);
        
        $oldStatus = $appointment->status;
        $appointment->update([
            'status' => $request->status,
            'cancellation_reason' => $request->cancellation_reason,
        ]);
        
        // Create notification for patient
        $appointment->patient->user->notifications()->create([
            'title' => 'Appointment Status Updated',
            'message' => "Your appointment on {$appointment->appointment_date} has been {$appointment->status}.",
            'type' => $appointment->status === 'confirmed' ? 'success' : 'info',
            'link' => route('patient.appointments.show', $appointment),
        ]);
        
        return redirect()->back()->with('success', 'Appointment status updated.');
    }

    public function reschedule(Request $request, Appointment $appointment)
    {
        $request->validate([
            'appointment_date' => 'required|date|after:today',
            'appointment_time' => 'required',
        ]);
        
        // Check for double booking
        $exists = Appointment::where('doctor_id', $appointment->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->where('status', '!=', 'cancelled')
            ->exists();
            
        if ($exists) {
            return redirect()->back()->with('error', 'This time slot is already booked.');
        }
        
        $appointment->update([
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'status' => 'pending',
        ]);
        
        return redirect()->back()->with('success', 'Appointment rescheduled successfully.');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return redirect()->route('admin.appointments.index')
            ->with('success', 'Appointment deleted successfully.');
    }
}