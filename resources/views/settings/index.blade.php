@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-cog me-2"></i>Settings</h2>
    </div>

    <div class="row g-4">
        <!-- Preferences -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header">
                    <i class="fas fa-sliders-h me-2"></i>My Preferences
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('settings.update') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Theme</label>
                            <div class="d-flex gap-3">
                                <label class="theme-option {{ ($user->theme ?? 'light') === 'light' ? 'selected' : '' }}">
                                    <input type="radio" name="theme" value="light" class="d-none" {{ ($user->theme ?? 'light') === 'light' ? 'checked' : '' }}>
                                    <i class="fas fa-sun"></i>
                                    <span>Light</span>
                                </label>
                                <label class="theme-option {{ ($user->theme ?? '') === 'dark' ? 'selected' : '' }}">
                                    <input type="radio" name="theme" value="dark" class="d-none" {{ ($user->theme ?? '') === 'dark' ? 'checked' : '' }}>
                                    <i class="fas fa-moon"></i>
                                    <span>Dark</span>
                                </label>
                                <label class="theme-option {{ ($user->theme ?? '') === 'system' ? 'selected' : '' }}">
                                    <input type="radio" name="theme" value="system" class="d-none" {{ ($user->theme ?? '') === 'system' ? 'checked' : '' }}>
                                    <i class="fas fa-desktop"></i>
                                    <span>System</span>
                                </label>
                            </div>
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="notifications_enabled" value="1" id="notificationsEnabled" {{ ($user->notifications_enabled ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="notificationsEnabled">
                                <i class="fas fa-bell me-1 text-warning"></i> Enable notifications
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- System Information -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header">
                    <i class="fas fa-server me-2"></i>System Information
                </div>
                <div class="card-body">
                    <div class="system-info-item">
                        <span class="detail-label">Application</span>
                        <span class="detail-value">Clinic Management System</span>
                    </div>
                    <div class="system-info-item">
                        <span class="detail-label">Version</span>
                        <span class="detail-value">{{ $systemInfo['version'] }}</span>
                    </div>
                    <div class="system-info-item">
                        <span class="detail-label">Laravel</span>
                        <span class="detail-value">{{ $systemInfo['laravel_version'] }}</span>
                    </div>
                    <div class="system-info-item">
                        <span class="detail-label">PHP</span>
                        <span class="detail-value">{{ $systemInfo['php_version'] }}</span>
                    </div>
                    <div class="system-info-item">
                        <span class="detail-label">Database</span>
                        <span class="detail-value">{{ $systemInfo['db_name'] }}</span>
                    </div>
                    <div class="system-info-item">
                        <span class="detail-label">Logged in as</span>
                        <span class="detail-value">{{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})</span>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header">
                    <i class="fas fa-question-circle me-2"></i>Need Help?
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Visit the help center for guides and support or send a message to the support team.</p>
                    <a href="{{ route('help.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-life-ring me-2"></i>Help Center
                    </a>
                    <a href="{{ route('chat.index') }}" class="btn btn-outline-success ms-2">
                        <i class="fas fa-comments me-2"></i>Chat
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection