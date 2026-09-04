<?php
/**
 * Admin - Manage Users
 */

$page_title = "Manage Users";
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
$role_filter = isset($_GET['role']) ? trim($_GET['role']) : '';
$status_filter = isset($_GET['status']) ? trim($_GET['status']) : '';

// Build query
$query = "SELECT * FROM users WHERE 1=1";

$types = "";
$params = [];

if (!empty($role_filter)) {
    $query .= " AND role = ?";
    $types .= "s";
    $params[] = $role_filter;
}

if (!empty($status_filter)) {
    $query .= " AND status = ?";
    $types .= "s";
    $params[] = $status_filter;
}

$query .= " ORDER BY created_at DESC";

$users = getRows($query, $types, $params);

include __DIR__ . '/../includes/admin-header.php';
?>

<!-- Filters -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <input type="search" class="form-control" id="searchInput" placeholder="Search by name or email...">
    </div>
    <div class="col-md-3">
        <select class="form-select" id="roleFilter">
            <option value="">All Roles</option>
            <option value="user" <?php echo $role_filter === 'user' ? 'selected' : ''; ?>>User</option>
            <option value="instructor" <?php echo $role_filter === 'instructor' ? 'selected' : ''; ?>>Instructor</option>
        </select>
    </div>
    <div class="col-md-3">
        <select class="form-select" id="statusFilter">
            <option value="">All Status</option>
            <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Active</option>
            <option value="inactive" <?php echo $status_filter === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
            <option value="blocked" <?php echo $status_filter === 'blocked' ? 'selected' : ''; ?>>Blocked</option>
        </select>
    </div>
    <div class="col-md-3">
        <button class="btn btn-primary w-100" onclick="applyFilters()">
            <i class="fas fa-filter me-2"></i> Filter
        </button>
    </div>
</div>

<!-- Users Table -->
<div class="card shadow">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-users me-2"></i> All Users
            <span class="badge bg-primary ms-2"><?php echo count($users); ?></span>
        </h5>
        <button class="btn btn-sm btn-success" onclick="exportToCSV('usersTable', 'users.csv')">
            <i class="fas fa-download me-1"></i> Export
        </button>
    </div>
    
    <div class="card-body">
        <?php if (empty($users)): ?>
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle me-2"></i> No users found
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover admin-table mb-0" id="usersTable">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $index => $user): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($user['name']); ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['phone']); ?></td>
                                <td>
                                    <span class="badge bg-info"><?php echo ucfirst(htmlspecialchars($user['role'])); ?></span>
                                </td>
                                <td>
                                    <?php
                                    $status = htmlspecialchars($user['status']);
                                    $badge_class = $status === 'active' ? 'success' : 
                                                  ($status === 'inactive' ? 'warning' : 'danger');
                                    ?>
                                    <span class="badge bg-<?php echo $badge_class; ?>"><?php echo ucfirst($status); ?></span>
                                </td>
                                <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button class="btn btn-outline-primary" onclick="viewUser(<?php echo $user['id']; ?>)">View</button>
                                        <button class="btn btn-outline-warning" onclick="editUser(<?php echo $user['id']; ?>)">Edit</button>
                                        <button class="btn btn-outline-danger" onclick="blockUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['name']); ?>')">Block</button>
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
function applyFilters() {
    const role = document.getElementById('roleFilter').value;
    const status = document.getElementById('statusFilter').value;
    
    let url = './users.php';
    const params = [];
    
    if (role) params.push('role=' + encodeURIComponent(role));
    if (status) params.push('status=' + encodeURIComponent(status));
    
    if (params.length > 0) {
        url += '?' + params.join('&');
    }
    
    window.location.href = url;
}

function viewUser(userId) {
    // Redirect to user details page
    window.location.href = './user-details.php?id=' + userId;
}

function editUser(userId) {
    // Open edit modal or redirect
    console.log('Edit user: ' + userId);
}

function blockUser(userId, userName) {
    if (confirm('Are you sure you want to block ' + userName + '?')) {
        // Call API to block user
        console.log('Blocking user: ' + userId);
    }
}

// Attach filter dropdown change events
document.getElementById('roleFilter').addEventListener('change', applyFilters);
document.getElementById('statusFilter').addEventListener('change', applyFilters);
</script>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
