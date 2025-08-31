<?php
require_once dirname(__DIR__) . '/config.php';

// Require admin access
requireAdmin();

$pdo = getDBConnection();
$csrf_token = generateCSRFToken();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid CSRF token';
    } else {
        try {
            // Update site settings
            $settings = [
                'site_name' => $_POST['site_name'],
                'site_description' => $_POST['site_description'],
                'admin_email' => $_POST['admin_email'],
                'max_file_size' => intval($_POST['max_file_size']),
                'max_login_attempts' => intval($_POST['max_login_attempts']),
                'session_timeout' => intval($_POST['session_timeout']),
                'maintenance_mode' => isset($_POST['maintenance_mode']) ? 1 : 0,
                'registration_enabled' => isset($_POST['registration_enabled']) ? 1 : 0,
                'guest_posting' => isset($_POST['guest_posting']) ? 1 : 0
            ];
            
            foreach ($settings as $key => $value) {
                $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value, setting_type) VALUES (?, ?, 'string') ON DUPLICATE KEY UPDATE setting_value = ?");
                $stmt->execute([$key, $value, $value]);
            }
            
            $success = 'Settings updated successfully';
        } catch (Exception $e) {
            $error = 'Error updating settings: ' . $e->getMessage();
        }
    }
}

// Get current settings
$current_settings = [];
try {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings");
    while ($row = $stmt->fetch()) {
        $current_settings[$row['setting_key']] = $row['setting_value'];
    }
} catch (Exception $e) {
    // Table might not exist yet, use defaults
}

// Set default values
$site_name = $current_settings['site_name'] ?? SITE_NAME;
$site_description = $current_settings['site_description'] ?? SITE_DESCRIPTION;
$admin_email = $current_settings['admin_email'] ?? ADMIN_EMAIL;
$max_file_size = $current_settings['max_file_size'] ?? MAX_FILE_SIZE;
$max_login_attempts = $current_settings['max_login_attempts'] ?? MAX_LOGIN_ATTEMPTS;
$session_timeout = $current_settings['session_timeout'] ?? SESSION_TIMEOUT;
$maintenance_mode = $current_settings['maintenance_mode'] ?? 0;
$registration_enabled = $current_settings['registration_enabled'] ?? 1;
$guest_posting = $current_settings['guest_posting'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Settings - Admin Panel - <?php echo SITE_NAME; ?></title>
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
                    <h1 class="h2">Site Settings</h1>
                </div>

                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">General Settings</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Site Name</label>
                                        <input type="text" name="site_name" class="form-control" value="<?php echo htmlspecialchars($site_name); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Site Description</label>
                                        <textarea name="site_description" class="form-control" rows="3"><?php echo htmlspecialchars($site_description); ?></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Admin Email</label>
                                        <input type="email" name="admin_email" class="form-control" value="<?php echo htmlspecialchars($admin_email); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Max File Size (bytes)</label>
                                        <input type="number" name="max_file_size" class="form-control" value="<?php echo $max_file_size; ?>" required>
                                        <small class="form-text text-muted">Current: <?php echo number_format($max_file_size / 1024 / 1024, 2); ?> MB</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Max Login Attempts</label>
                                        <input type="number" name="max_login_attempts" class="form-control" value="<?php echo $max_login_attempts; ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Session Timeout (seconds)</label>
                                        <input type="number" name="session_timeout" class="form-control" value="<?php echo $session_timeout; ?>" required>
                                        <small class="form-text text-muted">Current: <?php echo number_format($session_timeout / 60, 1); ?> minutes</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" name="maintenance_mode" class="form-check-input" id="maintenance_mode" <?php echo $maintenance_mode ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="maintenance_mode">Maintenance Mode</label>
                                            <small class="form-text text-muted d-block">When enabled, only admins can access the site</small>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" name="registration_enabled" class="form-check-input" id="registration_enabled" <?php echo $registration_enabled ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="registration_enabled">Enable User Registration</label>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" name="guest_posting" class="form-check-input" id="guest_posting" <?php echo $guest_posting ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="guest_posting">Allow Guest Posting</label>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">Save Settings</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="backup.php" class="btn btn-warning">
                                        <i class="fas fa-database me-2"></i>Backup Database
                                    </a>
                                    <a href="logs.php" class="btn btn-info">
                                        <i class="fas fa-list-alt me-2"></i>View System Logs
                                    </a>
                                    <a href="../index.php" class="btn btn-outline-primary">
                                        <i class="fas fa-home me-2"></i>View Site
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">System Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <strong>PHP Version:</strong> <?php echo PHP_VERSION; ?>
                                </div>
                                <div class="mb-2">
                                    <strong>Server:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?>
                                </div>
                                <div class="mb-2">
                                    <strong>Database:</strong> <?php echo DB_NAME; ?>
                                </div>
                                <div class="mb-2">
                                    <strong>Upload Max:</strong> <?php echo ini_get('upload_max_filesize'); ?>
                                </div>
                                <div class="mb-2">
                                    <strong>Post Max:</strong> <?php echo ini_get('post_max_size'); ?>
                                </div>
                                <div class="mb-2">
                                    <strong>Memory Limit:</strong> <?php echo ini_get('memory_limit'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="../js/main.js"></script>
</body>
</html>
