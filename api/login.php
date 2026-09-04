<?php
/**
 * User Login Handler
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

$email = isset($input['email']) ? trim($input['email']) : '';
$password = isset($input['password']) ? $input['password'] : '';

// Validation
if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email and password are required']);
    exit();
}

// Login user
$auth = new AuthManager();
$result = $auth->loginUser($email, $password);

if ($result['success']) {
    http_response_code(200);
    echo json_encode(array_merge($result, ['redirect' => './dashboard.php']));
} else {
    http_response_code(401);
    echo json_encode($result);
}
?>
