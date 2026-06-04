<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Prevent browser caching -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta http-equiv="Cache-Control" content="no-store">
    <title>@yield('title', 'Clinic Management System')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* Additional layout fix for footer only - preserves original sidebar */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        
        .app-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        .app-row {
            display: flex;
            flex: 1;
        }
        
        /* Original sidebar styles preserved */
        .sidebar {
            width: 260px;
            background: white;
            box-shadow: var(--shadow-lg);
            border-right: 1px solid var(--gray-100);
            position: relative;
            flex-shrink: 0;
        }
        
        .sidebar .nav-link {
            color: var(--gray-600) !important;
            padding: 0.7rem 1.25rem !important;
            margin: 0.15rem 0.75rem;
            border-radius: var(--radius-xl);
            font-weight: 500;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all var(--duration-base) var(--ease-out);
        }
        
        .sidebar .nav-link:hover {
            color: var(--brand-700) !important;
            background: var(--brand-50);
            transform: translateX(6px);
        }
        
        .sidebar .nav-link.active {
            background: var(--grad-brand) !important;
            color: white !important;
            box-shadow: var(--shadow-brand);
        }
        
        /* Content area */
        .app-content {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }
        
        .app-main {
            flex: 1;
            padding: 0 1.5rem 1.5rem;
        }
        
        /* Footer - full width */
        .app-footer {
            width: 100%;
            margin-top: auto;
        }
        
        /* Mobile responsive */
        @media (max-width: 992px) {
            .sidebar {
                position: fixed;
                left: -280px;
                top: 0;
                bottom: 0;
                z-index: 1050;
                transition: left 0.3s ease;
            }
            
            .sidebar.show {
                left: 0;
            }
            
            .app-row {
                display: block;
            }
            
            .app-content {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    @auth
    <div class="app-wrapper">
        <!-- Navbar - Original -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
            <div class="container-fluid">
                <button class="btn btn-link text-white d-lg-none me-2" type="button" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <a class="navbar-brand fw-bold" href="{{ url('/') }}">
                    <i class="fas fa-hospital-user me-2"></i>ClinicSystem
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                @if(auth()->user()->profile_image)
                                    <img src="{{ Storage::url(auth()->user()->profile_image) }}" class="rounded-circle me-1" width="30" height="30" style="object-fit: cover;">
                                @else
                                    <i class="fas fa-user-circle me-1"></i>
                                @endif
                                {{ auth()->user()->name }}
                                <span class="badge bg-light text-dark ms-1">
                                    @if(auth()->user()->isAdmin())
                                        Admin
                                    @elseif(auth()->user()->isDoctor())
                                        Doctor
                                    @else
                                        Patient
                                    @endif
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : (auth()->user()->isDoctor() ? route('doctor.dashboard') : route('patient.dashboard')) }}"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                                @if(auth()->user()->isAdmin())
                                <li><a class="dropdown-item" href="{{ route('admin.profile.index') }}"><i class="fas fa-user-shield me-2"></i>Profile</a></li>
                                @elseif(auth()->user()->isDoctor())
                                <li><a class="dropdown-item" href="{{ route('doctor.profile.index') }}"><i class="fas fa-user-md me-2"></i>Profile</a></li>
                                @else
                                <li><a class="dropdown-item" href="{{ route('patient.profile.index') }}"><i class="fas fa-user me-2"></i>Profile</a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" id="logout-form">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Sidebar + Content Row -->
        <div class="app-row">
            <!-- Sidebar - Original styling preserved -->
            @php
                $sidebarLinks = [];
                if(auth()->user()->isAdmin()) {
                    $sidebarLinks = [
                        ['url' => route('admin.dashboard'), 'icon' => 'fas fa-tachometer-alt', 'label' => 'Dashboard'],
                        ['url' => route('admin.doctors.index'), 'icon' => 'fas fa-user-md', 'label' => 'Doctors'],
                        ['url' => route('admin.patients.index'), 'icon' => 'fas fa-users', 'label' => 'Patients'],
                        ['url' => route('admin.appointments.index'), 'icon' => 'fas fa-calendar-check', 'label' => 'Appointments'],
                        ['url' => route('admin.departments.index'), 'icon' => 'fas fa-building', 'label' => 'Departments'],
                        ['url' => route('admin.reports.index'), 'icon' => 'fas fa-chart-line', 'label' => 'Reports'],
                        ['url' => route('admin.profile.index'), 'icon' => 'fas fa-user-shield', 'label' => 'My Profile'],
                    ];
                } elseif(auth()->user()->isDoctor()) {
                    $sidebarLinks = [
                        ['url' => route('doctor.dashboard'), 'icon' => 'fas fa-tachometer-alt', 'label' => 'Dashboard'],
                        ['url' => route('doctor.appointments.index'), 'icon' => 'fas fa-calendar-check', 'label' => 'Appointments'],
                        ['url' => route('doctor.profile.index'), 'icon' => 'fas fa-user-md', 'label' => 'My Profile'],
                    ];
                } elseif(auth()->user()->isPatient()) {
                    $sidebarLinks = [
                        ['url' => route('patient.dashboard'), 'icon' => 'fas fa-tachometer-alt', 'label' => 'Dashboard'],
                        ['url' => route('patient.appointments.index'), 'icon' => 'fas fa-calendar-check', 'label' => 'My Appointments'],
                        ['url' => route('patient.appointments.book'), 'icon' => 'fas fa-plus-circle', 'label' => 'Book Appointment'],
                        ['url' => route('patient.medical-history'), 'icon' => 'fas fa-file-medical', 'label' => 'Medical History'],
                        ['url' => route('patient.profile.index'), 'icon' => 'fas fa-user-circle', 'label' => 'Profile'],
                    ];
                }
            @endphp
            
            <div class="sidebar" id="sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        @foreach($sidebarLinks as $link)
                        <li class="nav-item">
                            <a class="nav-link {{ request()->url() == $link['url'] ? 'active bg-primary text-white' : '' }}" href="{{ $link['url'] }}">
                                <i class="{{ $link['icon'] }} me-2"></i>{{ $link['label'] }}
                            </a>
                        </li>
                        @endforeach
                        <li class="nav-item mt-3">
                            <hr>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="nav-link text-danger" style="background: none; border: none; width: 100%; text-align: left;">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Content Area -->
            <div class="app-content">
                <main class="app-main">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @yield('content')
                </main>
            </div>
        </div>
        
        <!-- Footer - FULL WIDTH (outside the row) -->
        @include('layouts.footer')
    </div>
    @else
        <!-- Guest Layout -->
        <div class="guest-wrapper">
            <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
                <div class="container-fluid">
                    <a class="navbar-brand fw-bold" href="{{ url('/') }}">
                        <i class="fas fa-hospital-user me-2"></i>ClinicSystem
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#guestNavbar">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="guestNavbar">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Register</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
            
            <main class="guest-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @yield('content')
            </main>
            
            @include('layouts.footer')
        </div>
    @endauth

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
        
        // Confirm delete
        $(document).on('click', '.confirm-delete', function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
            
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
        
        // Confirm logout with SweetAlert
        $(document).on('click', '.dropdown-item.text-danger', function(e) {
            e.preventDefault();
            const form = $('#logout-form');
            
            Swal.fire({
                title: 'Confirm Logout',
                text: 'Are you sure you want to logout?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, Logout',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
        
        // Mobile sidebar toggle
        $('#sidebarToggle').on('click', function() {
            $('#sidebar').toggleClass('show');
        });
        
        // Close sidebar when clicking outside on mobile
        $(document).on('click', function(e) {
            if ($(window).width() <= 992) {
                if (!$(e.target).closest('#sidebar').length && !$(e.target).closest('#sidebarToggle').length) {
                    $('#sidebar').removeClass('show');
                }
            }
        });
        
        // Session timeout warning (30 minutes)
        let sessionTimeout;
        const resetSessionTimer = () => {
            clearTimeout(sessionTimeout);
            sessionTimeout = setTimeout(() => {
                Swal.fire({
                    title: 'Session Expiring Soon',
                    text: 'Your session will expire in 1 minute due to inactivity. Click OK to stay logged in.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Stay Logged In',
                    cancelButtonText: 'Logout'
                }).then((result) => {
                    if (!result.isConfirmed) {
                        $('#logout-form').submit();
                    }
                });
            }, 29 * 60 * 1000);
        };
        
        resetSessionTimer();
        $(document).on('mousemove keypress click scroll', resetSessionTimer);

        
    // Prevent back button after logout
    (function() {
        // Check if user is not logged in (by checking if there's no auth user element)
        const isLoggedIn = document.querySelector('.navbar .dropdown-toggle') !== null;
        
        if (!isLoggedIn) {
            // Push a new state to prevent back navigation
            history.pushState(null, null, location.href);
            window.addEventListener('popstate', function () {
                history.pushState(null, null, location.href);
                // Redirect to home page if someone tries to go back
                window.location.href = '/';
            });
        }
    })();
    
    // Also check on page load for authenticated state
    window.addEventListener('pageshow', function(event) {
        // If page is loaded from cache (back/forward)
        if (event.persisted) {
            // Check if user should be logged in
            const userElement = document.querySelector('.navbar .dropdown-toggle');
            if (!userElement) {
                // User is not logged in, redirect to home
                window.location.href = '/';
            }
        }
    });

    </script>
    
    @stack('scripts')
</body>
</html>