<?php
/**
 * Update Review Status Handler (Admin Only)
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

$review_id = isset($input['review_id']) ? intval($input['review_id']) : 0;
$status = isset($input['status']) ? trim($input['status']) : '';
$admin_response = isset($input['admin_response']) ? trim($input['admin_response']) : '';

if (empty($review_id) || empty($status)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Review ID and status required']);
    exit();
}

// Validate status
$valid_statuses = [REVIEW_PENDING, REVIEW_APPROVED, REVIEW_REJECTED];
if (!in_array($status, $valid_statuses)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit();
}

// Check if review exists
$review = getRow("SELECT id FROM reviews WHERE id = ?", "i", [$review_id]);
if (!$review) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Review not found']);
    exit();
}

// Update review status
$query = "UPDATE reviews SET status = ?, admin_response = ? WHERE id = ?";

$stmt = getConnection()->prepare($query);

if (!$stmt) {
    error_log("Prepare failed: " . getConnection()->error);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit();
}

$stmt->bind_param("ssi", $status, $admin_response, $review_id);

if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode([
        'success' => true, 
        'message' => 'Review status updated successfully'
    ]);
} else {
    error_log("Execute failed: " . $stmt->error);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update review']);
}
?>
