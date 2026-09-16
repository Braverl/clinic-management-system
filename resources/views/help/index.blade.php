@extends('layouts.app')

@section('title', 'Help Center')

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-life-ring me-2"></i>Help Center</h2>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm help-intro-card">
                <div class="card-body text-center p-4">
                    <div class="help-intro-icon mb-3">
                        <i class="fas fa-{{ auth()->user()->isAdmin() ? 'user-shield' : (auth()->user()->isDoctor() ? 'user-md' : 'heart') }}"></i>
                    </div>
                    <h5>{{ $helpContent['title'] }}</h5>
                    <p class="text-muted">Everything you need to get the most out of the Clinic Management System.</p>

                    <hr>

                    <h6 class="fw-bold"><i class="fas fa-envelope me-1"></i> Still need help?</h6>
                    <p class="small text-muted">Message the support team directly</p>
                    <a href="{{ route('chat.index') }}" class="btn btn-primary w-100">
                        <i class="fas fa-comments me-2"></i>Contact Support
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="accordion help-accordion" id="helpAccordion">
                @foreach($helpContent['sections'] as $index => $section)
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#helpSection{{ $index }}">
                            <i class="fas fa-hand-point-right me-2 text-primary"></i>
                            {{ $section['heading'] }}
                        </button>
                    </h2>
                    <div id="helpSection{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" data-bs-parent="#helpAccordion">
                        <div class="accordion-body">
                            {{ $section['content'] }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body d-flex align-items-center gap-3">
                    <i class="fas fa-lightbulb fa-2x text-warning"></i>
                    <div>
                        <strong>Quick Tip</strong>
                        <p class="mb-0 text-muted">Use the search bar in the top navigation to quickly find patients, doctors, and appointments.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection