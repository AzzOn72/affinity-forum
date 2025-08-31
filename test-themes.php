<?php
$page_title = "Theme Test";
include 'includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h2>🎨 Theme System Test</h2>
                </div>
                <div class="card-body">
                    <h3>Theme Switcher</h3>
                    <div class="theme-switcher">
                        <button class="theme-btn" data-theme="light">☀️ Light</button>
                        <button class="theme-btn" data-theme="dark">🌙 Dark</button>
                        <button class="theme-btn" data-theme="cs2">🎮 CS2</button>
                        <button class="theme-btn" data-theme="premium">💎 Premium</button>
                    </div>
                    
                    <h3 class="mt-4">Device Selector</h3>
                    <div class="device-selector">
                        <button class="device-option" data-device="desktop">🖥️ Desktop</button>
                        <button class="device-option" data-device="tablet">📱 Tablet</button>
                        <button class="device-option" data-device="mobile">📱 Mobile</button>
                    </div>
                    
                    <h3 class="mt-4">Accessibility Controls</h3>
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-secondary" onclick="toggleHighContrast()">High Contrast</button>
                        <button class="btn btn-secondary" onclick="toggleReducedMotion()">Reduced Motion</button>
                        <button class="btn btn-secondary" onclick="toggleFontSize()">Font Size</button>
                        <button class="btn btn-secondary" onclick="toggleParticles()">Particles</button>
                        <button class="btn btn-secondary" onclick="toggleAudio()">Audio</button>
                    </div>
                    
                    <h3 class="mt-4">Settings Management</h3>
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-primary" onclick="exportSettings()">Export Settings</button>
                        <button class="btn btn-primary" onclick="document.getElementById('importFile').click()">Import Settings</button>
                        <button class="btn btn-warning" onclick="resetToDefaults()">Reset to Defaults</button>
                    </div>
                    
                    <input type="file" id="importFile" accept=".json" style="display: none;" onchange="importSettings(this.files[0])">
                    
                    <h3 class="mt-4">Current Status</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Current Theme:</strong> <span id="currentThemeDisplay">Loading...</span></p>
                            <p><strong>Current Device:</strong> <span id="currentDeviceDisplay">Loading...</span></p>
                            <p><strong>High Contrast:</strong> <span id="highContrastDisplay">Loading...</span></p>
                            <p><strong>Reduced Motion:</strong> <span id="reducedMotionDisplay">Loading...</span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Particles:</strong> <span id="particlesDisplay">Loading...</span></p>
                            <p><strong>Audio:</strong> <span id="audioDisplay">Loading...</span></p>
                            <p><strong>Font Size:</strong> <span id="fontSizeDisplay">Loading...</span></p>
                            <p><strong>JavaScript Status:</strong> <span id="jsStatus">Loading...</span></p>
                        </div>
                    </div>
                    
                    <h3 class="mt-4">Sample Content</h3>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Sample Card 1</h5>
                                    <p class="card-text">This is a sample card to test theme colors and styling.</p>
                                    <button class="btn btn-primary">Action</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="premium-card">
                                <h5>Premium Card</h5>
                                <p>This is a premium card with enhanced styling.</p>
                                <button class="btn btn-accent">Premium Action</button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Sample Card 3</h5>
                                    <p class="card-text">Another sample card for testing purposes.</p>
                                    <button class="btn btn-secondary">Secondary</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Update status displays
function updateStatus() {
    if (window.themeManager) {
        document.getElementById('currentThemeDisplay').textContent = window.themeManager.getCurrentTheme();
        document.getElementById('currentDeviceDisplay').textContent = window.themeManager.getCurrentDevice();
        document.getElementById('highContrastDisplay').textContent = window.themeManager.accessibility.highContrast ? 'Enabled' : 'Disabled';
        document.getElementById('reducedMotionDisplay').textContent = window.themeManager.accessibility.reducedMotion ? 'Enabled' : 'Disabled';
        document.getElementById('particlesDisplay').textContent = window.themeManager.features.particles ? 'Enabled' : 'Disabled';
        document.getElementById('audioDisplay').textContent = window.themeManager.features.audio ? 'Enabled' : 'Disabled';
        document.getElementById('fontSizeDisplay').textContent = window.themeManager.accessibility.fontSize + 'px';
        
        // Update active theme button
        document.querySelectorAll('.theme-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.theme === window.themeManager.getCurrentTheme()) {
                btn.classList.add('active');
            }
        });
        
        // Update active device button
        document.querySelectorAll('.device-option').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.device === window.themeManager.getCurrentDevice()) {
                btn.classList.add('active');
            }
        });
    }
}

// Update status every second
setInterval(updateStatus, 1000);

// Initial update
setTimeout(updateStatus, 500);
</script>

<?php include 'includes/footer.php'; ?>