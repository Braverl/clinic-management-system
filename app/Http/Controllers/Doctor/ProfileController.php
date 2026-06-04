<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $doctor = auth()->user();
        $doctorDetails = $doctor->doctor;
        return view('doctor.profile.index', compact('doctor', 'doctorDetails'));
    }

    public function edit()
    {
        $doctor = auth()->user();
        $doctorDetails = $doctor->doctor;
        return view('doctor.profile.edit', compact('doctor', 'doctorDetails'));
    }

    public function update(Request $request)
    {
        $doctor = auth()->user();
        $doctorDetails = $doctor->doctor;
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'bio' => 'nullable|string|max:1000',
        ]);
        
        // Update user
        $doctor->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);
        
        // Update doctor details
        $doctorDetails->update([
            'bio' => $request->bio,
        ]);
        
        // Handle profile image
        if ($request->hasFile('profile_image')) {
            if ($doctor->profile_image) {
                Storage::disk('public')->delete($doctor->profile_image);
            }
            $imagePath = $request->file('profile_image')->store('profiles', 'public');
            $doctor->update(['profile_image' => $imagePath]);
        }
        
        return redirect()->route('doctor.profile.index')->with('success', 'Profile updated successfully.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);
        
        $doctor = auth()->user();
        
        if (!Hash::check($request->current_password, $doctor->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }
        
        $doctor->update([
            'password' => Hash::make($request->new_password),
        ]);
        
        return redirect()->back()->with('success', 'Password changed successfully.');
    }
}