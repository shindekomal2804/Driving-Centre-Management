<?php
/**
 * Admin - Manage Courses
 */

$page_title = "Manage Courses";
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

// Get all courses
$courses = getRows("SELECT * FROM courses ORDER BY created_at DESC");

include __DIR__ . '/../includes/admin-header.php';
?>

<!-- Action Button -->
<div class="mb-4">
    <button class="btn btn-success" data-mdb-toggle="modal" data-mdb-target="#addCourseModal">
        <i class="fas fa-plus me-2"></i> Add New Course
    </button>
</div>

<!-- Courses Table -->
<div class="card shadow">
    <div class="card-header bg-light">
        <h5 class="mb-0">
            <i class="fas fa-book me-2"></i> All Courses
            <span class="badge bg-primary ms-2"><?php echo count($courses); ?></span>
        </h5>
    </div>
    
    <div class="card-body">
        <?php if (empty($courses)): ?>
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle me-2"></i> No courses found
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover admin-table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Course Title</th>
                            <th>Level</th>
                            <th>Price</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courses as $index => $course): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($course['title']); ?></strong>
                                    <br>
                                    <small class="text-muted"><?php echo htmlspecialchars(substr($course['description'], 0, 50) . '...'); ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-warning"><?php echo htmlspecialchars($course['level']); ?></span>
                                </td>
                                <td><strong>₹<?php echo number_format($course['price']); ?></strong></td>
                                <td>
                                    <?php echo htmlspecialchars($course['duration_weeks']); ?> weeks<br>
                                    <small>(<?php echo htmlspecialchars($course['duration_hours']); ?> hrs)</small>
                                </td>
                                <td>
                                    <?php
                                    $status = htmlspecialchars($course['status']);
                                    $badge_class = $status === 'active' ? 'success' : 'danger';
                                    ?>
                                    <span class="badge bg-<?php echo $badge_class; ?>"><?php echo ucfirst($status); ?></span>
                                </td>
                                <td><?php echo date('d M Y', strtotime($course['created_at'])); ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button class="btn btn-outline-primary">View</button>
                                        <button class="btn btn-outline-warning">Edit</button>
                                        <button class="btn btn-outline-danger" data-action="delete" data-name="<?php echo htmlspecialchars($course['title']); ?>">Delete</button>
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

<!-- Add Course Modal -->
<div class="modal fade" id="addCourseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i> Add New Course</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal"></button>
            </div>
            <form id="courseForm" class="admin-form" method="POST">
                <div class="modal-body p-4">
                    <!-- Course Title -->
                    <div class="mb-3">
                        <label for="title" class="form-label">Course Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    
                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    
                    <div class="row">
                        <!-- Level -->
                        <div class="col-md-6 mb-3">
                            <label for="level" class="form-label">Level</label>
                            <select class="form-select" id="level" name="level" required>
                                <option value="">-- Select Level --</option>
                                <option value="Beginner">Beginner</option>
                                <option value="Intermediate">Intermediate</option>
                                <option value="Advanced">Advanced</option>
                            </select>
                        </div>
                        
                        <!-- Price -->
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">Price (₹)</label>
                            <input type="number" class="form-control" id="price" name="price" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Duration Weeks -->
                        <div class="col-md-6 mb-3">
                            <label for="duration_weeks" class="form-label">Duration (Weeks)</label>
                            <input type="number" class="form-control" id="duration_weeks" name="duration_weeks" required>
                        </div>
                        
                        <!-- Duration Hours -->
                        <div class="col-md-6 mb-3">
                            <label for="duration_hours" class="form-label">Duration (Hours)</label>
                            <input type="number" class="form-control" id="duration_hours" name="duration_hours" required>
                        </div>
                    </div>
                    
                    <!-- Features -->
                    <div class="mb-3">
                        <label for="features" class="form-label">Features (comma-separated)</label>
                        <textarea class="form-control" id="features" name="features" rows="2" placeholder="Feature 1, Feature 2, Feature 3" required></textarea>
                    </div>
                    
                    <!-- Vehicle Types -->
                    <div class="mb-3">
                        <label for="vehicle_types" class="form-label">Vehicle Types</label>
                        <input type="text" class="form-control" id="vehicle_types" name="vehicle_types" placeholder="Car, Bike" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Add Course
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
