<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo generateCSRFToken(); ?>">
    <title><?php echo $page_title ?? 'Admin Panel - ' . SITE_NAME; ?></title>
    <meta name="description" content="<?php echo $page_subtitle ?? 'Administrative control panel'; ?>">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../css/style.css" rel="stylesheet">
    <link href="../css/themes.css" rel="stylesheet">
    
    <style>
        /* Admin-specific styles */
        .admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .admin-sidebar {
            background: #2c3e50;
            min-height: 100vh;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            z-index: 1000;
            transition: transform 0.3s ease;
        }
        
        .admin-sidebar.collapsed {
            transform: translateX(-250px);
        }
        
        .admin-main {
            margin-left: 250px;
            transition: margin-left 0.3s ease;
        }
        
        .admin-main.expanded {
            margin-left: 0;
        }
        
        .admin-nav {
            padding: 1rem 0;
        }
        
        .admin-nav .nav-link {
            color: #bdc3c7;
            padding: 0.75rem 1.5rem;
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
        }
        
        .admin-nav .nav-link:hover,
        .admin-nav .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
            border-left-color: #3498db;
        }
        
        .admin-nav .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        
        .admin-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            overflow: hidden;
        }
        
        .admin-card .card-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 1px solid #dee2e6;
            padding: 1.5rem;
        }
        
        .admin-card .card-header h3 {
            margin: 0;
            color: #2c3e50;
            font-size: 1.25rem;
            font-weight: 600;
        }
        
        .admin-card .card-body {
            padding: 1.5rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 25px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--accent-primary), var(--accent-secondary));
        }
        
        .stat-card-primary::before { background: linear-gradient(90deg, #007bff, #0056b3); }
        .stat-card-success::before { background: linear-gradient(90deg, #28a745, #1e7e34); }
        .stat-card-info::before { background: linear-gradient(90deg, #17a2b8, #117a8b); }
        .stat-card-warning::before { background: linear-gradient(90deg, #ffc107, #d39e00); }
        .stat-card-danger::before { background: linear-gradient(90deg, #dc3545, #bd2130); }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0.1) 100%);
        }
        
        .stat-card-primary .stat-icon { background: linear-gradient(135deg, #007bff, #0056b3); color: white; }
        .stat-card-success .stat-icon { background: linear-gradient(135deg, #28a745, #1e7e34); color: white; }
        .stat-card-info .stat-icon { background: linear-gradient(135deg, #17a2b8, #117a8b); color: white; }
        .stat-card-warning .stat-icon { background: linear-gradient(135deg, #ffc107, #d39e00); color: white; }
        .stat-card-danger .stat-icon { background: linear-gradient(135deg, #dc3545, #bd2130); color: white; }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 1rem;
        }
        
        .stat-trend {
            font-size: 0.8rem;
            color: #28a745;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .admin-dashboard-header {
            background: white;
            padding: 2rem 0;
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 2rem;
        }
        
        .admin-title {
            color: #2c3e50;
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
        }
        
        .admin-subtitle {
            color: #6c757d;
            font-size: 1.1rem;
            margin: 0.5rem 0 0 0;
        }
        
        .admin-actions {
            display: flex;
            gap: 1rem;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .section-header h2 {
            margin: 0;
            color: #2c3e50;
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .section-header h3 {
            margin: 0;
            color: #2c3e50;
            font-size: 1.25rem;
            font-weight: 600;
        }
        
        .events-timeline {
            position: relative;
        }
        
        .event-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            background: #f8f9fa;
            border-left: 4px solid #dee2e6;
        }
        
        .event-item.event-error { border-left-color: #dc3545; }
        .event-item.event-warning { border-left-color: #ffc107; }
        .event-item.event-info { border-left-color: #17a2b8; }
        .event-item.event-debug { border-left-color: #6c757d; }
        
        .event-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e9ecef;
            color: #6c757d;
            flex-shrink: 0;
        }
        
        .event-content {
            flex: 1;
        }
        
        .event-title {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        
        .event-details {
            color: #6c757d;
            margin-bottom: 0.5rem;
        }
        
        .event-meta {
            display: flex;
            gap: 1rem;
            font-size: 0.8rem;
        }
        
        .event-level {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-weight: 500;
        }
        
        .level-error { background: #f8d7da; color: #721c24; }
        .level-warning { background: #fff3cd; color: #856404; }
        .level-info { background: #d1ecf1; color: #0c5460; }
        .level-debug { background: #e2e3e5; color: #383d41; }
        
        .users-table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .rank-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .rank-admin { background: #dc3545; color: white; }
        .rank-moderator { background: #ffc107; color: #212529; }
        .rank-user { background: #6c757d; color: white; }
        .rank-premium { background: #fd7e14; color: white; }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .reports-list {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .report-item {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid #ffc107;
        }
        
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        
        .report-type {
            font-weight: 600;
            color: #2c3e50;
        }
        
        .report-time {
            font-size: 0.8rem;
            color: #6c757d;
        }
        
        .report-content {
            margin-bottom: 1rem;
        }
        
        .report-reason {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.25rem;
        }
        
        .report-details {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .report-meta {
            display: flex;
            gap: 1rem;
            font-size: 0.8rem;
            color: #6c757d;
            margin-bottom: 1rem;
        }
        
        .report-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .quick-actions-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        
        .action-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 8px;
            text-decoration: none;
            color: #2c3e50;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .action-item:hover {
            background: #e9ecef;
            border-color: #007bff;
            transform: translateY(-2px);
            text-decoration: none;
            color: #2c3e50;
        }
        
        .action-item i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: #007bff;
        }
        
        .status-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #dee2e6;
        }
        
        .status-item:last-child {
            border-bottom: none;
        }
        
        .status-label {
            font-weight: 500;
            color: #2c3e50;
        }
        
        .status-value {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #6c757d;
        }
        
        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }
        
        .status-online { background: #28a745; }
        .status-offline { background: #dc3545; }
        .status-warning { background: #ffc107; }
        
        .no-events, .no-reports {
            text-align: center;
            padding: 2rem;
            color: #6c757d;
        }
        
        .no-events i, .no-reports i {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-250px);
            }
            
            .admin-main {
                margin-left: 0;
            }
            
            .admin-sidebar.show {
                transform: translateX(0);
            }
            
            .admin-main.collapsed {
                margin-left: 0;
            }
            
            .quick-actions-grid {
                grid-template-columns: 1fr;
            }
            
            .admin-actions {
                flex-direction: column;
            }
        }
        
        /* Toggle button for mobile */
        .sidebar-toggle {
            display: none;
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 1001;
            background: #2c3e50;
            border: none;
            color: white;
            padding: 0.5rem;
            border-radius: 4px;
        }
        
        @media (max-width: 768px) {
            .sidebar-toggle {
                display: block;
            }
        }

        /* Timeline Component */
        .timeline {
            position: relative;
            padding-left: 2rem;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            left: 0.75rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--bs-border-color);
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .timeline-marker {
            position: absolute;
            left: -1.5rem;
            top: 0.25rem;
            width: 1rem;
            height: 1rem;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 0 0 2px var(--bs-border-color);
        }
        
        .timeline-content {
            background: var(--bs-light);
            padding: 1rem;
            border-radius: 0.5rem;
            border: 1px solid var(--bs-border-color);
        }
        
        .timeline-content h6 {
            margin-bottom: 0.5rem;
            color: var(--bs-dark);
        }
        
        .timeline-content p {
            margin-bottom: 0.5rem;
            color: var(--bs-secondary);
        }
        
        .timeline-content small {
            color: var(--bs-muted);
        }
        
        /* Enhanced Cards */
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: all 0.15s ease-in-out;
        }
        
        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--bs-primary), var(--bs-info));
            color: white;
            border-bottom: none;
            font-weight: 600;
        }
        
        /* Enhanced Buttons */
        .btn {
            transition: all 0.15s ease-in-out;
            border-radius: 0.5rem;
        }
        
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.15);
        }
        
        /* Stats Cards */
        .stat-card {
            background: linear-gradient(135deg, var(--bs-primary), var(--bs-info));
            color: white;
            border-radius: 1rem;
            padding: 1.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
        
        .stat-card .stat-content {
            position: relative;
            z-index: 1;
        }
        
        .stat-card .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .stat-card .stat-label {
            font-size: 0.875rem;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .timeline {
                padding-left: 1rem;
            }
            
            .timeline-marker {
                left: -1rem;
            }
            
            .stat-card .stat-number {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body data-theme="light">
    <!-- Mobile sidebar toggle -->
    <button class="sidebar-toggle" id="sidebar-toggle">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Admin Sidebar -->
    <nav class="admin-sidebar" id="admin-sidebar">
        <div class="admin-nav">
            <div class="text-center mb-4">
                <h4><i class="fas fa-shield-alt text-primary"></i> Admin Panel</h4>
                <p class="small text-muted"><?php echo SITE_NAME; ?></p>
            </div>
            
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>" href="index.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'users.php' ? 'active' : ''; ?>" href="users.php">
                        <i class="fas fa-users"></i> User Management
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'forums.php' ? 'active' : ''; ?>" href="forums.php">
                        <i class="fas fa-comments"></i> Forum Management
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'reports.php' ? 'active' : ''; ?>" href="reports.php">
                        <i class="fas fa-flag"></i> Reports
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'moderation.php' ? 'active' : ''; ?>" href="moderation.php">
                        <i class="fas fa-gavel"></i> Moderation
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'analytics.php' ? 'active' : ''; ?>" href="analytics.php">
                        <i class="fas fa-chart-line"></i> Analytics
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'settings.php' ? 'active' : ''; ?>" href="settings.php">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'backup.php' ? 'active' : ''; ?>" href="backup.php">
                        <i class="fas fa-database"></i> Backup
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'logs.php' ? 'active' : ''; ?>" href="logs.php">
                        <i class="fas fa-file-alt"></i> System Logs
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'maintenance.php' ? 'active' : ''; ?>" href="maintenance.php">
                        <i class="fas fa-tools"></i> Maintenance
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../index.php">
                        <i class="fas fa-home"></i> Back to Site
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="admin-main" id="admin-main">
        <!-- Admin Header -->
        <header class="admin-header">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col">
                        <h1 class="admin-title">
                            <?php echo $page_title ?? 'Admin Panel'; ?>
                        </h1>
                        <?php if (isset($page_subtitle)): ?>
                            <p class="admin-subtitle"><?php echo $page_subtitle; ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="col-auto">
                        <?php if (isset($page_actions)): ?>
                            <div class="admin-actions">
                                <?php echo $page_actions; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Breadcrumbs -->
        <?php if (isset($breadcrumbs) && !empty($breadcrumbs)): ?>
            <nav aria-label="breadcrumb" class="container mt-3">
                <ol class="breadcrumb">
                    <?php foreach ($breadcrumbs as $index => $breadcrumb): ?>
                        <?php if ($breadcrumb['url']): ?>
                            <li class="breadcrumb-item">
                                <a href="<?php echo htmlspecialchars($breadcrumb['url']); ?>">
                                    <?php echo htmlspecialchars($breadcrumb['text']); ?>
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="breadcrumb-item active" aria-current="page">
                                <?php echo htmlspecialchars($breadcrumb['text']); ?>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ol>
            </nav>
        <?php endif; ?>
        
        <!-- Page Content -->
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
