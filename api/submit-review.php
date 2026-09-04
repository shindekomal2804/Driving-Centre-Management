<?php
/**
 * Submit Review Handler
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

$instructor_id = isset($input['instructor_id']) ? intval($input['instructor_id']) : 0;
$course_id = isset($input['course_id']) ? intval($input['course_id']) : 0;
$booking_id = isset($input['booking_id']) ? intval($input['booking_id']) : 0;
$rating = isset($input['rating']) ? intval($input['rating']) : 0;
$title = isset($input['title']) ? trim($input['title']) : '';
$comment = isset($input['comment']) ? trim($input['comment']) : '';

// Validation
if (empty($instructor_id) || empty($rating) || empty($title) || empty($comment)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit();
}

if ($rating < 1 || $rating > 5) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Rating must be between 1 and 5']);
    exit();
}

if (strlen($title) < 3 || strlen($title) > 100) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Title must be between 3 and 100 characters']);
    exit();
}

if (strlen($comment) < 10 || strlen($comment) > 1000) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Comment must be between 10 and 1000 characters']);
    exit();
}

// Verify instructor exists
$instructor = getRow("SELECT id FROM instructors WHERE id = ?", "i", [$instructor_id]);
if (!$instructor) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid instructor']);
    exit();
}

// Insert review
$query = "INSERT INTO reviews (user_id, instructor_id, course_id, booking_id, rating, title, comment, status) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = getConnection()->prepare($query);

if (!$stmt) {
    error_log("Prepare failed: " . getConnection()->error);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit();
}

$status = REVIEW_PENDING;
$stmt->bind_param(
    "iiiisss",
    $user_id,
    $instructor_id,
    $course_id,
    $booking_id,
    $rating,
    $title,
    $comment,
    $status
);

if ($stmt->execute()) {
    http_response_code(201);
    echo json_encode([
        'success' => true, 
        'message' => 'Review submitted successfully. It will appear after admin approval.',
        'review_id' => getConnection()->insert_id
    ]);
} else {
    error_log("Execute failed: " . $stmt->error);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to submit review']);
}
?>
