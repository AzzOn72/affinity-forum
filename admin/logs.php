<?php
require_once '../config.php';

// Check if user is admin
if (!isAdmin()) {
    redirect('../login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

$pdo = getDBConnection();
$csrf_token = generateCSRFToken();

// Get filter parameters
$log_type = $_GET['type'] ?? '';
$user_filter = $_GET['user'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 50;
$offset = ($page - 1) * $per_page;

// Build query
$where_conditions = [];
$params = [];

if (!empty($log_type)) {
    $where_conditions[] = "log_type = ?";
    $params[] = $log_type;
}

if (!empty($user_filter)) {
    $where_conditions[] = "username LIKE ?";
    $params[] = "%$user_filter%";
}

if (!empty($date_from)) {
    $where_conditions[] = "created_at >= ?";
    $params[] = $date_from . ' 00:00:00';
}

if (!empty($date_to)) {
    $where_conditions[] = "created_at <= ?";
    $params[] = $date_to . ' 23:59:59';
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get total count
try {
    $count_sql = "SELECT COUNT(*) as total FROM system_logs $where_clause";
    $stmt = $pdo->prepare($count_sql);
    $stmt->execute($params);
    $total_logs = $stmt->fetch()['total'];
} catch (Exception $e) {
    $total_logs = 0;
}

$total_pages = ceil($total_logs / $per_page);

// Get logs
try {
    $sql = "SELECT l.*, u.username 
            FROM system_logs l 
            LEFT JOIN users u ON l.user_id = u.id 
            $where_clause 
            ORDER BY l.created_at DESC 
            LIMIT ? OFFSET ?";
    
    $params[] = $per_page;
    $params[] = $offset;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $logs = $stmt->fetchAll();
} catch (Exception $e) {
    $logs = [];
}

// Get log types for filter
try {
    $stmt = $pdo->query("SELECT DISTINCT log_type FROM system_logs ORDER BY log_type");
    $log_types = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    $log_types = [];
}

// Get recent users for filter
try {
    $stmt = $pdo->query("SELECT DISTINCT username FROM users ORDER BY username");
    $users = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    $users = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Logs - Admin Panel</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../css/style.css" rel="stylesheet">
    <link href="../css/themes.css" rel="stylesheet">
    
    <style>
        .log-entry {
            transition: background-color 0.2s;
        }
        .log-entry:hover {
            background-color: #f8f9fa;
        }
        .log-type {
            font-size: 0.8em;
            padding: 2px 8px;
            border-radius: 12px;
            font-weight: bold;
        }
        .log-type.login { background: #d4edda; color: #155724; }
        .log-type.logout { background: #f8d7da; color: #721c24; }
        .log-type.admin { background: #cce5ff; color: #004085; }
        .log-type.error { background: #f8d7da; color: #721c24; }
        .log-type.security { background: #fff3cd; color: #856404; }
        .log-type.user { background: #e2e3e5; color: #383d41; }
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
                            <a class="nav-link" href="forums.php">
                                <i class="fas fa-comments me-2"></i>
                                Forum Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="logs.php">
                                <i class="fas fa-list-alt me-2"></i>
                                System Logs
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
                    <h1 class="h2">System Logs</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button type="button" class="btn btn-outline-secondary" onclick="exportLogs()">
                            <i class="fas fa-download me-2"></i>Export
                        </button>
                        <button type="button" class="btn btn-outline-danger" onclick="clearLogs()">
                            <i class="fas fa-trash me-2"></i>Clear Logs
                        </button>
                    </div>
                </div>
                
                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-2">
                                <label for="type" class="form-label">Log Type</label>
                                <select class="form-select" id="type" name="type">
                                    <option value="">All Types</option>
                                    <?php foreach ($log_types as $type): ?>
                                    <option value="<?php echo htmlspecialchars($type); ?>" 
                                            <?php echo $log_type === $type ? 'selected' : ''; ?>>
                                        <?php echo ucfirst(htmlspecialchars($type)); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <label for="user" class="form-label">User</label>
                                <select class="form-select" id="user" name="user">
                                    <option value="">All Users</option>
                                    <?php foreach ($users as $user): ?>
                                    <option value="<?php echo htmlspecialchars($user); ?>" 
                                            <?php echo $user_filter === $user ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($user); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <label for="date_from" class="form-label">From Date</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" 
                                       value="<?php echo htmlspecialchars($date_from); ?>">
                            </div>
                            
                            <div class="col-md-2">
                                <label for="date_to" class="form-label">To Date</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" 
                                       value="<?php echo htmlspecialchars($date_to); ?>">
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-search me-1"></i>Filter
                                    </button>
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <a href="logs.php" class="btn btn-outline-secondary w-100">
                                        <i class="fas fa-times me-1"></i>Clear
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Logs Table -->
                <div class="card shadow">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-list me-2"></i>System Activity
                            (<?php echo number_format($total_logs); ?> total entries)
                        </h6>
                        <div class="text-muted">
                            Page <?php echo $page; ?> of <?php echo $total_pages; ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($logs)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No logs found</h5>
                            <p class="text-muted">Try adjusting your filter criteria</p>
                        </div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Timestamp</th>
                                        <th>Type</th>
                                        <th>User</th>
                                        <th>Action</th>
                                        <th>Details</th>
                                        <th>IP Address</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($logs as $log): ?>
                                    <tr class="log-entry">
                                        <td>
                                            <small class="text-muted">
                                                <?php echo formatDate($log['created_at']); ?>
                                                <br>
                                                <span class="text-muted"><?php echo formatTimeAgo($log['created_at']); ?></span>
                                            </small>
                                        </td>
                                        <td>
                                            <span class="log-type <?php echo $log['log_type']; ?>">
                                                <?php echo ucfirst(htmlspecialchars($log['log_type'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($log['username']): ?>
                                                <strong><?php echo htmlspecialchars($log['username']); ?></strong>
                                            <?php else: ?>
                                                <span class="text-muted">System</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($log['action']); ?></strong>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars($log['details']); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars($log['ip_address'] ?? 'N/A'); ?>
                                            </small>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                        <nav aria-label="Logs pagination">
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">Previous</a>
                                </li>
                                <?php endif; ?>
                                
                                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                                </li>
                                <?php endfor; ?>
                                
                                <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">Next</a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Clear Logs Confirmation Modal -->
    <div class="modal fade" id="clearLogsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Clear System Logs</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> This action will permanently delete all system logs. This cannot be undone.
                    </div>
                    <p>Are you sure you want to clear all system logs?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="confirmClearLogs()">Clear All Logs</button>
                </div>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <script>
        function exportLogs() {
            // Build export URL with current filters
            const params = new URLSearchParams(window.location.search);
            params.set('export', 'csv');
            window.location.href = 'export-logs.php?' + params.toString();
        }
        
        function clearLogs() {
            new bootstrap.Modal(document.getElementById('clearLogsModal')).show();
        }
        
        function confirmClearLogs() {
            if (confirm('This will permanently delete ALL system logs. Are you absolutely sure?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'clear-logs.php';
                form.innerHTML = `
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        // Auto-refresh every 30 seconds
        setInterval(function() {
            if (document.visibilityState === 'visible') {
                location.reload();
            }
        }, 30000);
    </script>
</body>
</html>
