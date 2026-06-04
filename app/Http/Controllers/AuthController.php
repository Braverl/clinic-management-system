<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use App\Mail\VerificationCodeMail;
use App\Mail\PasswordResetMail;

class AuthController extends Controller
{
    // =============================================
    // REGISTRATION WITH EMAIL VERIFICATION
    // =============================================
    
    public function showRegisterForm()
    {
        if (Auth::check()) {
            return $this->redirectToDashboard();
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Validate if email is real (check DNS)
        $domain = substr(strrchr($request->email, "@"), 1);
        if (!checkdnsrr($domain, 'MX') && !checkdnsrr($domain, 'A')) {
            return back()->withErrors(['email' => 'Please use a valid email address from a real email provider.'])->withInput();
        }

        // Store user data temporarily in session
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'dob' => $request->dob,
            'gender' => $request->gender,
            'role' => 'patient',
            'is_active' => false, // Inactive until email verified
        ];
        
        session(['pending_registration' => $userData]);
        
        // Generate and send verification code
        $verificationCode = $this->generateVerificationCode();
        
        // Store code in cache (expires in 10 minutes)
        Cache::put('email_verification_' . $request->email, $verificationCode, 600);
        
        // Send email
        try {
            Mail::to($request->email)->send(new VerificationCodeMail($verificationCode, $request->name));
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Unable to send verification email. Please check your email address.']);
        }
        
        // Store email in session for verification
        session(['verification_email' => $request->email]);
        
        return redirect()->route('verify.email.form')->with('success', 'Verification code sent to your email. Please check your inbox.');
    }
    
    public function showVerificationForm()
    {
        if (!session('verification_email')) {
            return redirect()->route('register');
        }
        return view('auth.verify-email');
    }
    
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|string|size:6',
        ]);
        
        $email = session('verification_email');
        $cachedCode = Cache::get('email_verification_' . $email);
        
        if (!$cachedCode) {
            return back()->withErrors(['verification_code' => 'Verification code has expired. Please register again.']);
        }
        
        if ($request->verification_code != $cachedCode) {
            return back()->withErrors(['verification_code' => 'Invalid verification code.']);
        }
        
        // Get pending user data
        $userData = session('pending_registration');
        
        if (!$userData) {
            return redirect()->route('register')->withErrors(['error' => 'Registration data not found. Please register again.']);
        }
        
        // Create verified user (is_active = true)
        $userData['is_active'] = true; // Force active
        $user = User::create($userData);
        
        Patient::create([
            'user_id' => $user->id,
        ]);
        
        // Clear temporary data
        Cache::forget('email_verification_' . $email);
        session()->forget(['pending_registration', 'verification_email']);
        
        // Auto login
        Auth::login($user);
        
        return redirect()->route('patient.dashboard')->with('success', 'Email verified successfully! Welcome to Clinic System.');
    }
    
    public function resendVerificationCode()
    {
        $email = session('verification_email');
        
        if (!$email) {
            return redirect()->route('register');
        }
        
        $userData = session('pending_registration');
        $verificationCode = $this->generateVerificationCode();
        
        Cache::put('email_verification_' . $email, $verificationCode, 600);
        
        try {
            Mail::to($email)->send(new VerificationCodeMail($verificationCode, $userData['name']));
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Unable to send verification email.']);
        }
        
        return back()->with('success', 'New verification code sent to your email.');
    }
    
    // =============================================
    // PASSWORD RESET WITH EMAIL VERIFICATION & ROLE
    // =============================================
    
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }
    
    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'name' => 'required|string',
            'role' => 'required|in:admin,doctor,patient',
        ]);
        
        // Find user by email AND name AND role
        $user = User::where('email', $request->email)
                    ->where('name', $request->name)
                    ->where('role', $request->role)
                    ->first();
        
        if (!$user) {
            return back()->withErrors(['email' => 'No account found with this email, name, and role combination. Please check your information.']);
        }
        
        // For patients, check if account is verified
        if ($user->role == 'patient' && !$user->is_active) {
            return back()->withErrors(['email' => 'Your account is not verified. Please complete email verification first by registering again or contact support.']);
        }
        
        // Generate reset code
        $resetCode = $this->generateVerificationCode();
        
        // Store in cache with role information (expires in 10 minutes)
        Cache::put('password_reset_' . $request->email, [
            'code' => $resetCode,
            'role' => $request->role
        ], 600);
        
        // Store email and role in session
        session(['reset_email' => $request->email, 'reset_role' => $request->role]);
        
        // Send email
        try {
            Mail::to($request->email)->send(new PasswordResetMail($resetCode, $user->name, $request->role));
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Unable to send reset email. Please try again later.']);
        }
        
        return redirect()->route('verify.reset.form')->with('success', 'Password reset code sent to your email.');
    }
    
    public function showVerifyResetForm()
    {
        if (!session('reset_email')) {
            return redirect()->route('password.request');
        }
        return view('auth.verify-reset');
    }
    
    public function verifyResetCode(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|string|size:6',
        ]);
        
        $email = session('reset_email');
        $expectedRole = session('reset_role');
        
        if (!$email || !$expectedRole) {
            return redirect()->route('password.request')->withErrors(['error' => 'Invalid reset session. Please start over.']);
        }
        
        $cachedData = Cache::get('password_reset_' . $email);
        
        if (!$cachedData) {
            return back()->withErrors(['verification_code' => 'Reset code has expired. Please request a new one.']);
        }
        
        // Verify the code and role match
        if ($request->verification_code != $cachedData['code']) {
            return back()->withErrors(['verification_code' => 'Invalid verification code.']);
        }
        
        if ($expectedRole != $cachedData['role']) {
            return back()->withErrors(['verification_code' => 'Role mismatch. Please request a new reset code with correct role.']);
        }
        
        // Mark as verified and proceed to reset password
        session(['reset_verified' => true]);
        Cache::forget('password_reset_' . $email);
        
        return redirect()->route('password.reset.form');
    }
    
    public function showResetPasswordForm()
    {
        if (!session('reset_verified') || !session('reset_email')) {
            return redirect()->route('password.request');
        }
        return view('auth.reset-password');
    }
    
    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);
        
        if (!session('reset_verified') || !session('reset_email')) {
            return redirect()->route('password.request')->withErrors(['error' => 'Invalid reset session.']);
        }
        
        $email = session('reset_email');
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return redirect()->route('login')->withErrors(['email' => 'User not found.']);
        }
        
        $user->update([
            'password' => Hash::make($request->password),
        ]);
        
        // Clear session
        session()->forget(['reset_email', 'reset_role', 'reset_verified']);
        
        return redirect()->route('login')->with('success', 'Password reset successfully. Please login with your new password.');
    }
    
    // =============================================
    // LOGIN
    // =============================================
    
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectToDashboard();
        }
        return view('auth.login');
    }
    
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,doctor,patient',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Check if user is active (email verified) - ONLY for patients
            if ($user->role == 'patient' && !$user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account is not verified. Please check your email for verification code or register again.']);
            }
            
            // Verify role matches selected role
            if ($user->role !== $request->role) {
                Auth::logout();
                return back()->withErrors(['role' => 'Invalid role selected.']);
            }
            
            $request->session()->regenerate();
            
            Session::put('user_id', $user->id);
            Session::put('user_role', $user->role);
            Session::put('user_name', $user->name);
            Session::put('login_time', now());
            
            return $this->redirectToDashboard();
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
    
    public function logout(Request $request)
    {
    // Clear all session data
    Session::flush();
    
    // Logout the user
    Auth::logout();
    
    // Invalidate the session
    $request->session()->invalidate();
    
    // Regenerate CSRF token
    $request->session()->regenerateToken();
    
    // Clear remember me cookie if exists
    if ($request->hasCookie('remember_web')) {
        return redirect('/')->withCookie(\Cookie::forget('remember_web'));
    }
    
    // Prevent browser back button from accessing cached pages
    return redirect('/')->with('success', 'You have been successfully logged out.')
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
        ->header('Pragma', 'no-cache')
        ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }
    
    // =============================================
    // HELPER FUNCTIONS
    // =============================================
    
    private function generateVerificationCode()
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
    
    private function redirectToDashboard()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isDoctor()) {
            return redirect()->route('doctor.dashboard');
        } else {
            return redirect()->route('patient.dashboard');
        }
    }
}