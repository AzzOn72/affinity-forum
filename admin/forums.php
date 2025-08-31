<?php
require_once '../config.php';

// Check if user is admin
if (!isAdmin()) {
    redirect('../login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

$pdo = getDBConnection();
$csrf_token = generateCSRFToken();

// Handle actions
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $message = 'Invalid request token';
        $message_type = 'danger';
    } else {
        switch ($_POST['action']) {
            case 'add_forum':
                $name = sanitizeInput($_POST['name'] ?? '');
                $description = sanitizeInput($_POST['description'] ?? '');
                $order_num = intval($_POST['order_num'] ?? 0);
                
                if (!empty($name)) {
                    try {
                        $stmt = $pdo->prepare("INSERT INTO forums (name, description, order_num, created_at) VALUES (?, ?, ?, NOW())");
                        $stmt->execute([$name, $description, $order_num]);
                        $message = 'Forum created successfully';
                        $message_type = 'success';
                    } catch (Exception $e) {
                        $message = 'Error creating forum: ' . $e->getMessage();
                        $message_type = 'danger';
                    }
                }
                break;
                
            case 'edit_forum':
                $forum_id = intval($_POST['forum_id'] ?? 0);
                $name = sanitizeInput($_POST['name'] ?? '');
                $description = sanitizeInput($_POST['description'] ?? '');
                $order_num = intval($_POST['order_num'] ?? 0);
                
                if ($forum_id && !empty($name)) {
                    try {
                        $stmt = $pdo->prepare("UPDATE forums SET name = ?, description = ?, order_num = ? WHERE id = ?");
                        $stmt->execute([$name, $description, $order_num, $forum_id]);
                        $message = 'Forum updated successfully';
                        $message_type = 'success';
                    } catch (Exception $e) {
                        $message = 'Error updating forum: ' . $e->getMessage();
                        $message_type = 'danger';
                    }
                }
                break;
                
            case 'delete_forum':
                $forum_id = intval($_POST['forum_id'] ?? 0);
                
                if ($forum_id) {
                    try {
                        // Check if forum has threads
                        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM threads WHERE forum_id = ?");
                        $stmt->execute([$forum_id]);
                        $thread_count = $stmt->fetch()['count'];
                        
                        if ($thread_count > 0) {
                            $message = 'Cannot delete forum with existing threads. Move or delete threads first.';
                            $message_type = 'warning';
                        } else {
                            $stmt = $pdo->prepare("DELETE FROM forums WHERE id = ?");
                            $stmt->execute([$forum_id]);
                            $message = 'Forum deleted successfully';
                            $message_type = 'success';
                        }
                    } catch (Exception $e) {
                        $message = 'Error deleting forum: ' . $e->getMessage();
                        $message_type = 'danger';
                    }
                }
                break;
        }
    }
}

// Get forums
try {
    $stmt = $pdo->query("
        SELECT f.*, 
               (SELECT COUNT(*) FROM threads WHERE forum_id = f.id) as thread_count,
               (SELECT COUNT(*) FROM posts p JOIN threads t ON p.thread_id = t.id WHERE t.forum_id = f.id) as post_count
        FROM forums f 
        ORDER BY f.order_num ASC, f.name ASC
    ");
    $forums = $stmt->fetchAll();
} catch (Exception $e) {
    $forums = [];
    $message = 'Error loading forums: ' . $e->getMessage();
    $message_type = 'danger';
}

// Get recent threads for overview
try {
    $stmt = $pdo->query("
        SELECT t.*, u.username, f.name as forum_name
        FROM threads t 
        JOIN users u ON t.user_id = u.id 
        JOIN forums f ON t.forum_id = f.id
        ORDER BY t.created_at DESC 
        LIMIT 10
    ");
    $recent_threads = $stmt->fetchAll();
} catch (Exception $e) {
    $recent_threads = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum Management - Admin Panel</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../css/style.css" rel="stylesheet">
    <link href="../css/themes.css" rel="stylesheet">
    
    <style>
        .forum-card {
            transition: transform 0.2s;
        }
        .forum-card:hover {
            transform: translateY(-2px);
        }
        .forum-stats {
            font-size: 0.9em;
            color: #6c757d;
        }
    </style>
</head>
<body data-theme="light">
    <?php include '../includes/header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <!-- Admin Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="users.php">
                                <i class="fas fa-users me-2"></i>
                                User Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="forums.php">
                                <i class="fas fa-comments me-2"></i>
                                Forum Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../index.php">
                                <i class="fas fa-home me-2"></i>
                                Back to Site
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Forum Management</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addForumModal">
                            <i class="fas fa-plus me-2"></i>Add Forum
                        </button>
                    </div>
                </div>
                
                <!-- Message Display -->
                <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <!-- Forum Overview -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card shadow">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-chart-pie me-2"></i>Forum Statistics
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="h4 text-primary"><?php echo count($forums); ?></div>
                                        <small class="text-muted">Total Forums</small>
                                    </div>
                                    <div class="col-6">
                                        <div class="h4 text-success">
                                            <?php 
                                            $total_threads = array_sum(array_column($forums, 'thread_count'));
                                            echo $total_threads;
                                            ?>
                                        </div>
                                        <small class="text-muted">Total Threads</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card shadow">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-clock me-2"></i>Recent Activity
                                </h6>
                            </div>
                            <div class="card-body">
                                <?php if (empty($recent_threads)): ?>
                                <p class="text-muted text-center mb-0">No recent threads</p>
                                <?php else: ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach (array_slice($recent_threads, 0, 3) as $thread): ?>
                                    <div class="list-group-item border-0 px-0 py-2">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <div class="fw-bold small">
                                                    <a href="../thread.php?id=<?php echo $thread['id']; ?>" target="_blank">
                                                        <?php echo htmlspecialchars($thread['title']); ?>
                                                    </a>
                                                </div>
                                                <small class="text-muted">
                                                    by <?php echo htmlspecialchars($thread['username']); ?> 
                                                    in <?php echo htmlspecialchars($thread['forum_name']); ?>
                                                </small>
                                            </div>
                                            <small class="text-muted">
                                                <?php echo formatTimeAgo($thread['created_at']); ?>
                                            </small>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Forums List -->
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-list me-2"></i>Manage Forums
                        </h6>
                    </div>
                    <div class="card-body">
                        <?php if (empty($forums)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No forums found</h5>
                            <p class="text-muted">Create your first forum to get started</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addForumModal">
                                <i class="fas fa-plus me-2"></i>Create Forum
                            </button>
                        </div>
                        <?php else: ?>
                        <div class="row">
                            <?php foreach ($forums as $forum): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card forum-card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title mb-0"><?php echo htmlspecialchars($forum['name']); ?></h6>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-primary btn-sm" 
                                                        data-bs-toggle="modal" data-bs-target="#editForumModal" 
                                                        data-forum='<?php echo json_encode($forum); ?>'>
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-danger btn-sm" 
                                                        onclick="deleteForum(<?php echo $forum['id']; ?>, '<?php echo htmlspecialchars($forum['name']); ?>')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <?php if (!empty($forum['description'])): ?>
                                        <p class="card-text small text-muted">
                                            <?php echo htmlspecialchars($forum['description']); ?>
                                        </p>
                                        <?php endif; ?>
                                        
                                        <div class="forum-stats">
                                            <div class="row text-center">
                                                <div class="col-6">
                                                    <div class="fw-bold"><?php echo $forum['thread_count']; ?></div>
                                                    <small>Threads</small>
                                                </div>
                                                <div class="col-6">
                                                    <div class="fw-bold"><?php echo $forum['post_count']; ?></div>
                                                    <small>Posts</small>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                Order: <?php echo $forum['order_num']; ?> | 
                                                Created: <?php echo formatDate($forum['created_at']); ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add Forum Modal -->
    <div class="modal fade" id="addForumModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Forum</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <input type="hidden" name="action" value="add_forum">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Forum Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3" placeholder="Optional forum description"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="order_num" class="form-label">Display Order</label>
                            <input type="number" class="form-control" name="order_num" value="0" min="0">
                            <small class="text-muted">Lower numbers appear first</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Forum</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Edit Forum Modal -->
    <div class="modal fade" id="editForumModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Forum</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <input type="hidden" name="action" value="edit_forum">
                        <input type="hidden" name="forum_id" id="editForumId">
                        
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Forum Name</label>
                            <input type="text" class="form-control" name="name" id="edit_name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="edit_description" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_order_num" class="form-label">Display Order</label>
                            <input type="number" class="form-control" name="order_num" id="edit_order_num" min="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Forum</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <script>
        // Modal data handling
        $('#editForumModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const forum = button.data('forum');
            const modal = $(this);
            modal.find('#editForumId').val(forum.id);
            modal.find('#edit_name').val(forum.name);
            modal.find('#edit_description').val(forum.description);
            modal.find('#edit_order_num').val(forum.order_num);
        });
        
        // Delete forum function
        function deleteForum(forumId, forumName) {
            if (confirm(`Are you sure you want to delete forum "${forumName}"? This action cannot be undone.`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="action" value="delete_forum">
                    <input type="hidden" name="forum_id" value="${forumId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>
