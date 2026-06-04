@extends('layouts.app')

@section('title', 'Verify Reset Code')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-warning text-white text-center py-4">
                    <h3 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Verify Reset Code</h3>
                </div>
                <div class="card-body p-4">
                    <p>A password reset code has been sent to your email. Please enter it below.</p>
                    
                    <form method="POST" action="{{ route('verify.reset.code') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Verification Code</label>
                            <input type="text" name="verification_code" class="form-control text-center" placeholder="Enter 6-digit code" maxlength="6" required autofocus>
                        </div>
                        
                        <button type="submit" class="btn btn-warning w-100 py-2">
                            <i class="fas fa-check-circle me-2"></i>Verify Code
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection