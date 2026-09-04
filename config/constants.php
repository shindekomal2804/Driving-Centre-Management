<?php
/**
 * Application Configuration
 * Set up paths, URL, and general settings
 */

// Display errors in development (disable in production)
define('SHOW_ERRORS', true);
if (SHOW_ERRORS) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// Website information
define('SITE_NAME', 'DriveEasy - Driving School');
define('SITE_URL', 'http://localhost/sj');
define('ADMIN_URL', SITE_URL . '/admin');
define('USER_URL', SITE_URL . '/user');

// Paths
define('BASE_PATH', __DIR__ . '/..');
define('INCLUDES_PATH', BASE_PATH . '/includes');
define('ASSETS_PATH', BASE_PATH . '/assets');
define('API_PATH', BASE_PATH . '/api');

// Session timeout (in seconds)
define('SESSION_TIMEOUT', 3600); // 1 hour

// Password hashing algorithm
define('PASSWORD_ALGO', PASSWORD_BCRYPT);
define('PASSWORD_COST', 10);

// Email configuration (optional)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-app-password');
define('FROM_EMAIL', 'noreply@driveeasy.com');
define('FROM_NAME', 'DriveEasy Driving School');

// Pagination
define('ITEMS_PER_PAGE', 10);

// File upload settings
define('MAX_FILE_SIZE', 5242880); // 5MB in bytes
define('ALLOWED_EXTENSIONS', array('jpg', 'jpeg', 'png', 'gif', 'pdf'));
define('UPLOAD_PATH', BASE_PATH . '/assets/uploads');

// Role definitions
define('ROLE_ADMIN', 'admin');
define('ROLE_USER', 'user');
define('ROLE_INSTRUCTOR', 'instructor');

// Default admin fallback (for initial setup)
define('DEFAULT_ADMIN_USERNAME', 'Admin');
define('DEFAULT_ADMIN_PASSWORD', 'admin123');

// Status definitions
define('STATUS_ACTIVE', 'active');
define('STATUS_INACTIVE', 'inactive');
define('STATUS_PENDING', 'pending');
define('STATUS_APPROVED', 'approved');
define('STATUS_REJECTED', 'rejected');
define('STATUS_CANCELLED', 'cancelled');

// Booking status
define('BOOKING_PENDING', 'pending');
define('BOOKING_APPROVED', 'approved');
define('BOOKING_REJECTED', 'rejected');
define('BOOKING_COMPLETED', 'completed');
define('BOOKING_CANCELLED', 'cancelled');

// Review status
define('REVIEW_PENDING', 'pending');
define('REVIEW_APPROVED', 'approved');
define('REVIEW_REJECTED', 'rejected');
?>
