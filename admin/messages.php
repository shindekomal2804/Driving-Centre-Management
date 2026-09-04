<?php
/**
 * Admin - Contact Messages
 */

$page_title = "Messages";
$css_path = "../assets/css/style.css";
$js_path = "../assets/js/admin-script.js";

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/auth.php';

if (!AuthManager::isAdminLoggedIn()) {
    header('Location: ../admin-login.php');
    exit();
}

$status_filter = isset($_GET['status']) ? trim($_GET['status']) : '';

$query = "SELECT * FROM contact_messages WHERE 1=1";

$types = "";
$params = [];

if (!empty($status_filter)) {
    $query .= " AND status = ?";
    $types .= "s";
    $params[] = $status_filter;
}

$query .= " ORDER BY created_at DESC";

$messages = getRows($query, $types, $params);

include __DIR__ . '/../includes/admin-header.php';
?>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <select class="form-select" id="statusFilter">
            <option value="">All Messages</option>
            <option value="new" <?php echo $status_filter === 'new' ? 'selected' : ''; ?>>New</option>
            <option value="read" <?php echo $status_filter === 'read' ? 'selected' : ''; ?>>Read</option>
            <option value="replied" <?php echo $status_filter === 'replied' ? 'selected' : ''; ?>>Replied</option>
        </select>
    </div>
    <div class="col-md-6">
        <button class="btn btn-primary" onclick="applyStatusFilter()">Filter</button>
    </div>
</div>

<div class="card shadow">
    <div class="card-header bg-light">
        <h5 class="mb-0">
            <i class="fas fa-envelope me-2"></i> Contact Messages
            <span class="badge bg-primary ms-2"><?php echo count($messages); ?></span>
        </h5>
    </div>
    
    <div class="card-body">
        <?php if (empty($messages)): ?>
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle me-2"></i> No messages found
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover admin-table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>From</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $index => $msg): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><strong><?php echo htmlspecialchars($msg['name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($msg['email']); ?></td>
                                <td><?php echo htmlspecialchars($msg['subject']); ?></td>
                                <td><?php echo date('d M Y h:i A', strtotime($msg['created_at'])); ?></td>
                                <td>
                                    <?php
                                    $status = htmlspecialchars($msg['status']);
                                    $badge_class = $status === 'new' ? 'warning' : 
                                                  ($status === 'replied' ? 'success' : 'info');
                                    ?>
                                    <span class="badge bg-<?php echo $badge_class; ?>"><?php echo ucfirst($status); ?></span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewMessage(<?php echo $msg['id']; ?>)">
                                        View
                                    </button>
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
    let url = './messages.php';
    
    if (status) {
        url += '?status=' + encodeURIComponent(status);
    }
    
    window.location.href = url;
}

function viewMessage(messageId) {
    window.location.href = './message-details.php?id=' + messageId;
}
</script>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
