@extends('layouts.app')

@section('title', 'Verify Email')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h3 class="mb-0"><i class="fas fa-envelope me-2"></i>Verify Your Email</h3>
                </div>
                <div class="card-body p-4">
                    <p class="text-center">A verification code has been sent to your email address. Please enter it below to complete registration.</p>
                    
                    <form method="POST" action="{{ route('verify.email') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Verification Code</label>
                            <input type="text" name="verification_code" class="form-control text-center" placeholder="Enter 6-digit code" maxlength="6" required autofocus>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="fas fa-check-circle me-2"></i>Verify Email
                        </button>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p class="mb-0">Didn't receive the code? 
                            <a href="{{ route('resend.verification') }}" class="text-primary">Resend Code</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection