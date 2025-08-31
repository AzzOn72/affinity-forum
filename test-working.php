<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Affinity Forum - Test Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        /* ===== AFFINITY FORUM - ULTRA PREMIUM WORKING STYLES ===== */
        
        /* ===== CSS VARIABLES ===== */
        :root {
            /* Light Theme */
            --light-bg-primary: #ffffff;
            --light-bg-secondary: #f8f9fa;
            --light-text-primary: #212529;
            --light-text-secondary: #6c757d;
            --light-border-primary: #dee2e6;
            --light-accent-primary: #667eea;
            --light-accent-secondary: #764ba2;
            
            /* Dark Theme */
            --dark-bg-primary: #1a1a1a;
            --dark-bg-secondary: #2d2d2d;
            --dark-text-primary: #ffffff;
            --dark-text-secondary: #b0b0b0;
            --dark-border-primary: #404040;
            --dark-accent-primary: #60a5fa;
            --dark-accent-secondary: #3b82f6;
            
            /* Common */
            --border-radius: 12px;
            --transition: all 0.3s ease;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        
        /* ===== THEME APPLICATIONS ===== */
        body[data-theme="light"] {
            --bg-primary: var(--light-bg-primary);
            --bg-secondary: var(--light-bg-secondary);
            --text-primary: var(--light-text-primary);
            --text-secondary: var(--light-text-secondary);
            --border-primary: var(--light-border-primary);
            --accent-primary: var(--light-accent-primary);
            --accent-secondary: var(--light-accent-secondary);
        }
        
        body[data-theme="dark"] {
            --bg-primary: var(--dark-bg-primary);
            --bg-secondary: var(--dark-bg-secondary);
            --text-primary: var(--dark-text-primary);
            --text-secondary: var(--dark-text-secondary);
            --border-primary: var(--dark-border-primary);
            --accent-primary: var(--dark-accent-primary);
            --accent-secondary: var(--dark-accent-secondary);
        }
        
        /* ===== BASE STYLES ===== */
        body {
            background-color: var(--bg-primary) !important;
            color: var(--text-primary) !important;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            transition: var(--transition);
        }
        
        /* ===== HERO SECTION ===== */
        .hero-section {
            background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-secondary) 100%);
            padding: 6rem 0;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .hero-title {
            font-size: 3.5rem;
            font-weight: 900;
            margin-bottom: 1.5rem;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }
        
        .hero-description {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        
        .hero-actions .btn {
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: var(--border-radius);
            transition: var(--transition);
        }
        
        .hero-actions .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.15);
        }
        
        /* ===== STAT CARDS ===== */
        .hero-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            text-align: center;
            color: var(--text-primary);
            box-shadow: var(--shadow);
            transition: var(--transition);
        }
        
        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.15);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 900;
            color: var(--accent-primary);
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            opacity: 0.8;
        }
        
        /* ===== DEVICE SELECTION OVERLAY ===== */
        .device-overlay {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            background: rgba(0, 0, 0, 0.9) !important;
            backdrop-filter: blur(20px) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            z-index: 9999 !important;
            pointer-events: auto !important;
        }
        
        .device-content {
            background: var(--bg-secondary) !important;
            border-radius: 24px !important;
            padding: 3rem !important;
            text-align: center !important;
            max-width: 600px !important;
            width: 90% !important;
            box-shadow: 0 16px 60px rgba(0, 0, 0, 0.2) !important;
            border: 1px solid var(--border-primary) !important;
            position: relative !important;
            z-index: 10000 !important;
        }
        
        .device-options {
            display: grid !important;
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 1.5rem !important;
            margin: 2rem 0 !important;
            position: relative !important;
            z-index: 10001 !important;
        }
        
        .device-option {
            background: var(--bg-primary) !important;
            border: 2px solid var(--border-primary) !important;
            border-radius: 16px !important;
            padding: 2rem 1.5rem !important;
            cursor: pointer !important;
            transition: var(--transition) !important;
            text-align: center !important;
            position: relative !important;
            overflow: hidden !important;
            user-select: none !important;
            z-index: 1000 !important;
            pointer-events: auto !important;
            display: block !important;
            min-height: 120px !important;
        }
        
        .device-option:hover {
            border-color: var(--accent-primary) !important;
            transform: translateY(-8px) !important;
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.15) !important;
            background: rgba(102, 126, 234, 0.1) !important;
        }
        
        .device-option i {
            font-size: 3rem;
            color: var(--accent-primary);
            margin-bottom: 1rem;
            display: block;
        }
        
        .device-option span {
            color: var(--text-primary);
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        /* ===== NOTIFICATIONS ===== */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--bg-secondary);
            border: 1px solid var(--border-primary);
            border-radius: var(--border-radius);
            padding: 1rem;
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.15);
            z-index: 10000;
            transform: translateX(100%);
            transition: var(--transition);
            max-width: 400px;
        }
        
        .notification.show {
            transform: translateX(0);
        }
        
        .notification-content {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .notification-close {
            background: none;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 0.25rem;
            margin-left: auto;
        }
        
        /* ===== BUTTONS ===== */
        .btn {
            border-radius: var(--border-radius);
            font-weight: 600;
            transition: var(--transition);
            border: none;
            padding: 0.75rem 1.5rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary)) !important;
            border: none !important;
            color: white !important;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.15);
        }
        
        .btn-outline-light {
            border: 2px solid white !important;
            color: white !important;
            background: transparent !important;
        }
        
        .btn-outline-light:hover {
            background: white !important;
            color: var(--accent-primary) !important;
            transform: translateY(-2px);
        }
        
        /* ===== THEME TRANSITIONS ===== */
        * {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }
        
        /* ===== THEME TOGGLE ===== */
        .theme-toggle {
            background: var(--accent-primary);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            padding: 0.5rem 1rem;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .theme-toggle:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }
    </style>
</head>
<body data-theme="light">

<!-- ===== PREMIUM HERO SECTION ===== -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="hero-content">
                    <h1 class="hero-title">Welcome to Affinity</h1>
                    <p class="hero-description">
                        The most advanced Counter-Strike 2 cheat community forum. 
                        Join thousands of players, share strategies, and dominate the competition.
                    </p>
                    <div class="hero-actions">
                        <button class="btn btn-primary btn-lg me-3" onclick="switchTheme('dark')">
                            <i class="fas fa-moon me-2"></i>Switch to Dark
                        </button>
                        <button class="btn btn-outline-light btn-lg" onclick="showDeviceSelection()">
                            <i class="fas fa-mobile-alt me-2"></i>Device Selection
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="hero-stats">
                    <div class="stat-card">
                        <div class="stat-number">1,234</div>
                        <div class="stat-label">MEMBERS</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">567</div>
                        <div class="stat-label">THREADS</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">8,901</div>
                        <div class="stat-label">POSTS</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== DEVICE SELECTION OVERLAY ===== -->
<div id="deviceOverlay" class="device-overlay" style="display: none;">
    <div class="device-content">
        <div class="device-header">
            <h2>Welcome to Affinity Forum</h2>
            <p>Select your device for the best experience</p>
        </div>
        <div class="device-options">
            <div class="device-option" data-device="desktop">
                <i class="fas fa-desktop"></i>
                <span>Desktop</span>
            </div>
            <div class="device-option" data-device="tablet">
                <i class="fas fa-tablet-alt"></i>
                <span>Tablet</span>
            </div>
            <div class="device-option" data-device="mobile">
                <i class="fas fa-mobile-alt"></i>
                <span>Mobile</span>
            </div>
        </div>
        <div class="device-footer">
            <small>You can change this later in settings</small>
        </div>
    </div>
</div>

<!-- ===== THEME TOGGLE ===== -->
<div class="text-center mt-4">
    <button class="theme-toggle" onclick="toggleTheme()">
        <i class="fas fa-sun me-2"></i>Toggle Theme
    </button>
</div>

<!-- ===== JAVASCRIPT ===== -->
<script src="js/themes-simple.js"></script>
<script>
// Test functions
function showDeviceSelection() {
    document.getElementById('deviceOverlay').style.display = 'flex';
}

function toggleTheme() {
    const currentTheme = document.body.getAttribute('data-theme');
    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
    switchTheme(newTheme);
}

// Initialize device selection
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Test page loaded, initializing...');
    
    // Initialize device selection
    if (typeof initDeviceSelection === 'function') {
        initDeviceSelection();
    } else {
        console.warn('⚠️ initDeviceSelection function not found');
    }
    
    console.log('✅ Test page initialized');
});
</script>

</body>
</html>
