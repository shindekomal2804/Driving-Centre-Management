<?php
/**
 * Admin - Manage Instructors
 */

$page_title = "Manage Instructors";
$css_path = "../assets/css/style.css";
$js_path = "../assets/js/admin-script.js";

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/auth.php';

if (!AuthManager::isAdminLoggedIn()) {
    header('Location: ../admin-login.php');
    exit();
}

$instructors = getRows(
    "SELECT u.id, u.name, u.email, u.phone, i.* 
     FROM instructors i 
     JOIN users u ON i.user_id = u.id 
     ORDER BY i.created_at DESC"
);

include __DIR__ . '/../includes/admin-header.php';
?>

<div class="mb-4">
    <button class="btn btn-success" data-mdb-toggle="modal" data-mdb-target="#addInstructorModal">
        <i class="fas fa-plus me-2"></i> Add New Instructor
    </button>
</div>

<div class="card shadow">
    <div class="card-header bg-light">
        <h5 class="mb-0">
            <i class="fas fa-chalkboard-user me-2"></i> All Instructors
            <span class="badge bg-primary ms-2"><?php echo count($instructors); ?></span>
        </h5>
    </div>
    
    <div class="card-body">
        <?php if (empty($instructors)): ?>
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle me-2"></i> No instructors found
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover admin-table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Specialty</th>
                            <th>Experience</th>
                            <th>Rating</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($instructors as $index => $instructor): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><strong><?php echo htmlspecialchars($instructor['name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($instructor['email']); ?></td>
                                <td><?php echo htmlspecialchars($instructor['phone']); ?></td>
                                <td><?php echo htmlspecialchars($instructor['specialty']); ?></td>
                                <td><?php echo htmlspecialchars($instructor['experience_years']); ?> years</td>
                                <td>
                                    <i class="fas fa-star text-warning"></i>
                                    <?php echo htmlspecialchars($instructor['rating']); ?>/5
                                </td>
                                <td>
                                    <?php
                                    $status = htmlspecialchars($instructor['status']);
                                    $badge_class = $status === 'active' ? 'success' : 
                                                  ($status === 'on_leave' ? 'warning' : 'danger');
                                    ?>
                                    <span class="badge bg-<?php echo $badge_class; ?>"><?php echo ucfirst($status); ?></span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button class="btn btn-outline-primary">View</button>
                                        <button class="btn btn-outline-warning">Edit</button>
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

<!-- Add Instructor Modal -->
<div class="modal fade" id="addInstructorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i> Add New Instructor</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal"></button>
            </div>
            <form id="instructorForm" class="admin-form" method="POST">
                <div class="modal-body p-4">
                    <div id="instructorAlert" class="alert d-none" role="alert"></div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="tel" class="form-control" id="phone" name="phone" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="specialty" class="form-label">Specialty</label>
                        <input type="text" class="form-control" id="specialty" name="specialty" placeholder="Car, Bike, Heavy Vehicle">
                    </div>
                    
                    <div class="mb-3">
                        <label for="experience" class="form-label">Years of Experience</label>
                        <input type="number" class="form-control" id="experience" name="experience" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Instructor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const instructorForm = document.getElementById('instructorForm');
if (instructorForm) {
    instructorForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const alertEl = document.getElementById('instructorAlert');
        alertEl.className = 'alert d-none';
        alertEl.textContent = '';

        const payload = {
            name: document.getElementById('name').value.trim(),
            email: document.getElementById('email').value.trim(),
            phone: document.getElementById('phone').value.trim(),
            specialty: document.getElementById('specialty').value.trim(),
            experience_years: parseInt(document.getElementById('experience').value, 10) || 0,
            bio: ''
        };

        try {
            const response = await fetch('../api/add-instructor.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const result = await response.json();
            if (result.success) {
                alertEl.className = 'alert alert-success';
                alertEl.textContent = 'Instructor added successfully. Reloading...';

                setTimeout(() => {
                    window.location.reload();
                }, 800);
            } else {
                alertEl.className = 'alert alert-danger';
                alertEl.textContent = result.message || 'Unable to add instructor';
            }
        } catch (error) {
            alertEl.className = 'alert alert-danger';
            alertEl.textContent = 'Error while adding instructor. Please try again.';
            console.error('Add instructor error:', error);
        }
    });
}
</script>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
