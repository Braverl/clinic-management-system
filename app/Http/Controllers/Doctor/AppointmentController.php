<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $doctor = auth()->user()->doctor;
        
        $query = Appointment::where('doctor_id', $doctor->id)
            ->with('patient.user');
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('date') && $request->date) {
            $query->whereDate('appointment_date', $request->date);
        } else {
            $query->orderBy('appointment_date', 'desc');
        }
        
        $appointments = $query->paginate(15);
        
        $stats = [
            'pending' => Appointment::where('doctor_id', $doctor->id)->where('status', 'pending')->count(),
            'today' => Appointment::where('doctor_id', $doctor->id)->whereDate('appointment_date', Carbon::today())->count(),
            'upcoming' => Appointment::where('doctor_id', $doctor->id)->where('appointment_date', '>', Carbon::today())->where('status', 'confirmed')->count(),
        ];
        
        return view('doctor.appointments.index', compact('appointments', 'stats'));
    }

    public function show(Appointment $appointment)
    {
        // Security check - only doctor's own appointments
        if ($appointment->doctor_id != auth()->user()->doctor->id) {
            abort(403);
        }
        
        $appointment->load(['patient.user', 'medicalRecord.prescriptions']);
        
        return view('doctor.appointments.show', compact('appointment'));
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        // Security check
        if ($appointment->doctor_id != auth()->user()->doctor->id) {
            abort(403);
        }
        
        $request->validate([
            'status' => 'required|in:confirmed,completed,cancelled',
            'cancellation_reason' => 'required_if:status,cancelled|nullable|string',
        ]);

        if ($request->status === $appointment->status) {
            return redirect()->back()->with('error', 'Appointment is already ' . $request->status . '.');
        }

        if (!$appointment->canTransitionTo($request->status)) {
            return redirect()->back()->with('error', 'Appointment status cannot be changed from "' . $appointment->status . '" to "' . $request->status . '".');
        }

        $appointment->update([
            'status' => $request->status,
            'cancellation_reason' => $request->cancellation_reason,
        ]);
        
        // Notify patient
        $appointment->patient->user->notifications()->create([
            'title' => 'Appointment ' . ucfirst($request->status),
            'message' => "Your appointment on {$appointment->appointment_date} has been {$request->status} by Dr. " . auth()->user()->name,
            'type' => $request->status === 'confirmed' ? 'success' : 'info',
            'link' => route('patient.appointments.show', $appointment),
        ]);
        
        return redirect()->back()->with('success', 'Appointment status updated.');
    }

    public function addMedicalRecord(Request $request, Appointment $appointment)
    {
        // Security check
        if ($appointment->doctor_id != auth()->user()->doctor->id) {
            abort(403);
        }
        
        $request->validate([
            'diagnosis' => 'required|string',
            'symptoms' => 'required|string',
            'treatment_plan' => 'nullable|string',
            'notes' => 'nullable|string',
            'weight' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'blood_pressure' => 'nullable|string',
            'temperature' => 'nullable|string',
        ]);
        
        $medicalRecord = MedicalRecord::updateOrCreate(
            ['appointment_id' => $appointment->id],
            [
                'patient_id' => $appointment->patient_id,
                'doctor_id' => $appointment->doctor_id,
                'diagnosis' => $request->diagnosis,
                'symptoms' => $request->symptoms,
                'treatment_plan' => $request->treatment_plan,
                'notes' => $request->notes,
                'weight' => $request->weight,
                'height' => $request->height,
                'blood_pressure' => $request->blood_pressure,
                'temperature' => $request->temperature,
            ]
        );
        
        // Update appointment status to completed if confirmed (respects state machine)
        if ($appointment->status === 'confirmed') {
            $appointment->update(['status' => 'completed']);
        }
        
        return redirect()->back()->with('success', 'Medical record saved successfully.');
    }

    public function addPrescription(Request $request, Appointment $appointment)
    {
        // Security check
        if ($appointment->doctor_id != auth()->user()->doctor->id) {
            abort(403);
        }
        
        $request->validate([
            'medical_record_id' => 'required|exists:medical_records,id',
            'medication_name' => 'required|string',
            'dosage' => 'required|string',
            'frequency' => 'required|string',
            'duration' => 'required|string',
            'instructions' => 'nullable|string',
        ]);

        // Security: prescription must be attached to this appointment's medical record
        $medicalRecord = MedicalRecord::where('id', $request->medical_record_id)
            ->where('appointment_id', $appointment->id)
            ->first();

        if (!$medicalRecord) {
            return redirect()->back()->with('error', 'Invalid medical record for this appointment.');
        }

        Prescription::create([
            'medical_record_id' => $medicalRecord->id,
            'medication_name' => $request->medication_name,
            'dosage' => $request->dosage,
            'frequency' => $request->frequency,
            'duration' => $request->duration,
            'instructions' => $request->instructions,
        ]);
        
        return redirect()->back()->with('success', 'Prescription added successfully.');
    }

    public function patientHistory($patientId)
    {
        $doctor = auth()->user()->doctor;

        $patient = \App\Models\Patient::with('user')->findOrFail($patientId);

        // Privacy check: doctor may only view patients they have an appointment with
        $hasRelationship = \App\Models\Appointment::where('doctor_id', $doctor->id)
            ->where('patient_id', $patientId)
            ->exists();

        if (!$hasRelationship) {
            abort(403, 'You can only view the history of your own patients.');
        }

        $medicalRecords = MedicalRecord::where('patient_id', $patientId)
            ->where('doctor_id', $doctor->id)
            ->with(['appointment', 'prescriptions'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('doctor.patients.history', compact('medicalRecords', 'patient'));
    }
}