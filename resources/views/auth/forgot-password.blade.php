@extends('layouts.app')

@section('title', 'Forgot Password')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-warning text-white text-center py-4">
                    <h3 class="mb-0"><i class="fas fa-key me-2"></i>Forgot Password</h3>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $error)
                                <p class="mb-0">{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif
                    
                    <p>Enter your registered email, full name, and select your role to receive a password reset code.</p>
                    
                    <form method="POST" action="{{ route('password.send.code') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Select Your Role</label>
                            <select name="role" id="role" class="form-select" required>
                                <option value="">Select Role</option>
                                <option value="patient">👤 Patient</option>
                                <option value="doctor">👨‍⚕️ Doctor</option>
                                <option value="admin">👑 Admin</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Enter your full name" value="{{ old('name') }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="Enter your registered email" value="{{ old('email') }}" required>
                        </div>
                        
                        <button type="submit" class="btn btn-warning w-100 py-2">
                            <i class="fas fa-paper-plane me-2"></i>Send Reset Code
                        </button>
                        
                        <div class="text-center mt-3">
                            <a href="{{ route('login') }}" class="text-muted">
                                <i class="fas fa-arrow-left me-1"></i>Back to Login
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('role').addEventListener('change', function() {
        const role = this.value;
        const nameField = document.querySelector('input[name="name"]');
        const emailField = document.querySelector('input[name="email"]');
        
        if (role === 'admin') {
            nameField.value = 'Admin User';
            emailField.value = 'admin@clinicsystem.com';
        } else if (role === 'doctor') {
            nameField.value = 'Dr. James Wilson';
            emailField.value = 'james.wilson@clinic.com';
        } else if (role === 'patient') {
            nameField.value = 'John Smith';
            emailField.value = 'john.smith@email.com';
        } else {
            nameField.value = '';
            emailField.value = '';
        }
    });
</script>
@endpush
@endsection