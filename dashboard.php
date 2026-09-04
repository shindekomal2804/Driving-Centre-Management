<?php
/**
 * Public dashboard route redirector.
 *
 * If user is logged in, sends to user dashboard.
 * If admin is logged in, sends to admin dashboard.
 * Otherwise, sends to login page.
 */

require_once './config/constants.php';
require_once './includes/auth.php';

// Ensure session loaded
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id'])) {
    header('Location: ./admin/dashboard.php');
    exit();
}

if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    header('Location: ./user/dashboard.php');
    exit();
}

// Unauthenticated users go to main landing page or login
header('Location: ./login.php');
exit();
