<?php
/**
 * Admin - Manage Bookings
 */

$page_title = "Manage Bookings";
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

// Get filter
$status_filter = isset($_GET['status']) ? trim($_GET['status']) : '';

// Build query
$query = "SELECT b.*, u.name as user_name, c.title as course_name, i_user.name as instructor_name
          FROM bookings b
          JOIN users u ON b.user_id = u.id
          JOIN courses c ON b.course_id = c.id
          JOIN instructors i ON b.instructor_id = i.id
          JOIN users i_user ON i.user_id = i_user.id
          WHERE 1=1";

$types = "";
$params = [];

if (!empty($status_filter)) {
    $query .= " AND b.status = ?";
    $types .= "s";
    $params[] = $status_filter;
}

$query .= " ORDER BY b.booking_date DESC";

$bookings = getRows($query, $types, $params);

include __DIR__ . '/../includes/admin-header.php';
?>

<!-- Filters -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <input type="search" class="form-control" id="searchInput" placeholder="Search by user name or email...">
    </div>
    <div class="col-md-6">
        <div class="input-group">
            <select class="form-select" id="statusFilter">
                <option value="">All Status</option>
                <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="approved" <?php echo $status_filter === 'approved' ? 'selected' : ''; ?>>Approved</option>
                <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                <option value="rejected" <?php echo $status_filter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
            </select>
            <button class="btn btn-primary" onclick="applyStatusFilter()">Filter</button>
        </div>
    </div>
</div>

<!-- Bookings Table -->
<div class="card shadow">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-calendar-check me-2"></i> All Bookings
            <span class="badge bg-primary ms-2"><?php echo count($bookings); ?></span>
        </h5>
        <button class="btn btn-sm btn-success" onclick="exportToCSV('bookingsTable', 'bookings.csv')">
            <i class="fas fa-download me-1"></i> Export
        </button>
    </div>
    
    <div class="card-body">
        <?php if (empty($bookings)): ?>
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle me-2"></i> No bookings found
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover admin-table mb-0" id="bookingsTable">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Course</th>
                            <th>Instructor</th>
                            <th>Date & Time</th>
                            <th>Status</th>
                            <th>Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $index => $booking): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($booking['user_name']); ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($booking['course_name']); ?></td>
                                <td><?php echo htmlspecialchars($booking['instructor_name']); ?></td>
                                <td>
                                    <?php echo date('d M Y', strtotime($booking['booking_date'])); ?><br>
                                    <small class="text-muted"><?php echo date('h:i A', strtotime($booking['start_time'])); ?></small>
                                </td>
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
                                <td><strong>₹<?php echo number_format($booking['price']); ?></strong></td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button class="btn btn-outline-primary" onclick="viewBooking(<?php echo $booking['id']; ?>)">View</button>
                                        <?php if ($status === 'pending'): ?>
                                            <button class="btn btn-outline-success" onclick="approveBooking(<?php echo $booking['id']; ?>)">Approve</button>
                                            <button class="btn btn-outline-danger" onclick="rejectBooking(<?php echo $booking['id']; ?>)">Reject</button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function applyStatusFilter() {
    const status = document.getElementById('statusFilter').value;
    let url = 'bookings.php';
    
    if (status) {
        url += '?status=' + encodeURIComponent(status);
    }
    
    window.location.href = url;
}

function viewBooking(bookingId) {
    window.location.href = './booking-details.php?id=' + bookingId;
}

function approveBooking(bookingId) {
    if (confirm('Approve this booking?')) {
        console.log('Approving booking: ' + bookingId);
        // Call API to approve booking
    }
}

function rejectBooking(bookingId) {
    if (confirm('Reject this booking?')) {
        console.log('Rejecting booking: ' + bookingId);
        // Call API to reject booking
    }
}
</script>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
