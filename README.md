# Clinic Management System

A full-featured, production-style clinic/hospital management system built with **Laravel 12**. It connects healthcare providers (admins & doctors) with patients through online appointment booking, digital medical records, invoicing, payments, and PDF reporting.

## Features

### Patients (Customers)
- Self-registration with **email verification** and secure password reset
- Online appointment booking with live doctor availability & 30-minute time slots
- View / cancel / reschedule appointments
- Medical history with diagnoses, prescriptions, vitals & BMI
- Pay consultation fees online (card / online / insurance / cash) with automatic PDF invoices
- Real-time notifications (booking confirmations, status updates, payments)
- Full profile & password management

### Doctors (Providers)
- Role dashboard with today's / upcoming / pending appointments
- Confirm or complete appointments, record cancellations
- Create & update medical records (diagnosis, symptoms, vitals)
- Write prescriptions per medical record
- Browse patient medical histories; manage own profile
- Receive notifications for new bookings & reschedules

### Administrators
- Complete doctor / patient / department management with activate-deactivate toggles
- All-appointments management: status changes, rescheduling, search & filters
- **Book appointments on behalf of patients**
- Full **billing & payments** panel with refunds and PDF invoice downloads
- Revenue & appointment analytics dashboard (last 7 days)
- PDF reports: appointment summaries, revenue by year/month, doctor workloads
- Email-based staff account setup

## Technology Stack

- **Backend:** Laravel 12 (PHP 8.2+), MySQL
- **Frontend:** Blade templates, Bootstrap 5, Font Awesome, Chart.js, SweetAlert2, jQuery
- **PDF:** Barryvdh/laravel-dompdf
- **Build:** Vite (Laravel Breeze asset pipeline)

## Installation

### Requirements
- PHP >= 8.2
- Composer
- MySQL (or MariaDB)
- Node.js & npm

### Setup
```bash
# 1. Install PHP dependencies
composer install

# 2. Environment configuration
copy .env.example .env        # Windows
php artisan key:generate

# 3. Configure your database in .env, then:
php artisan migrate --seed

# 4. Install & build front-end assets
npm install
npm run build

# 5. Serve the application
php artisan serve
```

> A local database dump for demo data is available on request or from your local backup; it is intentionally **not** included in this repository (the dump contains personally-identifiable account data). To start fresh, run `php artisan migrate --seed` instead.

### Demo Credentials
| Role     | Email                       | Password |
| -------- | --------------------------- | -------- |
| Admin    | admin@clinicsystem.com      | password |
| Doctor   | james.wilson@clinic.com     | password |
| Patient  | emily.johnson@email.com     | password |

> **Local mail:** registration & password-reset emails are sent via SMTP. Configure `MAIL_*` values in `.env` (e.g. a Gmail app password) so verification emails can be delivered.

## Email Features

- Registration requires **email verification** (6-digit code, 10 min expiry, resendable)
- **Password reset** via email code with name + role confirmation
- SMTP-based mails with branded Blade templates

## Security

- Role-based middleware (`admin`, `doctor`, `patient`) protects all routes
- Ownership checks prevent patients/doctors from accessing other users' data
- Email verification gate for patient registration
- No-cache headers on all web responses; session invalidation on logout
- CSRF protection on all forms; validated inputs throughout

## Project Structure

```
app/
  Http/Controllers/
    AuthController.php        # registration, login, verification, reset
    NotificationController.php
    PaymentController.php     # billing & invoice engine
    Admin/                    # dashboard, doctors, patients, appointments,
                              # departments, reports, profile + patient booking
    Doctor/                   # dashboard, appointments, medical records, profile
    Patient/                  # dashboard, appointments, profile, medical history
  Models/                     # User, Doctor, Patient, Appointment, Department,
                              # MedicalRecord, Prescription, Schedule, Payment,
                              # Invoice, Notification
database/migrations/          # full schema
database/seeders/             # departments, users, appointments
resources/views/              # Blade views for every role
routes/web.php                # all application routes
```

## Roles & Permissions

| Capability                     | Patient | Doctor | Admin |
| ------------------------------ | :-----: | :----: | :---: |
| Book / cancel / reschedule     |   ✅    |        |   ✅  |
| Manage appointments status     |         |   ✅   |   ✅  |
| Medical records & prescriptions|         |   ✅   |   ✅  |
| Patients & doctors management  |         |        |   ✅  |
| Departments management         |         |        |   ✅  |
| Billing / payments / invoices  |   ✅    |        |   ✅  |
| PDF reports                    |         |        |   ✅  |
| Notifications                  |   ✅    |   ✅   |   ✅  |

## License

MIT License. This project is provided for demonstration and production use.