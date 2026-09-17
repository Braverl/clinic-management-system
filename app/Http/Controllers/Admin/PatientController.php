<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PatientController extends Controller
{
    public function index()
    {
        $patients = Patient::with('user')->paginate(15);
        return view('admin.patients.index', compact('patients'));
    }

    public function create()
    {
        return view('admin.patients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|min:6|confirmed',
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'blood_group' => 'nullable|string|max:5',
            'allergies' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:20',
            'emergency_contact_name' => 'nullable|string|max:255',
            'insurance_provider' => 'nullable|string|max:255',
            'insurance_number' => 'nullable|string|max:255',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'dob' => $request->dob,
            'gender' => $request->gender,
            'address' => $request->address,
            'role' => 'patient',
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        if ($request->hasFile('profile_image')) {
            $imagePath = $request->file('profile_image')->store('patients', 'public');
            $user->update(['profile_image' => $imagePath]);
        }

        Patient::create([
            'user_id' => $user->id,
            'blood_group' => $request->blood_group,
            'allergies' => $request->allergies,
            'medical_history' => $request->medical_history,
            'emergency_contact' => $request->emergency_contact,
            'emergency_contact_name' => $request->emergency_contact_name,
            'insurance_provider' => $request->insurance_provider,
            'insurance_number' => $request->insurance_number,
        ]);

        return redirect()->route('admin.patients.index')
            ->with('success', 'Patient added successfully.');
    }

    public function show(Patient $patient)
    {
        $patient->load(['user', 'appointments.doctor.user', 'medicalRecords.doctor.user', 'payments']);
        $totalAppointments = $patient->appointments()->count();
        $completedAppointments = $patient->appointments()->where('status', 'completed')->count();
        $totalSpent = $patient->payments()->where('status', 'completed')->sum('amount');
        
        return view('admin.patients.show', compact('patient', 'totalAppointments', 'completedAppointments', 'totalSpent'));
    }

    public function edit(Patient $patient)
    {
        return view('admin.patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'blood_group' => 'nullable|string|max:5',
            'allergies' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:20',
            'emergency_contact_name' => 'nullable|string|max:255',
            'insurance_provider' => 'nullable|string|max:255',
            'insurance_number' => 'nullable|string|max:255',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $patient->user->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'dob' => $request->dob,
            'gender' => $request->gender,
            'address' => $request->address,
        ]);

        if ($request->hasFile('profile_image')) {
            if ($patient->user->profile_image) {
                Storage::disk('public')->delete($patient->user->profile_image);
            }
            $imagePath = $request->file('profile_image')->store('patients', 'public');
            $patient->user->update(['profile_image' => $imagePath]);
        }

        $patient->update([
            'blood_group' => $request->blood_group,
            'allergies' => $request->allergies,
            'medical_history' => $request->medical_history,
            'emergency_contact' => $request->emergency_contact,
            'emergency_contact_name' => $request->emergency_contact_name,
            'insurance_provider' => $request->insurance_provider,
            'insurance_number' => $request->insurance_number,
        ]);

        return redirect()->route('admin.patients.index')
            ->with('success', 'Patient updated successfully.');
    }

    public function destroy(Patient $patient)
    {
        $activeAppointments = $patient->appointments()
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        if ($activeAppointments > 0) {
            return redirect()->back()->with('error', 'Cannot delete patient with ' . $activeAppointments . ' active appointment(s). Please resolve them first.');
        }

        $hasMedicalRecords = $patient->medicalRecords()->exists();
        if ($hasMedicalRecords) {
            return redirect()->back()->with('error', 'Cannot delete patient with medical history. Medical records must be retained for audit and patient care.');
        }

        $hasPayments = $patient->payments()->exists();
        if ($hasPayments) {
            return redirect()->back()->with('error', 'Cannot delete patient with payment records. Financial records must be retained for auditing.');
        }

        if ($patient->user->profile_image) {
            Storage::disk('public')->delete($patient->user->profile_image);
        }
        $patient->user->delete();
        
        return redirect()->route('admin.patients.index')
            ->with('success', 'Patient deleted successfully.');
    }

    public function toggleStatus(Patient $patient)
    {
        $patient->user->update(['is_active' => !$patient->user->is_active]);
        return redirect()->back()->with('success', 'Patient status updated.');
    }

    // =============================================
    // ADMIN-SIDE APPOINTMENT MANAGEMENT
    // =============================================

    public function appointments(Patient $patient)
    {
        $appointments = Appointment::with(['doctor.user'])
            ->where('patient_id', $patient->id)
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->paginate(15);

        return view('admin.patients.appointments.index', compact('patient', 'appointments'));
    }

    public function bookAppointment(Patient $patient)
    {
        $doctors = Doctor::with(['user', 'department'])
            ->whereHas('user', function ($q) {
                $q->where('is_active', true);
            })
            ->get();

        return view('admin.patients.appointments.book', compact('patient', 'doctors'));
    }

    public function storeAppointment(Request $request, Patient $patient)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date|after:today',
            'appointment_time' => 'required|date_format:H:i',
            'symptoms' => 'nullable|string|max:1000',
        ]);

        $doctor = Doctor::with('user')->findOrFail($request->doctor_id);

        if (!$doctor->user->is_active) {
            return redirect()->back()->with('error', 'Cannot book appointment with an inactive doctor.');
        }

        // Prevent double booking
        $exists = Appointment::where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'This time slot is already booked. Please choose another time.');
        }

        // Validate doctor is available on this day and within working hours
        $weekday = strtolower(Carbon::parse($request->appointment_date)->format('l'));
        $availableDays = $doctor->available_days
            ? array_map('strtolower', array_map('trim', explode(',', $doctor->available_days)))
            : [];

        if (!empty($availableDays) && !in_array($weekday, $availableDays)) {
            return redirect()->back()->with('error', 'Dr. ' . $doctor->user->name . ' is not available on this day.');
        }

        $startTime = $doctor->available_from ? Carbon::parse($doctor->available_from)->format('H:i') : '09:00';
        $endTime = $doctor->available_to ? Carbon::parse($doctor->available_to)->format('H:i') : '17:00';
        $requestedTime = Carbon::parse($request->appointment_time)->format('H:i');

        if ($requestedTime < $startTime || $requestedTime >= $endTime) {
            return redirect()->back()->with('error', 'The selected time is outside the doctor\'s working hours.');
        }

        try {
            $appointment = Appointment::create([
                'patient_id' => $patient->id,
                'doctor_id' => $request->doctor_id,
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $request->appointment_time,
                'symptoms' => $request->symptoms,
                'status' => 'pending',
                'is_emergency' => $request->has('is_emergency'),
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            if (($e->errorInfo[1] ?? null) === 1062) {
                return redirect()->back()->with('error', 'This time slot has just been booked. Please choose a different time.');
            }
            throw $e;
        }

        // Notifications
        $doctor->user->notifications()->create([
            'title' => 'New Appointment Request',
            'message' => "Admin booked an appointment for patient {$patient->user->name} on {$request->appointment_date}.",
            'type' => 'info',
            'link' => route('doctor.appointments.show', $appointment),
        ]);

        $patient->user->notifications()->create([
            'title' => 'Appointment Booked',
            'message' => "Your appointment (#{$appointment->appointment_number}) has been booked. Please wait for the doctor's confirmation.",
            'type' => 'success',
            'link' => route('patient.appointments.show', $appointment),
        ]);

        return redirect()->route('admin.patients.appointments.show', [$patient, $appointment])
            ->with('success', 'Appointment booked successfully for ' . $patient->user->name . '.');
    }

    public function showAppointment(Patient $patient, Appointment $appointment)
    {
        if ($appointment->patient_id != $patient->id) {
            abort(404);
        }

        $appointment->load(['doctor.user', 'medicalRecord.prescriptions', 'payment.invoice']);

        return view('admin.patients.appointments.show', compact('patient', 'appointment'));
    }
}