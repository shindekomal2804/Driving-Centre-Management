<?php
/**
 * Update Instructor Handler (Admin Only)
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/auth.php';

// Check if admin is logged in
if (!AuthManager::isAdminLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Admin access required']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$instructor_id = isset($input['instructor_id']) ? intval($input['instructor_id']) : 0;
$email = isset($input['email']) ? trim($input['email']) : '';
$phone = isset($input['phone']) ? trim($input['phone']) : '';
$specialty = isset($input['specialty']) ? trim($input['specialty']) : '';
$experience_years = isset($input['experience_years']) ? intval($input['experience_years']) : 0;
$bio = isset($input['bio']) ? trim($input['bio']) : '';
$status = isset($input['status']) ? trim($input['status']) : STATUS_ACTIVE;

// Validation
if (empty($instructor_id) || empty($email) || empty($phone) || empty($specialty)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
    exit();
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit();
}

// Validate phone
if (!preg_match('/^\d{10}$/', $phone)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Phone must be 10 digits']);
    exit();
}

// Check instructor exists
$instructor = getRow("SELECT id FROM instructors WHERE id = ?", "i", [$instructor_id]);
if (!$instructor) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Instructor not found']);
    exit();
}

// Check if email already exists for another instructor
$existing = getRow("SELECT id FROM instructors WHERE email = ? AND id != ?", "si", [$email, $instructor_id]);
if ($existing) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email already registered']);
    exit();
}

// Update instructor
$query = "UPDATE instructors SET email = ?, phone = ?, experience_years = ?, specialty = ?, bio = ?, status = ? 
          WHERE id = ?";

$stmt = getConnection()->prepare($query);

if (!$stmt) {
    error_log("Prepare failed: " . getConnection()->error);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit();
}

$stmt->bind_param(
    "ssissi",
    $email,
    $phone,
    $experience_years,
    $specialty,
    $bio,
    $status,
    $instructor_id
);

if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode([
        'success' => true, 
        'message' => 'Instructor updated successfully'
    ]);
} else {
    error_log("Execute failed: " . $stmt->error);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update instructor']);
}
?>
