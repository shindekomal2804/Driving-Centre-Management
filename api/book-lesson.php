<?php
/**
 * Book Lesson Handler
 */

header('Content-Type: application/json; charset=utf-8');
@ini_set('display_errors', '0');
ob_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/auth.php';

// Check if user is logged in
if (!AuthManager::isUserLoggedIn()) {
    http_response_code(401);
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

// Get current user
$user = AuthManager::getCurrentUser();

$course_id = isset($input['course_id']) ? intval($input['course_id']) : 0;
$instructor_id = isset($input['instructor_id']) ? intval($input['instructor_id']) : 0;
$booking_date = isset($input['booking_date']) ? trim($input['booking_date']) : '';
$start_time = isset($input['start_time']) ? trim($input['start_time']) : '';
$duration_hours = isset($input['duration_hours']) ? intval($input['duration_hours']) : 0;
$vehicle_type = isset($input['vehicle_type']) ? trim($input['vehicle_type']) : '';
$location = isset($input['location']) ? trim($input['location']) : '';
$notes = isset($input['notes']) ? trim($input['notes']) : '';

// Validation
if (empty($course_id) || empty($instructor_id) || empty($booking_date) || empty($start_time) || empty($vehicle_type)) {
    http_response_code(400);
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
    exit();
}

// Validate date is not in the past
if (strtotime($booking_date) < strtotime(date('Y-m-d'))) {
    http_response_code(400);
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Booking date cannot be in the past']);
    exit();
}

// Get course details
$course = getRow("SELECT price FROM courses WHERE id = ?", "i", [$course_id]);
if (!$course) {
    http_response_code(400);
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid course selected']);
    exit();
}

// Get instructor details
$instructor = getRow("SELECT id FROM instructors WHERE id = ? AND status = ?", "is", [$instructor_id, STATUS_ACTIVE]);
if (!$instructor) {
    http_response_code(400);
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid instructor selected']);
    exit();
}

// Calculate end time
$end_time = date('H:i:s', strtotime($start_time) + ($duration_hours * 3600));

// Calculate price
$price = $course['price'] * $duration_hours;

// Insert booking
$query = "INSERT INTO bookings (user_id, course_id, instructor_id, booking_date, start_time, end_time, 
                               duration_hours, vehicle_type, location, notes, status, price) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = getConnection()->prepare($query);

if (!$stmt) {
    error_log("Prepare failed: " . getConnection()->error);
    http_response_code(500);
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit();
}

$status = BOOKING_PENDING;
$stmt->bind_param(
    "iiisssissssd",
    $user['id'],
    $course_id,
    $instructor_id,
    $booking_date,
    $start_time,
    $end_time,
    $duration_hours,
    $vehicle_type,
    $location,
    $notes,
    $status,
    $price
);

if ($stmt->execute()) {
    http_response_code(201);
    ob_clean();
    echo json_encode([
        'success' => true, 
        'message' => 'Booking submitted successfully',
        'booking_id' => getConnection()->insert_id
    ]);
} else {
    error_log("Execute failed: " . $stmt->error);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to create booking']);
}
?>
