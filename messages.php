<?php
require_once 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

$pdo = getDBConnection();
$user_id = $_SESSION['user_id'];
$csrf_token = generateCSRFToken();

// Handle message actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && validateCSRFToken($_POST['csrf_token'] ?? '')) {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'send':
            $recipient_id = intval($_POST['recipient_id']);
            $subject = sanitizeInput($_POST['subject']);
            $message = sanitizeInput($_POST['message']);
            
            if (empty($subject) || empty($message)) {
                $error = "Subject and message are required.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO private_messages (sender_id, recipient_id, subject, message) VALUES (?, ?, ?, ?)");
                if ($stmt->execute([$user_id, $recipient_id, $subject, $message])) {
                    // Create notification
                    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, title, message, link) VALUES (?, 'pm', ?, ?, ?)");
                    $stmt->execute([$recipient_id, 'New Private Message', "You have received a new message: $subject", "messages.php?inbox"]);
                    
                    $success = "Message sent successfully!";
                } else {
                    $error = "Failed to send message.";
                }
            }
            break;
            
        case 'delete':
            $message_id = intval($_POST['message_id']);
            $type = $_POST['type'] ?? 'inbox'; // inbox or sent
            
            if ($type === 'inbox') {
                $stmt = $pdo->prepare("UPDATE private_messages SET is_deleted_recipient = 1 WHERE id = ? AND recipient_id = ?");
            } else {
                $stmt = $pdo->prepare("UPDATE private_messages SET is_deleted_sender = 1 WHERE id = ? AND sender_id = ?");
            }
            
            if ($stmt->execute([$message_id, $user_id])) {
                $success = "Message deleted successfully!";
            } else {
                $error = "Failed to delete message.";
            }
            break;
            
        case 'mark_read':
            $message_id = intval($_POST['message_id']);
            $stmt = $pdo->prepare("UPDATE private_messages SET is_read = 1 WHERE id = ? AND recipient_id = ?");
            if ($stmt->execute([$message_id, $user_id])) {
                $success = "Message marked as read!";
            }
            break;
    }
}

// Get current tab
$tab = $_GET['tab'] ?? 'inbox';
$to_user_id = $_GET['to'] ?? null;

// Get messages based on current tab
$messages = [];
$total_messages = 0;

if ($tab === 'inbox') {
    $stmt = $pdo->prepare("
        SELECT pm.*, u.username as sender_username, u.avatar as sender_avatar
        FROM private_messages pm
        JOIN users u ON pm.sender_id = u.id
        WHERE pm.recipient_id = ? AND pm.is_deleted_recipient = 0
        ORDER BY pm.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $messages = $stmt->fetchAll();
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM private_messages WHERE recipient_id = ? AND is_deleted_recipient = 0");
    $stmt->execute([$user_id]);
    $total_messages = $stmt->fetchColumn();
} elseif ($tab === 'sent') {
    $stmt = $pdo->prepare("
        SELECT pm.*, u.username as recipient_username, u.avatar as recipient_avatar
        FROM private_messages pm
        JOIN users u ON pm.recipient_id = u.id
        WHERE pm.sender_id = ? AND pm.is_deleted_sender = 0
        ORDER BY pm.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $messages = $stmt->fetchAll();
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM private_messages WHERE sender_id = ? AND is_deleted_sender = 0");
    $stmt->execute([$user_id]);
    $total_messages = $stmt->fetchColumn();
}

// Get unread count for inbox
$stmt = $pdo->prepare("SELECT COUNT(*) FROM private_messages WHERE recipient_id = ? AND is_read = 0 AND is_deleted_recipient = 0");
$stmt->execute([$user_id]);
$unread_count = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/themes.css" rel="stylesheet">
</head>
<body data-theme="light">
    <?php include 'includes/header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <!-- Messages Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
                <div class="position-sticky pt-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                            <span>Messages</span>
                        </h6>
                        <a href="?tab=compose" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i>
                        </a>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?php echo $tab === 'inbox' ? 'active' : ''; ?>" href="?tab=inbox">
                                <i class="fas fa-inbox me-2"></i>
                                Inbox
                                <?php if ($unread_count > 0): ?>
                                <span class="badge bg-danger ms-auto"><?php echo $unread_count; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $tab === 'sent' ? 'active' : ''; ?>" href="?tab=sent">
                                <i class="fas fa-paper-plane me-2"></i>
                                Sent
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $tab === 'compose' ? 'active' : ''; ?>" href="?tab=compose">
                                <i class="fas fa-edit me-2"></i>
                                Compose
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">
                        <?php
                        switch ($tab) {
                            case 'inbox':
                                echo 'Inbox';
                                break;
                            case 'sent':
                                echo 'Sent Messages';
                                break;
                            case 'compose':
                                echo 'Compose Message';
                                break;
                        }
                        ?>
                    </h1>
                </div>
                
                <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if ($tab === 'compose'): ?>
                <!-- Compose Message Form -->
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            <input type="hidden" name="action" value="send">
                            
                            <div class="mb-3">
                                <label for="recipient" class="form-label">To:</label>
                                <select class="form-select" id="recipient" name="recipient_id" required>
                                    <option value="">Select recipient...</option>
                                    <?php
                                    $stmt = $pdo->prepare("SELECT id, username FROM users WHERE id != ? AND is_active = 1 ORDER BY username");
                                    $stmt->execute([$user_id]);
                                    $users = $stmt->fetchAll();
                                    foreach ($users as $user) {
                                        $selected = ($to_user_id && $to_user_id == $user['id']) ? 'selected' : '';
                                        echo '<option value="' . $user['id'] . '" ' . $selected . '>' . htmlspecialchars($user['username']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject:</label>
                                <input type="text" class="form-control" id="subject" name="subject" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="message" class="form-label">Message:</label>
                                <textarea class="form-control" id="message" name="message" rows="10" required maxlength="700"></textarea>
                                <div class="form-text">Maximum 700 characters.</div>
                                
                                <!-- Character Count -->
                                <div class="text-muted mt-1">
                                    <small><span id="messageCharCount">0</span>/700 characters</small>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="?tab=inbox" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i>Send Message
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <?php else: ?>
                <!-- Messages List -->
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                                                            <span>
                                    <?php echo ucfirst($tab); ?> Messages
                                    <span class="text-muted">(<?php echo $total_messages; ?>)</span>
                                </span>
                            <?php if ($tab === 'inbox'): ?>
                            <a href="?tab=compose" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-2"></i>New Message
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="card-body p-0">
                        <?php if (empty($messages)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5>No messages</h5>
                            <p class="text-muted">
                                <?php if ($tab === 'inbox'): ?>
                                You don't have any messages yet.
                                <?php else: ?>
                                You haven't sent any messages yet.
                                <?php endif; ?>
                            </p>
                            <?php if ($tab === 'inbox'): ?>
                            <a href="?tab=compose" class="btn btn-primary">Send your first message</a>
                            <?php endif; ?>
                        </div>
                        <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($messages as $msg): ?>
                            <div class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between align-items-start">
                                    <div class="d-flex align-items-center">
                                        <?php if ($tab === 'inbox'): ?>
                                        <img src="<?php echo $msg['sender_avatar'] ?: 'images/default-avatar.svg'; ?>" 
                                             alt="<?php echo htmlspecialchars($msg['sender_username']); ?>" 
                                             class="avatar-img-sm me-3">
                                        <div>
                                            <h6 class="mb-1">
                                                <?php if (!$msg['is_read']): ?>
                                                <strong><?php echo htmlspecialchars($msg['subject']); ?></strong>
                                                <?php else: ?>
                                                <?php echo htmlspecialchars($msg['subject']); ?>
                                                <?php endif; ?>
                                            </h6>
                                            <p class="mb-1 text-muted">
                                                From: <strong><?php echo htmlspecialchars($msg['sender_username']); ?></strong>
                                            </p>
                                        </div>
                                        <?php else: ?>
                                        <img src="<?php echo $msg['recipient_avatar'] ?: 'images/default-avatar.svg'; ?>" 
                                             alt="<?php echo htmlspecialchars($msg['recipient_username']); ?>" 
                                             class="avatar-img-sm me-3">
                                        <div>
                                            <h6 class="mb-1"><?php echo htmlspecialchars($msg['subject']); ?></h6>
                                            <p class="mb-1 text-muted">
                                                To: <strong><?php echo htmlspecialchars($msg['recipient_username']); ?></strong>
                                            </p>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="d-flex align-items-center">
                                        <small class="text-muted me-3">
                                            <?php echo formatTimeAgo($msg['created_at']); ?>
                                        </small>
                                        
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary btn-sm" 
                                                    onclick="viewMessage(<?php echo $msg['id']; ?>, '<?php echo $tab; ?>')">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            
                                            <form method="POST" action="" style="display: inline;" 
                                                  onsubmit="return confirm('Are you sure you want to delete this message?')">
                                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                                <input type="hidden" name="type" value="<?php echo $tab; ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-2">
                                    <p class="mb-1 text-muted">
                                        <?php echo htmlspecialchars(substr(strip_tags($msg['message']), 0, 150)); ?>
                                        <?php if (strlen(strip_tags($msg['message'])) > 150): ?>
                                        ...
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="js/main.js"></script>
    <script src="js/themes.js"></script>
    
    <!-- Message View Modal -->
    <div class="modal fade" id="messageModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="messageModalTitle">Message</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="messageModalBody">
                    <!-- Message content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="replyBtn" style="display: none;">
                        <i class="fas fa-reply me-2"></i>Reply
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function viewMessage(messageId, type) {
            // Load message content via AJAX
            $.get('ajax/get-message.php', {
                message_id: messageId,
                type: type,
                csrf_token: '<?php echo $csrf_token; ?>'
            })
            .done(function(response) {
                if (response.success) {
                    $('#messageModalTitle').text(response.data.subject);
                    $('#messageModalBody').html(response.data.html);
                    
                    // Show reply button only for inbox messages
                    if (type === 'inbox') {
                        $('#replyBtn').show().off('click').on('click', function() {
                            window.location.href = `?tab=compose&to=${response.data.sender_id}`;
                        });
                    } else {
                        $('#replyBtn').hide();
                    }
                    
                    $('#messageModal').modal('show');
                } else {
                    alert('Failed to load message: ' + response.message);
                }
            })
            .fail(function() {
                alert('Failed to load message');
            });
        }
        
        // Auto-resize textarea
        document.getElementById('message').addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
            
            // Update character count
            updateMessageCharCount();
        });
        
        // Character count for private messages
        function updateMessageCharCount() {
            const message = document.getElementById('message');
            const charCount = message.value.length;
            const maxChars = 700;
            const charCountElement = document.getElementById('messageCharCount');
            
            charCountElement.textContent = charCount + '/' + maxChars;
            
            // Color coding based on character count
            if (charCount > maxChars) {
                charCountElement.className = 'text-danger';
            } else if (charCount > maxChars * 0.9) {
                charCountElement.className = 'text-warning';
            } else {
                charCountElement.className = 'text-muted';
            }
        }
        
        // Initialize character count on page load
        $(document).ready(function() {
            updateMessageCharCount();
        });
    </script>
</body>
</html>
