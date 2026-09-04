<?php
/**
 * Admin - User Details & Management
 */

$page_title = "User Details";
$css_path = "../assets/css/style.css";
$js_path = "../assets/js/admin-script.js";

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/auth.php';

if (!AuthManager::isAdminLoggedIn()) {
    header('Location: ../admin-login.php');
    exit();
}

$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (empty($user_id)) {
    header('Location: ./users.php');
    exit();
}

// Get user details
$user = getRow("SELECT * FROM users WHERE id = ?", "i", [$user_id]);

if (!$user) {
    header('Location: ./users.php');
    exit();
}

// Get user statistics
$stats = getRow("
    SELECT
        COUNT(DISTINCT b.id) as total_bookings,
        COUNT(DISTINCT CASE WHEN b.status = 'completed' THEN b.id END) as completed_bookings,
        COUNT(DISTINCT CASE WHEN b.status = 'pending' THEN b.id END) as pending_bookings,
        COUNT(DISTINCT e.id) as total_enrollments,
        COUNT(DISTINCT r.id) as total_reviews
    FROM users u
    LEFT JOIN bookings b ON u.id = b.user_id
    LEFT JOIN enrollments e ON u.id = e.user_id
    LEFT JOIN reviews r ON u.id = r.user_id
    WHERE u.id = ?
", "i", [$user_id]);

// Get recent bookings
$recent_bookings = getRows("
    SELECT b.*, c.title as course_name, i_user.name as instructor_name
    FROM bookings b
    JOIN courses c ON b.course_id = c.id
    JOIN instructors i ON b.instructor_id = i.id
    JOIN users i_user ON i.user_id = i_user.id
    WHERE b.user_id = ?
    ORDER BY b.created_at DESC
    LIMIT 5
", "i", [$user_id]);

// Get recent enrollments
$recent_enrollments = getRows("
    SELECT e.*, c.title as course_name
    FROM enrollments e
    JOIN courses c ON e.course_id = c.id
    WHERE e.user_id = ?
    ORDER BY e.created_at DESC
    LIMIT 5
", "i", [$user_id]);

include __DIR__ . '/../includes/admin-header.php';
?>

<div class="row">
    <div class="col-lg-8">
        <!-- User Profile Card -->
        <div class="card shadow mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-user me-2"></i> User Profile
                </h5>
                <div>
                    <a href="./users.php" class="btn btn-sm btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i> Back to Users
                    </a>
                    <button class="btn btn-sm btn-primary" onclick="editUser()">
                        <i class="fas fa-edit me-1"></i> Edit User
                    </button>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <!-- Profile Image -->
                    <div class="col-md-3 text-center">
                        <div class="profile-image mb-3">
                            <?php if (!empty($user['profile_image'])): ?>
                                <img src="../<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile" class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 120px; height: 120px; font-size: 3rem;">
                                    <i class="fas fa-user"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-2">
                            <?php
                            $status = htmlspecialchars($user['status']);
                            $badge_class = $status === 'active' ? 'success' :
                                          ($status === 'inactive' ? 'warning' : 'danger');
                            ?>
                            <span class="badge bg-<?php echo $badge_class; ?> fs-6"><?php echo ucfirst($status); ?></span>
                        </div>
                        <div>
                            <span class="badge bg-info"><?php echo ucfirst(htmlspecialchars($user['role'])); ?></span>
                        </div>
                    </div>

                    <!-- User Details -->
                    <div class="col-md-9">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Full Name</label>
                                <p class="mb-0"><?php echo htmlspecialchars($user['name']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email</label>
                                <p class="mb-0">
                                    <a href="mailto:<?php echo htmlspecialchars($user['email']); ?>">
                                        <?php echo htmlspecialchars($user['email']); ?>
                                    </a>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Phone</label>
                                <p class="mb-0">
                                    <?php if (!empty($user['phone'])): ?>
                                        <a href="tel:<?php echo htmlspecialchars($user['phone']); ?>">
                                            <?php echo htmlspecialchars($user['phone']); ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Not provided</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Date of Birth</label>
                                <p class="mb-0">
                                    <?php if (!empty($user['date_of_birth'])): ?>
                                        <?php echo date('d M Y', strtotime($user['date_of_birth'])); ?>
                                        <small class="text-muted">(<?php echo date_diff(date_create($user['date_of_birth']), date_create('today'))->y; ?> years old)</small>
                                    <?php else: ?>
                                        <span class="text-muted">Not provided</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Address</label>
                                <p class="mb-0">
                                    <?php if (!empty($user['address'])): ?>
                                        <?php echo nl2br(htmlspecialchars($user['address'])); ?>
                                        <?php if (!empty($user['city']) || !empty($user['state']) || !empty($user['postal_code'])): ?>
                                            <br><small class="text-muted">
                                                <?php echo htmlspecialchars($user['city']); ?>
                                                <?php if (!empty($user['state'])) echo ', ' . htmlspecialchars($user['state']); ?>
                                                <?php if (!empty($user['postal_code'])) echo ' ' . htmlspecialchars($user['postal_code']); ?>
                                            </small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">Not provided</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Joined Date</label>
                                <p class="mb-0"><?php echo date('d M Y h:i A', strtotime($user['created_at'])); ?></p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Last Updated</label>
                                <p class="mb-0"><?php echo date('d M Y h:i A', strtotime($user['updated_at'])); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="card shadow mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="fas fa-calendar-check me-2"></i> Recent Bookings
                    <span class="badge bg-primary ms-2"><?php echo $stats['total_bookings']; ?> total</span>
                </h6>
            </div>
            <div class="card-body">
                <?php if (empty($recent_bookings)): ?>
                    <p class="text-muted mb-0">No bookings found</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Course</th>
                                    <th>Instructor</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_bookings as $booking): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($booking['course_name']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['instructor_name']); ?></td>
                                        <td><?php echo date('d M Y', strtotime($booking['booking_date'])); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $booking['status'] === 'completed' ? 'success' : ($booking['status'] === 'approved' ? 'info' : 'warning'); ?>">
                                                <?php echo ucfirst(htmlspecialchars($booking['status'])); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Enrollments -->
        <div class="card shadow">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="fas fa-graduation-cap me-2"></i> Recent Enrollments
                    <span class="badge bg-success ms-2"><?php echo $stats['total_enrollments']; ?> total</span>
                </h6>
            </div>
            <div class="card-body">
                <?php if (empty($recent_enrollments)): ?>
                    <p class="text-muted mb-0">No enrollments found</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Course</th>
                                    <th>Enrollment Date</th>
                                    <th>Status</th>
                                    <th>Progress</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_enrollments as $enrollment): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($enrollment['course_name']); ?></td>
                                        <td><?php echo date('d M Y', strtotime($enrollment['enrollment_date'])); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $enrollment['status'] === 'completed' ? 'success' : 'info'; ?>">
                                                <?php echo ucfirst(htmlspecialchars($enrollment['status'])); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($enrollment['progress_percentage']); ?>%</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- User Statistics -->
        <div class="card shadow mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i> Statistics
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="h4 text-primary"><?php echo $stats['total_bookings']; ?></div>
                        <small class="text-muted">Bookings</small>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="h4 text-success"><?php echo $stats['completed_bookings']; ?></div>
                        <small class="text-muted">Completed</small>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="h4 text-info"><?php echo $stats['total_enrollments']; ?></div>
                        <small class="text-muted">Enrollments</small>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="h4 text-warning"><?php echo $stats['total_reviews']; ?></div>
                        <small class="text-muted">Reviews</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card shadow mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="fas fa-bolt me-2"></i> Quick Actions
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary" onclick="window.open('mailto:<?php echo htmlspecialchars($user['email']); ?>', '_blank')">
                        <i class="fas fa-envelope me-2"></i> Email User
                    </button>
                    <?php if (!empty($user['phone'])): ?>
                        <button class="btn btn-outline-success" onclick="window.open('tel:<?php echo htmlspecialchars($user['phone']); ?>', '_blank')">
                            <i class="fas fa-phone me-2"></i> Call User
                        </button>
                    <?php endif; ?>
                    <button class="btn btn-outline-warning" onclick="changeUserStatus()">
                        <i class="fas fa-user-cog me-2"></i> Change Status
                    </button>
                    <button class="btn btn-outline-danger" onclick="deleteUser()">
                        <i class="fas fa-trash me-2"></i> Delete User
                    </button>
                </div>
            </div>
        </div>

        <!-- Account Status -->
        <div class="card shadow">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="fas fa-shield-alt me-2"></i> Account Status
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Current Status:</strong>
                    <span class="badge bg-<?php echo $badge_class; ?> ms-2"><?php echo ucfirst($status); ?></span>
                </div>
                <div class="mb-3">
                    <strong>Role:</strong>
                    <span class="badge bg-info ms-2"><?php echo ucfirst(htmlspecialchars($user['role'])); ?></span>
                </div>
                <div class="mb-3">
                    <strong>Account Created:</strong>
                    <br><small><?php echo date('F j, Y \a\t g:i A', strtotime($user['created_at'])); ?></small>
                </div>
                <div>
                    <strong>Last Activity:</strong>
                    <br><small><?php echo date('F j, Y \a\t g:i A', strtotime($user['updated_at'])); ?></small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function editUser() {
    // Open edit modal or redirect to edit page
    alert('Edit user functionality would be implemented here');
}

function changeUserStatus() {
    const currentStatus = '<?php echo $user['status']; ?>';
    const newStatus = prompt('Enter new status (active/inactive/blocked):', currentStatus);

    if (newStatus && newStatus !== currentStatus) {
        if (confirm(`Change user status to "${newStatus}"?`)) {
            // Call API to change status
            console.log('Changing status to: ' + newStatus);
        }
    }
}

function deleteUser() {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone and will remove all associated data.')) {
        fetch('../api/delete-user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                user_id: <?php echo $user['id']; ?>
            })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showAlert('User deleted successfully!', 'success');
                setTimeout(() => {
                    window.location.href = './users.php';
                }, 1500);
            } else {
                showAlert(result.message, 'danger');
            }
        })
        .catch(error => {
            showAlert('Failed to delete user. Please try again.', 'danger');
            console.error('Error:', error);
        });
    }
}

function showAlert(message, type) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show position-fixed"
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', alertHtml);

    // Auto remove after 5 seconds
    setTimeout(() => {
        const alert = document.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 5000);
}
</script>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>