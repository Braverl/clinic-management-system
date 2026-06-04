<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Schedule;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with(['doctor.user', 'payment'])
            ->where('patient_id', auth()->user()->patient->id)
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->paginate(10);
        
        return view('patient.appointments.index', compact('appointments'));
    }

    public function create()
    {
        $doctors = Doctor::with(['user', 'department'])
            ->whereHas('user', function($q) {
                $q->where('is_active', true);
            })
            ->get();
        
        return view('patient.appointments.book', compact('doctors'));
    }

    public function getDoctorSchedule($doctorId, $date)
    {
        $doctor = Doctor::findOrFail($doctorId);
        
        // Get booked appointments for this doctor on this date
        $bookedTimes = Appointment::where('doctor_id', $doctorId)
            ->where('appointment_date', $date)
            ->where('status', '!=', 'cancelled')
            ->pluck('appointment_time')
            ->map(function($time) {
                return Carbon::parse($time)->format('H:i');
            })
            ->toArray();
        
        // Generate available time slots (9 AM to 5 PM, 30 min slots)
        $availableSlots = [];
        $start = Carbon::parse($date . ' 09:00:00');
        $end = Carbon::parse($date . ' 17:00:00');
        
        while ($start < $end) {
            $slot = $start->format('H:i');
            if (!in_array($slot, $bookedTimes)) {
                $availableSlots[] = [
                    'time' => $slot,
                    'display' => $start->format('g:i A')
                ];
            }
            $start->addMinutes(30);
        }
        
        return response()->json([
            'available_slots' => $availableSlots,
            'fee' => $doctor->consultation_fee
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date|after:today',
            'appointment_time' => 'required',
            'symptoms' => 'nullable|string|max:1000',
        ]);
        
        // Check for double booking
        $exists = Appointment::where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->where('status', '!=', 'cancelled')
            ->exists();
        
        if ($exists) {
            return redirect()->back()->with('error', 'This time slot is already booked. Please choose another time.');
        }
        
        // Check if patient already has an appointment on this day
        $todayAppointment = Appointment::where('patient_id', auth()->user()->patient->id)
            ->where('appointment_date', $request->appointment_date)
            ->where('status', '!=', 'cancelled')
            ->exists();
        
        if ($todayAppointment) {
            return redirect()->back()->with('error', 'You already have an appointment booked for this date.');
        }
        
        DB::beginTransaction();
        
        try {
            $appointment = Appointment::create([
                'appointment_number' => 'APT-' . strtoupper(uniqid()),
                'patient_id' => auth()->user()->patient->id,
                'doctor_id' => $request->doctor_id,
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $request->appointment_time,
                'symptoms' => $request->symptoms,
                'status' => 'pending',
                'is_emergency' => $request->has('is_emergency'),
            ]);
            
            // Create notification for doctor
            $doctor = Doctor::find($request->doctor_id);
            $doctor->user->notifications()->create([
                'title' => 'New Appointment Request',
                'message' => "Patient " . auth()->user()->name . " requested an appointment for " . $request->appointment_date,
                'type' => 'info',
                'link' => route('doctor.appointments.show', $appointment),
            ]);
            
            // Create notification for patient
            auth()->user()->notifications()->create([
                'title' => 'Appointment Booked',
                'message' => 'Your appointment has been booked and is pending approval.',
                'type' => 'success',
                'link' => route('patient.appointments.show', $appointment),
            ]);
            
            DB::commit();
            
            return redirect()->route('patient.appointments.index')
                ->with('success', 'Appointment booked successfully. Waiting for confirmation.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function show(Appointment $appointment)
    {
        // Security check - only patient's own appointments
        if ($appointment->patient_id != auth()->user()->patient->id) {
            abort(403);
        }
        
        $appointment->load(['doctor.user', 'medicalRecord.prescriptions', 'payment']);
        
        return view('patient.appointments.show', compact('appointment'));
    }

    public function cancel(Appointment $appointment, Request $request)
    {
        // Security check
        if ($appointment->patient_id != auth()->user()->patient->id) {
            abort(403);
        }
        
        // Can only cancel if appointment is pending or confirmed
        if (!in_array($appointment->status, ['pending', 'confirmed'])) {
            return redirect()->back()->with('error', 'This appointment cannot be cancelled.');
        }
        
        // Can only cancel at least 24 hours before
        $appointmentDateTime = Carbon::parse($appointment->appointment_date . ' ' . $appointment->appointment_time);
        if (now()->diffInHours($appointmentDateTime, false) < 24 && $appointmentDateTime->gt(now())) {
            return redirect()->back()->with('error', 'Appointments can only be cancelled at least 24 hours in advance.');
        }
        
        $appointment->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->cancellation_reason ?? 'Cancelled by patient'
        ]);
        
        // Notify doctor
        $appointment->doctor->user->notifications()->create([
            'title' => 'Appointment Cancelled',
            'message' => "Patient cancelled appointment for " . $appointment->appointment_date,
            'type' => 'warning',
            'link' => route('doctor.appointments.show', $appointment),
        ]);
        
        return redirect()->back()->with('success', 'Appointment cancelled successfully.');
    }

    public function reschedule($appointmentId)
    {
        $appointment = Appointment::where('id', $appointmentId)
            ->where('patient_id', auth()->user()->patient->id)
            ->firstOrFail();
        
        // Can only reschedule if pending or confirmed
        if (!in_array($appointment->status, ['pending', 'confirmed'])) {
            return redirect()->back()->with('error', 'This appointment cannot be rescheduled.');
        }
        
        $doctors = Doctor::with('user')->get();
        
        return view('patient.appointments.reschedule', compact('appointment', 'doctors'));
    }

    public function updateReschedule(Request $request, Appointment $appointment)
    {
        // Security check
        if ($appointment->patient_id != auth()->user()->patient->id) {
            abort(403);
        }
        
        $request->validate([
            'appointment_date' => 'required|date|after:today',
            'appointment_time' => 'required',
        ]);
        
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
        
        $oldDate = $appointment->appointment_date;
        $oldTime = $appointment->appointment_time;
        
        $appointment->update([
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'status' => 'pending',
        ]);
        
        // Notify doctor
        $appointment->doctor->user->notifications()->create([
            'title' => 'Appointment Rescheduled',
            'message' => "Patient rescheduled appointment from {$oldDate} {$oldTime} to {$request->appointment_date} {$request->appointment_time}",
            'type' => 'info',
            'link' => route('doctor.appointments.show', $appointment),
        ]);
        
        return redirect()->route('patient.appointments.show', $appointment)
            ->with('success', 'Appointment rescheduled successfully.');
    }
}