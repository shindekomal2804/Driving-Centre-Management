<?php
/**
 * Update Contact Message Status Handler (Admin Only)
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

$message_id = isset($input['message_id']) ? intval($input['message_id']) : 0;
$status = isset($input['status']) ? trim($input['status']) : '';
$reply = isset($input['reply']) ? trim($input['reply']) : '';

if (empty($message_id) || empty($status)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Message ID and status required']);
    exit();
}

// Validate status
$valid_statuses = ['new', 'read', 'replied'];
if (!in_array($status, $valid_statuses)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit();
}

// Check if message exists
$message = getRow("SELECT id FROM contact_messages WHERE id = ?", "i", [$message_id]);
if (!$message) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Message not found']);
    exit();
}

// Update message status and reply
$query = "UPDATE contact_messages SET status = ?, reply = ?, reply_date = NOW() WHERE id = ?";

$stmt = getConnection()->prepare($query);

if (!$stmt) {
    error_log("Prepare failed: " . getConnection()->error);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit();
}

$stmt->bind_param("ssi", $status, $reply, $message_id);

if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode([
        'success' => true, 
        'message' => 'Message status updated successfully'
    ]);
} else {
    error_log("Execute failed: " . $stmt->error);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update message']);
}
?>
