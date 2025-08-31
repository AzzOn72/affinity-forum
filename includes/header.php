<?php
// Path fix for InfinityFree - config.php is in the parent directory of includes/
$config_path = dirname(__DIR__) . '/config.php';
if (file_exists($config_path)) {
    require_once $config_path;
} else {
    die("Config file not found at: $config_path");
}

// Get user theme preference
$user_theme = 'light'; // Default theme
if (isset($_SESSION['user_id'])) {
    try {
        $stmt = $pdo->prepare("SELECT theme FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user_data = $stmt->fetch();
        if ($user_data && $user_data['theme']) {
            $user_theme = $user_data['theme'];
        }
    } catch (Exception $e) {
        error_log("Failed to get user theme: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Affinity Forum</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    
    <!-- Meta tags -->
    <meta name="description" content="Affinity Forum - Connect, Share, and Grow Together">
    <meta name="keywords" content="forum, community, discussion, affinity">
    <meta name="author" content="Affinity Forum">
    
    <!-- Open Graph -->
    <meta property="og:title" content="Affinity Forum">
    <meta property="og:description" content="Connect, Share, and Grow Together">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo $_SERVER['REQUEST_URI']; ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    
    <!-- PWA -->
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#007bff">
</head>
<body data-theme="<?php echo $user_theme; ?>">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <!-- Brand -->
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-users"></i>
                Affinity
            </a>
            
            <!-- Mobile Toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Navigation Items -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-home me-1"></i>Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="forum.php">
                            <i class="fas fa-comments me-1"></i>Forums
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="members.php">
                            <i class="fas fa-users me-1"></i>Members
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="search.php">
                            <i class="fas fa-search me-1"></i>Search
                        </a>
                    </li>
                </ul>
                
                <!-- Right Side Navigation -->
                <ul class="navbar-nav">
                    <!-- Theme Toggle -->
                    <li class="nav-item">
                        <button class="btn btn-outline-primary theme-toggle-btn" onclick="openThemeSwitcher()" title="Change Theme">
                            <i class="fas fa-palette"></i>
                        </button>
                    </li>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- User Menu -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i>
                                <?php echo htmlspecialchars($_SESSION['username']); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="profile.php">
                                    <i class="fas fa-user me-2"></i>Profile
                                </a></li>
                                <li><a class="dropdown-item" href="settings.php">
                                    <i class="fas fa-cog me-2"></i>Settings
                                </a></li>
                                <li><a class="dropdown-item" href="notifications.php">
                                    <i class="fas fa-bell me-2"></i>Notifications
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <!-- Guest Menu -->
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary" href="register.php">
                                <i class="fas fa-user-plus me-1"></i>Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <main>
