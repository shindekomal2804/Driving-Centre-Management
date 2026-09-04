<?php
/**
 * Admin - Message Details & Reply
 */

$page_title = "Message Details";
$css_path = "../assets/css/style.css";
$js_path = "../assets/js/admin-script.js";

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/auth.php';

if (!AuthManager::isAdminLoggedIn()) {
    header('Location: ../admin-login.php');
    exit();
}

$message_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (empty($message_id)) {
    header('Location: ./messages.php');
    exit();
}

// Get message details
$message = getRow("SELECT * FROM contact_messages WHERE id = ?", "i", [$message_id]);

if (!$message) {
    header('Location: ./messages.php');
    exit();
}

// Mark as read if it's new
if ($message['status'] === 'new') {
    $query = "UPDATE contact_messages SET status = 'read' WHERE id = ?";
    $stmt = getConnection()->prepare($query);
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
    $message['status'] = 'read';
}

include __DIR__ . '/../includes/admin-header.php';
?>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-envelope me-2"></i> Message Details
                </h5>
                <a href="./messages.php" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Messages
                </a>
            </div>
            
            <div class="card-body">
                <!-- Message Info -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">From:</label>
                            <p class="mb-0"><?php echo htmlspecialchars($message['name']); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email:</label>
                            <p class="mb-0">
                                <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>">
                                    <?php echo htmlspecialchars($message['email']); ?>
                                </a>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Phone:</label>
                            <p class="mb-0">
                                <?php if (!empty($message['phone'])): ?>
                                    <a href="tel:<?php echo htmlspecialchars($message['phone']); ?>">
                                        <?php echo htmlspecialchars($message['phone']); ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">Not provided</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Date:</label>
                            <p class="mb-0"><?php echo date('d M Y h:i A', strtotime($message['created_at'])); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Subject:</label>
                            <p class="mb-0"><?php echo htmlspecialchars($message['subject']); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status:</label>
                            <p class="mb-0">
                                <?php
                                $status = htmlspecialchars($message['status']);
                                $badge_class = $status === 'new' ? 'warning' :
                                              ($status === 'replied' ? 'success' : 'info');
                                ?>
                                <span class="badge bg-<?php echo $badge_class; ?>"><?php echo ucfirst($status); ?></span>
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Message Content -->
                <div class="mb-4">
                    <label class="form-label fw-bold">Message:</label>
                    <div class="border rounded p-3 bg-light">
                        <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                    </div>
                </div>
                
                <!-- Previous Reply -->
                <?php if (!empty($message['reply'])): ?>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Your Previous Reply:</label>
                        <div class="border rounded p-3 bg-success bg-opacity-10 border-success">
                            <small class="text-muted">
                                Replied on <?php echo date('d M Y h:i A', strtotime($message['reply_date'])); ?>
                            </small>
                            <div class="mt-2">
                                <?php echo nl2br(htmlspecialchars($message['reply'])); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Reply Form -->
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-reply me-2"></i> Reply to Message
                        </h6>
                    </div>
                    <div class="card-body">
                        <form id="replyForm">
                            <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                            
                            <div class="mb-3">
                                <label for="reply" class="form-label">Your Reply <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="reply" name="reply" rows="6" placeholder="Type your reply here..." required></textarea>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i> Send Reply
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="markAsRead()">
                                    <i class="fas fa-check me-2"></i> Mark as Read Only
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="card shadow">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="fas fa-bolt me-2"></i> Quick Actions
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-info" onclick="window.open('mailto:<?php echo htmlspecialchars($message['email']); ?>', '_blank')">
                        <i class="fas fa-envelope me-2"></i> Email Sender
                    </button>
                    <?php if (!empty($message['phone'])): ?>
                        <button class="btn btn-outline-success" onclick="window.open('tel:<?php echo htmlspecialchars($message['phone']); ?>', '_blank')">
                            <i class="fas fa-phone me-2"></i> Call Sender
                        </button>
                    <?php endif; ?>
                    <button class="btn btn-outline-danger" onclick="deleteMessage()">
                        <i class="fas fa-trash me-2"></i> Delete Message
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Message Stats -->
        <div class="card shadow mt-3">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i> Message Stats
                </h6>
            </div>
            <div class="card-body">
                <?php
                $stats = getRow("SELECT
                    COUNT(CASE WHEN status = 'new' THEN 1 END) as new_count,
                    COUNT(CASE WHEN status = 'read' THEN 1 END) as read_count,
                    COUNT(CASE WHEN status = 'replied' THEN 1 END) as replied_count
                    FROM contact_messages", "", []);
                ?>
                <div class="row text-center">
                    <div class="col-4">
                        <div class="h4 text-warning"><?php echo $stats['new_count']; ?></div>
                        <small class="text-muted">New</small>
                    </div>
                    <div class="col-4">
                        <div class="h4 text-info"><?php echo $stats['read_count']; ?></div>
                        <small class="text-muted">Read</small>
                    </div>
                    <div class="col-4">
                        <div class="h4 text-success"><?php echo $stats['replied_count']; ?></div>
                        <small class="text-muted">Replied</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('replyForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = {
        message_id: formData.get('message_id'),
        status: 'replied',
        reply: formData.get('reply')
    };
    
    try {
        const response = await fetch('../api/update-message-status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Reply sent successfully!', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showAlert(result.message, 'danger');
        }
    } catch (error) {
        showAlert('Failed to send reply. Please try again.', 'danger');
        console.error('Error:', error);
    }
});

async function markAsRead() {
    const data = {
        message_id: <?php echo $message['id']; ?>,
        status: 'read'
    };
    
    try {
        const response = await fetch('../api/update-message-status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Message marked as read!', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showAlert(result.message, 'danger');
        }
    } catch (error) {
        showAlert('Failed to update message. Please try again.', 'danger');
        console.error('Error:', error);
    }
}

async function deleteMessage() {
    if (!confirm('Are you sure you want to delete this message? This action cannot be undone.')) {
        return;
    }
    
    try {
        const response = await fetch('../api/delete-message.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                message_id: <?php echo $message['id']; ?>
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Message deleted successfully!', 'success');
            setTimeout(() => {
                window.location.href = './messages.php';
            }, 1500);
        } else {
            showAlert(result.message, 'danger');
        }
    } catch (error) {
        showAlert('Failed to delete message. Please try again.', 'danger');
        console.error('Error:', error);
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