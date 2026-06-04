<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Patient;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
            'blood_group' => 'nullable|string|max:5',
            'allergies' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:20',
            'emergency_contact_name' => 'nullable|string|max:255',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'dob' => $request->dob,
            'gender' => $request->gender,
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
            'blood_group' => 'nullable|string|max:5',
            'allergies' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:20',
            'emergency_contact_name' => 'nullable|string|max:255',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $patient->user->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'dob' => $request->dob,
            'gender' => $request->gender,
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
        ]);

        return redirect()->route('admin.patients.index')
            ->with('success', 'Patient updated successfully.');
    }

    public function destroy(Patient $patient)
    {
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
}