<?php
/**
 * Delete Contact Message Handler (Admin Only)
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

if (empty($message_id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Message ID required']);
    exit();
}

// Check if message exists
$message = getRow("SELECT id FROM contact_messages WHERE id = ?", "i", [$message_id]);
if (!$message) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Message not found']);
    exit();
}

// Delete message
$query = "DELETE FROM contact_messages WHERE id = ?";

$stmt = getConnection()->prepare($query);

if (!$stmt) {
    error_log("Prepare failed: " . getConnection()->error);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit();
}

$stmt->bind_param("i", $message_id);

if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode([
        'success' => true, 
        'message' => 'Message deleted successfully'
    ]);
} else {
    error_log("Execute failed: " . $stmt->error);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to delete message']);
}
?>
