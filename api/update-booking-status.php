<?php
/**
 * Update Booking Status Handler (Admin Only)
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

$booking_id = isset($input['booking_id']) ? intval($input['booking_id']) : 0;
$status = isset($input['status']) ? trim($input['status']) : '';
$admin_notes = isset($input['admin_notes']) ? trim($input['admin_notes']) : '';

if (empty($booking_id) || empty($status)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Booking ID and status required']);
    exit();
}

// Validate status
$valid_statuses = [BOOKING_PENDING, BOOKING_APPROVED, BOOKING_REJECTED, BOOKING_COMPLETED, BOOKING_CANCELLED];
if (!in_array($status, $valid_statuses)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit();
}

// Check if booking exists
$booking = getRow("SELECT id FROM bookings WHERE id = ?", "i", [$booking_id]);
if (!$booking) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Booking not found']);
    exit();
}

// Update booking status
$query = "UPDATE bookings SET status = ?, admin_notes = ? WHERE id = ?";

$stmt = getConnection()->prepare($query);

if (!$stmt) {
    error_log("Prepare failed: " . getConnection()->error);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit();
}

$stmt->bind_param("ssi", $status, $admin_notes, $booking_id);

if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode([
        'success' => true, 
        'message' => 'Booking status updated successfully'
    ]);
} else {
    error_log("Execute failed: " . $stmt->error);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update booking']);
}
?>
