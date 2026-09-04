<?php
/**
 * Contact Page
 */

$page_title = "Contact Us";
$css_path = "./assets/css/style.css";
$js_path = "./assets/js/script.js";

require_once './config/database.php';
require_once './config/constants.php';
require_once './includes/auth.php';

include './includes/user-header.php';
?>

<section class="py-5 bg-light">
    <div class="container">
        <h2>Get In Touch With Us</h2>
        
        <div class="row g-4">
            <!-- Contact Form -->
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-envelope me-2"></i> Send us a Message</h5>
                    </div>
                    
                    <div class="card-body p-4">
                        <!-- Success Message -->
                        <div id="successAlert" class="alert alert-success d-none" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Success!</strong> Your message has been sent. We'll get back to you soon.
                        </div>
                        
                        <!-- Error Message -->
                        <div id="errorAlert" class="alert alert-danger d-none" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <span id="errorText"></span>
                        </div>
                        
                        <!-- Form -->
                        <form id="contactForm" method="POST">
                            <!-- Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label">Your Name</label>
                                <input type="text" class="form-control" id="name" name="name" required placeholder="John Doe">
                            </div>
                            
                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" required placeholder="your@email.com">
                            </div>
                            
                            <!-- Phone -->
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="10-digit number">
                            </div>
                            
                            <!-- Subject -->
                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject</label>
                                <select class="form-select" id="subject" name="subject" required>
                                    <option value="">-- Select a subject --</option>
                                    <option value="Course Inquiry">Course Inquiry</option>
                                    <option value="Booking Help">Booking Help</option>
                                    <option value="Feedback">Feedback</option>
                                    <option value="General">General Question</option>
                                    <option value="Complaint">Complaint</option>
                                </select>
                            </div>
                            
                            <!-- Message -->
                            <div class="mb-3">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control" id="message" name="message" rows="5" required placeholder="Tell us how we can help..."></textarea>
                            </div>
                            
                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-paper-plane me-2"></i> Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Contact Information -->
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Contact Information</h5>
                    </div>
                    
                    <div class="card-body p-4">
                        <!-- Phone -->
                        <div class="mb-4">
                            <h6 class="text-primary fw-bold mb-2">
                                <i class="fas fa-phone text-primary me-2"></i> Call Us
                            </h6>
                            <p class="mb-0">
                                <a href="tel:+919876543210" class="text-decoration-none">+91-9876543210</a>
                                <br>
                                <small class="text-muted">Mon-Sun: 06:00 AM - 08:00 PM</small>
                            </p>
                        </div>
                        
                        <!-- Email -->
                        <div class="mb-4">
                            <h6 class="text-primary fw-bold mb-2">
                                <i class="fas fa-envelope text-primary me-2"></i> Email Us
                            </h6>
                            <p class="mb-0">
                                <a href="mailto:info@driveeasy.com" class="text-decoration-none">info@driveeasy.com</a>
                                <br>
                                <small class="text-muted">Response within 24 hours</small>
                            </p>
                        </div>
                        
                        <!-- Address -->
                        <div class="mb-4">
                            <h6 class="text-primary fw-bold mb-2">
                                <i class="fas fa-map-marker-alt text-primary me-2"></i> Visit Us
                            </h6>
                            <p class="mb-0">
                                123 Driving Avenue<br>
                                City, State 12345<br>
                                <small class="text-muted">Open 6AM to 8PM daily</small>
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Business Hours -->
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-clock me-2"></i> Business Hours</h5>
                    </div>
                    
                    <div class="card-body p-4">
                        <ul class="list-unstyled">
                            <li class="d-flex justify-content-between mb-2">
                                <span>Monday - Friday</span>
                                <strong>6:00 AM - 8:00 PM</strong>
                            </li>
                            <li class="d-flex justify-content-between mb-2">
                                <span>Saturday</span>
                                <strong>7:00 AM - 8:00 PM</strong>
                            </li>
                            <li class="d-flex justify-content-between">
                                <span>Sunday</span>
                                <strong>8:00 AM - 6:00 PM</strong>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Google Maps Section -->
<section class="py-5">
    <div class="container">
        <h3 class="mb-4">Find Us on the Map</h3>
        <div style="position: relative; overflow: hidden; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3023.3316780659477!2d-74.00601592346212!3d40.71277107138062!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25a27e91b0a3f%3A0xf89c6d2b0b0c0c0c!2sNew%20York%2C%20NY!5e0!3m2!1sen!2sus!4v1234567890" 
                width="100%" 
                height="400" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy">
            </iframe>
        </div>
    </div>
</section>

<script>
document.getElementById('contactForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Collect form data
    const formData = new FormData(this);
    const data = {
        name: formData.get('name'),
        email: formData.get('email'),
        phone: formData.get('phone'),
        subject: formData.get('subject'),
        message: formData.get('message')
    };
    
    try {
        const response = await fetch('./api/contact.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Show success message
            document.getElementById('successAlert').classList.remove('d-none');
            document.getElementById('contactForm').reset();
            
            // Hide success message after 5 seconds
            setTimeout(() => {
                document.getElementById('successAlert').classList.add('d-none');
            }, 5000);
        } else {
            showError(result.message);
        }
    } catch (error) {
        showError('Failed to send message. Please try again.');
        console.error('Error:', error);
    }
});

function showError(message) {
    const errorAlert = document.getElementById('errorAlert');
    const errorText = document.getElementById('errorText');
    errorText.textContent = message;
    errorAlert.classList.remove('d-none');
    window.scrollTo(0, 0);
}
</script>

<?php include './includes/user-footer.php'; ?>
