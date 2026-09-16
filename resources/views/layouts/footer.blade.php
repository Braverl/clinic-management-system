@auth
<footer class="site-footer footer-auth">
    <div class="footer-wave"></div>
    <div class="footer-inner">
        <div class="container-fluid">
            <div class="row gy-4">
                <!-- Brand column -->
                <div class="col-lg-4">
                    <a class="footer-brand" href="{{ url('/') }}">
                        <img src="{{ asset('logo.svg') }}" alt="ClinicSystem" width="34" height="34" class="rounded-3">
                        <span>Clinic<span class="text-warning">System</span></span>
                    </a>
                    <p class="footer-tagline">Complete clinic &amp; hospital management platform for patients, doctors, and administrators.</p>
                    <div class="footer-badges mt-2">
                        <span class="footer-badge"><i class="fas fa-shield-alt me-1"></i> Secure &amp; Private</span>
                        <span class="footer-badge green"><i class="fas fa-check-circle me-1"></i> 24/7 Access</span>
                    </div>
                </div>

                <!-- Quick links -->
                <div class="col-6 col-lg-3">
                    <h6 class="footer-heading">Quick Links</h6>
                    <div class="footer-links d-flex flex-column">
                        <a href="{{ route('search.index') }}"><i class="fas fa-search me-2"></i>Search</a>
                        <a href="{{ route('chat.index') }}"><i class="fas fa-comments me-2"></i>Messages</a>
                        <a href="{{ route('settings.index') }}"><i class="fas fa-cog me-2"></i>Settings</a>
                        <a href="{{ route('help.index') }}"><i class="fas fa-life-ring me-2"></i>Help Center</a>
                        @if(auth()->user()->isPatient())
                        <a href="{{ route('patient.about') }}"><i class="fas fa-info-circle me-2"></i>About the Clinic</a>
                        @endif
                    </div>
                </div>

                <!-- Role links -->
                <div class="col-6 col-lg-3">
                    <h6 class="footer-heading">
                        @if(auth()->user()->isAdmin())
                            Administration
                        @elseif(auth()->user()->isDoctor())
                            My Practice
                        @else
                            My Health
                        @endif
                    </h6>
                    <div class="footer-links d-flex flex-column">
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.doctors.index') }}"><i class="fas fa-user-md me-2"></i>Doctors</a>
                            <a href="{{ route('admin.patients.index') }}"><i class="fas fa-users me-2"></i>Patients</a>
                            <a href="{{ route('admin.appointments.index') }}"><i class="fas fa-calendar-check me-2"></i>Appointments</a>
                            <a href="{{ route('admin.reports.index') }}"><i class="fas fa-chart-line me-2"></i>Reports</a>
                        @elseif(auth()->user()->isDoctor())
                            <a href="{{ route('doctor.appointments.index') }}"><i class="fas fa-calendar-check me-2"></i>My Appointments</a>
                            <a href="{{ route('doctor.medical-records.index') }}"><i class="fas fa-notes-medical me-2"></i>Medical Records</a>
                            <a href="{{ route('doctor.profile.index') }}"><i class="fas fa-user-md me-2"></i>My Profile</a>
                        @else
                            <a href="{{ route('patient.appointments.book') }}"><i class="fas fa-calendar-plus me-2"></i>Book Appointment</a>
                            <a href="{{ route('patient.appointments.index') }}"><i class="fas fa-calendar-check me-2"></i>My Appointments</a>
                            <a href="{{ route('patient.medical-history') }}"><i class="fas fa-file-medical me-2"></i>Medical History</a>
                            <a href="{{ route('patient.payments.index') }}"><i class="fas fa-credit-card me-2"></i>Bills &amp; Payments</a>
                        @endif
                    </div>
                </div>

                <!-- Contact / About -->
                <div class="col-lg-2">
                    <h6 class="footer-heading">Contact</h6>
                    <div class="footer-contact">
                        <span><i class="fas fa-map-marker-alt"></i> Kigali, Rwanda</span>
                        <span><i class="fas fa-envelope"></i> support@clinicsystem.rw</span>
                        <span><i class="fas fa-phone-alt"></i> +250 726 000 000</span>
                    </div>
                    @if(auth()->user()->isPatient())
                    <div class="mt-3">
                        <a href="{{ route('patient.about') }}" class="btn btn-sm btn-outline-light w-100">
                            <i class="fas fa-star me-1"></i> About Us
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <hr class="footer-divider">

            <div class="footer-bottom d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                <p class="mb-0">&copy; {{ date('Y') }} Clinic Management System. All rights reserved.</p>
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('help.index') }}" class="footer-link">Help</a>
                    <a href="{{ route('settings.index') }}" class="footer-link">Settings</a>
                    <span class="footer-version"><i class="fas fa-code-branch me-1"></i>v1.0.0</span>
                </div>
            </div>
        </div>
    </div>
</footer>
@else
<footer class="site-footer footer-guest">
    <div class="footer-wave"></div>
    <div class="footer-inner">
        <div class="container-fluid">
            <div class="row gy-3 align-items-center">
                <div class="col-md-6">
                    <a class="footer-brand" href="{{ url('/') }}">
                        <img src="{{ asset('logo.svg') }}" alt="ClinicSystem" width="30" height="30" class="rounded-3">
                        <span>Clinic<span class="text-warning">System</span></span>
                    </a>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="{{ route('login') }}" class="footer-link mx-2"><i class="fas fa-sign-in-alt me-1"></i>Login</a>
                    <a href="{{ route('register') }}" class="footer-link mx-2"><i class="fas fa-user-plus me-1"></i>Register</a>
                </div>
            </div>
            <hr class="footer-divider">
            <div class="footer-bottom">
                <p class="mb-0 text-center">&copy; {{ date('Y') }} Clinic Management System. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>
@endauth