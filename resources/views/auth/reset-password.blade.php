@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-success text-white text-center py-4">
                    <h3 class="mb-0"><i class="fas fa-lock me-2"></i>Set New Password</h3>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('password.reset') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Enter new password" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm new password" required>
                        </div>
                        
                        <button type="submit" class="btn btn-success w-100 py-2">
                            <i class="fas fa-save me-2"></i>Reset Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection