<?php
/**
 * Update Course Handler (Admin Only)
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

$course_id = isset($input['course_id']) ? intval($input['course_id']) : 0;
$title = isset($input['title']) ? trim($input['title']) : '';
$description = isset($input['description']) ? trim($input['description']) : '';
$level = isset($input['level']) ? trim($input['level']) : '';
$price = isset($input['price']) ? floatval($input['price']) : 0;
$duration_weeks = isset($input['duration_weeks']) ? intval($input['duration_weeks']) : 0;
$duration_hours = isset($input['duration_hours']) ? intval($input['duration_hours']) : 0;
$features = isset($input['features']) ? trim($input['features']) : '';
$vehicle_types = isset($input['vehicle_types']) ? trim($input['vehicle_types']) : '';
$status = isset($input['status']) ? trim($input['status']) : STATUS_ACTIVE;

// Validation
if (empty($course_id) || empty($title) || empty($level) || $price <= 0 || $duration_hours <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
    exit();
}

// Check course exists
$course = getRow("SELECT id FROM courses WHERE id = ?", "i", [$course_id]);
if (!$course) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Course not found']);
    exit();
}

// Validate level
$valid_levels = ['beginner', 'intermediate', 'advanced'];
if (!in_array(strtolower($level), $valid_levels)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid course level']);
    exit();
}

// Update course
$query = "UPDATE courses SET title = ?, description = ?, level = ?, price = ?, 
          duration_weeks = ?, duration_hours = ?, features = ?, vehicle_types = ?, status = ? 
          WHERE id = ?";

$stmt = getConnection()->prepare($query);

if (!$stmt) {
    error_log("Prepare failed: " . getConnection()->error);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit();
}

$level = strtolower($level);
$stmt->bind_param(
    "sssdiiissi",
    $title,
    $description,
    $level,
    $price,
    $duration_weeks,
    $duration_hours,
    $features,
    $vehicle_types,
    $status,
    $course_id
);

if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode([
        'success' => true, 
        'message' => 'Course updated successfully'
    ]);
} else {
    error_log("Execute failed: " . $stmt->error);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update course']);
}
?>
