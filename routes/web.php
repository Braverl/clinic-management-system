<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DoctorController as AdminDoctorController;
use App\Http\Controllers\Admin\PatientController as AdminPatientController;
use App\Http\Controllers\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\Admin\DepartmentController as AdminDepartmentController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Doctor\DashboardController as DoctorDashboardController;
use App\Http\Controllers\Doctor\AppointmentController as DoctorAppointmentController;
use App\Http\Controllers\Patient\DashboardController as PatientDashboardController;
use App\Http\Controllers\Patient\AppointmentController as PatientAppointmentController;
use App\Http\Controllers\Patient\ProfileController as PatientProfileController;

// Public Routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Admin Routes
Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Doctor Management (with proper DELETE)
    Route::get('/doctors', [AdminDoctorController::class, 'index'])->name('doctors.index');
    Route::get('/doctors/create', [AdminDoctorController::class, 'create'])->name('doctors.create');
    Route::post('/doctors', [AdminDoctorController::class, 'store'])->name('doctors.store');
    Route::get('/doctors/{doctor}/edit', [AdminDoctorController::class, 'edit'])->name('doctors.edit');
    Route::put('/doctors/{doctor}', [AdminDoctorController::class, 'update'])->name('doctors.update');
    Route::delete('/doctors/{doctor}', [AdminDoctorController::class, 'destroy'])->name('doctors.destroy');
    Route::post('/doctors/{doctor}/toggle-status', [AdminDoctorController::class, 'toggleStatus'])->name('doctors.toggle-status');
    
    // Patient Management
    Route::get('/patients', [AdminPatientController::class, 'index'])->name('patients.index');
    Route::get('/patients/create', [AdminPatientController::class, 'create'])->name('patients.create');
    Route::post('/patients', [AdminPatientController::class, 'store'])->name('patients.store');
    Route::get('/patients/{patient}', [AdminPatientController::class, 'show'])->name('patients.show');
    Route::get('/patients/{patient}/edit', [AdminPatientController::class, 'edit'])->name('patients.edit');
    Route::put('/patients/{patient}', [AdminPatientController::class, 'update'])->name('patients.update');
    Route::delete('/patients/{patient}', [AdminPatientController::class, 'destroy'])->name('patients.destroy');
    Route::post('/patients/{patient}/toggle-status', [AdminPatientController::class, 'toggleStatus'])->name('patients.toggle-status');
    
    // Appointment Management
    Route::get('/appointments', [AdminAppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/{appointment}', [AdminAppointmentController::class, 'show'])->name('appointments.show');
    Route::post('/appointments/{appointment}/update-status', [AdminAppointmentController::class, 'updateStatus'])->name('appointments.update-status');
    Route::post('/appointments/{appointment}/reschedule', [AdminAppointmentController::class, 'reschedule'])->name('appointments.reschedule');
    Route::delete('/appointments/{appointment}', [AdminAppointmentController::class, 'destroy'])->name('appointments.destroy');
    
    // Department Management
    Route::get('/departments', [AdminDepartmentController::class, 'index'])->name('departments.index');
    Route::get('/departments/create', [AdminDepartmentController::class, 'create'])->name('departments.create');
    Route::post('/departments', [AdminDepartmentController::class, 'store'])->name('departments.store');
    Route::get('/departments/{department}/edit', [AdminDepartmentController::class, 'edit'])->name('departments.edit');
    Route::put('/departments/{department}', [AdminDepartmentController::class, 'update'])->name('departments.update');
    Route::delete('/departments/{department}', [AdminDepartmentController::class, 'destroy'])->name('departments.destroy');
    Route::post('/departments/{department}/toggle-status', [AdminDepartmentController::class, 'toggleStatus'])->name('departments.toggle-status');
    
    // Reports
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/appointments', [AdminReportController::class, 'appointmentReport'])->name('reports.appointments');
    Route::post('/reports/revenue', [AdminReportController::class, 'revenueReport'])->name('reports.revenue');
    Route::post('/reports/doctors', [AdminReportController::class, 'doctorReport'])->name('reports.doctors');
});

// Doctor Routes
Route::prefix('doctor')->middleware(['auth', 'role:doctor'])->name('doctor.')->group(function () {
    Route::get('/dashboard', [DoctorDashboardController::class, 'index'])->name('dashboard');
    Route::get('/appointments', [DoctorAppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/{appointment}', [DoctorAppointmentController::class, 'show'])->name('appointments.show');
    Route::post('/appointments/{appointment}/update-status', [DoctorAppointmentController::class, 'updateStatus'])->name('appointments.update-status');
    Route::post('/appointments/{appointment}/medical-record', [DoctorAppointmentController::class, 'addMedicalRecord'])->name('appointments.add-medical-record');
    Route::post('/appointments/{appointment}/prescription', [DoctorAppointmentController::class, 'addPrescription'])->name('appointments.add-prescription');
    Route::get('/patients/{patient}/history', [DoctorAppointmentController::class, 'patientHistory'])->name('patients.history');
});

// Patient Routes
Route::prefix('patient')->middleware(['auth', 'role:patient'])->name('patient.')->group(function () {
    Route::get('/dashboard', [PatientDashboardController::class, 'index'])->name('dashboard');
    Route::get('/appointments', [PatientAppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/book', [PatientAppointmentController::class, 'create'])->name('appointments.book');
    Route::post('/appointments', [PatientAppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments/{appointment}', [PatientAppointmentController::class, 'show'])->name('appointments.show');
    Route::post('/appointments/{appointment}/cancel', [PatientAppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::get('/appointments/{appointment}/reschedule', [PatientAppointmentController::class, 'reschedule'])->name('appointments.reschedule');
    Route::post('/appointments/{appointment}/reschedule', [PatientAppointmentController::class, 'updateReschedule'])->name('appointments.update-reschedule');
    Route::get('/get-doctor-schedule/{doctorId}/{date}', [PatientAppointmentController::class, 'getDoctorSchedule'])->name('get-doctor-schedule');
    
    // Profile
    Route::get('/profile', [PatientProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [PatientProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [PatientProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/change-password', [PatientProfileController::class, 'changePassword'])->name('profile.change-password');
    Route::get('/medical-history', [PatientProfileController::class, 'medicalHistory'])->name('medical-history');
});

// Email Verification Routes
Route::get('/verify-email', [AuthController::class, 'showVerificationForm'])->name('verify.email.form');
Route::post('/verify-email', [AuthController::class, 'verifyEmail'])->name('verify.email');
Route::get('/resend-verification', [AuthController::class, 'resendVerificationCode'])->name('resend.verification');

// Password Reset Routes
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetCode'])->name('password.send.code');
Route::get('/verify-reset', [AuthController::class, 'showVerifyResetForm'])->name('verify.reset.form');
Route::post('/verify-reset', [AuthController::class, 'verifyResetCode'])->name('verify.reset.code');
Route::get('/reset-password', [AuthController::class, 'showResetPasswordForm'])->name('password.reset.form');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');

Route::get('/test-mail', function () {
    try {
        Mail::raw('Test email from Clinic System', function ($message) {
            $message->to('placidebraverl@gmail.com')
                    ->subject('Test Email');
        });
        return 'Email sent successfully! Check your inbox.';
    } catch (Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

// Admin Profile Routes
Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    // ... existing routes ...
    
    // Profile Routes
    Route::get('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/change-password', [App\Http\Controllers\Admin\ProfileController::class, 'changePassword'])->name('profile.change-password');
});

// Doctor Profile Routes
Route::prefix('doctor')->middleware(['auth', 'role:doctor'])->name('doctor.')->group(function () {
    // ... existing routes ...
    
    // Profile Routes
    Route::get('/profile', [App\Http\Controllers\Doctor\ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [App\Http\Controllers\Doctor\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [App\Http\Controllers\Doctor\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/change-password', [App\Http\Controllers\Doctor\ProfileController::class, 'changePassword'])->name('profile.change-password');
});