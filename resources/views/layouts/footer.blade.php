@auth
    @php
        $user = auth()->user();
        $role = $user->role;
    @endphp
    
    @if($role == 'admin')
        <!-- Admin Footer -->
        <footer class="site-footer footer-admin">
            <div class="footer-wave"></div>
            <div class="footer-inner">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="footer-brand">
                                <i class="fas fa-hospital-user"></i>
                                <span>ClinicSystem Admin</span>
                            </div>
                            <p class="footer-tagline">Complete hospital management solution</p>
                            <div class="mt-2">
                                <span class="footer-badge">
                                    <i class="fas fa-shield-alt me-1"></i> Admin Access
                                </span>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="footer-links">
                                <a href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt me-1"></i> Dashboard</a>
                                <a href="{{ route('admin.doctors.index') }}"><i class="fas fa-user-md me-1"></i> Doctors</a>
                                <a href="{{ route('admin.patients.index') }}"><i class="fas fa-users me-1"></i> Patients</a>
                                <a href="{{ route('admin.appointments.index') }}"><i class="fas fa-calendar-check me-1"></i> Appointments</a>
                                <a href="{{ route('admin.reports.index') }}"><i class="fas fa-chart-line me-1"></i> Reports</a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-6">
                                    <div class="footer-stat">
                                        <div class="footer-stat-number">{{ \App\Models\Doctor::count() }}</div>
                                        <div class="footer-stat-label">Doctors</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="footer-stat">
                                        <div class="footer-stat-number">{{ \App\Models\Patient::count() }}</div>
                                        <div class="footer-stat-label">Patients</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="footer-divider">
                    <div class="footer-bottom">
                        <p class="mb-0">&copy; {{ date('Y') }} Clinic Management System. All rights reserved. | Version 2.0</p>
                    </div>
                </div>
            </div>
        </footer>
        
    @elseif($role == 'doctor')
        <!-- Doctor Footer -->
        <footer class="site-footer footer-doctor">
            <div class="footer-wave"></div>
            <div class="footer-inner">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="footer-brand">
                                <i class="fas fa-stethoscope"></i>
                                <span>ClinicSystem Doctor Portal</span>
                            </div>
                            <p class="footer-tagline">Your dedicated practice management platform</p>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="footer-info-item">
                                <div class="footer-info-icon"><i class="fas fa-user-md"></i></div>
                                <div class="footer-info-text">
                                    <strong>Dr. {{ $user->name }}</strong>
                                    <span>{{ $user->doctor->specialization ?? 'Medical Professional' }}</span>
                                </div>
                            </div>
                            <div class="footer-info-item">
                                <div class="footer-info-icon"><i class="fas fa-calendar-alt"></i></div>
                                <div class="footer-info-text">
                                    <strong>Today's Schedule</strong>
                                    <span>{{ \App\Models\Appointment::where('doctor_id', $user->doctor->id)->whereDate('appointment_date', today())->count() }} appointments today</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="footer-quick-links">
                                <a href="{{ route('doctor.dashboard') }}" class="footer-quick-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                                <a href="{{ route('doctor.appointments.index') }}" class="footer-quick-link"><i class="fas fa-calendar-check"></i> My Appointments</a>
                                <a href="#" class="footer-quick-link"><i class="fas fa-prescription"></i> Prescriptions</a>
                            </div>
                        </div>
                    </div>
                    <hr class="footer-divider">
                    <div class="footer-bottom">
                        <p class="mb-0">&copy; {{ date('Y') }} Clinic Management System. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </footer>
        
    @elseif($role == 'patient')
        <!-- Patient Footer -->
        <footer class="site-footer footer-patient">
            <div class="footer-wave"></div>
            <div class="footer-inner">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="footer-brand">
                                <i class="fas fa-heartbeat"></i>
                                <span>ClinicSystem Patient Portal</span>
                            </div>
                            <p class="footer-tagline">Your health, our priority</p>
                            <div class="health-tip">
                                <p><i class="fas fa-lightbulb"></i> <strong>Health Tip:</strong> Stay hydrated! Drink at least 8 glasses of water daily for optimal health.</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="row">
                                <div class="col-6">
                                    <a href="{{ route('patient.appointments.book') }}" class="footer-action">
                                        <i class="fas fa-calendar-plus"></i> Book Appointment
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('patient.medical-history') }}" class="footer-action outline">
                                        <i class="fas fa-file-medical"></i> History
                                    </a>
                                </div>
                            </div>
                            <div class="mt-3 text-center">
                                <small class="text-muted">Emergency? Call +1-555-0123</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="footer-quick-links">
                                <a href="{{ route('patient.dashboard') }}" class="footer-quick-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                                <a href="{{ route('patient.appointments.index') }}" class="footer-quick-link"><i class="fas fa-calendar-check"></i> My Appointments</a>
                                <a href="{{ route('patient.profile.index') }}" class="footer-quick-link"><i class="fas fa-user-circle"></i> My Profile</a>
                            </div>
                        </div>
                    </div>
                    <hr class="footer-divider">
                    <div class="footer-bottom">
                        <p class="mb-0">&copy; {{ date('Y') }} Clinic Management System. All rights reserved. | <i class="fas fa-heart text-danger"></i> Caring for your health</p>
                    </div>
                </div>
            </div>
        </footer>
    @endif
@else
    <!-- Guest Footer -->
    <footer class="site-footer footer-guest">
        <div class="footer-inner">
            <div class="container">
                <div class="footer-bottom">
                    <p class="mb-0">&copy; {{ date('Y') }} Clinic Management System. All rights reserved.</p>
                    <div class="mt-2">
                        <a href="{{ route('login') }}" class="footer-link mx-2">Login</a>
                        <a href="{{ route('register') }}" class="footer-link mx-2">Register</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
@endauth