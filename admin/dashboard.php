<?php
/**
 * Admin Dashboard
 */

$page_title = "Dashboard";
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

// Get statistics
$total_users = getRow("SELECT COUNT(*) as count FROM users WHERE role = ?", "s", [ROLE_USER]);
$total_bookings = getRow("SELECT COUNT(*) as count FROM bookings");
$total_courses = getRow("SELECT COUNT(*) as count FROM courses");
$total_revenue = getRow("SELECT SUM(amount) as total FROM payments WHERE status = 'completed'");

$pending_bookings = getRow("SELECT COUNT(*) as count FROM bookings WHERE status = 'pending'");
$approved_bookings = getRow("SELECT COUNT(*) as count FROM bookings WHERE status = 'approved'");

// Get recent bookings
$recent_bookings = getRows(
    "SELECT b.*, u.name as user_name, c.title as course_name 
     FROM bookings b
     JOIN users u ON b.user_id = u.id
     JOIN courses c ON b.course_id = c.id
     ORDER BY b.created_at DESC
     LIMIT 5"
);

include __DIR__ . '/../includes/admin-header.php';
?>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <!-- Total Users -->
    <div class="col-md-3 col-sm-6">
        <div class="card shadow h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-1">Total Users</h6>
                        <h3 class="text-primary mb-0"><?php echo $total_users['count']; ?></h3>
                    </div>
                    <i class="fas fa-users fa-3x text-primary" style="opacity: 0.2;"></i>
                </div>
                <small class="text-muted">
                    <i class="fas fa-arrow-up text-success"></i> +12 this month
                </small>
            </div>
        </div>
    </div>
    
    <!-- Total Bookings -->
    <div class="col-md-3 col-sm-6">
        <div class="card shadow h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-1">Total Bookings</h6>
                        <h3 class="text-success mb-0"><?php echo $total_bookings['count']; ?></h3>
                    </div>
                    <i class="fas fa-calendar-check fa-3x text-success" style="opacity: 0.2;"></i>
                </div>
                <small class="text-muted">
                    <span class="badge bg-warning">
                        <?php echo $pending_bookings['count']; ?> Pending
                    </span>
                </small>
            </div>
        </div>
    </div>
    
    <!-- Total Courses -->
    <div class="col-md-3 col-sm-6">
        <div class="card shadow h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-1">Active Courses</h6>
                        <h3 class="text-info mb-0"><?php echo $total_courses['count']; ?></h3>
                    </div>
                    <i class="fas fa-book fa-3x text-info" style="opacity: 0.2;"></i>
                </div>
                <a href="./courses.php" class="small text-primary">Manage courses →</a>
            </div>
        </div>
    </div>
    
    <!-- Revenue -->
    <div class="col-md-3 col-sm-6">
        <div class="card shadow h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-1">Total Revenue</h6>
                        <h3 class="text-warning mb-0">
                            ₹<?php echo number_format($total_revenue['total'] ?? 0, 0); ?>
                        </h3>
                    </div>
                    <i class="fas fa-rupee-sign fa-3x text-warning" style="opacity: 0.2;"></i>
                </div>
                <small class="text-muted">
                    <i class="fas fa-arrow-up text-success"></i> +8.5% growth
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <!-- Revenue Chart -->
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header bg-light">
                <h5 class="mb-0">Monthly Revenue</h5>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="80"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Bookings Status Chart -->
    <div class="col-lg-4">
        <div class="card shadow">
            <div class="card-header bg-light">
                <h5 class="mb-0">Booking Status</h5>
            </div>
            <div class="card-body">
                <canvas id="bookingsChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Bookings -->
<div class="card shadow">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Recent Bookings</h5>
        <a href="./bookings.php" class="btn btn-sm btn-primary">View All</a>
    </div>
    
    <div class="card-body">
        <?php if (empty($recent_bookings)): ?>
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle me-2"></i> No recent bookings
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover admin-table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Course</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_bookings as $booking): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($booking['user_name']); ?></td>
                                <td><?php echo htmlspecialchars($booking['course_name']); ?></td>
                                <td><?php echo date('d M Y', strtotime($booking['booking_date'])); ?></td>
                                <td>
                                    <?php
                                    $status = htmlspecialchars($booking['status']);
                                    $badge_class = $status === 'approved' ? 'success' : 
                                                  ($status === 'completed' ? 'info' : 
                                                  ($status === 'rejected' ? 'danger' : 'warning'));
                                    ?>
                                    <span class="badge bg-<?php echo $badge_class; ?>"><?php echo ucfirst($status); ?></span>
                                </td>
                                <td>
                                    <a href="./booking-details.php?id=<?php echo $booking['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
