<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DoctorController as AdminDoctorController;
use App\Http\Controllers\Admin\PatientController as AdminPatientController;
use App\Http\Controllers\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\Admin\DepartmentController as AdminDepartmentController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;

use App\Http\Controllers\Doctor\DashboardController as DoctorDashboardController;
use App\Http\Controllers\Doctor\AppointmentController as DoctorAppointmentController;
use App\Http\Controllers\Doctor\MedicalRecordController as DoctorMedicalRecordController;
use App\Http\Controllers\Doctor\ProfileController as DoctorProfileController;

use App\Http\Controllers\Patient\DashboardController as PatientDashboardController;
use App\Http\Controllers\Patient\AppointmentController as PatientAppointmentController;
use App\Http\Controllers\Patient\ProfileController as PatientProfileController;

// =============================================
// PUBLIC
// =============================================
Route::get('/', function () {
    return view('welcome');
})->name('home');

// =============================================
// AUTHENTICATION
// =============================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:3,1');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Email verification
Route::middleware('guest')->group(function () {
    Route::get('/verify-email', [AuthController::class, 'showVerificationForm'])->name('verify.email.form');
    Route::post('/verify-email', [AuthController::class, 'verifyEmail'])->name('verify.email');
    Route::get('/resend-verification', [AuthController::class, 'resendVerificationCode'])->name('resend.verification');
});

// Password reset
Route::middleware('guest')->group(function () {
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetCode'])->name('password.send.code')->middleware('throttle:3,1');
    Route::get('/verify-reset', [AuthController::class, 'showVerifyResetForm'])->name('verify.reset.form');
    Route::post('/verify-reset', [AuthController::class, 'verifyResetCode'])->name('verify.reset.code')->middleware('throttle:5,1');
    Route::get('/reset-password', [AuthController::class, 'showResetPasswordForm'])->name('password.reset.form');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset')->middleware('throttle:5,1');
});

// =============================================
// SHARED AUTHENTICATED FEATURES
// =============================================
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.mark-read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');

    // Settings & Help (all roles)
    Route::get('/settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');

    Route::get('/help', [App\Http\Controllers\HelpController::class, 'index'])->name('help.index');

    // Search (all roles)
    Route::get('/search', [App\Http\Controllers\SearchController::class, 'index'])->name('search.index');
    Route::get('/search/live', [App\Http\Controllers\SearchController::class, 'globalSearch'])->name('search.live');

    // Chat (all roles)
    Route::get('/chat', [App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/conversations', [App\Http\Controllers\ChatController::class, 'getConversations'])->name('chat.conversations');
    Route::get('/chat/conversations/{conversation}/messages', [App\Http\Controllers\ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/chat/send', [App\Http\Controllers\ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('/chat/edit', [App\Http\Controllers\ChatController::class, 'editMessage'])->name('chat.edit');
    Route::post('/chat/unsend/{message}', [App\Http\Controllers\ChatController::class, 'unsendMessage'])->name('chat.unsend');
    Route::post('/chat/delete', [App\Http\Controllers\ChatController::class, 'deleteMessage'])->name('chat.delete');
    Route::get('/chat/unread-count', [App\Http\Controllers\ChatController::class, 'unreadCount'])->name('chat.unread-count');
    Route::get('/chat/search-users', [App\Http\Controllers\ChatController::class, 'searchUsers'])->name('chat.search-users');

    Route::get('/get-doctor-schedule/{doctorId}/{date}', [PatientAppointmentController::class, 'getDoctorSchedule'])->name('get-doctor-schedule');
});

// =============================================
// ADMIN
// =============================================
Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Doctor management
    Route::resource('doctors', AdminDoctorController::class)->except(['show']);
    Route::post('/doctors/{doctor}/toggle-status', [AdminDoctorController::class, 'toggleStatus'])->name('doctors.toggle-status');

    // Patient management (including admin-side appointment booking)
    Route::get('/patients', [AdminPatientController::class, 'index'])->name('patients.index');
    Route::get('/patients/create', [AdminPatientController::class, 'create'])->name('patients.create');
    Route::post('/patients', [AdminPatientController::class, 'store'])->name('patients.store');
    Route::get('/patients/{patient}', [AdminPatientController::class, 'show'])->name('patients.show');
    Route::get('/patients/{patient}/edit', [AdminPatientController::class, 'edit'])->name('patients.edit');
    Route::put('/patients/{patient}', [AdminPatientController::class, 'update'])->name('patients.update');
    Route::delete('/patients/{patient}', [AdminPatientController::class, 'destroy'])->name('patients.destroy');
    Route::post('/patients/{patient}/toggle-status', [AdminPatientController::class, 'toggleStatus'])->name('patients.toggle-status');

    Route::get('/patients/{patient}/appointments', [AdminPatientController::class, 'appointments'])->name('patients.appointments.index');
    Route::get('/patients/{patient}/appointments/create', [AdminPatientController::class, 'bookAppointment'])->name('patients.appointments.create');
    Route::post('/patients/{patient}/appointments', [AdminPatientController::class, 'storeAppointment'])->name('patients.appointments.store');
    Route::get('/patients/{patient}/appointments/{appointment}', [AdminPatientController::class, 'showAppointment'])->name('patients.appointments.show');

    // Appointment management
    Route::get('/appointments', [AdminAppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/{appointment}', [AdminAppointmentController::class, 'show'])->name('appointments.show');
    Route::post('/appointments/{appointment}/update-status', [AdminAppointmentController::class, 'updateStatus'])->name('appointments.update-status');
    Route::post('/appointments/{appointment}/reschedule', [AdminAppointmentController::class, 'reschedule'])->name('appointments.reschedule');
    Route::delete('/appointments/{appointment}', [AdminAppointmentController::class, 'destroy'])->name('appointments.destroy');

    // Department management
    Route::get('/departments', [AdminDepartmentController::class, 'index'])->name('departments.index');
    Route::get('/departments/create', [AdminDepartmentController::class, 'create'])->name('departments.create');
    Route::post('/departments', [AdminDepartmentController::class, 'store'])->name('departments.store');
    Route::get('/departments/{department}/edit', [AdminDepartmentController::class, 'edit'])->name('departments.edit');
    Route::put('/departments/{department}', [AdminDepartmentController::class, 'update'])->name('departments.update');
    Route::delete('/departments/{department}', [AdminDepartmentController::class, 'destroy'])->name('departments.destroy');
    Route::post('/departments/{department}/toggle-status', [AdminDepartmentController::class, 'toggleStatus'])->name('departments.toggle-status');

    // Billing / payments
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::get('/payments/{payment}/invoice', [PaymentController::class, 'invoice'])->name('payments.invoice');
    Route::post('/payments/{payment}/refund', [PaymentController::class, 'refund'])->name('payments.refund');

    // Reports
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/appointments', [AdminReportController::class, 'appointmentReport'])->name('reports.appointments');
    Route::post('/reports/revenue', [AdminReportController::class, 'revenueReport'])->name('reports.revenue');
    Route::post('/reports/doctors', [AdminReportController::class, 'doctorReport'])->name('reports.doctors');

    // Profile
    Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [AdminProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [AdminProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/change-password', [AdminProfileController::class, 'changePassword'])->name('profile.change-password');
});

// =============================================
// DOCTOR
// =============================================
Route::prefix('doctor')->middleware(['auth', 'role:doctor'])->name('doctor.')->group(function () {
    Route::get('/dashboard', [DoctorDashboardController::class, 'index'])->name('dashboard');

    Route::get('/appointments', [DoctorAppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/{appointment}', [DoctorAppointmentController::class, 'show'])->name('appointments.show');
    Route::post('/appointments/{appointment}/update-status', [DoctorAppointmentController::class, 'updateStatus'])->name('appointments.update-status');
    Route::post('/appointments/{appointment}/medical-record', [DoctorAppointmentController::class, 'addMedicalRecord'])->name('appointments.add-medical-record');
    Route::post('/appointments/{appointment}/prescription', [DoctorAppointmentController::class, 'addPrescription'])->name('appointments.add-prescription');

    Route::get('/patients/{patient}/history', [DoctorAppointmentController::class, 'patientHistory'])->name('patients.history');

    Route::get('/medical-records', [DoctorMedicalRecordController::class, 'index'])->name('medical-records.index');
    Route::get('/medical-records/{medicalRecord}', [DoctorMedicalRecordController::class, 'show'])->name('medical-records.show');
    Route::get('/medical-records/{medicalRecord}/edit', [DoctorMedicalRecordController::class, 'edit'])->name('medical-records.edit');
    Route::put('/medical-records/{medicalRecord}', [DoctorMedicalRecordController::class, 'update'])->name('medical-records.update');

    Route::get('/profile', [DoctorProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [DoctorProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [DoctorProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/change-password', [DoctorProfileController::class, 'changePassword'])->name('profile.change-password');
});

// =============================================
// PATIENT
// =============================================
Route::prefix('patient')->middleware(['auth', 'role:patient'])->name('patient.')->group(function () {
    Route::get('/dashboard', [PatientDashboardController::class, 'index'])->name('dashboard');

    Route::get('/appointments', [PatientAppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/book', [PatientAppointmentController::class, 'create'])->name('appointments.book');
    Route::post('/appointments', [PatientAppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments/{appointment}', [PatientAppointmentController::class, 'show'])->name('appointments.show');
    Route::post('/appointments/{appointment}/cancel', [PatientAppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::get('/appointments/{appointment}/reschedule', [PatientAppointmentController::class, 'reschedule'])->name('appointments.reschedule');
    Route::post('/appointments/{appointment}/reschedule', [PatientAppointmentController::class, 'updateReschedule'])->name('appointments.update-reschedule');

    // Billing / payments
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/create/{appointment}', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::get('/payments/{payment}/invoice', [PaymentController::class, 'invoice'])->name('payments.invoice');

    // Profile
    Route::get('/profile', [PatientProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [PatientProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [PatientProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/change-password', [PatientProfileController::class, 'changePassword'])->name('profile.change-password');
    Route::get('/medical-history', [PatientProfileController::class, 'medicalHistory'])->name('medical-history');
    Route::get('/about', [PatientProfileController::class, 'about'])->name('about');
});