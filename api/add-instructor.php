<?php
/**
 * Add Instructor Handler (Admin Only)
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

$name = isset($input['name']) ? trim($input['name']) : '';
$email = isset($input['email']) ? trim($input['email']) : '';
$phone = isset($input['phone']) ? trim($input['phone']) : '';
$specialty = isset($input['specialty']) ? trim($input['specialty']) : '';
$experience_years = isset($input['experience_years']) ? intval($input['experience_years']) : 0;
$bio = isset($input['bio']) ? trim($input['bio']) : '';

// Validation
if (empty($name) || empty($email) || empty($phone) || empty($specialty)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Name, email, phone, and specialty are required']);
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

// Check if user already exists by email
$user = getRow("SELECT id FROM users WHERE email = ?", "s", [$email]);
if ($user) {
    $user_id = $user['id'];

    // Check if already instructor assigned
    $existingInstructor = getRow("SELECT id FROM instructors WHERE user_id = ?", "i", [$user_id]);
    if ($existingInstructor) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Email already registered as an instructor']);
        exit();
    }
} else {
    // Create a user account for this instructor
    $defaultPassword = password_hash('Pass@1234', PASSWORD_BCRYPT, ['cost' => PASSWORD_COST]);
    $role = ROLE_INSTRUCTOR;
    $statusUser = STATUS_ACTIVE;

    $insertUserQuery = "INSERT INTO users (name, email, phone, password, role, status) VALUES (?, ?, ?, ?, ?, ?)";
    $userStmt = getConnection()->prepare($insertUserQuery);
    if (!$userStmt) {
        error_log("Prepare failed: " . getConnection()->error);
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error']);
        exit();
    }
    $userStmt->bind_param("ssssss", $name, $email, $phone, $defaultPassword, $role, $statusUser);
    if (!$userStmt->execute()) {
        error_log("Execute failed: " . $userStmt->error);
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to create instructor user account']);
        exit();
    }
    $user_id = getConnection()->insert_id;
}

$query = "INSERT INTO instructors (user_id, email, phone, experience_years, specialty, bio, status) 
          VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = getConnection()->prepare($query);

if (!$stmt) {
    error_log("Prepare failed: " . getConnection()->error);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit();
}

$status = STATUS_ACTIVE;

$stmt->bind_param(
    "ississs",
    $user_id,
    $email,
    $phone,
    $experience_years,
    $specialty,
    $bio,
    $status
);

if ($stmt->execute()) {
    http_response_code(201);
    echo json_encode([
        'success' => true, 
        'message' => 'Instructor added successfully',
        'instructor_id' => getConnection()->insert_id
    ]);
} else {
    error_log("Execute failed: " . $stmt->error);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to add instructor']);
}
?>
