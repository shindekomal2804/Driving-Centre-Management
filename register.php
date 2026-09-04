<?php
/**
 * User Registration Page
 */

$page_title = "Register";
$css_path = "./assets/css/style.css";
$js_path = "./assets/js/script.js";

require_once './config/database.php';
require_once './config/constants.php';
require_once './includes/auth.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: ./user/dashboard.php');
    exit();
}

include './includes/user-header.php';
?>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white py-4">
                        <h3 class="mb-0">
                            <i class="fas fa-user-plus me-2"></i> Create Your Account
                        </h3>
                        <p class="small mb-0 mt-2">Join DriveEasy and start your driving journey</p>
                    </div>
                    
                    <div class="card-body p-4">
                        <!-- Error Message -->
                        <div id="errorAlert" class="alert alert-danger d-none" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <span id="errorText"></span>
                        </div>
                        
                        <!-- Form -->
                        <form id="registrationForm" method="POST" action="./api/registers.php" data-validate="true">
                            <!-- Full Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" required placeholder="Enter your full name">
                                <small class="form-text text-muted">Must be at least 2 characters</small>
                            </div>
                            
                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" required placeholder="you@example.com">
                                <small class="form-text text-muted">We'll never share your email</small>
                            </div>
                            
                            <!-- Phone -->
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" required placeholder="10-digit mobile number">
                                <small class="form-text text-muted">10 digits without country code</small>
                            </div>
                            
                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required placeholder="Minimum 6 characters">
                                <small class="form-text text-muted">At least 6 characters with uppercase, lowercase, and numbers</small>
                            </div>
                            
                            <!-- Confirm Password -->
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required placeholder="Re-enter your password">
                            </div>
                            
                            <!-- Terms -->
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="#" class="text-primary">Terms & Conditions</a> and <a href="#" class="text-primary">Privacy Policy</a>
                                </label>
                            </div>
                            
                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="fas fa-user-check me-2"></i> Create Account
                            </button>
                        </form>
                        
                        <!-- Login Link -->
                        <p class="text-center small">
                            Already have an account? <a href="./login.php" class="text-primary fw-bold">Login here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('registrationForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Validate passwords match
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (password !== confirmPassword) {
        showError('Passwords do not match');
        return;
    }
    
    // Collect form data
    const formData = new FormData(this);
    const data = {
        name: formData.get('name'),
        email: formData.get('email'),
        phone: formData.get('phone'),
        password: formData.get('password'),
        confirm_password: formData.get('confirm_password')
    };
    
    try {
        const response = await fetch('./api/registers.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Show success message
            alert('Registration successful! Please login to your account.');
            window.location.href = './login.php';
        } else {
            showError(result.message);
        }
    } catch (error) {
        showError('Registration failed. Please try again.');
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
