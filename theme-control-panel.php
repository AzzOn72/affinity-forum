<?php
$page_title = "Theme Control Panel";
include 'includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="premium-card text-center mb-4">
                <h1 class="display-4 text-gradient mb-3">🎛️ Theme Control Panel</h1>
                <p class="lead">Master control center for all theme system features</p>
            </div>
        </div>
    </div>
    
    <div class="row g-4">
        
        <!-- Quick Actions -->
        <div class="col-lg-4">
            <div class="premium-card h-100">
                <div class="card-header">
                    <h3>⚡ Quick Actions</h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" onclick="showPerformanceDashboard()">📊 Performance Dashboard</button>
                        <button class="btn btn-info" onclick="window.location.href='test-themes.php'">🧪 Theme Tester</button>
                        <button class="btn btn-success" onclick="window.location.href='features-showcase.php'">🚀 Features Showcase</button>
                        <button class="btn btn-warning" onclick="enableVoiceControl()">🎤 Voice Control</button>
                        <button class="btn btn-gradient" onclick="createFireworkShow()">🎆 Firework Show</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Theme Controls -->
        <div class="col-lg-4">
            <div class="premium-card h-100">
                <div class="card-header">
                    <h3>🎨 Theme Controls</h3>
                </div>
                <div class="card-body">
                    <div class="theme-switcher mb-3">
                        <button class="theme-btn" data-theme="light">☀️ Light</button>
                        <button class="theme-btn" data-theme="dark">🌙 Dark</button>
                        <button class="theme-btn" data-theme="cs2">🎮 CS2</button>
                        <button class="theme-btn" data-theme="premium">💎 Premium</button>
                    </div>
                    
                    <div class="device-selector">
                        <button class="device-option" data-device="desktop">🖥️ Desktop</button>
                        <button class="device-option" data-device="tablet">📱 Tablet</button>
                        <button class="device-option" data-device="mobile">📱 Mobile</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- System Status -->
        <div class="col-lg-4">
            <div class="premium-card h-100">
                <div class="card-header">
                    <h3>📊 System Status</h3>
                </div>
                <div class="card-body">
                    <div class="status-grid">
                        <div class="status-item">
                            <span class="status-label">Theme:</span>
                            <span class="status-value" id="statusTheme">Loading...</span>
                        </div>
                        <div class="status-item">
                            <span class="status-label">Device:</span>
                            <span class="status-value" id="statusDevice">Loading...</span>
                        </div>
                        <div class="status-item">
                            <span class="status-label">FPS:</span>
                            <span class="status-value" id="statusFps">Loading...</span>
                        </div>
                        <div class="status-item">
                            <span class="status-label">Memory:</span>
                            <span class="status-value" id="statusMemory">Loading...</span>
                        </div>
                        <div class="status-item">
                            <span class="status-label">Particles:</span>
                            <span class="status-value" id="statusParticles">Loading...</span>
                        </div>
                        <div class="status-item">
                            <span class="status-label">Audio:</span>
                            <span class="status-value" id="statusAudio">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Particle Effects -->
        <div class="col-lg-6">
            <div class="premium-card h-100">
                <div class="card-header">
                    <h3>✨ Particle Effects</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Choose from 6 different particle effects with physics simulation:</p>
                    
                    <div class="particle-effects-selector">
                        <button class="particle-effect-btn" onclick="switchParticleEffect('standard')">⚪ Standard</button>
                        <button class="particle-effect-btn" onclick="switchParticleEffect('fireworks')">🎆 Fireworks</button>
                        <button class="particle-effect-btn" onclick="switchParticleEffect('snow')">❄️ Snow</button>
                        <button class="particle-effect-btn" onclick="switchParticleEffect('rain')">🌧️ Rain</button>
                        <button class="particle-effect-btn" onclick="switchParticleEffect('stars')">⭐ Stars</button>
                        <button class="particle-effect-btn" onclick="switchParticleEffect('bubbles')">🫧 Bubbles</button>
                    </div>
                    
                    <div class="mt-3">
                        <small class="text-muted">💡 Tip: Click anywhere on the screen for interactive effects!</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Audio System -->
        <div class="col-lg-6">
            <div class="premium-card h-100">
                <div class="card-header">
                    <h3>🎵 Audio System</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Immersive audio feedback with volume control and visualization:</p>
                    
                    <div class="volume-control mb-3">
                        <label>🔉 Master Volume:</label>
                        <input type="range" class="volume-slider" min="0" max="1" step="0.1" value="0.5" 
                               onchange="setVolume(this.value); updateControlPanelVolume(this.value)">
                        <span id="controlPanelVolume">50%</span>
                    </div>
                    
                    <div class="d-flex gap-2 flex-wrap mb-3">
                        <button class="btn btn-sm btn-primary" onclick="window.themeManager.playSound('theme-change')">🎵 Theme</button>
                        <button class="btn btn-sm btn-info" onclick="window.themeManager.playSound('notification')">🔔 Notification</button>
                        <button class="btn btn-sm btn-success" onclick="window.themeManager.playSound('click')">👆 Click</button>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button class="btn btn-gradient" onclick="toggleAudioVisualizer()">📊 Visualizer</button>
                        <button class="btn btn-secondary" onclick="toggleAudio()">🔇 Toggle</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Accessibility Center -->
        <div class="col-lg-6">
            <div class="premium-card h-100">
                <div class="card-header">
                    <h3>♿ Accessibility Center</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Comprehensive accessibility features for all users:</p>
                    
                    <div class="row g-2">
                        <div class="col-6">
                            <button class="btn btn-outline-primary w-100" onclick="toggleHighContrast()">🔲 High Contrast</button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-outline-primary w-100" onclick="toggleReducedMotion()">⏸️ Reduced Motion</button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-outline-primary w-100" onclick="toggleFontSize()">🔤 Font Size</button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-outline-primary w-100" onclick="toggleColorBlindSupport()">👁️ Color Blind</button>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <button class="btn btn-warning w-100" onclick="enableVoiceControl()">🎤 Enable Voice Control</button>
                        <small class="text-muted d-block mt-2">Say "switch to dark theme" or "enable particles"</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Settings Management -->
        <div class="col-lg-6">
            <div class="premium-card h-100">
                <div class="card-header">
                    <h3>⚙️ Settings Management</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Manage and backup your theme preferences:</p>
                    
                    <div class="d-grid gap-2 mb-3">
                        <button class="btn btn-primary" onclick="exportSettings()">📤 Export Settings</button>
                        <button class="btn btn-info" onclick="document.getElementById('importFileControl').click()">📥 Import Settings</button>
                        <button class="btn btn-success" onclick="exportPerformanceReport()">📊 Export Performance</button>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button class="btn btn-warning" onclick="resetToDefaults()">🔄 Reset to Defaults</button>
                        <button class="btn btn-danger" onclick="clearAllData()">🗑️ Clear All Data</button>
                    </div>
                    
                    <input type="file" id="importFileControl" accept=".json" style="display: none;" onchange="importSettings(this.files[0])">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Navigation Links -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="premium-card">
                <div class="card-header">
                    <h3>🔗 Navigation</h3>
                </div>
                <div class="card-body text-center">
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="index.php" class="btn btn-outline-primary">🏠 Home</a>
                        <a href="test-themes.php" class="btn btn-outline-info">🧪 Theme Tester</a>
                        <a href="features-showcase.php" class="btn btn-outline-success">🚀 Features Showcase</a>
                        <a href="forum.php" class="btn btn-outline-warning">💬 Forum</a>
                        <a href="settings.php" class="btn btn-outline-secondary">⚙️ User Settings</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.status-grid {
    display: grid;
    gap: 0.5rem;
}

.status-item {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem;
    background: var(--hover-bg);
    border-radius: var(--border-radius);
    border: 1px solid var(--border-color);
}

.status-label {
    font-weight: bold;
    color: var(--text-color);
}

.status-value {
    color: var(--primary-color);
    font-family: monospace;
}

.text-gradient {
    background: linear-gradient(45deg, var(--primary-color), var(--accent-color));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
</style>

<script>
// Update control panel status
function updateControlPanelStatus() {
    if (window.themeManager) {
        document.getElementById('statusTheme').textContent = window.themeManager.getCurrentTheme();
        document.getElementById('statusDevice').textContent = window.themeManager.getCurrentDevice();
        document.getElementById('statusFps').textContent = window.themeManager.performanceMetrics.fps + ' fps';
        document.getElementById('statusMemory').textContent = Math.round(window.themeManager.performanceMetrics.memoryUsage / 1024 / 1024) + 'MB';
        document.getElementById('statusParticles').textContent = window.themeManager.particleSystem ? window.themeManager.particleSystem.particles.length : 0;
        document.getElementById('statusAudio').textContent = window.themeManager.features.audio ? 'Enabled' : 'Disabled';
        
        // Update active buttons
        document.querySelectorAll('.theme-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.theme === window.themeManager.getCurrentTheme()) {
                btn.classList.add('active');
            }
        });
        
        document.querySelectorAll('.device-option').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.device === window.themeManager.getCurrentDevice()) {
                btn.classList.add('active');
            }
        });
        
        document.querySelectorAll('.particle-effect-btn').forEach(btn => {
            btn.classList.remove('active');
            const effectName = btn.textContent.toLowerCase().split(' ')[1];
            if (effectName === window.themeManager.particleEffects.currentType) {
                btn.classList.add('active');
            }
        });
    }
}

// Update volume display
function updateControlPanelVolume(value) {
    const display = document.getElementById('controlPanelVolume');
    if (display) {
        display.textContent = Math.round(value * 100) + '%';
    }
}

// Clear all data
function clearAllData() {
    if (confirm('⚠️ This will clear ALL theme data and reset everything to defaults. Continue?')) {
        localStorage.clear();
        window.location.reload();
    }
}

// Create firework show
function createFireworkShow() {
    if (!window.themeManager.particleSystem) {
        toggleParticles();
        setTimeout(() => createFireworkShow(), 1000);
        return;
    }
    
    switchParticleEffect('fireworks');
    
    const bursts = 8;
    for (let i = 0; i < bursts; i++) {
        setTimeout(() => {
            const x = Math.random() * window.innerWidth;
            const y = Math.random() * window.innerHeight * 0.6;
            window.themeManager.createExplosion(x, y);
            window.themeManager.playSound('theme-change');
        }, i * 800);
    }
    
    window.themeManager.showNotification('🎆 Epic firework show started!', 'success');
}

// Initialize control panel
document.addEventListener('DOMContentLoaded', () => {
    // Update status every second
    setInterval(updateControlPanelStatus, 1000);
    
    // Initial update
    setTimeout(updateControlPanelStatus, 500);
    
    // Welcome message
    setTimeout(() => {
        if (window.themeManager) {
            window.themeManager.showNotification('🎛️ Welcome to the Theme Control Panel!', 'success');
        }
    }, 2000);
});
</script>

<?php include 'includes/footer.php'; ?>