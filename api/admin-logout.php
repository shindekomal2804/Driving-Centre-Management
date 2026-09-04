<?php
/**
 * Admin Logout Handler
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/auth.php';

// Logout admin
AuthManager::logoutUser();

// Redirect to admin login page
header('Location: ' . SITE_URL . '/admin-login.php');
exit();
?>
