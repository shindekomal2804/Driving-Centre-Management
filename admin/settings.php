<?php
/**
 * Admin - Settings
 */

$page_title = "Settings";
$css_path = "../assets/css/style.css";
$js_path = "../assets/js/admin-script.js";

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/auth.php';

if (!AuthManager::isAdminLoggedIn()) {
    header('Location: ../admin-login.php');
    exit();
}

include __DIR__ . '/../includes/admin-header.php';
?>

<div class="row">
    <!-- Settings Menu -->
    <div class="col-md-3 mb-4">
        <div class="card shadow">
            <div class="list-group list-group-flush">
                <a href="#general-settings" class="list-group-item list-group-item-action active" data-bs-toggle="tab">
                    <i class="fas fa-cog me-2"></i> General Settings
                </a>
                <a href="#site-info" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                    <i class="fas fa-info-circle me-2"></i> Site Information
                </a>
                <a href="#email-config" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                    <i class="fas fa-envelope me-2"></i> Email Configuration
                </a>
                <a href="#seo-settings" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                    <i class="fas fa-search me-2"></i> SEO Settings
                </a>
            </div>
        </div>
    </div>
    
    <!-- Settings Content -->
    <div class="col-md-9">
        <div class="tab-content">
            <!-- General Settings Tab -->
            <div class="tab-pane fade show active" id="general-settings">
                <div class="card shadow">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">General Settings</h5>
                    </div>
                    <div class="card-body p-4">
                        <form id="generalSettingsForm" class="admin-form" method="POST">
                            <div class="mb-3">
                                <label for="site_name" class="form-label">Site Name</label>
                                <input type="text" class="form-control" id="site_name" name="site_name" value="DriveEasy" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="site_timezone" class="form-label">Timezone</label>
                                <select class="form-select" id="site_timezone" name="site_timezone" required>
                                    <option value="Asia/Kolkata">Asia/Kolkata (IST)</option>
                                    <option value="UTC">UTC</option>
                                    <option value="Asia/Dubai">Asia/Dubai</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="currency" class="form-label">Currency</label>
                                <input type="text" class="form-control" id="currency" name="currency" value="INR" required>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode">
                                <label class="form-check-label" for="maintenance_mode">
                                    Enable Maintenance Mode
                                </label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Save Settings
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Site Information Tab -->
            <div class="tab-pane fade" id="site-info">
                <div class="card shadow">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Site Information</h5>
                    </div>
                    <div class="card-body p-4">
                        <form id="siteInfoForm" class="admin-form" method="POST">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Contact Phone</label>
                                <input type="tel" class="form-control" id="phone" name="phone" value="+91-9876543210" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Contact Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="info@driveeasy.com" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3">123 Driving Avenue, City, State 12345</textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="working_hours" class="form-label">Working Hours</label>
                                <textarea class="form-control" id="working_hours" name="working_hours" rows="2">Mon-Sun: 06:00 AM - 08:00 PM</textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Save Information
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Email Configuration Tab -->
            <div class="tab-pane fade" id="email-config">
                <div class="card shadow">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Email Configuration</h5>
                    </div>
                    <div class="card-body p-4">
                        <form id="emailConfigForm" class="admin-form" method="POST">
                            <div class="mb-3">
                                <label for="smtp_host" class="form-label">SMTP Host</label>
                                <input type="text" class="form-control" id="smtp_host" name="smtp_host" value="smtp.gmail.com">
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="smtp_port" class="form-label">SMTP Port</label>
                                    <input type="number" class="form-control" id="smtp_port" name="smtp_port" value="587">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="smtp_encryption" class="form-label">Encryption</label>
                                    <select class="form-select" id="smtp_encryption" name="smtp_encryption">
                                        <option value="TLS">TLS</option>
                                        <option value="SSL">SSL</option>
                                        <option value="None">None</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="smtp_user" class="form-label">SMTP Username</label>
                                <input type="email" class="form-control" id="smtp_user" name="smtp_user" placeholder="your-email@gmail.com">
                            </div>
                            
                            <div class="mb-3">
                                <label for="smtp_password" class="form-label">SMTP Password</label>
                                <input type="password" class="form-control" id="smtp_password" name="smtp_password" placeholder="App password">
                            </div>
                            
                            <div class="mb-3">
                                <label for="from_email" class="form-label">From Email</label>
                                <input type="email" class="form-control" id="from_email" name="from_email" value="noreply@driveeasy.com">
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Save Configuration
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- SEO Settings Tab -->
            <div class="tab-pane fade" id="seo-settings">
                <div class="card shadow">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">SEO Settings</h5>
                    </div>
                    <div class="card-body p-4">
                        <form id="seoForm" class="admin-form" method="POST">
                            <div class="mb-3">
                                <label for="meta_description" class="form-label">Meta Description</label>
                                <textarea class="form-control" id="meta_description" name="meta_description" rows="2" placeholder="Page meta description for search engines"></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="meta_keywords" class="form-label">Meta Keywords</label>
                                <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" placeholder="keyword1, keyword2, keyword3">
                            </div>
                            
                            <div class="mb-3">
                                <label for="google_analytics" class="form-label">Google Analytics ID</label>
                                <input type="text" class="form-control" id="google_analytics" name="google_analytics" placeholder="UA-XXXXXXXXX-X">
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Save SEO Settings
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
