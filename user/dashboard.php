<?php
/**
 * User Dashboard
 */

$page_title = "My Dashboard";
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
$user_data = getRow("SELECT * FROM users WHERE id = ?", "i", [$user['id']]);

// Get user stats
$total_enrollments = getRow(
    "SELECT COUNT(*) as count FROM enrollments WHERE user_id = ?",
    "i",
    [$user['id']]
);

$active_bookings = getRow(
    "SELECT COUNT(*) as count FROM bookings WHERE user_id = ? AND status IN ('pending', 'approved')",
    "i",
    [$user['id']]
);

$completed_lessons = getRow(
    "SELECT COUNT(*) as count FROM bookings WHERE user_id = ? AND status = 'completed'",
    "i",
    [$user['id']]
);

// Get recent bookings
$recent_bookings = getRows(
    "SELECT b.*, c.title as course_name, u.name as instructor_name 
     FROM bookings b
     JOIN courses c ON b.course_id = c.id
     JOIN instructors i ON b.instructor_id = i.id
     JOIN users u ON i.user_id = u.id
     WHERE b.user_id = ?
     ORDER BY b.booking_date DESC
     LIMIT 5",
    "i",
    [$user['id']]
);

include __DIR__ . '/../includes/user-header.php';
?>

<section class="py-5">
    <div class="container">
        <h2 class="mb-4">
            <i class="fas fa-tachometer-alt me-2"></i> My Dashboard
        </h2>
        
        <!-- Welcome Section -->
        <div class="card bg-primary text-white mb-4 shadow">
            <div class="card-body p-4">
                <h4>Welcome back, <?php echo htmlspecialchars($user['name']); ?>!</h4>
                <p class="mb-0">
                    <?php if ($total_enrollments['count'] == 0): ?>
                        You haven't enrolled in any courses yet. 
                        <a href="../courses.php" class="text-warning fw-bold">Browse courses</a>
                    <?php else: ?>
                        You have <?php echo $active_bookings['count']; ?> upcoming lessons scheduled.
                    <?php endif; ?>
                </p>
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <!-- Total Enrollments -->
            <div class="col-md-3 col-sm-6">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-muted mb-1">Total Enrollments</h6>
                                <h3 class="text-primary mb-0"><?php echo $total_enrollments['count']; ?></h3>
                            </div>
                            <i class="fas fa-book fa-3x text-primary" style="opacity: 0.2;"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Active Bookings -->
            <div class="col-md-3 col-sm-6">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-muted mb-1">Upcoming Lessons</h6>
                                <h3 class="text-success mb-0"><?php echo $active_bookings['count']; ?></h3>
                            </div>
                            <i class="fas fa-calendar-check fa-3x text-success" style="opacity: 0.2;"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Completed Lessons -->
            <div class="col-md-3 col-sm-6">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-muted mb-1">Completed Lessons</h6>
                                <h3 class="text-info mb-0"><?php echo $completed_lessons['count']; ?></h3>
                            </div>
                            <i class="fas fa-check-circle fa-3x text-info" style="opacity: 0.2;"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Account Status -->
            <div class="col-md-3 col-sm-6">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-muted mb-1">Account Status</h6>
                                <span class="badge bg-success"><?php echo htmlspecialchars($user_data['status']); ?></span>
                            </div>
                            <i class="fas fa-shield-alt fa-3x text-warning" style="opacity: 0.2;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="row g-4 mb-4">
            <div class="col-md-12">
                <h5 class="mb-3">Quick Actions</h5>
                <div class="d-flex flex-wrap gap-2">
                    <a href="../book-lesson.php" class="btn btn-primary">
                        <i class="fas fa-calendar-plus me-2"></i> Book a Lesson
                    </a>
                    <a href="../courses.php" class="btn btn-outline-primary">
                        <i class="fas fa-book me-2"></i> View Courses
                    </a>
                    <a href="./profile.php" class="btn btn-outline-primary">
                        <i class="fas fa-user-edit me-2"></i> Edit Profile
                    </a>
                    <a href="./bookings.php" class="btn btn-outline-primary">
                        <i class="fas fa-list me-2"></i> View All Bookings
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Recent Bookings -->
        <div class="card shadow">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i> Recent Bookings
                </h5>
            </div>
            
            <div class="card-body">
                <?php if (empty($recent_bookings)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        No bookings yet. <a href="../book-lesson.php">Book your first lesson</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover admin-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Course</th>
                                    <th>Instructor</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_bookings as $booking): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($booking['course_name']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['instructor_name']); ?></td>
                                        <td><?php echo date('d M Y', strtotime($booking['booking_date'])); ?></td>
                                        <td><?php echo date('h:i A', strtotime($booking['start_time'])); ?></td>
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
                    
                    <div class="text-center mt-3">
                        <a href="./bookings.php" class="btn btn-primary">View All Bookings</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/user-footer.php'; ?>
