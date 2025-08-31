<?php
require_once dirname(__DIR__) . '/config.php';

// Require admin access
requireAdmin();

$pdo = getDBConnection();
$csrf_token = generateCSRFToken();

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!validateCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid CSRF token';
    } else {
        $report_id = intval($_POST['report_id']);
        
        switch ($_POST['action']) {
            case 'resolve':
                $status = $_POST['status'];
                $notes = sanitizeInput($_POST['admin_notes']);
                
                $stmt = $pdo->prepare("UPDATE reports SET status = ?, admin_notes = ?, resolved_by = ?, resolved_at = NOW() WHERE id = ?");
                $stmt->execute([$status, $notes, $_SESSION['user_id'], $report_id]);
                
                $success = 'Report updated successfully';
                break;
                
            case 'delete':
                $stmt = $pdo->prepare("DELETE FROM reports WHERE id = ?");
                $stmt->execute([$report_id]);
                
                $success = 'Report deleted successfully';
                break;
        }
    }
}

// Get reports with pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$type_filter = isset($_GET['type']) ? $_GET['type'] : '';

$where_clause = "WHERE 1=1";
$params = [];

if ($status_filter) {
    $where_clause .= " AND r.status = ?";
    $params[] = $status_filter;
}

if ($type_filter) {
    $where_clause .= " AND r.report_type = ?";
    $params[] = $type_filter;
}

// Get total count
$count_sql = "SELECT COUNT(*) as total FROM reports r $where_clause";
$stmt = $pdo->prepare($count_sql);
$stmt->execute($params);
$total_reports = $stmt->fetchColumn();
$total_pages = ceil($total_reports / $per_page);

// Get reports
$sql = "
    SELECT r.*, 
           u1.username as reporter_username,
           u2.username as reported_username,
           t.title as thread_title,
           p.content as post_content
    FROM reports r
    LEFT JOIN users u1 ON r.reporter_id = u1.id
    LEFT JOIN users u2 ON r.reported_user_id = u2.id
    LEFT JOIN threads t ON r.reported_thread_id = t.id
    LEFT JOIN posts p ON r.reported_post_id = p.id
    $where_clause
    ORDER BY r.created_at DESC
    LIMIT ? OFFSET ?
";

$params[] = $per_page;
$params[] = $offset;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$reports = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Admin Panel - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <link href="../css/themes.css" rel="stylesheet">
</head>
<body data-theme="light">
    <?php include '../includes/header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Reports</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportReports()">Export</button>
                        </div>
                    </div>
                </div>

                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">All Statuses</option>
                                    <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="investigating" <?php echo $status_filter === 'investigating' ? 'selected' : ''; ?>>Investigating</option>
                                    <option value="resolved" <?php echo $status_filter === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                                    <option value="dismissed" <?php echo $status_filter === 'dismissed' ? 'selected' : ''; ?>>Dismissed</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Type</label>
                                <select name="type" class="form-select">
                                    <option value="">All Types</option>
                                    <option value="user" <?php echo $type_filter === 'user' ? 'selected' : ''; ?>>User</option>
                                    <option value="post" <?php echo $type_filter === 'post' ? 'selected' : ''; ?>>Post</option>
                                    <option value="thread" <?php echo $type_filter === 'thread' ? 'selected' : ''; ?>>Thread</option>
                                    <option value="other" <?php echo $type_filter === 'other' ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary d-block">Filter</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Reports List -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Reports (<?php echo $total_reports; ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($reports)): ?>
                            <p class="text-muted">No reports found.</p>
                        <?php else: ?>
                            <?php foreach ($reports as $report): ?>
                                <div class="report-item border-bottom pb-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="badge bg-<?php echo $report['status'] === 'pending' ? 'warning' : ($report['status'] === 'resolved' ? 'success' : 'secondary'); ?> me-2">
                                                    <?php echo ucfirst($report['status']); ?>
                                                </span>
                                                <span class="badge bg-info me-2"><?php echo ucfirst($report['report_type']); ?></span>
                                                <small class="text-muted"><?php echo formatTimeAgo($report['created_at']); ?></small>
                                            </div>
                                            
                                            <h6 class="mb-1">
                                                Reported by: <a href="../profile.php?user=<?php echo urlencode($report['reporter_username']); ?>"><?php echo htmlspecialchars($report['reporter_username']); ?></a>
                                            </h6>
                                            
                                            <?php if ($report['reported_username']): ?>
                                                <p class="mb-1">
                                                    <strong>Reported User:</strong> 
                                                    <a href="../profile.php?user=<?php echo urlencode($report['reported_username']); ?>"><?php echo htmlspecialchars($report['reported_username']); ?></a>
                                                </p>
                                            <?php endif; ?>
                                            
                                            <?php if ($report['thread_title']): ?>
                                                <p class="mb-1">
                                                    <strong>Thread:</strong> 
                                                    <a href="../thread.php?id=<?php echo $report['reported_thread_id']; ?>"><?php echo htmlspecialchars($report['thread_title']); ?></a>
                                                </p>
                                            <?php endif; ?>
                                            
                                            <p class="mb-1"><strong>Reason:</strong> <?php echo htmlspecialchars($report['reason']); ?></p>
                                            
                                            <?php if ($report['description']): ?>
                                                <p class="mb-1"><strong>Description:</strong> <?php echo htmlspecialchars($report['description']); ?></p>
                                            <?php endif; ?>
                                            
                                            <?php if ($report['admin_notes']): ?>
                                                <p class="mb-1"><strong>Admin Notes:</strong> <?php echo htmlspecialchars($report['admin_notes']); ?></p>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="ms-3">
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#resolveModal<?php echo $report['id']; ?>">
                                                Resolve
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteReport(<?php echo $report['id']; ?>)">
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Resolve Modal -->
                                <div class="modal fade" id="resolveModal<?php echo $report['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Resolve Report</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                    <input type="hidden" name="action" value="resolve">
                                                    <input type="hidden" name="report_id" value="<?php echo $report['id']; ?>">
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">Status</label>
                                                        <select name="status" class="form-select" required>
                                                            <option value="resolved">Resolved</option>
                                                            <option value="dismissed">Dismissed</option>
                                                            <option value="investigating">Investigating</option>
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">Admin Notes</label>
                                                        <textarea name="admin_notes" class="form-control" rows="3" placeholder="Add notes about how this report was handled..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Reports pagination" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&status=<?php echo urlencode($status_filter); ?>&type=<?php echo urlencode($type_filter); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="../js/main.js"></script>
    
    <script>
        function deleteReport(reportId) {
            if (confirm('Are you sure you want to delete this report? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="report_id" value="${reportId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function exportReports() {
            alert('Export functionality coming soon!');
        }
    </script>
</body>
</html>
