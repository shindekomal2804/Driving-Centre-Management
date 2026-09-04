<?php
/**
 * User Profile Page
 */

$page_title = "My Profile";
$css_path = "../assets/css/style.css";
$js_path = "../assets/js/script.js";

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/auth.php';

// Check if user is logged in
if (!AuthManager::isUserLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

// Get current user
$user = AuthManager::getCurrentUser();
$user_data = getRow("SELECT * FROM users WHERE id = ?", "i", [$user['id']]);

include __DIR__ . '/../includes/user-header.php';
?>

<section class="py-5 bg-light">
    <div class="container">
        <h2 class="mb-4">
            <i class="fas fa-user-circle me-2"></i> My Profile
        </h2>
        
        <div class="row">
            <!-- Profile Sidebar -->
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fas fa-user-circle fa-5x text-primary"></i>
                        </div>
                        <h5><?php echo htmlspecialchars($user_data['name']); ?></h5>
                        <p class="text-muted small mb-3"><?php echo htmlspecialchars($user_data['email']); ?></p>
                        <span class="badge bg-success mb-3">
                            <i class="fas fa-check-circle me-1"></i> <?php echo htmlspecialchars($user_data['status']); ?>
                        </span>
                        <div class="mt-4">
                            <small class="text-muted d-block mb-2">Member since</small>
                            <p class="mb-0"><?php echo date('d M Y', strtotime($user_data['created_at'])); ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Menu -->
                <div class="card shadow">
                    <div class="list-group list-group-flush">
                        <a href="#profile-info" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                            <i class="fas fa-user me-2"></i> Profile Information
                        </a>
                        <a href="#change-password" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                            <i class="fas fa-lock me-2"></i> Change Password
                        </a>
                        <a href="../api/logout.php" class="list-group-item list-group-item-action text-danger">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Profile Content -->
            <div class="col-md-8">
                <div class="tab-content">
                    <!-- Profile Information Tab -->
                    <div class="tab-pane fade show active" id="profile-info">
                        <div class="card shadow">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Personal Information</h5>
                            </div>
                            <div class="card-body p-4">
                                <form id="profileForm" method="POST">
                                    <!-- Full Name -->
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Full Name</label>
                                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user_data['name']); ?>" required>
                                    </div>
                                    
                                    <!-- Email -->
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" readonly>
                                        <small class="form-text text-muted">Email cannot be changed</small>
                                    </div>
                                    
                                    <!-- Phone -->
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user_data['phone']); ?>" required>
                                    </div>
                                    
                                    <!-- Date of Birth -->
                                    <div class="mb-3">
                                        <label for="dob" class="form-label">Date of Birth</label>
                                        <input type="date" class="form-control" id="dob" name="dob" value="<?php echo htmlspecialchars($user_data['date_of_birth'] ?? ''); ?>">
                                    </div>
                                    
                                    <!-- Address -->
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <textarea class="form-control" id="address" name="address" rows="2"><?php echo htmlspecialchars($user_data['address'] ?? ''); ?></textarea>
                                    </div>
                                    
                                    <div class="row">
                                        <!-- City -->
                                        <div class="col-md-6 mb-3">
                                            <label for="city" class="form-label">City</label>
                                            <input type="text" class="form-control" id="city" name="city" value="<?php echo htmlspecialchars($user_data['city'] ?? ''); ?>">
                                        </div>
                                        
                                        <!-- State -->
                                        <div class="col-md-6 mb-3">
                                            <label for="state" class="form-label">State</label>
                                            <input type="text" class="form-control" id="state" name="state" value="<?php echo htmlspecialchars($user_data['state'] ?? ''); ?>">
                                        </div>
                                    </div>
                                    
                                    <!-- Postal Code -->
                                    <div class="mb-3">
                                        <label for="postal_code" class="form-label">Postal Code</label>
                                        <input type="text" class="form-control" id="postal_code" name="postal_code" value="<?php echo htmlspecialchars($user_data['postal_code'] ?? ''); ?>">
                                    </div>
                                    
                                    <!-- Submit -->
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i> Save Changes
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Change Password Tab -->
                    <div class="tab-pane fade" id="change-password">
                        <div class="card shadow">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Change Password</h5>
                            </div>
                            <div class="card-body p-4">
                                <form id="passwordForm" method="POST">
                                    <!-- Current Password -->
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">Current Password</label>
                                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                                    </div>
                                    
                                    <!-- New Password -->
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                                        <small class="form-text text-muted">Minimum 6 characters</small>
                                    </div>
                                    
                                    <!-- Confirm Password -->
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirm Password</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    </div>
                                    
                                    <!-- Submit -->
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-key me-2"></i> Change Password
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/user-footer.php'; ?>
