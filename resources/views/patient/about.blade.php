@extends('layouts.app')

@section('title', 'About the Clinic')

@section('content')
<div class="container mt-4">
    <!-- Hero -->
    <div class="about-hero rounded-4 p-5 text-center text-white mb-4">
        <img src="{{ asset('logo.svg') }}" alt="Logo" width="80" height="80" class="mb-3 about-logo">
        <h2 class="fw-bold mb-2">Clinic Management System</h2>
        <p class="mb-0 opacity-75">Caring for your health with modern, professional healthcare services.</p>
    </div>

    <div class="row g-4">
        <!-- About -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header">
                    <i class="fas fa-info-circle me-2"></i>About Us
                </div>
                <div class="card-body">
                    <p>
                        Welcome to the <strong>Clinic Management System</strong> — an integrated healthcare platform designed
                        to connect patients, doctors, and administrators in one secure place. We simplify the way you book
                        appointments, manage medical records, track billing, and communicate with your healthcare providers.
                    </p>
                    <p class="mb-0">
                        Our mission is to deliver <strong>accessible, efficient, and compassionate healthcare</strong>.
                        Whether you are booking a consultation, reviewing your medical history, or paying your bill,
                        everything is just a few clicks away.
                    </p>

                    <hr class="my-4">

                    <h5 class="fw-bold mb-3"><i class="fas fa-star me-2 text-warning"></i>Why Choose Us</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="feature-mini">
                                <i class="fas fa-calendar-check text-primary"></i>
                                <div>
                                    <strong>Easy Online Booking</strong>
                                    <p class="mb-0 text-muted small">Book appointments 24/7 with real-time availability.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-mini">
                                <i class="fas fa-shield-alt text-success"></i>
                                <div>
                                    <strong>Secure Medical Records</strong>
                                    <p class="mb-0 text-muted small">Your history is private, safe, and always accessible.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-mini">
                                <i class="fas fa-stethoscope text-info"></i>
                                <div>
                                    <strong>Qualified Doctors</strong>
                                    <p class="mb-0 text-muted small">A dedicated team of experienced specialists.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-mini">
                                <i class="fas fa-credit-card text-warning"></i>
                                <div>
                                    <strong>Transparent Billing</strong>
                                    <p class="mb-0 text-muted small">Clear invoices and multiple payment options.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Location & Contact -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header">
                    <i class="fas fa-map-marker-alt me-2"></i>Our Location
                </div>
                <div class="card-body">
                    <div class="map-container rounded-3 mb-3 overflow-hidden">
                        <iframe
                            title="Clinic Location Map"
                            src="https://www.openstreetmap.org/export/embed.html?bbox=30.0206%2C-1.9403%2C30.0606%2C-1.9203&layer=mapnik&marker=-1.9303%2C30.0406"
                            class="w-100"
                            style="height: 220px; border: 0;"
                            loading="lazy"></iframe>
                    </div>
                    <div class="location-detail">
                        <i class="fas fa-location-arrow"></i>
                        <span><strong>Address:</strong> Peace Plaza, KN 4 Ave, Kigali, Rwanda</span>
                    </div>
                    <div class="location-detail">
                        <i class="fas fa-phone-alt"></i>
                        <span><strong>Phone:</strong> +250 726 000 000</span>
                    </div>
                    <div class="location-detail">
                        <i class="fas fa-envelope"></i>
                        <span><strong>Email:</strong> support@clinicsystem.rw</span>
                    </div>
                    <div class="location-detail">
                        <i class="fas fa-clock"></i>
                        <span><strong>Hours:</strong> Mon – Sat, 8:00 AM – 6:00 PM</span>
                    </div>

                    <a href="#" id="getDirectionsBtn" class="btn btn-primary w-100 mt-3">
                        <i class="fas fa-directions me-1"></i>Get Directions
                    </a>
                </div>
            </div>

            <!-- Social platforms -->
            <div class="card border-0 shadow-sm">
                <div class="card-header">
                    <i class="fas fa-share-alt me-2"></i>Follow Us
                </div>
                <div class="card-body">
                    <p class="text-muted small">Connect with us on our social platforms for news, health tips, and updates.</p>
                    <div class="d-grid gap-2">
                        <a href="https://facebook.com" target="_blank" class="btn btn-social social-facebook">
                            <i class="fab fa-facebook-f"></i><span>Facebook</span>
                        </a>
                        <a href="https://twitter.com" target="_blank" class="btn btn-social social-twitter">
                            <i class="fab fa-twitter"></i><span>Twitter (X)</span>
                        </a>
                        <a href="https://instagram.com" target="_blank" class="btn btn-social social-instagram">
                            <i class="fab fa-instagram"></i><span>Instagram</span>
                        </a>
                        <a href="https://youtube.com" target="_blank" class="btn btn-social social-youtube">
                            <i class="fab fa-youtube"></i><span>YouTube Channel</span>
                        </a>
                        <a href="mailto:support@clinicsystem.rw" class="btn btn-social social-gmail">
                            <i class="fab fa-google"></i><span>Gmail</span>
                        </a>
                        <a href="https://wa.me/250726000000" target="_blank" class="btn btn-social social-whatsapp">
                            <i class="fab fa-whatsapp"></i><span>WhatsApp</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $('#getDirectionsBtn').on('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Get Directions',
            text: 'Open our location in Google Maps?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Open Maps',
            cancelButtonText: 'Cancel'
        }).then(result => {
            if (result.isConfirmed) {
                window.open('https://www.google.com/maps?q=-1.9303,30.0406', '_blank');
            }
        });
    });
</script>
@endpush
@endsection