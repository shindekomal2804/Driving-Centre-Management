<?php
/**
 * Admin Login Handler
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/auth.php';

// Get POST data
$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

$username = isset($input['username']) ? trim($input['username']) : '';
$password = isset($input['password']) ? $input['password'] : '';

// Validation
if (empty($username) || empty($password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Username and password are required']);
    exit();
}

// Login admin
$auth = new AuthManager();
$result = $auth->loginAdmin($username, $password);

if ($result['success']) {
    http_response_code(200);
    echo json_encode(array_merge($result, ['redirect' => 'dashboard.php']));
} else {
    http_response_code(401);
    echo json_encode($result);
}
?>
