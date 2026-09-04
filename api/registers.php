<?php
/**
 * Register User Handler
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

$name = isset($input['name']) ? trim($input['name']) : '';
$email = isset($input['email']) ? trim($input['email']) : '';
$phone = isset($input['phone']) ? trim($input['phone']) : '';
$password = isset($input['password']) ? $input['password'] : '';
$confirm_password = isset($input['confirm_password']) ? $input['confirm_password'] : '';

// Validation
if (empty($name) || empty($email) || empty($phone) || empty($password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit();
}

if ($password !== $confirm_password) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
    exit();
}

if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit();
}

if (!preg_match('/^[0-9]{10}$/', preg_replace('/\D/', '', $phone))) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Phone must be 10 digits']);
    exit();
}

// Register user
$auth = new AuthManager();
$result = $auth->registerUser($name, $email, $phone, $password);

if ($result['success']) {
    http_response_code(201);
    echo json_encode($result);
} else {
    http_response_code(400);
    echo json_encode($result);
}
?>
