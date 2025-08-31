<?php
require_once '../config.php';

// Check if user is admin
if (!isAdmin()) {
    redirect('../login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

$pdo = getDBConnection();
$csrf_token = generateCSRFToken();

// Initialize variables with safe defaults
$total_users = 0;
$total_threads = 0;
$total_posts = 0;
$errors = [];

// Get basic statistics with error handling
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM users");
    $stmt->execute();
    $total_users = $stmt->fetch()['total'];
} catch (Exception $e) {
    $errors[] = "Users count error: " . $e->getMessage();
}

try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM threads");
    $stmt->execute();
    $total_threads = $stmt->fetchColumn();
} catch (Exception $e) {
    $errors[] = "Threads count error: " . $e->getMessage();
}

try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM posts");
    $stmt->execute();
    $total_posts = $stmt->fetchColumn();
} catch (Exception $e) {
    $errors[] = "Posts count error: " . $e->getMessage();
}

// Get recent users (simplified)
$recent_users = [];
try {
    $stmt = $pdo->prepare("SELECT username, rank, created_at FROM users ORDER BY created_at DESC LIMIT 5");
    $stmt->execute();
    $recent_users = $stmt->fetchAll();
} catch (Exception $e) {
    $errors[] = "Recent users error: " . $e->getMessage();
}

$system_status = count($errors) > 0 ? 'WARNING' : 'OK';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <link href="../css/themes.css" rel="stylesheet">
</head>
<body data-theme="light">
    <?php include '../includes/header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="index-simple.php">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="users.php">
                                <i class="fas fa-users me-2"></i>Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../index.php">
                                <i class="fas fa-home me-2"></i>View Site
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Admin Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.reload()">Refresh</button>
                        </div>
                    </div>
                </div>

                <?php if (count($errors) > 0): ?>
                    <div class="alert alert-warning">
                        <h6>Warnings:</h6>
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- System Status -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">System Status</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-<?php echo $system_status === 'OK' ? 'success' : 'warning'; ?> me-2">
                                        <?php echo $system_status; ?>
                                    </span>
                                    <span>System is running</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-users fa-3x text-primary mb-3"></i>
                                <h3 class="card-title"><?php echo number_format($total_users); ?></h3>
                                <p class="card-text">Total Users</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-comments fa-3x text-success mb-3"></i>
                                <h3 class="card-title"><?php echo number_format($total_threads); ?></h3>
                                <p class="card-text">Total Threads</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-reply fa-3x text-info mb-3"></i>
                                <h3 class="card-title"><?php echo number_format($total_posts); ?></h3>
                                <p class="card-text">Total Posts</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Users -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Recent Users</h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($recent_users)): ?>
                                    <p class="text-muted">No users found.</p>
                                <?php else: ?>
                                    <div class="list-group list-group-flush">
                                        <?php foreach ($recent_users as $user): ?>
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                                                    <span class="badge bg-secondary ms-2"><?php echo ucfirst($user['rank']); ?></span>
                                                </div>
                                                <small class="text-muted"><?php echo formatDate($user['created_at']); ?></small>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="users.php" class="btn btn-primary">
                                        <i class="fas fa-users me-2"></i>Manage Users
                                    </a>
                                    <a href="../downloads.php" class="btn btn-info">
                                        <i class="fas fa-download me-2"></i>Manage Downloads
                                    </a>
                                    <a href="../index.php" class="btn btn-outline-primary">
                                        <i class="fas fa-home me-2"></i>View Site
                                    </a>
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
