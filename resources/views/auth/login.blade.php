@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h3 class="mb-0"><i class="fas fa-sign-in-alt me-2"></i>Login to Clinic System</h3>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Login As</label>
                            <select name="role" id="role" class="form-select" required>
                                <option value="">Select Role</option>
                                <option value="patient" {{ old('role') == 'patient' ? 'selected' : '' }}>👤 Patient</option>
                                <option value="doctor" {{ old('role') == 'doctor' ? 'selected' : '' }}>👨‍⚕️ Doctor</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>👑 Admin</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>
                            </div>
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                            </div>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="remember" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">Remember Me</label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </button>
                        
                        <!-- FORGOT PASSWORD LINK - DIRECT HTML -->
                        <div style="text-align: center; margin-top: 15px;">
                            <a href="/forgot-password" style="color: #6c757d; text-decoration: none;">
                                <i class="fas fa-key"></i> Forgot Your Password?
                            </a>
                        </div>
                        <!-- END FORGOT PASSWORD LINK -->
                        
                        <div class="text-center mt-2">
                            <p class="mb-0">Don't have an account? <a href="{{ route('register') }}">Register here</a></p>
                        </div>
                    </form>
                    
                    <hr class="my-4">
                    
                    <div class="text-center">
                        <small class="text-muted">Demo Credentials:</small><br>
                        <small class="text-muted">Admin: admin@clinicsystem.com / password</small><br>
                        <small class="text-muted">Doctor: james.wilson@clinic.com / password</small><br>
                        <small class="text-muted">Patient: emily.johnson@email.com / password</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('role').addEventListener('change', function() {
        const role = this.value;
        const emailField = document.querySelector('input[name="email"]');
        
        if (role === 'admin') {
            emailField.value = 'admin@clinicsystem.com';
        } else if (role === 'doctor') {
            emailField.value = 'james.wilson@clinic.com';
        } else if (role === 'patient') {
            emailField.value = 'emily.johnson@email.com';
        } else {
            emailField.value = '';
        }
    });
</script>
@endpush
@endsection