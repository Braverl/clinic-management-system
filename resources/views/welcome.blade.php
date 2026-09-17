@extends('layouts.app')

@section('title', 'ClinicSystem — Modern Healthcare for Everyone')

@section('content')

{{-- Emergency strip --}}
<div class="landing-emergency d-none d-lg-block">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-4 text-lg-start">
                <a href="{{ route('login') }}" class="landing-emergency-link"><i class="fas fa-envelope me-2"></i>emergency@clinicsystem.com</a>
            </div>
            <div class="col-lg-4 text-lg-center">
                <span class="landing-emergency-item"><i class="fas fa-truck-medical me-2"></i>24/7 Road Ambulance &amp; Emergency Response</span>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="tel:+250788000000" class="landing-emergency-phone"><i class="fas fa-phone-alt me-2"></i>+250 788 000 000</a>
            </div>
        </div>
    </div>
</div>

{{-- Hero --}}
<section class="landing-hero">
    <div class="landing-hero-bg" style="background-image: url('{{ asset('images/welcome/hero-hospital.jpg') }}');"></div>
    <div class="landing-hero-overlay"></div>
    <div class="container landing-hero-inner">
        <div class="row justify-content-center text-center">
            <div class="col-lg-9">
                <span class="landing-eyebrow animate-on-load"><i class="fas fa-heartbeat me-2"></i>Trusted Care Since 2010</span>
                <h1 class="landing-hero-title animate-on-load">Welcome to <span class="text-gradient">ClinicSystem</span></h1>
                <p class="landing-hero-sub animate-on-load">
                    Quality healthcare for you and your family. Book appointments with the
                    region's most trusted specialists, access your medical records online,
                    and get the care you deserve — all from one place.
                </p>
                <div class="landing-hero-actions animate-on-load">
                    <a href="{{ route('login') }}" class="btn btn-lg btn-landing-primary me-2"><i class="fas fa-calendar-check me-2"></i>Book an Appointment</a>
                    <a href="{{ route('register') }}" class="btn btn-lg btn-landing-ghost"><i class="fas fa-user-plus me-2"></i>Join as Patient</a>
                </div>
                <div class="landing-hero-badges animate-on-load">
                    <span><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i> Rated 4.9/5</span>
                    <span><i class="fas fa-shield-alt me-1"></i> Secure &amp; Confidential</span>
                    <span><i class="fas fa-certificate me-1"></i> Licensed Hospital</span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Stats --}}
<section class="landing-stats">
    <div class="container">
        <div class="row g-3">
            <div class="col-6 col-lg-3">
                <div class="landing-stat-card"><i class="fas fa-user-injured landing-stat-icon"></i><div class="landing-stat-number" data-count="12000" data-prefix="+" data-suffix="K">0</div><div class="landing-stat-label">Patients Treated</div></div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="landing-stat-card"><i class="fas fa-user-md landing-stat-icon"></i><div class="landing-stat-number" data-count="128" data-suffix="+">0</div><div class="landing-stat-label">Expert Doctors</div></div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="landing-stat-card"><i class="fas fa-hospital-user landing-stat-icon"></i><div class="landing-stat-number" data-count="24" data-suffix="+">0</div><div class="landing-stat-label">Specialties</div></div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="landing-stat-card"><i class="fas fa-procedures landing-stat-icon"></i><div class="landing-stat-number" data-count="15" data-suffix="+">0</div><div class="landing-stat-label">Years of Service</div></div>
            </div>
        </div>
    </div>
</section>

{{-- What we treat --}}
<section class="landing-section landing-section-light">
    <div class="container">
        <div class="text-center landing-section-heading">
            <span class="landing-eyebrow">Medical Services</span>
            <h2 class="landing-section-title">Compassionate Care, <span class="text-brand">Every Treatment</span></h2>
            <p class="landing-section-sub">From routine check-ups to complex surgery, our specialists cover every branch of medicine under one roof.</p>
        </div>
        <div class="row g-4">
            @php
                $treatments = [
                    ['icon' => 'fa-stethoscope', 'title' => 'General Medicine', 'desc' => 'Diagnosis and treatment of common and chronic illnesses for all ages.'],
                    ['icon' => 'fa-heart-pulse', 'title' => 'Cardiology', 'desc' => 'Advanced heart care, ECGs, scans and follow-up for healthy living.'],
                    ['icon' => 'fa-baby', 'title' => 'Pediatrics', 'desc' => 'Gentle, caring treatment for infants, children and teenagers.'],
                    ['icon' => 'fa-bone', 'title' => 'Orthopedics', 'desc' => 'Fracture care, joint replacements and physiotherapy support.'],
                    ['icon' => 'fa-truck-medical', 'title' => 'Emergency Care', 'desc' => 'Rapid-response emergency room available 24 hours a day.'],
                    ['icon' => 'fa-flask-vial', 'title' => 'Laboratory', 'desc' => 'Same-day blood tests, imaging and diagnostic results.'],
                    ['icon' => 'fa-pills', 'title' => 'Pharmacy', 'desc' => 'Certified pharmacists with quality medicines always in stock.'],
                    ['icon' => 'fa-x-ray', 'title' => 'Radiology', 'desc' => 'X-ray, ultrasound and modern imaging for accurate diagnosis.'],
                ];
            @endphp
            @foreach($treatments as $t)
            <div class="col-md-6 col-lg-3">
                <div class="landing-treat-card">
                    <div class="landing-treat-icon"><i class="fas {{ $t['icon'] }}"></i></div>
                    <h5>{{ $t['title'] }}</h5>
                    <p>{{ $t['desc'] }}</p>
                    <a href="{{ route('login') }}" class="landing-treat-link">Learn more <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Why choose us --}}
<section class="landing-section landing-section-dark">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="landing-image-frame landing-image-frame-left">
                    <img src="{{ asset('images/welcome/doctor-patient.jpg') }}" alt="Doctor caring for a patient" class="img-fluid landing-image">
                    <div class="landing-image-badge">
                        <i class="fas fa-certificate fa-2x me-2"></i>
                        <div><strong>Accredited</strong><br>Ministry of Health</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <span class="landing-eyebrow">Why Choose Us</span>
                <h2 class="landing-section-title text-white">A Health Partner You Can <span class="text-brand">Trust</span></h2>
                <p class="landing-section-sub text-white-50">Every day we put patients first. Here is why thousands of families already choose ClinicSystem.</p>
                <ul class="landing-why-list">
                    <li><span class="landing-why-icon"><i class="fas fa-user-md"></i></span><div><strong>Qualified specialists</strong><br>Board-certified doctors with years of hands-on experience.</div></li>
                    <li><span class="landing-why-icon"><i class="fas fa-microscope"></i></span><div><strong>Modern technology</strong><br>Digital records, online booking and state-of-the-art equipment.</div></li>
                    <li><span class="landing-why-icon"><i class="fas fa-wallet"></i></span><div><strong>Affordable care</strong><br>Transparent pricing and flexible payment options for everyone.</div></li>
                    <li><span class="landing-why-icon"><i class="fas fa-clock"></i></span><div><strong>Quick appointments</strong><br>Short waiting times and same-day slots for urgent cases.</div></li>
                </ul>
                <a href="{{ route('register') }}" class="btn btn-lg btn-landing-primary mt-2"><i class="fas fa-heart me-2"></i>Start Your Health Journey</a>
            </div>
        </div>
    </div>
</section>

{{-- Meet the doctors --}}
<section class="landing-section landing-section-light">
    <div class="container">
        <div class="text-center landing-section-heading">
            <span class="landing-eyebrow">Our Specialists</span>
            <h2 class="landing-section-title">Meet the Experts <span class="text-brand">Behind Your Care</span></h2>
            <p class="landing-section-sub">A dedicated team of doctors, nurses and technicians working around the clock for your wellbeing.</p>
        </div>
        <div class="row g-4 justify-content-center">
            <div class="col-md-4">
                <div class="landing-doc-card">
                    <div class="landing-doc-photo"><img src="{{ asset('images/welcome/doctor-portrait.jpg') }}" alt="Doctor"></div>
                    <div class="landing-doc-info">
                        <h5>Dr. Sarah Mitchell</h5>
                        <span class="landing-doc-specialty">Chief of Surgery</span>
                        <div class="landing-doc-social"><a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a><a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a><a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="landing-doc-card">
                    <div class="landing-doc-photo"><img src="{{ asset('images/welcome/doctor-portrait2.jpg') }}" alt="Doctor"></div>
                    <div class="landing-doc-info">
                        <h5>Dr. Mark Okello</h5>
                        <span class="landing-doc-specialty">Cardiologist</span>
                        <div class="landing-doc-social"><a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a><a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a><a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="landing-doc-card">
                    <div class="landing-doc-photo"><img src="{{ asset('images/welcome/doctor-portrait3.jpg') }}" alt="Doctor"></div>
                    <div class="landing-doc-info">
                        <h5>Dr. Amina Uwase</h5>
                        <span class="landing-doc-specialty">Pediatric Specialist</span>
                        <div class="landing-doc-social"><a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a><a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a><a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- About + image --}}
<section class="landing-section landing-section-dark">
    <div class="container">
        <div class="row align-items-center g-5 flex-lg-row-reverse">
            <div class="col-lg-6">
                <div class="landing-image-frame landing-image-frame-right">
                    <img src="{{ asset('images/welcome/team-doctors.jpg') }}" alt="Our medical team" class="img-fluid landing-image">
                    <div class="landing-image-badge landing-image-badge-dark">
                        <i class="fas fa-hand-holding-heart fa-2x me-2"></i>
                        <div><strong>120+ Specialists</strong><br>One caring team</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <span class="landing-eyebrow">About Our Hospital</span>
                <h2 class="landing-section-title text-white">Modern Facilities Built Around <span class="text-brand">Patients</span></h2>
                <p class="landing-section-sub text-white-50">
                    ClinicSystem began in 2010 with a simple promise: bring world-class, affordable healthcare to our community.
                    Today we operate a full modern hospital and specialist centre providing everything from a routine consultation
                    to advanced surgery — all accessible online through your patient portal.
                </p>
                <div class="row g-3 landing-facility-list">
                    <div class="col-6"><div class="landing-facility-item"><i class="fas fa-hospital"></i> 250+ Patient Beds</div></div>
                    <div class="col-6"><div class="landing-facility-item"><i class="fas fa-heart-broken"></i> Cardiac Care Unit</div></div>
                    <div class="col-6"><div class="landing-facility-item"><i class="fas fa-flask"></i> Full Diagnostics Lab</div></div>
                    <div class="col-6"><div class="landing-facility-item"><i class="fas fa-ambulance"></i> 24/7 Ambulance Fleet</div></div>
                    <div class="col-6"><div class="landing-facility-item"><i class="fas fa-user-nurse"></i> 350+ Nurses &amp; Staff</div></div>
                    <div class="col-6"><div class="landing-facility-item"><i class="fas fa-baby-carriage"></i> Maternity Wing</div></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- How it works --}}
<section class="landing-section landing-section-light">
    <div class="container">
        <div class="text-center landing-section-heading">
            <span class="landing-eyebrow">Get Started</span>
            <h2 class="landing-section-title">Health Care in <span class="text-brand">Three Simple Steps</span></h2>
            <p class="landing-section-sub">Going to the hospital has never been this easy.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="landing-step-card">
                    <div class="landing-step-number">01</div>
                    <div class="landing-step-icon"><i class="fas fa-user-plus"></i></div>
                    <h5>Create Your Account</h5>
                    <p>Register in under a minute and build your personal health profile online.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="landing-step-card">
                    <div class="landing-step-number">02</div>
                    <div class="landing-step-icon"><i class="fas fa-calendar-check"></i></div>
                    <h5>Book an Appointment</h5>
                    <p>Pick your specialist and preferred time — receive instant confirmation.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="landing-step-card">
                    <div class="landing-step-number">03</div>
                    <div class="landing-step-icon"><i class="fas fa-file-medical"></i></div>
                    <h5>Visit &amp; Get Care</h5>
                    <p>See your doctor, get your treatment and track your records online anytime.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Testimonials --}}
<section class="landing-section landing-section-cloud">
    <div class="container">
        <div class="text-center landing-section-heading">
            <span class="landing-eyebrow">Patient Stories</span>
            <h2 class="landing-section-title">What Our <span class="text-brand">Patients Say</span></h2>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="landing-testimonial-card">
                    <div class="landing-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                    <p>"Booking was so fast and the doctor explained everything clearly. My whole family trusts ClinicSystem."</p>
                    <div class="landing-testimonial-author"><div class="landing-avatar"><i class="fas fa-user"></i></div><div><strong>Claudine Mukamana</strong><span>Patient</span></div></div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="landing-testimonial-card">
                    <div class="landing-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                    <p>"I received my lab results the same morning through the portal. Clean, modern and very professional."</p>
                    <div class="landing-testimonial-author"><div class="landing-avatar"><i class="fas fa-user"></i></div><div><strong>Jean-Bosco Habimana</strong><span>Patient</span></div></div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="landing-testimonial-card">
                    <div class="landing-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i></div>
                    <p>"The paediatric wing is wonderful — friendly nurses and almost no waiting time for my son."</p>
                    <div class="landing-testimonial-author"><div class="landing-avatar"><i class="fas fa-user"></i></div><div><strong>Aline Niyonsaba</strong><span>Mother &amp; Patient</span></div></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- CTA banner --}}
<section class="landing-cta">
    <div class="container">
        <div class="landing-cta-banner">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h2 class="text-white fw-bold mb-2">Ready to Prioritize Your Health?</h2>
                    <p class="text-white-50 mb-0">Create an account today and book your first appointment with one of our specialists.</p>
                </div>
                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                    <a href="{{ route('register') }}" class="btn btn-lg btn-white me-2"><i class="fas fa-user-plus me-2"></i>Register Now</a>
                    <a href="{{ route('login') }}" class="btn btn-lg btn-outline-light">Login</a>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var counters = document.querySelectorAll('.landing-stat-number[data-count]');
        counters.forEach(function (el) {
            var target = parseInt(el.getAttribute('data-count'), 10);
            var prefix = el.getAttribute('data-prefix') || '';
            var suffix = el.getAttribute('data-suffix') || '';
            var dur = 1600;
            var start = null;
            function step(ts) {
                if (!start) start = ts;
                var p = Math.min((ts - start) / dur, 1);
                var eased = 1 - Math.pow(1 - p, 3);
                el.textContent = prefix + Math.floor(eased * target) + suffix;
                if (p < 1) { requestAnimationFrame(step); } else { el.textContent = prefix + target + suffix; }
            }
            requestAnimationFrame(step);
        });
    });
</script>
@endpush