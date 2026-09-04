<?php
/**
 * User Bookings Page
 */

$page_title = "My Bookings";
$css_path = "../assets/css/style.css";
$js_path = "../assets/js/script.js";

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/auth.php';

// Check if user is logged in
if (!AuthManager::isUserLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

// Get current user
$user = AuthManager::getCurrentUser();

// Get filter from URL
$status_filter = isset($_GET['status']) ? trim($_GET['status']) : '';

// Build query
$query = "SELECT b.*, c.title as course_name, u.name as instructor_name 
          FROM bookings b
          JOIN courses c ON b.course_id = c.id
          JOIN instructors i ON b.instructor_id = i.id
          JOIN users u ON i.user_id = u.id
          WHERE b.user_id = ?";

$types = "i";
$params = [$user['id']];

if (!empty($status_filter)) {
    $query .= " AND b.status = ?";
    $types .= "s";
    $params[] = $status_filter;
}

$query .= " ORDER BY b.booking_date DESC";

// Get all bookings
$bookings = getRows($query, $types, $params);

include __DIR__ . '/../includes/user-header.php';
?>

<section class="py-5 bg-light">
    <div class="container">
        <h2 class="mb-4">
            <i class="fas fa-calendar-list me-2"></i> My Bookings
        </h2>
        
        <!-- Filter Tabs -->
        <div class="mb-4">
            <div class="btn-group" role="group">
                <a href="./bookings.php" class="btn btn-<?php echo empty($status_filter) ? 'primary' : 'outline-primary'; ?>">
                    All Bookings
                </a>
                <a href="./bookings.php?status=pending" class="btn btn-<?php echo $status_filter === 'pending' ? 'primary' : 'outline-primary'; ?>">
                    <i class="fas fa-clock me-1"></i> Pending
                </a>
                <a href="./bookings.php?status=approved" class="btn btn-<?php echo $status_filter === 'approved' ? 'primary' : 'outline-primary'; ?>">
                    <i class="fas fa-check me-1"></i> Approved
                </a>
                <a href="./bookings.php?status=completed" class="btn btn-<?php echo $status_filter === 'completed' ? 'primary' : 'outline-primary'; ?>">
                    <i class="fas fa-check-double me-1"></i> Completed
                </a>
                <a href="./bookings.php?status=cancelled" class="btn btn-<?php echo $status_filter === 'cancelled' ? 'primary' : 'outline-primary'; ?>">
                    <i class="fas fa-times me-1"></i> Cancelled
                </a>
            </div>
        </div>
        
        <?php if (empty($bookings)): ?>
            <div class="alert alert-info rounded-3">
                <i class="fas fa-info-circle me-2"></i>
                <strong>No bookings found.</strong> <a href="../book-lesson.php">Book your first lesson</a>
            </div>
        <?php else: ?>
            <!-- Bookings Table -->
            <div class="card shadow">
                <div class="table-responsive">
                    <table class="table table-hover admin-table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Course</th>
                                <th>Instructor</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                                <th>Price</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings as $booking): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($booking['course_name']); ?></strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($booking['instructor_name']); ?></td>
                                    <td><?php echo date('d M Y', strtotime($booking['booking_date'])); ?></td>
                                    <td><?php echo date('h:i A', strtotime($booking['start_time'])); ?></td>
                                    <td>
                                        <?php
                                        $status = htmlspecialchars($booking['status']);
                                        $badge_class = $status === 'approved' ? 'success' : 
                                                      ($status === 'completed' ? 'info' : 
                                                      ($status === 'rejected' ? 'danger' : 
                                                      ($status === 'cancelled' ? 'secondary' : 'warning')));
                                        ?>
                                        <span class="badge bg-<?php echo $badge_class; ?>"><?php echo ucfirst($status); ?></span>
                                    </td>
                                    <td>₹<?php echo number_format($booking['price']); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="./booking-details.php?id=<?php echo $booking['id']; ?>" class="btn btn-outline-primary">View</a>
                                            <?php if ($booking['payment_status'] !== 'paid' && !in_array($status, ['cancelled', 'rejected'], true)): ?>
                                                <a href="../payment.php?booking_id=<?php echo $booking['id']; ?>" class="btn btn-outline-success">Pay Now</a>
                                            <?php endif; ?>
                                            <?php if ($status === 'approved'): ?>
                                                <button class="btn btn-outline-danger" onclick="cancelBooking(<?php echo $booking['id']; ?>)">Cancel</button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Action Button -->
        <div class="text-center mt-4">
            <a href="../book-lesson.php" class="btn btn-primary btn-lg">
                <i class="fas fa-plus me-2"></i> Book Another Lesson
            </a>
        </div>
    </div>
</section>

<script>
function cancelBooking(bookingId) {
    if (confirm('Are you sure you want to cancel this booking? This action cannot be undone.')) {
        fetch('../api/cancel-booking.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ booking_id: bookingId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Booking cancelled successfully!');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while cancelling the booking.');
        });
    }
}
</script>

<?php include __DIR__ . '/../includes/user-footer.php'; ?>
