<?php
/**
 * Delete Instructor Handler (Admin Only)
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

if (empty($instructor_id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Instructor ID required']);
    exit();
}

// Check if instructor exists
$instructor = getRow("SELECT id FROM instructors WHERE id = ?", "i", [$instructor_id]);
if (!$instructor) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Instructor not found']);
    exit();
}

// Delete instructor
$query = "DELETE FROM instructors WHERE id = ?";

$stmt = getConnection()->prepare($query);

if (!$stmt) {
    error_log("Prepare failed: " . getConnection()->error);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit();
}

$stmt->bind_param("i", $instructor_id);

if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode([
        'success' => true, 
        'message' => 'Instructor deleted successfully'
    ]);
} else {
    error_log("Execute failed: " . $stmt->error);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to delete instructor']);
}
?>
