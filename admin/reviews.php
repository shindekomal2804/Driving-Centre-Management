<?php
/**
 * Admin - Manage Reviews
 */

$page_title = "Manage Reviews";
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

$query = "SELECT r.*, u.name as user_name FROM reviews r JOIN users u ON r.user_id = u.id WHERE 1=1";

$types = "";
$params = [];

if (!empty($status_filter)) {
    $query .= " AND r.status = ?";
    $types .= "s";
    $params[] = $status_filter;
}

$query .= " ORDER BY r.created_at DESC";

$reviews = getRows($query, $types, $params);

include __DIR__ . '/../includes/admin-header.php';
?>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <select class="form-select" id="statusFilter">
            <option value="">All Reviews</option>
            <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending Approval</option>
            <option value="approved" <?php echo $status_filter === 'approved' ? 'selected' : ''; ?>>Approved</option>
            <option value="rejected" <?php echo $status_filter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
        </select>
    </div>
    <div class="col-md-6">
        <button class="btn btn-primary" onclick="applyStatusFilter()">Filter</button>
    </div>
</div>

<div class="card shadow">
    <div class="card-header bg-light">
        <h5 class="mb-0">
            <i class="fas fa-star me-2"></i> All Reviews
            <span class="badge bg-primary ms-2"><?php echo count($reviews); ?></span>
        </h5>
    </div>
    
    <div class="card-body">
        <?php if (empty($reviews)): ?>
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle me-2"></i> No reviews found
            </div>
        <?php else: ?>
            <?php foreach ($reviews as $review): ?>
                <div class="card mb-3 border">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="mb-1"><?php echo htmlspecialchars($review['user_name']); ?></h6>
                                <div class="text-warning">
                                    <?php
                                    for ($i = 0; $i < $review['rating']; $i++):
                                        echo '<i class="fas fa-star"></i>';
                                    endfor;
                                    for ($i = $review['rating']; $i < 5; $i++):
                                        echo '<i class="far fa-star"></i>';
                                    endfor;
                                    ?>
                                    <small class="text-muted">(<?php echo $review['rating']; ?>/5)</small>
                                </div>
                            </div>
                            <span class="badge bg-<?php
                                $status = htmlspecialchars($review['status']);
                                echo $status === 'approved' ? 'success' : ($status === 'rejected' ? 'danger' : 'warning');
                            ?>"><?php echo ucfirst($status); ?></span>
                        </div>
                        
                        <h6 class="mt-3"><?php echo htmlspecialchars($review['title']); ?></h6>
                        <p class="text-muted"><?php echo htmlspecialchars($review['comment']); ?></p>
                        
                        <small class="text-muted">
                            Submitted on <?php echo date('d M Y', strtotime($review['created_at'])); ?>
                        </small>
                        
                        <?php if (!empty($review['admin_response'])): ?>
                            <div class="mt-3 p-2 bg-light border-start border-3 border-primary">
                                <strong class="small">Admin Response:</strong>
                                <p class="small mb-0"><?php echo htmlspecialchars($review['admin_response']); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <div class="mt-3">
                            <?php if ($status === 'pending'): ?>
                                <button class="btn btn-sm btn-success" onclick="approveReview(<?php echo $review['id']; ?>)">
                                    <i class="fas fa-check me-1"></i> Approve
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="rejectReview(<?php echo $review['id']; ?>)">
                                    <i class="fas fa-times me-1"></i> Reject
                                </button>
                            <?php endif; ?>
                            <button class="btn btn-sm btn-outline-primary" onclick="viewReviewDetails(<?php echo $review['id']; ?>)">
                                <i class="fas fa-eye me-1"></i> View Details
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
function applyStatusFilter() {
    const status = document.getElementById('statusFilter').value;
    let url = './reviews.php';
    
    if (status) {
        url += '?status=' + encodeURIComponent(status);
    }
    
    window.location.href = url;
}

function approveReview(reviewId) {
    if (confirm('Approve this review?')) {
        console.log('Approving review: ' + reviewId);
    }
}

function rejectReview(reviewId) {
    if (confirm('Reject this review?')) {
        console.log('Rejecting review: ' + reviewId);
    }
}

function viewReviewDetails(reviewId) {
    window.location.href = './review-details.php?id=' + reviewId;
}
</script>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
