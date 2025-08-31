<?php
require_once '../config.php';

// Check if user is admin
if (!isAdmin()) {
    redirect('../login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

$pdo = getDBConnection();
$csrf_token = generateCSRFToken();

$message = '';
$message_type = '';

// Handle backup creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $message = 'Invalid request token';
        $message_type = 'danger';
    } else {
        switch ($_POST['action']) {
            case 'create_backup':
                try {
                    $backup_dir = '../backups/';
                    if (!is_dir($backup_dir)) {
                        mkdir($backup_dir, 0755, true);
                    }
                    
                    $timestamp = date('Y-m-d_H-i-s');
                    $backup_file = $backup_dir . 'backup_' . $timestamp . '.sql';
                    
                    // Get database structure and data
                    $tables = [];
                    $stmt = $pdo->query("SHOW TABLES");
                    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
                        $tables[] = $row[0];
                    }
                    
                    $backup_content = "-- Affinity Forum Database Backup\n";
                    $backup_content .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
                    $backup_content .= "-- Database: " . DB_NAME . "\n\n";
                    
                    foreach ($tables as $table) {
                        // Get table structure
                        $stmt = $pdo->query("SHOW CREATE TABLE `$table`");
                        $row = $stmt->fetch(PDO::FETCH_NUM);
                        $backup_content .= "\n-- Table structure for table `$table`\n";
                        $backup_content .= "DROP TABLE IF EXISTS `$table`;\n";
                        $backup_content .= $row[1] . ";\n\n";
                        
                        // Get table data
                        $stmt = $pdo->query("SELECT * FROM `$table`");
                        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        if (!empty($rows)) {
                            $backup_content .= "-- Data for table `$table`\n";
                            foreach ($rows as $row) {
                                $backup_content .= "INSERT INTO `$table` VALUES (";
                                $values = [];
                                foreach ($row as $value) {
                                    if ($value === null) {
                                        $values[] = 'NULL';
                                    } else {
                                        $values[] = "'" . addslashes($value) . "'";
                                    }
                                }
                                $backup_content .= implode(', ', $values) . ");\n";
                            }
                            $backup_content .= "\n";
                        }
                    }
                    
                    if (file_put_contents($backup_file, $backup_content)) {
                        $message = 'Backup created successfully: ' . basename($backup_file);
                        $message_type = 'success';
                    } else {
                        $message = 'Failed to create backup file';
                        $message_type = 'danger';
                    }
                    
                } catch (Exception $e) {
                    $message = 'Error creating backup: ' . $e->getMessage();
                    $message_type = 'danger';
                }
                break;
                
            case 'delete_backup':
                $backup_file = $_POST['backup_file'] ?? '';
                if (!empty($backup_file)) {
                    $backup_path = '../backups/' . basename($backup_file);
                    if (file_exists($backup_path) && unlink($backup_path)) {
                        $message = 'Backup deleted successfully';
                        $message_type = 'success';
                    } else {
                        $message = 'Failed to delete backup file';
                        $message_type = 'danger';
                    }
                }
                break;
        }
    }
}

// Get existing backups
$backups = [];
$backup_dir = '../backups/';
if (is_dir($backup_dir)) {
    $files = glob($backup_dir . '*.sql');
    foreach ($files as $file) {
        $backups[] = [
            'filename' => basename($file),
            'size' => filesize($file),
            'created' => filemtime($file),
            'path' => $file
        ];
    }
    // Sort by creation time (newest first)
    usort($backups, function($a, $b) {
        return $b['created'] - $a['created'];
    });
}

// Get database info
try {
    $stmt = $pdo->query("SELECT COUNT(*) as table_count FROM information_schema.tables WHERE table_schema = '" . DB_NAME . "'");
    $table_count = $stmt->fetch()['table_count'];
    
    $stmt = $pdo->query("SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) as db_size FROM information_schema.tables WHERE table_schema = '" . DB_NAME . "'");
    $db_size = $stmt->fetch()['db_size'];
} catch (Exception $e) {
    $table_count = 'Unknown';
    $db_size = 'Unknown';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Backup - Admin Panel</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../css/style.css" rel="stylesheet">
    <link href="../css/themes.css" rel="stylesheet">
    
    <style>
        .backup-card {
            transition: transform 0.2s;
        }
        .backup-card:hover {
            transform: translateY(-2px);
        }
        .backup-info {
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
                            <a class="nav-link" href="forums.php">
                                <i class="fas fa-comments me-2"></i>
                                Forum Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logs.php">
                                <i class="fas fa-list-alt me-2"></i>
                                System Logs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="backup.php">
                                <i class="fas fa-database me-2"></i>
                                Database Backup
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
                    <h1 class="h2">Database Backup & Restore</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button type="button" class="btn btn-primary" onclick="createBackup()">
                            <i class="fas fa-download me-2"></i>Create Backup
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
                
                <!-- Database Info -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card shadow">
                            <div class="card-body text-center">
                                <i class="fas fa-database fa-3x text-primary mb-3"></i>
                                <h5>Database Size</h5>
                                <div class="h3 text-primary"><?php echo $db_size; ?> MB</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card shadow">
                            <div class="card-body text-center">
                                <i class="fas fa-table fa-3x text-success mb-3"></i>
                                <h5>Total Tables</h5>
                                <div class="h3 text-success"><?php echo $table_count; ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card shadow">
                            <div class="card-body text-center">
                                <i class="fas fa-save fa-3x text-info mb-3"></i>
                                <h5>Backup Files</h5>
                                <div class="h3 text-info"><?php echo count($backups); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Backup Actions -->
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-cogs me-2"></i>Backup Actions
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Create New Backup</h6>
                                <p class="text-muted">Generate a complete backup of your database including all tables and data.</p>
                                <button type="button" class="btn btn-primary" onclick="createBackup()">
                                    <i class="fas fa-download me-2"></i>Create Backup Now
                                </button>
                            </div>
                            
                            <div class="col-md-6">
                                <h6>Backup Information</h6>
                                <ul class="text-muted">
                                    <li>Backups include all tables and data</li>
                                    <li>Files are stored in the backups/ directory</li>
                                    <li>Each backup is timestamped</li>
                                    <li>Backups can be used to restore your database</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Existing Backups -->
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-list me-2"></i>Existing Backups
                        </h6>
                    </div>
                    <div class="card-body">
                        <?php if (empty($backups)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-save fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No backups found</h5>
                            <p class="text-muted">Create your first backup to get started</p>
                            <button type="button" class="btn btn-primary" onclick="createBackup()">
                                <i class="fas fa-download me-2"></i>Create First Backup
                            </button>
                        </div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Backup File</th>
                                        <th>Size</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($backups as $backup): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-file-archive text-primary me-2"></i>
                                                <div>
                                                    <div class="fw-bold"><?php echo htmlspecialchars($backup['filename']); ?></div>
                                                    <small class="text-muted">Full database backup</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                <?php echo number_format($backup['size'] / 1024, 1); ?> KB
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?php echo formatDate($backup['created']); ?>
                                                <br>
                                                <span class="text-muted"><?php echo formatTimeAgo($backup['created']); ?></span>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="../backups/<?php echo urlencode($backup['filename']); ?>" 
                                                   class="btn btn-outline-primary btn-sm" download>
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-danger btn-sm" 
                                                        onclick="deleteBackup('<?php echo htmlspecialchars($backup['filename']); ?>')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
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
                
                <!-- Restore Instructions -->
                <div class="card shadow mt-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-info-circle me-2"></i>Restore Instructions
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Important:</strong> Restoring a backup will overwrite your current database. Make sure you have a current backup before proceeding.
                        </div>
                        
                        <h6>To restore from a backup:</h6>
                        <ol>
                            <li>Download the backup file you want to restore from</li>
                            <li>Use phpMyAdmin or MySQL command line to restore</li>
                            <li>Select your database and import the .sql file</li>
                            <li>Verify the restore was successful</li>
                        </ol>
                        
                        <h6>Command line restore:</h6>
                        <code>mysql -u username -p database_name < backup_file.sql</code>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Create Backup Confirmation Modal -->
    <div class="modal fade" id="createBackupModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Database Backup</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>This will create a complete backup of your database including:</p>
                    <ul>
                        <li>All table structures</li>
                        <li>All data</li>
                        <li>Database configuration</li>
                    </ul>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        The backup process may take a few moments depending on your database size.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="confirmCreateBackup()">Create Backup</button>
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
        function createBackup() {
            new bootstrap.Modal(document.getElementById('createBackupModal')).show();
        }
        
        function confirmCreateBackup() {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="action" value="create_backup">
            `;
            document.body.appendChild(form);
            form.submit();
        }
        
        function deleteBackup(filename) {
            if (confirm(`Are you sure you want to delete backup "${filename}"? This action cannot be undone.`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="action" value="delete_backup">
                    <input type="hidden" name="backup_file" value="${filename}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>
