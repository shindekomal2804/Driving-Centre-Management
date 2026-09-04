<?php
/**
 * User Logout Handler
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/auth.php';

// Logout user
AuthManager::logoutUser();

// Redirect to login page
header('Location: ' . SITE_URL . '/login.php');
exit();
?>
