<?php
/**
 * Update User Profile Handler
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/auth.php';

// Check if user is logged in
if (!AuthManager::isUserLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$user = AuthManager::getCurrentUser();
$user_id = $user['id'];

$name = isset($input['name']) ? trim($input['name']) : '';
$phone = isset($input['phone']) ? trim($input['phone']) : '';
$dob = isset($input['dob']) ? trim($input['dob']) : '';
$address = isset($input['address']) ? trim($input['address']) : '';
$city = isset($input['city']) ? trim($input['city']) : '';
$state = isset($input['state']) ? trim($input['state']) : '';
$postal_code = isset($input['postal_code']) ? trim($input['postal_code']) : '';

// Validation
if (empty($name)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Name is required']);
    exit();
}

if (!empty($phone) && !preg_match('/^\d{10}$/', $phone)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Phone must be 10 digits']);
    exit();
}

// Update user
$query = "UPDATE users SET name = ?, phone = ?, date_of_birth = ?, address = ?, city = ?, state = ?, postal_code = ? 
          WHERE id = ?";

$stmt = getConnection()->prepare($query);

if (!$stmt) {
    error_log("Prepare failed: " . getConnection()->error);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit();
}

$stmt->bind_param(
    "sssssss",
    $name,
    $phone,
    $dob,
    $address,
    $city,
    $state,
    $postal_code
);

// Update the binding to include user_id (it was missing!)
// Re-prepare with correct binding
$stmt = getConnection()->prepare($query);
$stmt->bind_param(
    "ssssssi",
    $name,
    $phone,
    $dob,
    $address,
    $city,
    $state,
    $postal_code,
    $user_id
);

if ($stmt->execute()) {
    // Update session
    $_SESSION['user_name'] = $name;
    
    http_response_code(200);
    echo json_encode([
        'success' => true, 
        'message' => 'Profile updated successfully'
    ]);
} else {
    error_log("Execute failed: " . $stmt->error);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
}
?>
