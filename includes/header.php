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
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/themes.css">
    
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
    <meta name="theme-color" content="#3b82f6">
    
    <script>
        console.log('🚀 Header script loaded');
        console.log('Document ready state:', document.readyState);
        
        // Check if we can access basic JavaScript
        window.testFunction = function() {
            console.log('Test function called from header');
            return 'Header test function works!';
        };
        
        // Check JavaScript status after a delay
        setTimeout(() => {
            const statusElement = document.getElementById('jsStatus');
            if (statusElement) {
                let status = 'Basic JS: OK';
                if (typeof switchTheme === 'function') status += ' | switchTheme: OK';
                else status += ' | switchTheme: MISSING';
                if (typeof selectDevice === 'function') status += ' | selectDevice: OK';
                else status += ' | selectDevice: MISSING';
                if (window.themeManager) status += ' | ThemeManager: OK';
                else status += ' | ThemeManager: MISSING';
                
                statusElement.textContent = 'Status: ' + status;
                console.log('JavaScript status:', status);
                
                // Test the functions if they exist
                if (typeof switchTheme === 'function') {
                    console.log('✅ switchTheme function found, testing...');
                    try {
                        switchTheme('dark');
                        console.log('✅ switchTheme test successful');
                    } catch (e) {
                        console.error('❌ switchTheme test failed:', e);
                    }
                }
                
                if (typeof selectDevice === 'function') {
                    console.log('✅ selectDevice function found, testing...');
                    try {
                        selectDevice('tablet');
                        console.log('✅ selectDevice test successful');
                    } catch (e) {
                        console.error('❌ selectDevice test failed:', e);
                    }
                }
            }
        }, 2000);
    </script>
    
    <!-- Test script directly in header -->
    
</head>
<body data-theme="<?php echo isset($_COOKIE['selectedTheme']) ? $_COOKIE['selectedTheme'] : 'light'; ?>" data-device="<?php echo isset($_COOKIE['selectedDevice']) ? $_COOKIE['selectedDevice'] : 'desktop'; ?>">
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-premium">
        <div class="container">
            <a class="navbar-brand text-gradient fw-bold" href="index.php">
                <i class="fas fa-users me-2"></i>Affinity Forum
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-home me-1"></i>Home
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
                
                <div class="navbar-nav ms-auto">
                    <div class="nav-item dropdown">
                        <button class="btn btn-outline-light dropdown-toggle" type="button" id="themeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            🎨 Theme
                        </button>
                        <ul class="dropdown-menu dropdown-menu-premium" aria-labelledby="themeDropdown">
                            <li><button class="dropdown-item" onclick="console.log('Light theme clicked'); switchTheme('light')">☀️ Light</button></li>
                            <li><button class="dropdown-item" onclick="console.log('Dark theme clicked'); switchTheme('dark')">🌙 Dark</button></li>
                            <li><button class="dropdown-item" onclick="console.log('CS2 theme clicked'); switchTheme('cs2')">🎮 CS2 Gaming</button></li>
                            <li><button class="dropdown-item" onclick="console.log('Premium theme clicked'); switchTheme('premium')">💎 Premium</button></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><button class="dropdown-item" onclick="console.log('Developer mode clicked'); enableDeveloperMode()">🔧 Developer Mode</button></li>
                        </ul>
                    </div>
                    
                    <button class="btn btn-outline-light ms-2" onclick="console.log('Device button clicked'); showDeviceOverlay();">
                        📱 Device
                    </button>
                    
                    <button class="btn btn-outline-light ms-2" onclick="console.log('Shortcuts button clicked'); showShortcutsHelp();">
                        ⌨️ Shortcuts
                    </button>
                    
                    <a href="theme-control-panel.php" class="btn btn-gradient ms-2">
                        🎛️ Control Panel
                    </a>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="profile.php" class="btn btn-outline-light ms-2">👤 Profile</a>
                        <a href="logout.php" class="btn btn-outline-light ms-2">🚪 Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline-light ms-2">🔑 Login</a>
                        <a href="register.php" class="btn btn-outline-light ms-2">📝 Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Device Overlay -->
    <div id="deviceOverlay" class="device-overlay">
        <div class="device-content">
            <h3>Select Device Type</h3>
            <div class="device-options">
                <div class="device-option premium-card" data-device="desktop" onclick="console.log('Desktop clicked'); selectDevice('desktop');">
                    <div class="device-icon">🖥️</div>
                    <div class="device-info">
                        <h4>Desktop</h4>
                        <p>Full desktop experience</p>
                    </div>
                </div>
                <div class="device-option premium-card" data-device="tablet" onclick="console.log('Tablet clicked'); selectDevice('tablet');">
                    <div class="device-icon">📱</div>
                    <div class="device-info">
                        <h4>Tablet</h4>
                        <p>Optimized for tablet devices</p>
                    </div>
                </div>
                <div class="device-option premium-card" data-device="mobile" onclick="console.log('Mobile clicked'); selectDevice('mobile');">
                    <div class="device-icon">📱</div>
                    <div class="device-info">
                        <h4>Mobile</h4>
                        <p>Mobile-optimized interface</p>
                    </div>
                </div>
            </div>
            <button class="btn btn-secondary" onclick="console.log('Close clicked'); document.getElementById('deviceOverlay').style.display='none'">Close</button>
        </div>
    </div>

    <!-- Shortcuts Help Modal -->
    <div id="shortcutsModal" class="shortcuts-overlay" style="display: none;">
        <div class="shortcuts-help">
            <button class="close-btn" onclick="hideShortcutsHelp()">×</button>
            <h3>⌨️ Keyboard Shortcuts</h3>
            <div class="shortcut">
                <span class="key">Ctrl + T</span>
                <span class="description">Toggle theme (cycle through themes)</span>
            </div>
            <div class="shortcut">
                <span class="key">Ctrl + D</span>
                <span class="description">Toggle device mode</span>
            </div>
            <div class="shortcut">
                <span class="key">Ctrl + H</span>
                <span class="description">Toggle high contrast mode</span>
            </div>
            <div class="shortcut">
                <span class="key">Ctrl + M</span>
                <span class="description">Toggle reduced motion</span>
            </div>
            <div class="shortcut">
                <span class="key">Ctrl + F</span>
                <span class="description">Toggle font size</span>
            </div>
            <div class="shortcut">
                <span class="key">Ctrl + P</span>
                <span class="description">Toggle particle effects</span>
            </div>
            <div class="shortcut">
                <span class="key">Ctrl + A</span>
                <span class="description">Toggle audio feedback</span>
            </div>
            <div class="shortcut">
                <span class="key">Ctrl + S</span>
                <span class="description">Export settings</span>
            </div>
            <div class="shortcut">
                <span class="key">Ctrl + I</span>
                <span class="description">Import settings</span>
            </div>
            <div class="shortcut">
                <span class="key">Ctrl + R</span>
                <span class="description">Reset to defaults</span>
            </div>
            <div class="shortcut">
                <span class="key">F1</span>
                <span class="description">Show this help</span>
            </div>
        </div>
    </div>

    <!-- Notification Container -->
    <div id="notificationContainer"></div>

    <!-- Performance Indicator -->
    <div id="performanceIndicator" class="performance-indicator" style="display: none;">
        <div class="fps">FPS: <span id="currentFps">--</span></div>
        <div class="memory">Memory: <span id="currentMemory">--</span></div>
        <div class="theme">Theme: <span id="currentTheme">--</span></div>
        <div class="device">Device: <span id="currentDevice">--</span></div>
    </div>

    <!-- Main Content Container -->
    <main class="container-fluid py-4">
        <div class="row">
            <div class="col-12">

                
                <!-- Page Header -->
                <?php if (isset($page_title)): ?>
                    <div class="page-header premium-card p-4 mb-4 text-center">
                        <h1 class="text-gradient mb-0">
                            <i class="fas fa-star me-2"></i><?php echo htmlspecialchars($page_title); ?>
                        </h1>
                    </div>
                <?php endif; ?>
