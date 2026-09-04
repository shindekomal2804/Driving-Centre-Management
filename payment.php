<?php
/**
 * Payment Page
 */

$page_title = "Secure Payment";
$css_path = "./assets/css/style.css";
$js_path = "./assets/js/script.js";

require_once './config/database.php';
require_once './config/constants.php';
require_once './includes/auth.php';

if (!AuthManager::isUserLoggedIn()) {
    $redirectUrl = './login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']);
    header('Location: ' . $redirectUrl);
    exit();
}

$user = AuthManager::getCurrentUser();
$booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;

$openBookings = getRows(
    "SELECT b.id, b.booking_date, b.start_time, b.price, b.payment_status, c.title AS course_name
     FROM bookings b
     JOIN courses c ON b.course_id = c.id
     WHERE b.user_id = ? AND b.status NOT IN (?, ?)
     ORDER BY b.created_at DESC",
    "iss",
    [$user['id'], BOOKING_CANCELLED, BOOKING_REJECTED]
);

if ($booking_id <= 0 && !empty($openBookings)) {
    foreach ($openBookings as $candidate) {
        if ($candidate['payment_status'] !== 'paid') {
            $booking_id = (int) $candidate['id'];
            break;
        }
    }

    if ($booking_id <= 0) {
        $booking_id = (int) $openBookings[0]['id'];
    }
}

$booking = null;
$latestPayment = null;

if ($booking_id > 0) {
    $booking = getRow(
        "SELECT b.*, c.title AS course_name, c.level, u.name AS instructor_name
         FROM bookings b
         JOIN courses c ON b.course_id = c.id
         JOIN instructors i ON b.instructor_id = i.id
         JOIN users u ON i.user_id = u.id
         WHERE b.id = ? AND b.user_id = ?",
        "ii",
        [$booking_id, $user['id']]
    );

    if ($booking) {
        $latestPayment = getRow(
            "SELECT transaction_id, payment_method, status, created_at
             FROM payments
             WHERE booking_id = ?
             ORDER BY created_at DESC, id DESC
             LIMIT 1",
            "i",
            [$booking['id']]
        );
    }
}

$paymentComplete = $booking && $booking['payment_status'] === 'paid';
$paymentBlocked = $booking && in_array($booking['status'], [BOOKING_CANCELLED, BOOKING_REJECTED], true);

include './includes/user-header.php';
?>

<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h2 class="mb-1"><i class="fas fa-credit-card me-2"></i> Payment Page</h2>
                <p class="text-muted mb-0">Complete your booking payment and receive an instant confirmation.</p>
            </div>
            <a href="./user/bookings.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> My Bookings
            </a>
        </div>

        <?php if (!empty($openBookings)): ?>
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body py-3">
                    <label for="bookingSwitcher" class="form-label fw-bold mb-2">Choose a booking</label>
                    <select id="bookingSwitcher" class="form-select" onchange="if (this.value) window.location.href = './payment.php?booking_id=' + this.value;">
                        <?php foreach ($openBookings as $entry): ?>
                            <option value="<?php echo (int) $entry['id']; ?>" <?php echo $booking && (int) $booking['id'] === (int) $entry['id'] ? 'selected' : ''; ?>>
                                #<?php echo (int) $entry['id']; ?> - <?php echo htmlspecialchars($entry['course_name']); ?> - ₹<?php echo number_format($entry['price'], 2); ?>
                                (<?php echo ucfirst(htmlspecialchars($entry['payment_status'])); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!$booking): ?>
            <div class="alert alert-info shadow-sm">
                <i class="fas fa-info-circle me-2"></i>
                You do not have any active bookings waiting for payment right now.
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="./book-lesson.php" class="btn btn-primary">
                    <i class="fas fa-calendar-plus me-2"></i> Book a Lesson
                </a>
                <a href="./user/bookings.php" class="btn btn-outline-secondary">View Bookings</a>
            </div>
        <?php else: ?>
            <div class="alert alert-secondary border-0 shadow-sm mb-4">
                <i class="fas fa-shield-alt me-2 text-primary"></i>
                <strong>Sandbox checkout:</strong> this page records the payment inside the project and updates the booking instantly.
            </div>

            <div id="paymentSuccess" class="alert alert-success d-none" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <span id="paymentSuccessMessage"></span>
            </div>

            <div id="paymentError" class="alert alert-danger d-none" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <span id="paymentErrorMessage"></span>
            </div>

            <div class="row g-4">
                <div class="col-lg-7">
                    <?php if ($paymentComplete): ?>
                        <div class="card shadow border-0">
                            <div class="card-body p-4 p-lg-5">
                                <div class="text-center mb-4">
                                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success text-white mb-3" style="width: 72px; height: 72px;">
                                        <i class="fas fa-check fa-2x"></i>
                                    </div>
                                    <h4 class="mb-2">Payment Completed</h4>
                                    <p class="text-muted mb-0">Booking #<?php echo (int) $booking['id']; ?> has already been marked as paid.</p>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <div class="border rounded-3 p-3 h-100">
                                            <small class="text-muted d-block">Transaction ID</small>
                                            <strong><?php echo htmlspecialchars($latestPayment['transaction_id'] ?? 'Recorded'); ?></strong>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="border rounded-3 p-3 h-100">
                                            <small class="text-muted d-block">Method</small>
                                            <strong><?php echo ucwords(str_replace('_', ' ', htmlspecialchars($latestPayment['payment_method'] ?? 'card'))); ?></strong>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex gap-2 flex-wrap">
                                    <a href="./user/booking-details.php?id=<?php echo (int) $booking['id']; ?>" class="btn btn-primary">
                                        <i class="fas fa-receipt me-2"></i> View Receipt Details
                                    </a>
                                    <a href="./user/bookings.php" class="btn btn-outline-secondary">Back to Bookings</a>
                                </div>
                            </div>
                        </div>
                    <?php elseif ($paymentBlocked): ?>
                        <div class="alert alert-warning shadow-sm mb-0">
                            <i class="fas fa-ban me-2"></i>
                            Payments are unavailable for bookings that were cancelled or rejected.
                        </div>
                    <?php else: ?>
                        <div class="card shadow border-0">
                            <div class="card-header bg-primary text-white py-3">
                                <h5 class="mb-0"><i class="fas fa-lock me-2"></i> Enter Payment Details</h5>
                            </div>
                            <div class="card-body p-4">
                                <form id="paymentForm" novalidate>
                                    <input type="hidden" name="booking_id" value="<?php echo (int) $booking['id']; ?>">

                                    <div class="mb-4">
                                        <label class="form-label fw-bold d-block">Payment Method</label>
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <input class="btn-check" type="radio" name="payment_method" id="payment_card" value="card" checked>
                                                <label class="btn btn-outline-primary w-100 py-3" for="payment_card">
                                                    <i class="fas fa-credit-card d-block mb-2"></i> Card
                                                </label>
                                            </div>
                                            <div class="col-md-4">
                                                <input class="btn-check" type="radio" name="payment_method" id="payment_upi" value="upi">
                                                <label class="btn btn-outline-primary w-100 py-3" for="payment_upi">
                                                    <i class="fas fa-mobile-screen-button d-block mb-2"></i> UPI
                                                </label>
                                            </div>
                                            <div class="col-md-4">
                                                <input class="btn-check" type="radio" name="payment_method" id="payment_bank" value="bank_transfer">
                                                <label class="btn btn-outline-primary w-100 py-3" for="payment_bank">
                                                    <i class="fas fa-building-columns d-block mb-2"></i> Bank Transfer
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="cardFields" class="payment-fields">
                                        <div class="mb-3">
                                            <label for="card_holder" class="form-label fw-bold">Cardholder Name</label>
                                            <input type="text" class="form-control form-control-lg" id="card_holder" name="card_holder" placeholder="Name on card">
                                        </div>

                                        <div class="mb-3">
                                            <label for="card_number" class="form-label fw-bold">Card Number</label>
                                            <input type="text" class="form-control form-control-lg" id="card_number" name="card_number" maxlength="19" inputmode="numeric" placeholder="4242 4242 4242 4242">
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="expiry_date" class="form-label fw-bold">Expiry</label>
                                                <input type="text" class="form-control form-control-lg" id="expiry_date" name="expiry_date" maxlength="5" placeholder="MM/YY">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="cvv" class="form-label fw-bold">CVV</label>
                                                <input type="password" class="form-control form-control-lg" id="cvv" name="cvv" maxlength="4" inputmode="numeric" placeholder="123">
                                            </div>
                                        </div>
                                    </div>

                                    <div id="upiFields" class="payment-fields d-none">
                                        <label for="upi_id" class="form-label fw-bold">UPI ID</label>
                                        <input type="text" class="form-control form-control-lg" id="upi_id" name="upi_id" placeholder="yourname@bank">
                                        <div class="form-text">Example: learner@oksbi</div>
                                    </div>

                                    <div id="bankFields" class="payment-fields d-none">
                                        <div class="alert alert-info mb-0">
                                            <i class="fas fa-circle-info me-2"></i>
                                            Submit this to record a bank transfer payment against your booking.
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-lg w-100 mt-4" id="payNowButton">
                                        <i class="fas fa-lock me-2"></i> Pay ₹<?php echo number_format($booking['price'], 2); ?>
                                    </button>

                                    <p class="text-muted small mt-3 mb-0">
                                        Your payment receipt will be linked to booking #<?php echo (int) $booking['id']; ?>.
                                    </p>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-lg-5">
                    <div class="card shadow border-0 sticky-top" style="top: 20px;">
                        <div class="card-header bg-light py-3">
                            <h5 class="mb-0">Booking Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <small class="text-muted d-block">Booking ID</small>
                                <strong>#<?php echo (int) $booking['id']; ?></strong>
                            </div>
                            <hr>
                            <div class="mb-3">
                                <small class="text-muted d-block">Course</small>
                                <strong><?php echo htmlspecialchars($booking['course_name']); ?></strong>
                            </div>
                            <hr>
                            <div class="mb-3">
                                <small class="text-muted d-block">Instructor</small>
                                <strong><?php echo htmlspecialchars($booking['instructor_name']); ?></strong>
                            </div>
                            <hr>
                            <div class="mb-3">
                                <small class="text-muted d-block">Schedule</small>
                                <strong><?php echo date('d M Y', strtotime($booking['booking_date'])); ?></strong><br>
                                <span class="text-muted"><?php echo date('h:i A', strtotime($booking['start_time'])); ?> - <?php echo date('h:i A', strtotime($booking['end_time'])); ?></span>
                            </div>
                            <hr>
                            <div class="mb-3">
                                <small class="text-muted d-block">Vehicle</small>
                                <strong><?php echo ucwords(str_replace('_', ' ', htmlspecialchars($booking['vehicle_type']))); ?></strong>
                            </div>
                            <?php if (!empty($booking['location'])): ?>
                                <hr>
                                <div class="mb-3">
                                    <small class="text-muted d-block">Location</small>
                                    <strong><?php echo htmlspecialchars($booking['location']); ?></strong>
                                </div>
                            <?php endif; ?>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold">Total Amount</span>
                                <span class="text-primary fw-bold fs-5">₹<?php echo number_format($booking['price'], 2); ?></span>
                            </div>

                            <div class="alert alert-info mt-4 mb-0 small">
                                <i class="fas fa-receipt me-2"></i>
                                Payment status for this booking is currently
                                <strong><?php echo ucfirst(htmlspecialchars($booking['payment_status'])); ?></strong>.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
(function () {
    const paymentForm = document.getElementById('paymentForm');
    const paymentSuccess = document.getElementById('paymentSuccess');
    const paymentError = document.getElementById('paymentError');
    const successMessage = document.getElementById('paymentSuccessMessage');
    const errorMessage = document.getElementById('paymentErrorMessage');

    function showError(message) {
        if (!paymentError || !errorMessage) {
            return;
        }
        errorMessage.textContent = message;
        paymentError.classList.remove('d-none');
        if (paymentSuccess) {
            paymentSuccess.classList.add('d-none');
        }
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function showSuccess(message) {
        if (!paymentSuccess || !successMessage) {
            return;
        }
        successMessage.textContent = message;
        paymentSuccess.classList.remove('d-none');
        if (paymentError) {
            paymentError.classList.add('d-none');
        }
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function togglePaymentFields() {
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
        const method = selectedMethod ? selectedMethod.value : 'card';

        const cardFields = document.getElementById('cardFields');
        const upiFields = document.getElementById('upiFields');
        const bankFields = document.getElementById('bankFields');

        if (cardFields) {
            cardFields.classList.toggle('d-none', method !== 'card');
        }
        if (upiFields) {
            upiFields.classList.toggle('d-none', method !== 'upi');
        }
        if (bankFields) {
            bankFields.classList.toggle('d-none', method !== 'bank_transfer');
        }
    }

    document.querySelectorAll('input[name="payment_method"]').forEach(function (input) {
        input.addEventListener('change', togglePaymentFields);
    });

    const cardNumberInput = document.getElementById('card_number');
    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', function () {
            const digits = this.value.replace(/\D/g, '').substring(0, 16);
            this.value = digits.replace(/(.{4})/g, '$1 ').trim();
        });
    }

    const expiryInput = document.getElementById('expiry_date');
    if (expiryInput) {
        expiryInput.addEventListener('input', function () {
            const digits = this.value.replace(/\D/g, '').substring(0, 4);
            if (digits.length >= 3) {
                this.value = digits.substring(0, 2) + '/' + digits.substring(2);
            } else {
                this.value = digits;
            }
        });
    }

    togglePaymentFields();

    if (!paymentForm) {
        return;
    }

    paymentForm.addEventListener('submit', async function (event) {
        event.preventDefault();

        const submitButton = document.getElementById('payNowButton');
        const originalLabel = submitButton ? submitButton.innerHTML : '';
        const formData = new FormData(paymentForm);
        const payload = Object.fromEntries(formData.entries());

        if (submitButton) {
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processing...';
        }

        try {
            const response = await fetch('./api/process-payment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const rawText = await response.text();
            let result = {};

            try {
                result = JSON.parse(rawText);
            } catch (parseError) {
                showError('Unexpected response from payment server.');
                return;
            }

            if (!response.ok || !result.success) {
                showError(result.message || 'Payment could not be completed.');
                return;
            }

            const transactionNote = result.transaction_id ? ' Transaction ID: ' + result.transaction_id : '';
            showSuccess((result.message || 'Payment completed successfully.') + transactionNote);

            setTimeout(function () {
                window.location.href = result.redirect_url || './user/booking-details.php?id=' + payload.booking_id;
            }, 1600);
        } catch (error) {
            showError(error.message || 'Unable to process your payment right now.');
        } finally {
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.innerHTML = originalLabel;
            }
        }
    });
})();
</script>

<?php include './includes/user-footer.php'; ?>
