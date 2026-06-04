<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $admin = auth()->user();
        return view('admin.profile.index', compact('admin'));
    }

    public function edit()
    {
        $admin = auth()->user();
        return view('admin.profile.edit', compact('admin'));
    }

    public function update(Request $request)
    {
        $admin = auth()->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        // Update user
        $admin->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);
        
        // Handle profile image
        if ($request->hasFile('profile_image')) {
            if ($admin->profile_image) {
                Storage::disk('public')->delete($admin->profile_image);
            }
            $imagePath = $request->file('profile_image')->store('profiles', 'public');
            $admin->update(['profile_image' => $imagePath]);
        }
        
        return redirect()->route('admin.profile.index')->with('success', 'Profile updated successfully.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);
        
        $admin = auth()->user();
        
        if (!Hash::check($request->current_password, $admin->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }
        
        $admin->update([
            'password' => Hash::make($request->new_password),
        ]);
        
        return redirect()->back()->with('success', 'Password changed successfully.');
    }
}