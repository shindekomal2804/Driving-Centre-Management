<?php
/**
 * Process Payment Handler
 */

header('Content-Type: application/json; charset=utf-8');
@ini_set('display_errors', '0');
ob_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/auth.php';

if (!AuthManager::isUserLoggedIn()) {
    http_response_code(401);
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$user = AuthManager::getCurrentUser();

$booking_id = isset($input['booking_id']) ? intval($input['booking_id']) : 0;
$payment_method = isset($input['payment_method']) ? trim($input['payment_method']) : 'card';

if ($booking_id <= 0) {
    http_response_code(400);
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'A valid booking is required for payment.']);
    exit();
}

$allowedMethods = ['card', 'upi', 'bank_transfer'];
if (!in_array($payment_method, $allowedMethods, true)) {
    http_response_code(400);
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid payment method selected.']);
    exit();
}

$booking = getRow(
    "SELECT id, status, payment_status, price
     FROM bookings
     WHERE id = ? AND user_id = ?",
    "ii",
    [$booking_id, $user['id']]
);

if (!$booking) {
    http_response_code(404);
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Booking not found.']);
    exit();
}

if (in_array($booking['status'], [BOOKING_CANCELLED, BOOKING_REJECTED], true)) {
    http_response_code(400);
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Payment is not available for this booking status.']);
    exit();
}

if ($booking['payment_status'] === 'paid') {
    $existingPayment = getRow(
        "SELECT transaction_id FROM payments WHERE booking_id = ? ORDER BY created_at DESC, id DESC LIMIT 1",
        "i",
        [$booking_id]
    );

    ob_clean();
    echo json_encode([
        'success' => true,
        'message' => 'This booking is already paid.',
        'transaction_id' => $existingPayment['transaction_id'] ?? null,
        'redirect_url' => './user/booking-details.php?id=' . $booking_id
    ]);
    exit();
}

$card_holder = isset($input['card_holder']) ? trim($input['card_holder']) : '';
$card_number = isset($input['card_number']) ? preg_replace('/\D+/', '', $input['card_number']) : '';
$expiry_date = isset($input['expiry_date']) ? trim($input['expiry_date']) : '';
$cvv = isset($input['cvv']) ? preg_replace('/\D+/', '', $input['cvv']) : '';
$upi_id = isset($input['upi_id']) ? trim($input['upi_id']) : '';

if ($payment_method === 'card') {
    if ($card_holder === '' || strlen($card_number) < 12 || strlen($card_number) > 19 || !preg_match('/^(0[1-9]|1[0-2])\/[0-9]{2}$/', $expiry_date) || !preg_match('/^[0-9]{3,4}$/', $cvv)) {
        http_response_code(400);
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Please enter valid card payment details.']);
        exit();
    }
}

if ($payment_method === 'upi' && !preg_match('/^[A-Za-z0-9.\-_]{2,}@[A-Za-z]{2,}$/', $upi_id)) {
    http_response_code(400);
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Please enter a valid UPI ID.']);
    exit();
}

$gateway = 'Sandbox Card Gateway';
if ($payment_method === 'upi') {
    $gateway = 'Sandbox UPI Gateway';
} elseif ($payment_method === 'bank_transfer') {
    $gateway = 'Manual Bank Transfer';
}

$transaction_id = 'TXN' . date('YmdHis') . random_int(1000, 9999);
$amount = (float) $booking['price'];
$paymentStatus = 'completed';
$receiptUrl = './payment.php?booking_id=' . $booking_id;

$conn = getConnection();

try {
    $conn->begin_transaction();

    $insertQuery = "INSERT INTO payments (user_id, booking_id, amount, payment_method, gateway, transaction_id, status, receipt_url)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $insertStmt = $conn->prepare($insertQuery);

    if (!$insertStmt) {
        throw new Exception('Unable to prepare the payment record.');
    }

    $insertStmt->bind_param(
        "iidsssss",
        $user['id'],
        $booking_id,
        $amount,
        $payment_method,
        $gateway,
        $transaction_id,
        $paymentStatus,
        $receiptUrl
    );

    if (!$insertStmt->execute()) {
        throw new Exception('Unable to save the payment.');
    }

    $updateStmt = $conn->prepare("UPDATE bookings SET payment_status = 'paid' WHERE id = ? AND user_id = ?");
    if (!$updateStmt) {
        throw new Exception('Unable to update booking payment status.');
    }

    $updateStmt->bind_param("ii", $booking_id, $user['id']);

    if (!$updateStmt->execute()) {
        throw new Exception('Unable to mark the booking as paid.');
    }

    $conn->commit();

    ob_clean();
    echo json_encode([
        'success' => true,
        'message' => 'Payment completed successfully.',
        'transaction_id' => $transaction_id,
        'redirect_url' => './user/booking-details.php?id=' . $booking_id
    ]);
} catch (Throwable $exception) {
    $conn->rollback();
    error_log('Payment processing failed: ' . $exception->getMessage());
    http_response_code(500);
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Unable to process the payment right now. Please try again.']);
}
?>
