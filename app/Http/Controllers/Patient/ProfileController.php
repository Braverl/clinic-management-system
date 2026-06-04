<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $patient = auth()->user()->patient;
        $user = auth()->user();
        
        return view('patient.profile.index', compact('patient', 'user'));
    }

    public function edit()
    {
        $patient = auth()->user()->patient;
        $user = auth()->user();
        
        return view('patient.profile.edit', compact('patient', 'user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $patient = $user->patient;
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'blood_group' => 'nullable|string|max:5',
            'allergies' => 'nullable|string|max:500',
            'medical_history' => 'nullable|string|max:1000',
            'emergency_contact' => 'nullable|string|max:20',
            'emergency_contact_name' => 'nullable|string|max:255',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        // Update user
        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'dob' => $request->dob,
            'gender' => $request->gender,
        ]);
        
        // Update patient
        $patient->update([
            'blood_group' => $request->blood_group,
            'allergies' => $request->allergies,
            'medical_history' => $request->medical_history,
            'emergency_contact' => $request->emergency_contact,
            'emergency_contact_name' => $request->emergency_contact_name,
        ]);
        
        // Handle profile image
        if ($request->hasFile('profile_image')) {
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $imagePath = $request->file('profile_image')->store('profiles', 'public');
            $user->update(['profile_image' => $imagePath]);
        }
        
        return redirect()->route('patient.profile.index')
            ->with('success', 'Profile updated successfully.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);
        
        $user = auth()->user();
        
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }
        
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);
        
        return redirect()->back()->with('success', 'Password changed successfully.');
    }

    public function medicalHistory()
    {
        $patient = auth()->user()->patient;
        
        // Get all medical records for this patient with doctor and appointment relationships
        $medicalRecords = MedicalRecord::where('patient_id', $patient->id)
            ->with(['doctor.user', 'appointment'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('patient.profile.medical-history', compact('medicalRecords'));
    }
}