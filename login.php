<?php
/**
 * User Login Page
 */

$page_title = "Login";
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

// Handle non-JS form submission fallback
$errorMessage = '';
$redirectTarget = './user/dashboard.php';
if (!empty($_GET['redirect'])) {
    $redirectTarget = $_GET['redirect'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $redirectTarget = $_POST['redirect'] ?? $redirectTarget;

    if ($email === '' || $password === '') {
        $errorMessage = 'Email and password are required.';
    } else {
        $auth = new AuthManager();
        $result = $auth->loginUser($email, $password);

        if ($result['success']) {
            header('Location: ' . $redirectTarget);
            exit();
        } else {
            $errorMessage = $result['message'];
        }
    }
}

include './includes/user-header.php';
?>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white py-4">
                        <h3 class="mb-0">
                            <i class="fas fa-sign-in-alt me-2"></i> Welcome Back
                        </h3>
                        <p class="small mb-0 mt-2">Login to your DriveEasy account</p>
                    </div>
                    
                    <div class="card-body p-4">
                        <!-- Error Message -->
                        <div id="errorAlert" class="alert alert-danger d-none" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <span id="errorText"></span>
                        </div>
                        
                        <?php if (!empty($errorMessage)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo htmlspecialchars($errorMessage); ?>
                        </div>
                        <?php endif; ?>

                        <!-- Form -->
                        <form id="loginForm" method="POST" action="./login.php">
                            <input type="hidden" id="redirect" name="redirect" value="<?php echo htmlspecialchars($redirectTarget); ?>">
                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" required placeholder="your@email.com">
                            </div>
                            
                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required placeholder="Enter your password">
                            </div>
                            
                            <!-- Remember Me -->
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                <label class="form-check-label" for="remember">
                                    Remember me
                                </label>
                            </div>
                            
                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="fas fa-arrow-right me-2"></i> Login
                            </button>
                        </form>
                        
                        <!-- Divider -->
                        <hr>
                        
                        <!-- Register Link -->
                        <p class="text-center small mb-2">
                            Don't have an account? <a href="./register.php" class="text-primary fw-bold">Register here</a>
                        </p>
                        
                        <!-- Forgot Password -->
                        <p class="text-center small">
                            <a href="#" class="text-muted">Forgot your password?</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Collect form data
    const formData = new FormData(this);
    const data = {
        email: formData.get('email'),
        password: formData.get('password')
    };
    
    try {
        const response = await fetch('./api/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Redirect to dashboard or previous page
            const queryRedirect = new URLSearchParams(window.location.search).get('redirect');
            const formRedirect = document.getElementById('redirect') ? document.getElementById('redirect').value : '';
            const redirect = queryRedirect || formRedirect || './user/dashboard.php';
            window.location.href = redirect;
        } else {
            showError(result.message);
        }
    } catch (error) {
        showError('Login failed. Please try again.');
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
