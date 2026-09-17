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
            $query->where(function ($q) use ($search) {
                $q->whereHas('patient.user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('doctor.user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%");
                })->orWhere('appointment_number', 'like', "%{$search}%");
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

        if ($request->status === $appointment->status) {
            return redirect()->back()->with('error', 'Appointment is already ' . $request->status . '.');
        }

        if (!$appointment->canTransitionTo($request->status)) {
            return redirect()->back()->with('error', 'Appointment status cannot be changed from "' . $appointment->status . '" to "' . $request->status . '".');
        }

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
        if (!in_array($appointment->status, ['pending', 'confirmed'])) {
            return redirect()->back()->with('error', 'Only pending or confirmed appointments can be rescheduled.');
        }

        $request->validate([
            'appointment_date' => 'required|date|after:today',
            'appointment_time' => 'required|date_format:H:i',
        ]);

        $doctor = $appointment->doctor()->with('user')->first();

        if (!$doctor || !$doctor->user->is_active) {
            return redirect()->back()->with('error', 'This doctor is currently unavailable.');
        }

        $weekday = strtolower(Carbon::parse($request->appointment_date)->format('l'));
        $availableDays = $doctor->available_days
            ? array_map('strtolower', array_map('trim', explode(',', $doctor->available_days)))
            : [];

        if (!empty($availableDays) && !in_array($weekday, $availableDays)) {
            return redirect()->back()->with('error', 'The doctor is not available on the selected day.');
        }

        $startTime = $doctor->available_from ? Carbon::parse($doctor->available_from)->format('H:i') : '09:00';
        $endTime = $doctor->available_to ? Carbon::parse($doctor->available_to)->format('H:i') : '17:00';
        $requestedTime = Carbon::parse($request->appointment_time)->format('H:i');

        if ($requestedTime < $startTime || $requestedTime >= $endTime) {
            return redirect()->back()->with('error', 'The selected time is outside the doctor\'s working hours.');
        }

        // Check for double booking
        $exists = Appointment::where('doctor_id', $appointment->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->where('id', '!=', $appointment->id)
            ->where('status', '!=', 'cancelled')
            ->exists();
            
        if ($exists) {
            return redirect()->back()->with('error', 'This time slot is already booked.');
        }
        
        try {
            $appointment->update([
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $request->appointment_time,
                'status' => 'pending',
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            if (($e->errorInfo[1] ?? null) === 1062) {
                return redirect()->back()->with('error', 'This time slot has just been booked. Please choose a different time.');
            }
            throw $e;
        }
        
        return redirect()->back()->with('success', 'Appointment rescheduled successfully.');
    }

    public function destroy(Appointment $appointment)
    {
        if ($appointment->status === 'completed') {
            return redirect()->back()->with('error', 'Completed appointments cannot be deleted. They are part of the medical and financial audit trail.');
        }

        if ($appointment->payment) {
            return redirect()->back()->with('error', 'Appointments with payments cannot be deleted. Refund this payment or keep the record for financial auditing.');
        }

        if ($appointment->medicalRecord) {
            return redirect()->back()->with('error', 'Appointments with medical records cannot be deleted. Medical records must be retained for audit and patient care.');
        }

        $appointment->delete();
        return redirect()->route('admin.appointments.index')
            ->with('success', 'Appointment deleted successfully.');
    }
}