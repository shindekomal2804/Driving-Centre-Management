/**
 * DriveEasy - User Frontend JavaScript
 * Main script for user-facing pages
 */

// Document Ready
document.addEventListener('DOMContentLoaded', function() {
    initializeScripts();
});

function initializeScripts() {
    // Add smooth scroll behavior
    smoothScroll();
    
    // Initialize tooltips and popovers
    initializeBootstrapComponents();
    
    // Form validation
    setupFormValidation();
    
    // Time slot selection
    setupTimeSlotSelection();
    
    // Modal handlers
    setupModalHandlers();
}

/**
 * Smooth scrolling for anchor links
 */
function smoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

/**
 * Initialize Bootstrap components (tooltips, popovers)
 */
function initializeBootstrapComponents() {
    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-mdb-toggle="tooltip"]');
    tooltips.forEach(tooltip => {
        new mdb.Tooltip(tooltip);
    });
    
    // Initialize popovers
    const popovers = document.querySelectorAll('[data-mdb-toggle="popover"]');
    popovers.forEach(popover => {
        new mdb.Popover(popover);
    });
}

/**
 * Form validation
 */
function setupFormValidation() {
    const forms = document.querySelectorAll('form[data-validate="true"]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
                e.stopPropagation();
            }
            this.classList.add('was-validated');
        });
    });
}

function validateForm(form) {
    let isValid = true;
    
    // Check required fields
    const requiredFields = form.querySelectorAll('[required]');
    requiredFields.forEach(field => {
        if (field.type === 'email') {
            if (!validateEmail(field.value)) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        } else if (field.value.trim() === '') {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    return isValid;
}

/**
 * Email validation
 */
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Phone validation
 */
function validatePhone(phone) {
    const phoneRegex = /^[0-9]{10}$/;
    return phoneRegex.test(phone.replace(/\D/g, ''));
}

/**
 * Time slot selection
 */
function setupTimeSlotSelection() {
    const timeSlots = document.querySelectorAll('.time-slot');
    
    timeSlots.forEach(slot => {
        slot.addEventListener('click', function() {
            // Remove selection from all slots
            timeSlots.forEach(s => s.classList.remove('selected'));
            
            // Add selection to clicked slot
            this.classList.add('selected');
            
            // Set hidden input value
            const timeInput = document.getElementById('selected_time');
            if (timeInput) {
                timeInput.value = this.dataset.time;
            }
        });
    });
}

/**
 * Modal handlers
 */
function setupModalHandlers() {
    // Booking modal
    const bookingModal = document.getElementById('bookingModal');
    if (bookingModal) {
        bookingModal.addEventListener('show.mdb.modal', function() {
            // Initialize calendar or time picker
            initializeBookingForm();
        });
    }
    
    // Review modal
    const reviewModal = document.getElementById('reviewModal');
    if (reviewModal) {
        reviewModal.addEventListener('show.mdb.modal', function() {
            initializeReviewForm();
        });
    }
}

/**
 * Initialize booking form
 */
function initializeBookingForm() {
    const dateInput = document.getElementById('booking_date');
    if (dateInput) {
        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        dateInput.min = today;
    }
}

/**
 * Initialize review form with star rating
 */
function initializeReviewForm() {
    const stars = document.querySelectorAll('.star-rating .star');
    
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = this.dataset.rating;
            const ratingInput = document.getElementById('rating_input');
            if (ratingInput) {
                ratingInput.value = rating;
            }
            
            stars.forEach((s, index) => {
                if (index < rating) {
                    s.classList.add('active');
                } else {
                    s.classList.remove('active');
                }
            });
        });
    });
}

/**
 * Format currency
 */
function formatCurrency(amount, currency = 'INR') {
    const formatter = new Intl.NumberFormat('en-IN', {
        style: 'currency',
        currency: currency
    });
    return formatter.format(amount);
}

/**
 * Show toast notification
 */
function showToast(message, type = 'info') {
    const toastId = 'toast_' + Date.now();
    const toastHTML = `
        <div id="${toastId}" class="toast fade show" role="alert">
            <div class="toast-header bg-${type} text-white">
                <strong class="me-auto">${type.charAt(0).toUpperCase() + type.slice(1)}</strong>
                <button type="button" class="btn-close" data-mdb-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;
    
    const toastContainer = document.getElementById('toastContainer');
    if (toastContainer) {
        toastContainer.insertAdjacentHTML('beforeend', toastHTML);
        const toastElement = document.getElementById(toastId);
        const toast = new mdb.Toast(toastElement);
        toast.show();
    }
}

/**
 * Show loading spinner
 */
function showLoading(show = true) {
    const spinner = document.getElementById('loadingSpinner');
    if (spinner) {
        if (show) {
            spinner.style.display = 'block';
        } else {
            spinner.style.display = 'none';
        }
    }
}

/**
 * AJAX request helper
 */
function makeAjaxRequest(url, method = 'GET', data = null, callback = null) {
    const options = {
        method: method,
        headers: {
            'Content-Type': 'application/json'
        }
    };
    
    if (method !== 'GET' && data) {
        options.body = JSON.stringify(data);
    }
    
    fetch(url, options)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (callback) {
                callback(null, data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (callback) {
                callback(error, null);
            }
        });
}

/**
 * Logout confirmation
 */
function confirmLogout() {
    if (confirm('Are you sure you want to logout?')) {
        window.location.href = '../api/logout.php';
    }
}
