<?php
/**
 * Admin Booking Details Page
 */

$page_title = "Booking Details";
$css_path = "../assets/css/style.css";
$js_path = "../assets/js/admin-script.js";

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/auth.php';

// Check if admin is logged in
if (!AuthManager::isAdminLoggedIn()) {
    header('Location: ../admin-login.php');
    exit();
}

// Get booking ID from URL
$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (empty($booking_id)) {
    header('Location: ./bookings.php');
    exit();
}

// Get booking details with related data
$query = "SELECT b.*, 
          c.title as course_name, c.description as course_description, c.level, c.vehicle_types,
          i.experience_years, i.specialty, i.bio, i.rating as instructor_rating,
          i_user.name as instructor_name, i_user.email as instructor_email, i_user.phone as instructor_phone,
          u.name as user_name, u.email as user_email, u.phone as user_phone
          FROM bookings b
          JOIN courses c ON b.course_id = c.id
          JOIN instructors i ON b.instructor_id = i.id
          JOIN users i_user ON i.user_id = i_user.id
          JOIN users u ON b.user_id = u.id
          WHERE b.id = ?";

$booking = getRow($query, "i", [$booking_id]);

if (!$booking) {
    header('Location: ./bookings.php');
    exit();
}

include __DIR__ . '/../includes/admin-header.php';
?>

<section class="py-5 bg-light">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="./dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="./bookings.php">Manage Bookings</a></li>
                        <li class="breadcrumb-item active">Booking #<?php echo $booking['id']; ?></li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <!-- Booking Details -->
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-calendar-check me-2"></i>
                            Booking Details
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="text-primary mb-3"><?php echo htmlspecialchars($booking['course_name']); ?></h5>
                                <p class="text-muted mb-3"><?php echo htmlspecialchars($booking['course_description']); ?></p>

                                <div class="mb-3">
                                    <strong>Level:</strong>
                                    <span class="badge bg-info ms-2"><?php echo htmlspecialchars($booking['level']); ?></span>
                                </div>

                                <div class="mb-3">
                                    <strong>Vehicle Type:</strong>
                                    <span class="text-muted"><?php echo htmlspecialchars($booking['vehicle_types']); ?></span>
                                </div>

                                <div class="mb-3">
                                    <strong>Duration:</strong>
                                    <span class="text-muted"><?php echo $booking['duration_hours']; ?> hours</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <strong>Booking Date:</strong>
                                    <span class="text-muted"><?php echo date('l, d F Y', strtotime($booking['booking_date'])); ?></span>
                                </div>

                                <div class="mb-3">
                                    <strong>Time:</strong>
                                    <span class="text-muted">
                                        <?php echo date('h:i A', strtotime($booking['start_time'])); ?> -
                                        <?php echo date('h:i A', strtotime($booking['end_time'])); ?>
                                    </span>
                                </div>

                                <div class="mb-3">
                                    <strong>Status:</strong>
                                    <?php
                                    $status = htmlspecialchars($booking['status']);
                                    $badge_class = $status === BOOKING_APPROVED ? 'success' :
                                                  ($status === BOOKING_COMPLETED ? 'info' :
                                                  ($status === BOOKING_REJECTED ? 'danger' :
                                                  ($status === BOOKING_CANCELLED ? 'secondary' : 'warning')));
                                    ?>
                                    <span class="badge bg-<?php echo $badge_class; ?> ms-2"><?php echo ucfirst($status); ?></span>
                                </div>

                                <div class="mb-3">
                                    <strong>Payment Status:</strong>
                                    <?php
                                    $payment_status = htmlspecialchars($booking['payment_status']);
                                    $payment_badge = $payment_status === 'paid' ? 'success' : 'warning';
                                    ?>
                                    <span class="badge bg-<?php echo $payment_badge; ?> ms-2"><?php echo ucfirst($payment_status); ?></span>
                                </div>

                                <div class="mb-3">
                                    <strong>Price:</strong>
                                    <span class="text-success fw-bold">₹<?php echo number_format($booking['price'], 2); ?></span>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($booking['location'])): ?>
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <strong>Location:</strong>
                                    <span class="text-muted"><?php echo htmlspecialchars($booking['location']); ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($booking['notes'])): ?>
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <strong>User Notes:</strong>
                                    <p class="text-muted mt-2"><?php echo nl2br(htmlspecialchars($booking['notes'])); ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($booking['admin_notes'])): ?>
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <strong>Admin Notes:</strong>
                                    <p class="mb-0 mt-2"><?php echo nl2br(htmlspecialchars($booking['admin_notes'])); ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Admin Actions -->
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="mb-0">Admin Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex gap-2 flex-wrap">
                            <?php if ($booking['status'] === BOOKING_PENDING): ?>
                                <button class="btn btn-success" onclick="updateBookingStatus(<?php echo $booking['id']; ?>, 'approved')">
                                    <i class="fas fa-check me-1"></i> Approve Booking
                                </button>
                                <button class="btn btn-danger" onclick="updateBookingStatus(<?php echo $booking['id']; ?>, 'rejected')">
                                    <i class="fas fa-times me-1"></i> Reject Booking
                                </button>
                            <?php endif; ?>

                            <?php if ($booking['status'] === BOOKING_APPROVED): ?>
                                <button class="btn btn-info" onclick="updateBookingStatus(<?php echo $booking['id']; ?>, 'completed')">
                                    <i class="fas fa-check-circle me-1"></i> Mark as Completed
                                </button>
                            <?php endif; ?>

                            <button class="btn btn-warning" onclick="editAdminNotes(<?php echo $booking['id']; ?>)">
                                <i class="fas fa-edit me-1"></i> Add/Edit Admin Notes
                            </button>

                            <a href="./bookings.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Back to Bookings
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User & Instructor Details -->
            <div class="col-lg-4">
                <!-- User Details -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-user me-2"></i>
                            User Details
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <i class="fas fa-user-circle fa-3x text-primary mb-2"></i>
                            <h6><?php echo htmlspecialchars($booking['user_name']); ?></h6>
                        </div>

                        <div class="mb-2">
                            <strong>Email:</strong>
                            <span class="text-muted"><?php echo htmlspecialchars($booking['user_email']); ?></span>
                        </div>

                        <div class="mb-2">
                            <strong>Phone:</strong>
                            <span class="text-muted"><?php echo htmlspecialchars($booking['user_phone']); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Instructor Details -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-user-tie me-2"></i>
                            Instructor Details
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <i class="fas fa-user-circle fa-3x text-primary mb-2"></i>
                            <h6><?php echo htmlspecialchars($booking['instructor_name']); ?></h6>
                        </div>

                        <div class="mb-2">
                            <strong>Experience:</strong>
                            <span class="text-muted"><?php echo $booking['experience_years']; ?> years</span>
                        </div>

                        <div class="mb-2">
                            <strong>Specialty:</strong>
                            <span class="text-muted"><?php echo htmlspecialchars($booking['specialty']); ?></span>
                        </div>

                        <div class="mb-2">
                            <strong>Rating:</strong>
                            <span class="text-warning">
                                <?php
                                $rating = round($booking['instructor_rating']);
                                for ($i = 1; $i <= 5; $i++) {
                                    echo $i <= $rating ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
                                }
                                ?>
                                (<?php echo number_format($booking['instructor_rating'], 1); ?>)
                            </span>
                        </div>

                        <div class="mb-2">
                            <strong>Email:</strong>
                            <span class="text-muted"><?php echo htmlspecialchars($booking['instructor_email']); ?></span>
                        </div>

                        <div class="mb-2">
                            <strong>Phone:</strong>
                            <span class="text-muted"><?php echo htmlspecialchars($booking['instructor_phone']); ?></span>
                        </div>

                        <?php if (!empty($booking['bio'])): ?>
                        <div class="mt-3">
                            <strong>Bio:</strong>
                            <p class="text-muted small mt-2"><?php echo nl2br(htmlspecialchars($booking['bio'])); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Admin Notes Modal -->
<div class="modal fade" id="adminNotesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add/Edit Admin Notes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="adminNotesForm">
                <div class="modal-body">
                    <input type="hidden" id="notesBookingId" name="booking_id">
                    <div class="mb-3">
                        <label for="adminNotes" class="form-label">Admin Notes</label>
                        <textarea class="form-control" id="adminNotes" name="admin_notes" rows="4" maxlength="500"><?php echo htmlspecialchars($booking['admin_notes']); ?></textarea>
                        <div class="form-text">These notes are visible to the user and instructor.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Notes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateBookingStatus(bookingId, status) {
    const statusText = status.charAt(0).toUpperCase() + status.slice(1);
    if (confirm(`Are you sure you want to ${status} this booking?`)) {
        fetch('../api/update-booking-status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ booking_id: bookingId, status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`Booking ${statusText.toLowerCase()} successfully!`);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the booking status.');
        });
    }
}

function editAdminNotes(bookingId) {
    document.getElementById('notesBookingId').value = bookingId;
    const modal = new bootstrap.Modal(document.getElementById('adminNotesModal'));
    modal.show();
}

document.getElementById('adminNotesForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('../api/update-booking-status.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Admin notes updated successfully!');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the admin notes.');
    });
});
</script>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>