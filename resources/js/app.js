import './bootstrap';
import 'bootstrap';
import Chart from 'chart.js/auto';
import 'sweetalert2/dist/sweetalert2.min.css';

// SweetAlert2
window.Swal = require('sweetalert2');

// Chart.js (available globally for inline view scripts)
window.Chart = Chart;

// Global AJAX Setup
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// Advanced Document Ready Function
$(document).ready(function() {
    
    // ========================================
    // 1. INITIALIZE ALL COMPONENTS
    // ========================================
    initializeTooltips();
    initializePopovers();
    initializeDatePickers();
    initializeCharts();
    initializeNotifications();
    initializeAnimations();
    
    // ========================================
    // 2. SMOOTH PAGE LOADING ANIMATION
    // ========================================
    $('body').addClass('loaded');
    
    // Hide loading spinner after page load
    setTimeout(() => {
        $('.loading-spinner').fadeOut(500);
    }, 500);
    
    // ========================================
    // 3. AUTO-HIDE ALERTS WITH SLIDE EFFECT
    // ========================================
    $('.alert').each(function() {
        const $alert = $(this);
        setTimeout(() => {
            $alert.fadeOut(500, function() {
                $(this).remove();
            });
        }, 5000);
    });
    
    // ========================================
    // 4. CONFIRM DELETE WITH BEAUTIFUL MODAL
    // ========================================
    $('.confirm-delete').on('click', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');
        const itemName = $(this).data('item') || 'this item';
        
        Swal.fire({
            title: 'Are you sure?',
            text: `You won't be able to revert ${itemName}!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            showClass: {
                popup: 'animate__animated animate__fadeInDown'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
                Swal.fire(
                    'Deleted!',
                    `${itemName} has been deleted.`,
                    'success'
                );
            }
        });
    });
    
    // ========================================
    // 5. DYNAMIC DOCTOR SCHEDULE FETCH
    // ========================================
    $('#doctor_id, #appointment_date').on('change', function() {
        const doctorId = $('#doctor_id').val();
        const date = $('#appointment_date').val();
        
        if (doctorId && date) {
            // Show loading state
            $('#appointment_time').html('<option>Loading available slots...</option>');
            
            $.ajax({
                url: `/patient/get-doctor-schedule/${doctorId}/${date}`,
                method: 'GET',
                success: function(response) {
                    let slotsHtml = '<option value="">Select Time</option>';
                    
                    if (response.available_slots && response.available_slots.length > 0) {
                        response.available_slots.forEach(slot => {
                            slotsHtml += `<option value="${slot.time}">${slot.display}</option>`;
                        });
                        $('#consultation_fee').text('$' + response.fee);
                        $('#fee_display').fadeIn();
                    } else {
                        slotsHtml = '<option value="">No slots available on this date</option>';
                        $('#fee_display').fadeOut();
                    }
                    
                    $('#appointment_time').html(slotsHtml).fadeIn();
                    
                    // Show success notification
                    showToast('Schedule loaded successfully!', 'success');
                },
                error: function() {
                    $('#appointment_time').html('<option value="">Error loading slots. Please try again.</option>');
                    showToast('Failed to load schedule', 'error');
                }
            });
        }
    });
    
    // ========================================
    // 6. LIVE SEARCH WITH ANIMATION
    // ========================================
    $('.live-search').on('keyup', function() {
        const search = $(this).val().toLowerCase();
        const target = $(this).data('target');
        
        $(target).find('tbody tr').each(function() {
            const $row = $(this);
            const text = $row.text().toLowerCase();
            
            if (text.indexOf(search) === -1) {
                $row.hide(200);
            } else {
                $row.show(200);
            }
        });
        
        // Show search results count
        const visibleRows = $(target).find('tbody tr:visible').length;
        const totalRows = $(target).find('tbody tr').length;
        $(target).find('.search-count').remove();
        $(target).before(`
            <div class="alert alert-info search-count animate__animated animate__fadeIn">
                Found ${visibleRows} out of ${totalRows} results
            </div>
        `);
        
        setTimeout(() => {
            $('.search-count').fadeOut(500, function() { $(this).remove(); });
        }, 3000);
    });
    
    // ========================================
    // 7. FORM VALIDATION WITH REAL-TIME FEEDBACK
    // ========================================
    $('form').each(function() {
        const $form = $(this);
        
        $form.find('input, select, textarea').on('blur', function() {
            const $field = $(this);
            const value = $field.val();
            
            if ($field.prop('required') && !value) {
                $field.addClass('is-invalid');
                $field.siblings('.invalid-feedback').remove();
                $field.after('<div class="invalid-feedback animate__animated animate__fadeIn">This field is required</div>');
            } else if ($field.attr('type') === 'email' && value && !isValidEmail(value)) {
                $field.addClass('is-invalid');
                $field.siblings('.invalid-feedback').remove();
                $field.after('<div class="invalid-feedback animate__animated animate__fadeIn">Please enter a valid email address</div>');
            } else {
                $field.removeClass('is-invalid');
                $field.addClass('is-valid');
            }
        });
    });
    
    // ========================================
    // 8. STATUS UPDATE WITH REASON MODAL
    // ========================================
    $('.update-status').on('click', function() {
        const status = $(this).data('status');
        const url = $(this).data('url');
        
        if (status === 'cancelled' || status === 'rejected') {
            Swal.fire({
                title: `Provide Reason for ${status}`,
                input: 'textarea',
                inputPlaceholder: 'Enter cancellation/rejection reason...',
                inputAttributes: {
                    'aria-label': 'Reason'
                },
                showCancelButton: true,
                confirmButtonText: 'Submit',
                cancelButtonText: 'Cancel',
                showLoaderOnConfirm: true,
                preConfirm: (reason) => {
                    if (!reason || reason.trim() === '') {
                        Swal.showValidationMessage('Reason is required');
                        return false;
                    }
                    return reason;
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    $('<form>', {
                        method: 'POST',
                        action: url
                    }).append($('<input>', {
                        name: 'status',
                        value: status,
                        type: 'hidden'
                    })).append($('<input>', {
                        name: 'cancellation_reason',
                        value: result.value,
                        type: 'hidden'
                    })).append($('<input>', {
                        name: '_token',
                        value: $('meta[name="csrf-token"]').attr('content'),
                        type: 'hidden'
                    })).appendTo('body').submit();
                }
            });
        } else {
            Swal.fire({
                title: `Confirm ${status}`,
                text: `Are you sure you want to mark this as ${status}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: `Yes, ${status} it!`
            }).then((result) => {
                if (result.isConfirmed) {
                    $('<form>', {
                        method: 'POST',
                        action: url
                    }).append($('<input>', {
                        name: 'status',
                        value: status,
                        type: 'hidden'
                    })).append($('<input>', {
                        name: '_token',
                        value: $('meta[name="csrf-token"]').attr('content'),
                        type: 'hidden'
                    })).appendTo('body').submit();
                }
            });
        }
    });
    
    // ========================================
    // 9. DATE VALIDATION (PREVENT PAST DATES)
    // ========================================
    $('input[type="date"]').on('change', function() {
        const selectedDate = new Date($(this).val());
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (selectedDate < today) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Date',
                text: 'Cannot select a past date!',
                timer: 2000,
                showConfirmButton: false
            });
            $(this).val('');
        }
    });
    
    // ========================================
    // 10. PRINT FUNCTIONALITY
    // ========================================
    $('.print-btn').on('click', function() {
        const printContent = $(this).data('print') || 'body';
        const originalContent = $('body').html();
        
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Print Report</title>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                    <style>
                        body { padding: 2rem; }
                        @media print {
                            .no-print { display: none; }
                        }
                    </style>
                </head>
                <body>
                    ${$(printContent).html()}
                </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    });
    
    // ========================================
    // 11. EXPORT TO CSV
    // ========================================
    $('.export-csv').on('click', function() {
        const tableId = $(this).data('table');
        const $table = $(tableId);
        const filename = $(this).data('filename') || 'export.csv';
        
        let csv = [];
        // Get headers
        $table.find('thead th').each(function() {
            csv.push('"' + $(this).text().trim() + '"');
        });
        csv.push('\n');
        
        // Get data rows
        $table.find('tbody tr').each(function() {
            const row = [];
            $(this).find('td').each(function() {
                row.push('"' + $(this).text().trim().replace(/"/g, '""') + '"');
            });
            csv.push(row.join(',') + '\n');
        });
        
        // Download
        const blob = new Blob([csv.join('')], { type: 'text/csv' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = filename;
        link.click();
        URL.revokeObjectURL(link.href);
        
        showToast('Export completed successfully!', 'success');
    });
    
    // ========================================
    // 12. DARK MODE TOGGLE
    // ========================================
    $('.dark-mode-toggle').on('click', function() {
        $('body').toggleClass('dark-mode');
        const isDark = $('body').hasClass('dark-mode');
        localStorage.setItem('darkMode', isDark);
        
        showToast(`${isDark ? 'Dark' : 'Light'} mode activated!`, 'info');
    });
    
    // Check saved preference
    if (localStorage.getItem('darkMode') === 'true') {
        $('body').addClass('dark-mode');
    }
    
    // ========================================
    // 13. REAL-TIME NOTIFICATIONS (POLLING)
    // ========================================
    function checkNotifications() {
        if ($('.notification-badge').length) {
            $.ajax({
                url: '/notifications/unread-count',
                method: 'GET',
                success: function(data) {
                    if (data.count > 0) {
                        $('.notification-badge').text(data.count).show();
                        if (data.count !== previousCount) {
                            playNotificationSound();
                            showToast(`You have ${data.count} new notification(s)!`, 'info');
                        }
                        previousCount = data.count;
                    } else {
                        $('.notification-badge').hide();
                    }
                }
            });
        }
    }
    
    // Poll every 30 seconds
    let previousCount = 0;
    if ($('.notification-badge').length) {
        setInterval(checkNotifications, 30000);
    }
    
    // ========================================
    // 14. SCROLL TO TOP BUTTON
    // ========================================
    const $scrollTop = $('<button>', {
        class: 'scroll-top-btn',
        html: '<i class="fas fa-arrow-up"></i>',
        css: {
            position: 'fixed',
            bottom: '20px',
            right: '20px',
            width: '50px',
            height: '50px',
            borderRadius: '50%',
            background: 'var(--primary-gradient)',
            color: 'white',
            border: 'none',
            cursor: 'pointer',
            display: 'none',
            zIndex: '1000',
            transition: 'all 0.3s ease',
            boxShadow: '0 4px 15px rgba(0,0,0,0.2)'
        }
    }).appendTo('body');
    
    $scrollTop.on('click', function() {
        $('html, body').animate({ scrollTop: 0 }, 500);
    });
    
    $(window).on('scroll', function() {
        if ($(this).scrollTop() > 300) {
            $scrollTop.fadeIn();
        } else {
            $scrollTop.fadeOut();
        }
    });
    
    // ========================================
    // 15. ANIMATE ON SCROLL
    // ========================================
    const animateElements = $('.animate-on-scroll');
    
    function checkInView() {
        animateElements.each(function() {
            const $element = $(this);
            const elementTop = $element.offset().top;
            const elementBottom = elementTop + $element.outerHeight();
            const viewportTop = $(window).scrollTop();
            const viewportBottom = viewportTop + $(window).height();
            
            if (elementBottom > viewportTop && elementTop < viewportBottom) {
                $element.addClass('animated');
            }
        });
    }
    
    $(window).on('scroll', checkInView);
    checkInView();
    
    // ========================================
    // 16. PASSWORD STRENGTH METER
    // ========================================
    $('#password').on('keyup', function() {
        const password = $(this).val();
        let strength = 0;
        
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/)) strength++;
        if (password.match(/[A-Z]/)) strength++;
        if (password.match(/[0-9]/)) strength++;
        if (password.match(/[^a-zA-Z0-9]/)) strength++;
        
        const $meter = $('.password-strength');
        if ($meter.length) {
            const percentage = (strength / 5) * 100;
            $meter.css('width', percentage + '%');
            
            if (strength <= 2) {
                $meter.css('background', '#f56565');
            } else if (strength <= 4) {
                $meter.css('background', '#ed8936');
            } else {
                $meter.css('background', '#48bb78');
            }
        }
    });
    
    // ========================================
    // 17. IMAGE PREVIEW BEFORE UPLOAD
    // ========================================
    $('input[type="file"][accept*="image"]').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#image-preview').attr('src', e.target.result).show();
            };
            reader.readAsDataURL(file);
        }
    });
    
    // ========================================
    // 18. CONFIRMATION FOR IMPORTANT ACTIONS
    // ========================================
    $('.confirm-action').on('click', function(e) {
        e.preventDefault();
        const message = $(this).data('message') || 'Are you sure?';
        const url = $(this).attr('href');
        
        Swal.fire({
            title: message,
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, proceed!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });
    
    // ========================================
    // 19. AUTO-REFRESH DASHBOARD DATA
    // ========================================
    if ($('.auto-refresh').length) {
        setInterval(() => {
            $.ajax({
                url: window.location.href,
                method: 'GET',
                success: function(data) {
                    const $newStats = $(data).find('.stats-container');
                    $('.stats-container').html($newStats.html());
                }
            });
        }, 60000); // Refresh every minute
    }
    
    // ========================================
    // 20. CHART.JS ADVANCED CONFIGURATION
    // ========================================
    function initializeCharts() {
        $('canvas[data-chart]').each(function() {
            const chartData = $(this).data('chart');
            const config = {
                type: chartData.type || 'line',
                data: {
                    labels: chartData.labels || [],
                    datasets: chartData.datasets || []
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    family: 'Poppins'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleColor: 'white',
                            bodyColor: '#e2e8f0',
                            borderColor: '#667eea',
                            borderWidth: 2
                        }
                    },
                    animation: {
                        duration: 2000,
                        easing: 'easeInOutQuart'
                    }
                }
            };
            new Chart($(this).get(0).getContext('2d'), config);
        });
    }
    
    // ========================================
    // 21. CARD HOVER 3D EFFECT
    // ========================================
    $('.card-3d').on('mousemove', function(e) {
        const card = $(this);
        const cardRect = card[0].getBoundingClientRect();
        const mouseX = e.clientX - cardRect.left;
        const mouseY = e.clientY - cardRect.top;
        const centerX = cardRect.width / 2;
        const centerY = cardRect.height / 2;
        const rotateX = (mouseY - centerY) / 20;
        const rotateY = (centerX - mouseX) / 20;
        
        card.css({
            'transform': `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-10px)`,
            'transition': 'transform 0.1s ease'
        });
    });
    
    $('.card-3d').on('mouseleave', function() {
        $(this).css({
            'transform': 'perspective(1000px) rotateX(0) rotateY(0) translateY(0)',
            'transition': 'transform 0.3s ease'
        });
    });
});

// ========================================
// HELPER FUNCTIONS
// ========================================

// Initialize Bootstrap Tooltips
function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            animation: true,
            delay: { show: 100, hide: 100 }
        });
    });
}

// Initialize Bootstrap Popovers
function initializePopovers() {
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl, {
            animation: true,
            html: true
        });
    });
}

// Initialize Date Pickers
function initializeDatePickers() {
    $('input[type="date"]').each(function() {
        const $input = $(this);
        if (!$input.val()) {
            $input.attr('min', new Date().toISOString().split('T')[0]);
        }
    });
}

// Initialize Animations
function initializeAnimations() {
    $('.animate-on-load').each(function(index) {
        $(this).css('animation-delay', `${index * 0.1}s`);
    });
}

// Initialize Notifications
function initializeNotifications() {
    $('.mark-notification-read').on('click', function() {
        const id = $(this).data('id');
        $.ajax({
            url: `/notifications/${id}/read`,
            method: 'POST',
            success: function() {
                $(this).closest('.notification-item').fadeOut();
                showToast('Notification marked as read', 'success');
            }
        });
    });
}

// Show Toast Notification
function showToast(message, type = 'info') {
    const toastHtml = `
        <div class="toast-notification animate__animated animate__slideInRight">
            <div class="alert alert-${type} shadow-lg">
                <i class="fas fa-${type === 'success' ? 'check-circle' : (type === 'error' ? 'exclamation-circle' : 'info-circle')} me-2"></i>
                ${message}
                <button type="button" class="btn-close float-end" data-bs-dismiss="alert"></button>
            </div>
        </div>
    `;
    
    $('body').append(toastHtml);
    setTimeout(() => {
        $('.toast-notification').fadeOut(500, function() {
            $(this).remove();
        });
    }, 5000);
}

// Email Validation
function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Play Notification Sound
function playNotificationSound() {
    try {
        const AudioContext = window.AudioContext || window.webkitAudioContext;
        const ctx = new AudioContext();
        const oscillator = ctx.createOscillator();
        const gain = ctx.createGain();
        oscillator.connect(gain);
        gain.connect(ctx.destination);
        oscillator.frequency.value = 880;
        oscillator.type = 'sine';
        gain.gain.setValueAtTime(0.3, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.4);
        oscillator.start();
        oscillator.stop(ctx.currentTime + 0.4);
        setTimeout(() => ctx.close(), 500);
    } catch (e) {
        console.log('Audio play failed:', e);
    }
}

// Format Currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

// Format Date
function formatDate(date, format = 'MM/DD/YYYY') {
    const d = new Date(date);
    const day = d.getDate().toString().padStart(2, '0');
    const month = (d.getMonth() + 1).toString().padStart(2, '0');
    const year = d.getFullYear();
    
    return format.replace('MM', month).replace('DD', day).replace('YYYY', year);
}

// Get Time Ago
function timeAgo(date) {
    const seconds = Math.floor((new Date() - new Date(date)) / 1000);
    let interval = seconds / 31536000;
    
    if (interval > 1) return Math.floor(interval) + ' years ago';
    interval = seconds / 2592000;
    if (interval > 1) return Math.floor(interval) + ' months ago';
    interval = seconds / 86400;
    if (interval > 1) return Math.floor(interval) + ' days ago';
    interval = seconds / 3600;
    if (interval > 1) return Math.floor(interval) + ' hours ago';
    interval = seconds / 60;
    if (interval > 1) return Math.floor(interval) + ' minutes ago';
    return Math.floor(seconds) + ' seconds ago';
}

// Export functions globally
window.showToast = showToast;
window.formatCurrency = formatCurrency;
window.formatDate = formatDate;
window.timeAgo = timeAgo;