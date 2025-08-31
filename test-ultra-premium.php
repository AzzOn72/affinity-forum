<?php
require_once 'config.php';
require_once 'includes/header.php';
?>

<div class="container-fluid">
    <!-- Hero Section -->
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="display-3 text-gradient mb-4">🚀 Ultra Premium Theme System</h1>
            <p class="lead mb-4">Experience the most advanced theme management system with cutting-edge features</p>
            
            <!-- Theme Quick Switch -->
            <div class="d-flex justify-content-center gap-3 mb-4">
                <button class="btn btn-ultra-premium" onclick="switchTheme('light')">☀️ Light</button>
                <button class="btn btn-ultra-premium" onclick="switchTheme('dark')">🌙 Dark</button>
                <button class="btn btn-ultra-premium" onclick="switchTheme('cs2')">🎮 CS2</button>
                <button class="btn btn-ultra-premium" onclick="switchTheme('premium')">💎 Premium</button>
            </div>
            
            <!-- Device Quick Switch -->
            <div class="d-flex justify-content-center gap-3 mb-4">
                <button class="btn btn-ultra-premium" onclick="selectDevice('desktop')">🖥️ Desktop</button>
                <button class="btn btn-ultra-premium" onclick="selectDevice('tablet')">📱 Tablet</button>
                <button class="btn btn-ultra-premium" onclick="selectDevice('mobile')">📱 Mobile</button>
            </div>
        </div>
    </div>

    <!-- Feature Showcase -->
    <div class="row mb-5">
        <div class="col-md-4 mb-4">
            <div class="premium-card h-100">
                <div class="card-body text-center">
                    <h3 class="text-gradient">🎨 Themes</h3>
                    <p>4 premium themes with dynamic color schemes and smooth transitions</p>
                    <div class="theme-preview">
                        <div class="color-swatch" style="background: var(--primary-color)"></div>
                        <div class="color-swatch" style="background: var(--secondary-color)"></div>
                        <div class="color-swatch" style="background: var(--accent-color)"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="premium-card h-100">
                <div class="card-body text-center">
                    <h3 class="text-gradient">✨ Particles</h3>
                    <p>Dynamic particle system with theme-aware colors and performance optimization</p>
                    <button class="btn btn-ultra-premium" onclick="toggleParticles()">Toggle Particles</button>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="premium-card h-100">
                <div class="card-body text-center">
                    <h3 class="text-gradient">🔊 Audio</h3>
                    <p>Immersive audio feedback with Web Audio API integration</p>
                    <button class="btn btn-ultra-premium" onclick="toggleAudio()">Toggle Audio</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Accessibility Features -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="text-center text-gradient mb-4">♿ Accessibility Features</h2>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="premium-card text-center">
                        <h4>High Contrast</h4>
                        <button class="btn btn-ultra-premium" onclick="toggleHighContrast()">Toggle</button>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="premium-card text-center">
                        <h4>Reduced Motion</h4>
                        <button class="btn btn-ultra-premium" onclick="toggleReducedMotion()">Toggle</button>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="premium-card text-center">
                        <h4>Font Size</h4>
                        <button class="btn btn-ultra-premium" onclick="toggleFontSize()">Adjust</button>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="premium-card text-center">
                        <h4>Screen Reader</h4>
                        <span class="badge bg-success">Enabled</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Monitoring -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="text-center text-gradient mb-4">📊 Performance Monitoring</h2>
            <div class="premium-card">
                <div class="row">
                    <div class="col-md-3 text-center">
                        <h4>FPS</h4>
                        <div class="display-6" id="fpsDisplay">--</div>
                    </div>
                    <div class="col-md-3 text-center">
                        <h4>Memory</h4>
                        <div class="display-6" id="memoryDisplay">--</div>
                    </div>
                    <div class="col-md-3 text-center">
                        <h4>Theme Switches</h4>
                        <div class="display-6" id="themeSwitches">--</div>
                    </div>
                    <div class="col-md-3 text-center">
                        <h4>Device Switches</h4>
                        <div class="display-6" id="deviceSwitches">--</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Features -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="text-center text-gradient mb-4">🔧 Advanced Features</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="premium-card">
                        <h4>Developer Mode</h4>
                        <p>Access advanced debugging and development tools</p>
                        <button class="btn btn-ultra-premium" onclick="enableDeveloperMode()">Enable Dev Mode</button>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="premium-card">
                        <h4>Settings Export/Import</h4>
                        <p>Backup and restore your personalized settings</p>
                        <button class="btn btn-ultra-premium" onclick="exportSettings()">Export</button>
                        <input type="file" id="importFile" accept=".json" style="display: none;" onchange="importSettings(this.files[0])">
                        <button class="btn btn-ultra-premium" onclick="document.getElementById('importFile').click()">Import</button>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="premium-card">
                        <h4>Reset to Defaults</h4>
                        <p>Restore all settings to their original values</p>
                        <button class="btn btn-ultra-premium" onclick="resetToDefaults()">Reset</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Keyboard Shortcuts -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="text-center text-gradient mb-4">⌨️ Keyboard Shortcuts</h2>
            <div class="premium-card">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Theme & Device</h5>
                        <ul class="list-unstyled">
                            <li><kbd>Ctrl + T</kbd> - Toggle Theme</li>
                            <li><kbd>Ctrl + D</kbd> - Toggle Device</li>
                            <li><kbd>F1</kbd> - Show Help</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>Accessibility</h5>
                        <ul class="list-unstyled">
                            <li><kbd>Ctrl + H</kbd> - High Contrast</li>
                            <li><kbd>Ctrl + M</kbd> - Reduced Motion</li>
                            <li><kbd>Ctrl + F</kbd> - Font Size</li>
                        </ul>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <button class="btn btn-ultra-premium" onclick="showShortcutsHelp()">View All Shortcuts</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Status -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="text-center text-gradient mb-4">📋 Current Status</h2>
            <div class="premium-card">
                <div class="row">
                    <div class="col-md-3 text-center">
                        <h5>Current Theme</h5>
                        <div class="badge bg-primary fs-6" id="currentThemeDisplay">--</div>
                    </div>
                    <div class="col-md-3 text-center">
                        <h5>Current Device</h5>
                        <div class="badge bg-success fs-6" id="currentDeviceDisplay">--</div>
                    </div>
                    <div class="col-md-3 text-center">
                        <h5>Particles</h5>
                        <div class="badge bg-info fs-6" id="particlesStatus">--</div>
                    </div>
                    <div class="col-md-3 text-center">
                        <h5>Audio</h5>
                        <div class="badge bg-warning fs-6" id="audioStatus">--</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Update displays every second
setInterval(() => {
    if (window.themeManager) {
        // Update performance displays
        const perfReport = window.themeManager.getPerformanceReport();
        document.getElementById('fpsDisplay').textContent = perfReport.fps || '--';
        document.getElementById('memoryDisplay').textContent = perfReport.memoryUsage ? 
            Math.round(perfReport.memoryUsage / 1024 / 1024) + 'MB' : '--';
        document.getElementById('themeSwitches').textContent = perfReport.themeSwitchCount || 0;
        document.getElementById('deviceSwitches').textContent = perfReport.deviceSwitchCount || 0;
        
        // Update status displays
        document.getElementById('currentThemeDisplay').textContent = perfReport.currentTheme || '--';
        document.getElementById('currentDeviceDisplay').textContent = perfReport.currentDevice || '--';
        document.getElementById('particlesStatus').textContent = perfReport.features?.particles ? 'ON' : 'OFF';
        document.getElementById('audioStatus').textContent = perfReport.features?.audio ? 'ON' : 'OFF';
    }
}, 1000);

// Test functions
function testTheme(themeName) {
    if (window.themeManager) {
        window.themeManager.switchTheme(themeName);
    }
}

function testDevice(device) {
    if (window.themeManager) {
        window.themeManager.selectDevice(device);
    }
}

function testNotification(type) {
    if (window.themeManager) {
        window.themeManager.showNotification(`This is a ${type} notification!`, type);
    }
}

// Initialize displays
document.addEventListener('DOMContentLoaded', () => {
    if (window.themeManager) {
        // Show welcome notification
        setTimeout(() => {
            window.themeManager.showNotification('Welcome to the Ultra Premium Theme System! 🎉', 'success');
        }, 1000);
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
