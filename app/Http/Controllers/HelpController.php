<?php

namespace App\Http\Controllers;

class HelpController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $helpContent = match ($user->role) {
            'admin' => [
                'title' => 'Admin Help',
                'sections' => [
                    [
                        'heading' => 'Managing Doctors',
                        'content' => 'Add, edit, and manage doctor profiles, specializations, and availability schedules.',
                    ],
                    [
                        'heading' => 'Managing Patients',
                        'content' => 'View and manage patient records, book appointments, and track patient history.',
                    ],
                    [
                        'heading' => 'Appointments',
                        'content' => 'View all appointments, update statuses, reschedule, or cancel bookings.',
                    ],
                    [
                        'heading' => 'Departments',
                        'content' => 'Create and manage hospital departments and their status.',
                    ],
                    [
                        'heading' => 'Billing & Payments',
                        'content' => 'Track payments, generate invoices, and process refunds.',
                    ],
                    [
                        'heading' => 'Reports',
                        'content' => 'Generate appointment, revenue, and doctor performance reports.',
                    ],
                ],
            ],
            'doctor' => [
                'title' => 'Doctor Help',
                'sections' => [
                    [
                        'heading' => 'Viewing Appointments',
                        'content' => 'Access your daily and upcoming appointments from the dashboard.',
                    ],
                    [
                        'heading' => 'Updating Appointment Status',
                        'content' => 'Mark appointments as confirmed, completed, or cancelled.',
                    ],
                    [
                        'heading' => 'Medical Records',
                        'content' => 'Add and manage medical records for patient visits including diagnoses and notes.',
                    ],
                    [
                        'heading' => 'Prescriptions',
                        'content' => 'Create and manage prescriptions for patients after consultations.',
                    ],
                    [
                        'heading' => 'Patient History',
                        'content' => 'View the complete medical history of your patients.',
                    ],
                ],
            ],
            default => [
                'title' => 'Patient Help',
                'sections' => [
                    [
                        'heading' => 'Booking Appointments',
                        'content' => 'Select a doctor, choose a date and time slot, and book your appointment.',
                    ],
                    [
                        'heading' => 'Viewing Appointments',
                        'content' => 'View your upcoming and past appointments from the dashboard.',
                    ],
                    [
                        'heading' => 'Cancelling Appointments',
                        'content' => 'Cancel an upcoming appointment if needed.',
                    ],
                    [
                        'heading' => 'Payments',
                        'content' => 'View your payment history and pay for confirmed or completed appointments.',
                    ],
                    [
                        'heading' => 'Medical History',
                        'content' => 'Access your past medical records, prescriptions, and diagnoses.',
                    ],
                ],
            ],
        };

        return view('help.index', compact('helpContent'));
    }
}
