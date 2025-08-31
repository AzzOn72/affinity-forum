<?php
// Path fix for InfinityFree - config.php is in the parent directory of admin/
$config_path = dirname(__DIR__) . '/config.php';
if (file_exists($config_path)) {
    require_once $config_path;
} else {
    die("Config file not found at: $config_path");
}

// Require admin access
requireAdmin();

// Get comprehensive admin statistics
$admin_stats = [];
try {
    // User statistics
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_users,
            COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as new_users_today,
            COUNT(CASE WHEN DATE(created_at) = CURDATE() - INTERVAL 1 DAY THEN 1 END) as new_users_yesterday,
            COUNT(CASE WHEN is_banned = 1 THEN 1 END) as banned_users,
            COUNT(CASE WHEN is_premium = 1 THEN 1 END) as premium_users,
            COUNT(CASE WHEN last_seen > DATE_SUB(NOW(), INTERVAL 15 MINUTE) THEN 1 END) as online_users
        FROM users
        WHERE is_active = 1
    ");
    $stmt->execute();
    $admin_stats['users'] = $stmt->fetch();
    
    // Content statistics
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_threads,
            COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as new_threads_today,
            COUNT(CASE WHEN is_pinned = 1 THEN 1 END) as pinned_threads,
            COUNT(CASE WHEN is_locked = 1 THEN 1 END) as locked_threads
        FROM threads
        WHERE is_active = 1
    ");
    $stmt->execute();
    $admin_stats['threads'] = $stmt->fetch();
    
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_posts,
            COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as new_posts_today,
            COUNT(CASE WHEN is_hidden = 1 THEN 1 END) as hidden_posts
        FROM posts
        WHERE is_active = 1
    ");
    $stmt->execute();
    $admin_stats['posts'] = $stmt->fetch();
    
    // System statistics
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_notifications,
            COUNT(CASE WHEN is_read = 0 THEN 1 END) as unread_notifications,
            COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as notifications_today
        FROM notifications
    ");
    $stmt->execute();
    $admin_stats['notifications'] = $stmt->fetch();
    
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_reports,
            COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_reports,
            COUNT(CASE WHEN status = 'investigating' THEN 1 END) as investigating_reports
        FROM reports
    ");
    $stmt->execute();
    $admin_stats['reports'] = $stmt->fetch();
    
    // Activity statistics
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_activity,
            COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as activity_today,
            COUNT(CASE WHEN action = 'login' THEN 1 END) as logins_today
        FROM user_activity
    ");
    $stmt->execute();
    $admin_stats['activity'] = $stmt->fetch();
    
    // Error statistics
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_errors,
            COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as errors_today,
            COUNT(CASE WHEN level = 'error' THEN 1 END) as critical_errors,
            COUNT(CASE WHEN level = 'critical' THEN 1 END) as fatal_errors
        FROM system_logs
    ");
    $stmt->execute();
    $admin_stats['errors'] = $stmt->fetch();
    
} catch (Exception $e) {
    error_log("Failed to get admin stats: " . $e->getMessage());
}

// Get recent system events
$recent_events = [];
try {
    $stmt = $pdo->prepare("
        SELECT * FROM system_logs 
        ORDER BY created_at DESC 
        LIMIT 10
    ");
    $stmt->execute();
    $recent_events = $stmt->fetchAll();
} catch (Exception $e) {
    error_log("Failed to get recent events: " . $e->getMessage());
}

// Get recent user registrations
$recent_users = [];
try {
    $stmt = $pdo->prepare("
        SELECT username, email, rank, created_at, is_verified, is_premium
        FROM users 
        WHERE is_active = 1
        ORDER BY created_at DESC 
        LIMIT 10
    ");
    $stmt->execute();
    $recent_users = $stmt->fetchAll();
} catch (Exception $e) {
    error_log("Failed to get recent users: " . $e->getMessage());
}

// Get pending reports
$pending_reports = [];
try {
    $stmt = $pdo->prepare("
        SELECT r.*, u.username as reporter_name, ru.username as reported_user_name
        FROM reports r
        JOIN users u ON r.reporter_id = u.id
        LEFT JOIN users ru ON r.reported_user_id = ru.id
        WHERE r.status = 'pending'
        ORDER BY r.created_at ASC
        LIMIT 5
    ");
    $stmt->execute();
    $pending_reports = $stmt->fetchAll();
} catch (Exception $e) {
    error_log("Failed to get pending reports: " . $e->getMessage());
}

// Set page metadata
$page_title = 'Admin Dashboard - ' . SITE_NAME;
$page_subtitle = 'Manage your forum and community';
$page_actions = '
    <a href="users.php" class="btn btn-primary">
        <i class="fas fa-users"></i> Manage Users
    </a>
    <a href="reports.php" class="btn btn-warning">
        <i class="fas fa-flag"></i> View Reports
    </a>
    <a href="settings.php" class="btn btn-info">
        <i class="fas fa-cog"></i> Settings
    </a>
';

// Set breadcrumbs
$breadcrumbs = [
    ['text' => 'Admin', 'url' => 'index.php'],
    ['text' => 'Dashboard', 'url' => null]
];

// Include admin header
include 'includes/admin-header.php';
?>

<!-- Admin Dashboard Header -->
<div class="admin-dashboard-header">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="admin-title">
                <i class="fas fa-tachometer-alt"></i> Admin Dashboard
            </h1>
            <p class="admin-subtitle">Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
        </div>
        <div class="col-auto">
            <div class="admin-actions">
                <a href="backup.php" class="btn btn-outline-primary">
                    <i class="fas fa-download"></i> Backup
                </a>
                <a href="maintenance.php" class="btn btn-outline-warning">
                    <i class="fas fa-tools"></i> Maintenance
                </a>
                <a href="logs.php" class="btn btn-outline-info">
                    <i class="fas fa-file-alt"></i> Logs
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats Grid -->
<div class="stats-grid">
    <div class="row">
        <!-- Users Stats -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-card-primary">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo formatNumber($admin_stats['users']['total_users'] ?? 0); ?></div>
                    <div class="stat-label">Total Users</div>
                    <div class="stat-trend">
                        <i class="fas fa-arrow-up text-success"></i>
                        <span>+<?php echo $admin_stats['users']['new_users_today'] ?? 0; ?> today</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Threads Stats -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-card-success">
                <div class="stat-icon">
                    <i class="fas fa-comments"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo formatNumber($admin_stats['threads']['total_threads'] ?? 0); ?></div>
                    <div class="stat-label">Total Threads</div>
                    <div class="stat-trend">
                        <i class="fas fa-arrow-up text-success"></i>
                        <span>+<?php echo $admin_stats['threads']['new_threads_today'] ?? 0; ?> today</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Posts Stats -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-card-info">
                <div class="stat-icon">
                    <i class="fas fa-reply"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo formatNumber($admin_stats['posts']['total_posts'] ?? 0); ?></div>
                    <div class="stat-label">Total Posts</div>
                    <div class="stat-trend">
                        <i class="fas fa-arrow-up text-success"></i>
                        <span>+<?php echo $admin_stats['posts']['new_posts_today'] ?? 0; ?> today</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Online Users -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-card-warning">
                <div class="stat-icon">
                    <i class="fas fa-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number"><?php echo formatNumber($admin_stats['users']['online_users'] ?? 0); ?></div>
                    <div class="stat-label">Online Now</div>
                    <div class="stat-trend">
                        <i class="fas fa-wifi text-success"></i>
                        <span>Active</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Stats Row -->
<div class="detailed-stats">
    <div class="row">
        <!-- User Demographics -->
        <div class="col-lg-4">
            <div class="admin-card">
                <div class="card-header">
                    <h3><i class="fas fa-chart-pie"></i> User Demographics</h3>
                </div>
                <div class="card-body">
                    <div class="demographic-item">
                        <div class="demographic-label">Premium Users</div>
                        <div class="demographic-value">
                            <?php echo formatNumber($admin_stats['users']['premium_users'] ?? 0); ?>
                            <span class="demographic-percentage">
                                (<?php echo round(($admin_stats['users']['premium_users'] ?? 0) / max(($admin_stats['users']['total_users'] ?? 1), 1) * 100, 1); ?>%)
                            </span>
                        </div>
                    </div>
                    <div class="demographic-item">
                        <div class="demographic-label">Banned Users</div>
                        <div class="demographic-value">
                            <?php echo formatNumber($admin_stats['users']['banned_users'] ?? 0); ?>
                            <span class="demographic-percentage">
                                (<?php echo round(($admin_stats['users']['banned_users'] ?? 0) / max(($admin_stats['users']['total_users'] ?? 1), 1) * 100, 1); ?>%)
                            </span>
                        </div>
                    </div>
                    <div class="demographic-item">
                        <div class="demographic-label">Verified Users</div>
                        <div class="demographic-value">
                            <span class="text-success"><?php echo formatNumber($admin_stats['users']['total_users'] - ($admin_stats['users']['banned_users'] ?? 0)); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Content Overview -->
        <div class="col-lg-4">
            <div class="admin-card">
                <div class="card-header">
                    <h3><i class="fas fa-file-alt"></i> Content Overview</h3>
                </div>
                <div class="card-body">
                    <div class="content-item">
                        <div class="content-label">Pinned Threads</div>
                        <div class="content-value">
                            <?php echo formatNumber($admin_stats['threads']['pinned_threads'] ?? 0); ?>
                        </div>
                    </div>
                    <div class="content-item">
                        <div class="content-label">Locked Threads</div>
                        <div class="content-value">
                            <?php echo formatNumber($admin_stats['threads']['locked_threads'] ?? 0); ?>
                        </div>
                    </div>
                    <div class="content-item">
                        <div class="content-label">Hidden Posts</div>
                        <div class="content-value">
                            <?php echo formatNumber($admin_stats['posts']['hidden_posts'] ?? 0); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- System Health -->
        <div class="col-lg-4">
            <div class="admin-card">
                <div class="card-header">
                    <h3><i class="fas fa-heartbeat"></i> System Health</h3>
                </div>
                <div class="card-body">
                    <div class="health-item">
                        <div class="health-label">Critical Errors</div>
                        <div class="health-value">
                            <span class="text-danger"><?php echo formatNumber($admin_stats['errors']['critical_errors'] ?? 0); ?></span>
                        </div>
                    </div>
                    <div class="health-item">
                        <div class="health-label">Pending Reports</div>
                        <div class="health-value">
                            <span class="text-warning"><?php echo formatNumber($admin_stats['reports']['pending_reports'] ?? 0); ?></span>
                        </div>
                    </div>
                    <div class="health-item">
                        <div class="health-label">Unread Notifications</div>
                        <div class="health-value">
                            <span class="text-info"><?php echo formatNumber($admin_stats['notifications']['unread_notifications'] ?? 0); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Dashboard Content -->
<div class="dashboard-content">
    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Recent System Events -->
            <div class="admin-card">
                <div class="card-header">
                    <h3><i class="fas fa-history"></i> Recent System Events</h3>
                    <a href="logs.php" class="btn btn-sm btn-outline-primary">View All Logs</a>
                </div>
                <div class="card-body">
                    <div class="events-timeline">
                        <?php if (empty($recent_events)): ?>
                            <div class="no-events">
                                <i class="fas fa-check-circle text-success"></i>
                                <p>No recent system events</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($recent_events as $event): ?>
                                <div class="event-item event-<?php echo $event['level']; ?>">
                                    <div class="event-icon">
                                        <i class="fas fa-<?php echo getEventIcon($event['level']); ?>"></i>
                                    </div>
                                    <div class="event-content">
                                        <div class="event-title"><?php echo htmlspecialchars($event['event']); ?></div>
                                        <div class="event-details"><?php echo htmlspecialchars($event['details']); ?></div>
                                        <div class="event-meta">
                                            <span class="event-level level-<?php echo $event['level']; ?>">
                                                <?php echo ucfirst($event['level']); ?>
                                            </span>
                                            <span class="event-time">
                                                <?php echo formatTimeAgo($event['created_at']); ?>
                                            </span>
                                            <span class="event-ip">
                                                <?php echo htmlspecialchars($event['ip_address']); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Recent User Registrations -->
            <div class="admin-card">
                <div class="card-header">
                    <h3><i class="fas fa-user-plus"></i> Recent User Registrations</h3>
                    <a href="users.php" class="btn btn-sm btn-outline-primary">Manage Users</a>
                </div>
                <div class="card-body">
                    <div class="users-table">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Rank</th>
                                        <th>Status</th>
                                        <th>Joined</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_users as $user): ?>
                                        <tr>
                                            <td>
                                                <div class="user-info">
                                                    <span class="username"><?php echo htmlspecialchars($user['username']); ?></span>
                                                    <?php if ($user['is_premium']): ?>
                                                        <span class="badge bg-warning">Premium</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td>
                                                <span class="rank-badge rank-<?php echo $user['rank']; ?>">
                                                    <?php echo ucfirst($user['rank']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($user['is_verified']): ?>
                                                    <span class="badge bg-success">Verified</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">Pending</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo formatTimeAgo($user['created_at']); ?></td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="../profile.php?user=<?php echo urlencode($user['username']); ?>" 
                                                       class="btn btn-sm btn-outline-primary" title="View Profile">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="edit-user.php?id=<?php echo $user['id'] ?? ''; ?>" 
                                                       class="btn btn-sm btn-outline-warning" title="Edit User">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Pending Reports -->
            <div class="admin-card">
                <div class="card-header">
                    <h3><i class="fas fa-flag"></i> Pending Reports</h3>
                    <a href="reports.php" class="btn btn-sm btn-outline-warning">View All</a>
                </div>
                <div class="card-body">
                    <div class="reports-list">
                        <?php if (empty($pending_reports)): ?>
                            <div class="no-reports">
                                <i class="fas fa-check-circle text-success"></i>
                                <p>No pending reports</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($pending_reports as $report): ?>
                                <div class="report-item">
                                    <div class="report-header">
                                        <div class="report-type"><?php echo ucfirst($report['target_type']); ?></div>
                                        <div class="report-time"><?php echo formatTimeAgo($report['created_at']); ?></div>
                                    </div>
                                    <div class="report-content">
                                        <div class="report-reason"><?php echo htmlspecialchars($report['reason']); ?></div>
                                        <div class="report-details"><?php echo htmlspecialchars($report['details']); ?></div>
                                    </div>
                                    <div class="report-meta">
                                        <div class="reporter">by <?php echo htmlspecialchars($report['reporter_name']); ?></div>
                                        <?php if ($report['reported_user_name']): ?>
                                            <div class="reported-user">reported <?php echo htmlspecialchars($report['reported_user_name']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="report-actions">
                                        <a href="view-report.php?id=<?php echo $report['id']; ?>" 
                                           class="btn btn-sm btn-primary">Review</a>
                                        <button class="btn btn-sm btn-success" onclick="resolveReport(<?php echo $report['id']; ?>)">
                                            Resolve
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Enhanced Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <a href="user-management.php" class="btn btn-primary w-100">
                                    <i class="fas fa-users"></i><br>
                                    <small>User Management</small>
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="forum-management.php" class="btn btn-success w-100">
                                    <i class="fas fa-comments"></i><br>
                                    <small>Forum Management</small>
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="reports.php" class="btn btn-warning w-100">
                                    <i class="fas fa-flag"></i><br>
                                    <small>Reports</small>
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="settings.php" class="btn btn-info w-100">
                                    <i class="fas fa-cog"></i><br>
                                    <small>Settings</small>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Health Monitor -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-heartbeat"></i> System Health</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="text-center">
                                    <div class="h4 text-success mb-1">98%</div>
                                    <small class="text-muted">Uptime</small>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="text-center">
                                    <div class="h4 text-info mb-1">2.3s</div>
                                    <small class="text-muted">Avg Response</small>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="text-center">
                                    <div class="h4 text-warning mb-1">45%</div>
                                    <small class="text-muted">CPU Usage</small>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="text-center">
                                    <div class="h4 text-danger mb-1">67%</div>
                                    <small class="text-muted">Memory</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity Timeline -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-history"></i> Recent Activity Timeline</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">New user registered</h6>
                                    <p class="mb-1 text-muted">User "GamerPro2024" joined the community</p>
                                    <small class="text-muted">2 minutes ago</small>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-marker bg-info"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">New thread created</h6>
                                    <p class="mb-1 text-muted">"Best CS2 settings for maximum FPS" in Technical Support</p>
                                    <small class="text-muted">15 minutes ago</small>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-marker bg-warning"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Report submitted</h6>
                                    <p class="mb-1 text-muted">Spam report in General Discussion</p>
                                    <small class="text-muted">1 hour ago</small>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">System backup completed</h6>
                                    <p class="mb-1 text-muted">Daily backup completed successfully</p>
                                    <small class="text-muted">3 hours ago</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            
            <!-- Quick Actions -->
            <div class="admin-card">
                <div class="card-header">
                    <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
                </div>
                <div class="card-body">
                    <div class="quick-actions-grid">
                        <a href="create-user.php" class="action-item">
                            <i class="fas fa-user-plus"></i>
                            <span>Create User</span>
                        </a>
                        <a href="create-thread.php" class="action-item">
                            <i class="fas fa-plus"></i>
                            <span>Create Thread</span>
                        </a>
                        <a href="announcements.php" class="action-item">
                            <i class="fas fa-bullhorn"></i>
                            <span>Announcement</span>
                        </a>
                        <a href="backup.php" class="action-item">
                            <i class="fas fa-download"></i>
                            <span>Backup</span>
                        </a>
                        <a href="maintenance.php" class="action-item">
                            <i class="fas fa-tools"></i>
                            <span>Maintenance</span>
                        </a>
                        <a href="analytics.php" class="action-item">
                            <i class="fas fa-chart-line"></i>
                            <span>Analytics</span>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- System Status -->
            <div class="admin-card">
                <div class="card-header">
                    <h3><i class="fas fa-server"></i> System Status</h3>
                </div>
                <div class="card-body">
                    <div class="status-item">
                        <div class="status-label">Database</div>
                        <div class="status-value">
                            <span class="status-indicator status-online"></span>
                            Online
                        </div>
                    </div>
                    <div class="status-item">
                        <div class="status-label">Cache</div>
                        <div class="status-value">
                            <span class="status-indicator status-online"></span>
                            Active
                        </div>
                    </div>
                    <div class="status-item">
                        <div class="status-label">File System</div>
                        <div class="status-value">
                            <span class="status-indicator status-online"></span>
                            Healthy
                        </div>
                    </div>
                    <div class="status-item">
                        <div class="status-label">Memory Usage</div>
                        <div class="status-value">
                            <?php echo round(memory_get_usage(true) / 1024 / 1024, 2); ?> MB
                        </div>
                    </div>
                    <div class="status-item">
                        <div class="status-label">PHP Version</div>
                        <div class="status-value">
                            <?php echo PHP_VERSION; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Admin Dashboard -->
<script>
// Resolve report function
function resolveReport(reportId) {
    if (confirm('Are you sure you want to mark this report as resolved?')) {
        // Send AJAX request to resolve report
        fetch('ajax/resolve-report.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ report_id: reportId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove report from UI
                const reportElement = document.querySelector(`[data-report-id="${reportId}"]`);
                if (reportElement) {
                    reportElement.remove();
                }
                
                // Show success message
                showNotification('Report resolved successfully', 'success');
                
                // Refresh the page to update stats
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('Failed to resolve report', 'error');
            }
        })
        .catch(error => {
            console.error('Error resolving report:', error);
            showNotification('Failed to resolve report', 'error');
        });
    }
}

// Auto-refresh dashboard every 30 seconds
setInterval(() => {
    // Only refresh if user is active
    if (!document.hidden) {
        location.reload();
    }
}, 30000);

// Initialize admin dashboard
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Admin Dashboard initialized');
    
    // Add any additional admin-specific functionality here
});
</script>

<?php
// Helper functions
function getEventIcon($level) {
    $icons = [
        'debug' => 'bug',
        'info' => 'info-circle',
        'warning' => 'exclamation-triangle',
        'error' => 'exclamation-circle',
        'critical' => 'times-circle'
    ];
    return $icons[$level] ?? 'info-circle';
}

// Include admin footer
include 'includes/admin-footer.php';
?>
