@extends('layouts.app')

@section('title', 'Welcome to Clinic System')

@section('content')
<div class="container mt-5">
    <!-- Hero Section -->
    <div class="row justify-content-center">
        <div class="col-md-10 text-center">
            <div class="card border-0 shadow-lg bg-gradient-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body py-5">
                    <i class="fas fa-hospital-user fa-5x mb-4"></i>
                    <h1 class="display-4 fw-bold mb-3">Welcome to Clinic Management System</h1>
                    <p class="lead mb-4">Your Health, Our Priority - Book appointments with top doctors easily</p>
                    <div class="mt-4">
                        <a href="{{ route('login') }}" class="btn btn-light btn-lg me-3">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-user-plus me-2"></i>Register
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="row mt-5 g-4">
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm text-center">
                <div class="card-body">
                    <i class="fas fa-calendar-check fa-3x text-primary mb-3"></i>
                    <h4>Easy Appointment</h4>
                    <p class="text-muted">Book appointments with specialist doctors online</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm text-center">
                <div class="card-body">
                    <i class="fas fa-user-md fa-3x text-primary mb-3"></i>
                    <h4>Expert Doctors</h4>
                    <p class="text-muted">Experienced and qualified medical professionals</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm text-center">
                <div class="card-body">
                    <i class="fas fa-file-medical fa-3x text-primary mb-3"></i>
                    <h4>Digital Records</h4>
                    <p class="text-muted">Access your medical history anytime, anywhere</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection