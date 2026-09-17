<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DoctorController extends Controller
{
    public function index()
    {
        $doctors = Doctor::with(['user', 'department'])->paginate(15);
        return view('admin.doctors.index', compact('doctors'));
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->get();
        return view('admin.doctors.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|min:6|confirmed',
            'department_id' => 'required|exists:departments,id',
            'specialization' => 'required|string',
            'qualification' => 'required|string',
            'experience_years' => 'required|integer|min:0',
            'consultation_fee' => 'required|numeric|min:0',
            'license_number' => 'required|string|unique:doctors',
            'bio' => 'nullable|string',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => 'doctor',
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        if ($request->hasFile('profile_image')) {
            $imagePath = $request->file('profile_image')->store('doctors', 'public');
        }

        Doctor::create([
            'user_id' => $user->id,
            'department_id' => $request->department_id,
            'specialization' => $request->specialization,
            'qualification' => $request->qualification,
            'experience_years' => $request->experience_years,
            'consultation_fee' => $request->consultation_fee,
            'license_number' => $request->license_number,
            'bio' => $request->bio,
            'available_days' => $request->available_days ? implode(',', $request->available_days) : null,
            'available_from' => $request->available_from,
            'available_to' => $request->available_to,
        ]);

        return redirect()->route('admin.doctors.index')
            ->with('success', 'Doctor added successfully.');
    }

    public function edit(Doctor $doctor)
    {
        $departments = Department::where('is_active', true)->get();
        $availableDays = $doctor->available_days ? explode(',', $doctor->available_days) : [];
        return view('admin.doctors.edit', compact('doctor', 'departments', 'availableDays'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'department_id' => 'required|exists:departments,id',
            'specialization' => 'required|string',
            'qualification' => 'required|string',
            'experience_years' => 'required|integer|min:0',
            'consultation_fee' => 'required|numeric|min:0',
            'bio' => 'nullable|string',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $doctor->user->update([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        if ($request->hasFile('profile_image')) {
            if ($doctor->user->profile_image) {
                Storage::disk('public')->delete($doctor->user->profile_image);
            }
            $imagePath = $request->file('profile_image')->store('doctors', 'public');
            $doctor->user->update(['profile_image' => $imagePath]);
        }

        $doctor->update([
            'department_id' => $request->department_id,
            'specialization' => $request->specialization,
            'qualification' => $request->qualification,
            'experience_years' => $request->experience_years,
            'consultation_fee' => $request->consultation_fee,
            'bio' => $request->bio,
            'available_days' => $request->available_days ? implode(',', $request->available_days) : null,
            'available_from' => $request->available_from,
            'available_to' => $request->available_to,
        ]);

        return redirect()->route('admin.doctors.index')
            ->with('success', 'Doctor updated successfully.');
    }

    public function destroy(Doctor $doctor)
    {
        $activeAppointments = $doctor->appointments()
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        if ($activeAppointments > 0) {
            return redirect()->back()->with('error', 'Cannot delete doctor with ' . $activeAppointments . ' active appointment(s). Please cancel or complete them first.');
        }

        $medicalRecords = $doctor->medicalRecords()->count();
        if ($medicalRecords > 0) {
            return redirect()->back()->with('error', 'Cannot delete doctor with ' . $medicalRecords . ' medical record(s). Medical records must be retained for audit and patient care.');
        }

        $payments = $doctor->payments()->count();
        if ($payments > 0) {
            return redirect()->back()->with('error', 'Cannot delete doctor with ' . $payments . ' payment record(s). Financial records must be retained for auditing.');
        }

        if ($doctor->user->profile_image) {
            Storage::disk('public')->delete($doctor->user->profile_image);
        }

        $doctor->user->delete();
        return redirect()->route('admin.doctors.index')
            ->with('success', 'Doctor deleted successfully.');
    }

    public function toggleStatus(Doctor $doctor)
    {
        $doctor->user->update(['is_active' => !$doctor->user->is_active]);
        return redirect()->back()->with('success', 'Doctor status updated.');
    }
}