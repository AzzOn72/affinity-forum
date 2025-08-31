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
    <link rel="stylesheet" href="css/themes-ultra-premium.css">
    <link rel="stylesheet" href="css/ultra-premium-components.css">
    
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
    
    <!-- Ultra Premium Scripts -->
    <script src="js/themes-ultra-premium.js"></script>
    <script src="js/ultra-premium-features.js"></script>
    <script src="js/performance-optimizer.js"></script>
    <script src="js/fun-facts.js"></script>
</head>
<body data-theme="<?php echo isset($_COOKIE['selectedTheme']) ? $_COOKIE['selectedTheme'] : 'light'; ?>" data-device="<?php echo isset($_COOKIE['selectedDevice']) ? $_COOKIE['selectedDevice'] : 'desktop'; ?>">
    <!-- Ultra Premium Navigation Bar -->
    <nav class="navbar navbar-expand-lg ultra-premium-navbar">
        <div class="container">
            <a class="navbar-brand ultra-premium-brand" href="index.php">
                <div class="brand-icon">
                    <i class="fas fa-shield-alt"></i>
                    <div class="brand-glow"></div>
                </div>
                <div class="brand-text">
                    <span class="brand-name">Affinity</span>
                    <span class="brand-tagline">Elite CS2 Community</span>
                </div>
            </a>
            
            <button class="navbar-toggler ultra-premium-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="toggler-line"></span>
                <span class="toggler-line"></span>
                <span class="toggler-line"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto ultra-premium-nav">
                    <li class="nav-item">
                        <a class="nav-link ultra-premium-nav-link" href="index.php">
                            <div class="nav-link-content">
                                <i class="fas fa-home"></i>
                                <span>Home</span>
                                <div class="nav-link-glow"></div>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link ultra-premium-nav-link" href="forum.php">
                            <div class="nav-link-content">
                                <i class="fas fa-comments"></i>
                                <span>Forums</span>
                                <div class="nav-link-glow"></div>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link ultra-premium-nav-link" href="downloads.php">
                            <div class="nav-link-content">
                                <i class="fas fa-download"></i>
                                <span>Download</span>
                                <div class="nav-link-glow"></div>
                                <div class="nav-badge">NEW</div>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link ultra-premium-nav-link" href="members.php">
                            <div class="nav-link-content">
                                <i class="fas fa-users"></i>
                                <span>Members</span>
                                <div class="nav-link-glow"></div>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link ultra-premium-nav-link" href="search.php">
                            <div class="nav-link-content">
                                <i class="fas fa-search"></i>
                                <span>Search</span>
                                <div class="nav-link-glow"></div>
                            </div>
                        </a>
                    </li>
                </ul>
                
                <div class="navbar-nav ms-auto ultra-premium-nav-controls">
                    <!-- Theme Selector -->
                    <div class="nav-item dropdown">
                        <button class="btn ultra-premium-nav-btn dropdown-toggle" type="button" id="themeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="btn-content">
                                <i class="fas fa-palette"></i>
                                <span>Theme</span>
                                <div class="btn-glow"></div>
                            </div>
                        </button>
                        <ul class="dropdown-menu ultra-premium-dropdown" aria-labelledby="themeDropdown">
                            <li><button class="dropdown-item ultra-premium-dropdown-item" onclick="switchTheme('light')">
                                <i class="fas fa-sun"></i> Light Mode
                            </button></li>
                            <li><button class="dropdown-item ultra-premium-dropdown-item" onclick="switchTheme('dark')">
                                <i class="fas fa-moon"></i> Dark Mode
                            </button></li>
                            <li><button class="dropdown-item ultra-premium-dropdown-item" onclick="switchTheme('cs2')">
                                <i class="fas fa-gamepad"></i> CS2 Gaming
                            </button></li>
                            <li><button class="dropdown-item ultra-premium-dropdown-item" onclick="switchTheme('premium')">
                                <i class="fas fa-crown"></i> Premium Luxury
                            </button></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><button class="dropdown-item ultra-premium-dropdown-item" onclick="enableDeveloperMode()">
                                <i class="fas fa-code"></i> Developer Mode
                            </button></li>
                        </ul>
                    </div>
                    
                    <!-- Device Selector -->
                    <button class="btn ultra-premium-nav-btn ms-2" onclick="showDeviceOverlay();">
                        <div class="btn-content">
                            <i class="fas fa-mobile-alt"></i>
                            <span>Device</span>
                            <div class="btn-glow"></div>
                        </div>
                    </button>
                    
                    <!-- Shortcuts Help -->
                    <button class="btn ultra-premium-nav-btn ms-2" onclick="showShortcutsHelp();">
                        <div class="btn-content">
                            <i class="fas fa-keyboard"></i>
                            <span>Shortcuts</span>
                            <div class="btn-glow"></div>
                        </div>
                    </button>
                    
                    <!-- User Controls -->
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- User Menu -->
                        <div class="nav-item dropdown ms-2">
                            <button class="btn ultra-premium-nav-btn dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="btn-content">
                                    <div class="user-avatar-small">
                                        <img src="images/default-avatar.svg" alt="Avatar" width="20" height="20">
                                    </div>
                                    <span><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></span>
                                    <div class="btn-glow"></div>
                                </div>
                            </button>
                            <ul class="dropdown-menu ultra-premium-dropdown" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item ultra-premium-dropdown-item" href="profile.php">
                                    <i class="fas fa-user"></i> My Profile
                                </a></li>
                                <li><a class="dropdown-item ultra-premium-dropdown-item" href="settings.php">
                                    <i class="fas fa-cog"></i> Settings
                                </a></li>
                                <li><a class="dropdown-item ultra-premium-dropdown-item" href="notifications.php">
                                    <i class="fas fa-bell"></i> Notifications
                                    <span class="notification-badge">3</span>
                                </a></li>
                                <li><a class="dropdown-item ultra-premium-dropdown-item" href="messages.php">
                                    <i class="fas fa-envelope"></i> Messages
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item ultra-premium-dropdown-item" href="logout.php">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <!-- Guest Controls -->
                        <a href="login.php" class="btn ultra-premium-nav-btn ms-2">
                            <div class="btn-content">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>Login</span>
                                <div class="btn-glow"></div>
                            </div>
                        </a>
                        <a href="register.php" class="btn btn-ultra-premium ms-2">
                            <div class="btn-content">
                                <i class="fas fa-user-plus"></i>
                                <span>Join Now</span>
                                <div class="btn-glow"></div>
                            </div>
                        </a>
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
