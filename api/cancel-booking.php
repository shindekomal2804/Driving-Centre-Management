<?php
/**
 * Cancel Booking Handler (User)
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

$booking_id = isset($input['booking_id']) ? intval($input['booking_id']) : 0;

if (empty($booking_id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Booking ID required']);
    exit();
}

// Check if booking exists and belongs to user
$booking = getRow("SELECT id, status FROM bookings WHERE id = ? AND user_id = ?", "ii", [$booking_id, $user_id]);
if (!$booking) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Booking not found']);
    exit();
}

// Check if booking can be cancelled (only approved bookings can be cancelled by user)
if ($booking['status'] !== BOOKING_APPROVED) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Only approved bookings can be cancelled']);
    exit();
}

// Update booking status to cancelled
$query = "UPDATE bookings SET status = ?, updated_at = NOW() WHERE id = ?";
$stmt = executeQuery($query, "si", [BOOKING_CANCELLED, $booking_id]);

if ($stmt) {
    echo json_encode(['success' => true, 'message' => 'Booking cancelled successfully']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to cancel booking']);
}
?></content>
<parameter name="filePath">c:\xampp\htdocs\sj\api\cancel-booking.php