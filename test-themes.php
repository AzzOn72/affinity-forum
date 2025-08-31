<?php
$page_title = "Advanced Theme System Test";
include 'includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card animate-in">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2>🎨 Advanced Theme System v3.0</h2>
                    <button class="btn btn-gradient" onclick="showPerformanceDashboard()">📊 Dashboard</button>
                </div>
                <div class="card-body">
                    
                    <!-- Theme Switcher -->
                    <section class="mb-5">
                        <h3 class="mb-3">🎨 Theme Switcher</h3>
                        <div class="theme-switcher">
                            <button class="theme-btn interactive-element" data-theme="light">☀️ Light</button>
                            <button class="theme-btn interactive-element" data-theme="dark">🌙 Dark</button>
                            <button class="theme-btn interactive-element" data-theme="cs2">🎮 CS2</button>
                            <button class="theme-btn interactive-element" data-theme="premium">💎 Premium</button>
                        </div>
                    </section>
                    
                    <!-- Device Selector -->
                    <section class="mb-5">
                        <h3 class="mb-3">📱 Device Selector</h3>
                        <div class="device-selector">
                            <button class="device-option interactive-element" data-device="desktop">🖥️ Desktop</button>
                            <button class="device-option interactive-element" data-device="tablet">📱 Tablet</button>
                            <button class="device-option interactive-element" data-device="mobile">📱 Mobile</button>
                        </div>
                    </section>
                    
                    <!-- Particle Effects -->
                    <section class="mb-5">
                        <h3 class="mb-3">✨ Particle Effects</h3>
                        <div class="particle-effects-selector">
                            <button class="particle-effect-btn" onclick="switchParticleEffect('standard')">⚪ Standard</button>
                            <button class="particle-effect-btn" onclick="switchParticleEffect('fireworks')">🎆 Fireworks</button>
                            <button class="particle-effect-btn" onclick="switchParticleEffect('snow')">❄️ Snow</button>
                            <button class="particle-effect-btn" onclick="switchParticleEffect('rain')">🌧️ Rain</button>
                            <button class="particle-effect-btn" onclick="switchParticleEffect('stars')">⭐ Stars</button>
                            <button class="particle-effect-btn" onclick="switchParticleEffect('bubbles')">🫧 Bubbles</button>
                        </div>
                    </section>
                    
                    <!-- Audio Controls -->
                    <section class="mb-5">
                        <h3 class="mb-3">🔊 Audio Controls</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="volume-control">
                                    <label>🔉 Volume:</label>
                                    <input type="range" class="volume-slider" min="0" max="1" step="0.1" value="0.5" 
                                           onchange="setVolume(this.value)">
                                    <span id="volumeDisplay">50%</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <button class="btn btn-secondary" onclick="toggleAudioVisualizer()">🎵 Visualizer</button>
                            </div>
                        </div>
                    </section>
                    
                    <!-- Accessibility Controls -->
                    <section class="mb-5">
                        <h3 class="mb-3">♿ Accessibility Controls</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex gap-2 flex-wrap mb-3">
                                    <button class="btn btn-secondary" onclick="toggleHighContrast()">🔲 High Contrast</button>
                                    <button class="btn btn-secondary" onclick="toggleReducedMotion()">⏸️ Reduced Motion</button>
                                    <button class="btn btn-secondary" onclick="toggleFontSize()">🔤 Font Size</button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex gap-2 flex-wrap mb-3">
                                    <button class="btn btn-info" onclick="enableVoiceControl()">🎤 Voice Control</button>
                                    <button class="btn btn-info" onclick="toggleColorBlindSupport()">👁️ Color Blind</button>
                                </div>
                            </div>
                        </div>
                    </section>
                    
                    <!-- Custom Theme Creator -->
                    <section class="mb-5">
                        <h3 class="mb-3">🎨 Custom Theme Creator</h3>
                        <div class="theme-creator">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="color-input-group">
                                        <label>Primary Color:</label>
                                        <input type="color" id="primaryColor" value="#007bff">
                                        <input type="text" id="primaryColorText" value="#007bff">
                                    </div>
                                    <div class="color-input-group">
                                        <label>Background:</label>
                                        <input type="color" id="bgColor" value="#ffffff">
                                        <input type="text" id="bgColorText" value="#ffffff">
                                    </div>
                                    <div class="color-input-group">
                                        <label>Text Color:</label>
                                        <input type="color" id="textColor" value="#212529">
                                        <input type="text" id="textColorText" value="#212529">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="color-input-group">
                                        <label>Accent Color:</label>
                                        <input type="color" id="accentColor" value="#28a745">
                                        <input type="text" id="accentColorText" value="#28a745">
                                    </div>
                                    <div class="color-input-group">
                                        <label>Border Color:</label>
                                        <input type="color" id="borderColor" value="#dee2e6">
                                        <input type="text" id="borderColorText" value="#dee2e6">
                                    </div>
                                    <div class="mt-3">
                                        <input type="text" id="themeName" placeholder="Theme Name" class="form-control mb-2">
                                        <button class="btn btn-success" onclick="createThemeFromInputs()">Create Theme</button>
                                        <button class="btn btn-info" onclick="previewTheme()">Live Preview</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    
                    <!-- Settings Management -->
                    <section class="mb-5">
                        <h3 class="mb-3">⚙️ Settings Management</h3>
                        <div class="d-flex gap-2 flex-wrap">
                            <button class="btn btn-primary" onclick="exportSettings()">📤 Export Settings</button>
                            <button class="btn btn-primary" onclick="document.getElementById('importFile').click()">📥 Import Settings</button>
                            <button class="btn btn-warning" onclick="resetToDefaults()">🔄 Reset to Defaults</button>
                            <button class="btn btn-info" onclick="showPerformanceDashboard()">📊 Performance</button>
                        </div>
                        
                        <input type="file" id="importFile" accept=".json" style="display: none;" onchange="importSettings(this.files[0])">
                    </section>
                    
                    <!-- Current Status -->
                    <section class="mb-5">
                        <h3 class="mb-3">📊 Current Status</h3>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="premium-card">
                                    <h5>🎨 Theme Settings</h5>
                                    <p><strong>Theme:</strong> <span id="currentThemeDisplay" class="text-primary">Loading...</span></p>
                                    <p><strong>Device:</strong> <span id="currentDeviceDisplay" class="text-primary">Loading...</span></p>
                                    <p><strong>Particle Effect:</strong> <span id="particleEffectDisplay" class="text-primary">Loading...</span></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="premium-card">
                                    <h5>♿ Accessibility</h5>
                                    <p><strong>High Contrast:</strong> <span id="highContrastDisplay" class="text-success">Loading...</span></p>
                                    <p><strong>Reduced Motion:</strong> <span id="reducedMotionDisplay" class="text-success">Loading...</span></p>
                                    <p><strong>Font Size:</strong> <span id="fontSizeDisplay" class="text-success">Loading...</span></p>
                                    <p><strong>Voice Control:</strong> <span id="voiceControlDisplay" class="text-success">Loading...</span></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="premium-card">
                                    <h5>🚀 Performance</h5>
                                    <p><strong>FPS:</strong> <span id="fpsDisplay" class="text-info">Loading...</span></p>
                                    <p><strong>Memory:</strong> <span id="memoryDisplay" class="text-info">Loading...</span></p>
                                    <p><strong>Particles:</strong> <span id="particleCountDisplay" class="text-info">Loading...</span></p>
                                    <p><strong>Audio Volume:</strong> <span id="audioVolumeDisplay" class="text-info">Loading...</span></p>
                                </div>
                            </div>
                        </div>
                    </section>
                    
                    <!-- Sample Content -->
                    <section class="mb-5">
                        <h3 class="mb-3">🎯 Sample Content</h3>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card interactive-element">
                                    <div class="card-body">
                                        <h5 class="card-title">Standard Card</h5>
                                        <p class="card-text">This is a sample card to test theme colors and styling.</p>
                                        <button class="btn btn-primary" onclick="this.classList.add('animate-pulse')">Primary Action</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="premium-card interactive-element">
                                    <h5>Premium Card</h5>
                                    <p>This is a premium card with enhanced styling and effects.</p>
                                    <button class="btn btn-gradient" onclick="this.classList.add('animate-bounce')">Premium Action</button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card interactive-element">
                                    <div class="card-body">
                                        <h5 class="card-title">Interactive Card</h5>
                                        <p class="card-text">Click and hover to test interactive effects.</p>
                                        <button class="btn btn-glow" onclick="this.classList.add('animate-shake')">Glowing Action</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    
                    <!-- Advanced Features Demo -->
                    <section class="mb-5">
                        <h3 class="mb-3">🚀 Advanced Features</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="premium-card">
                                    <h5>🎵 Audio Features</h5>
                                    <button class="btn btn-info me-2" onclick="window.themeManager.playSound('theme-change')">Theme Sound</button>
                                    <button class="btn btn-info me-2" onclick="window.themeManager.playSound('notification')">Notification</button>
                                    <button class="btn btn-info" onclick="window.themeManager.playSound('click')">Click Sound</button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="premium-card">
                                    <h5>✨ Particle Features</h5>
                                    <p class="small text-muted mb-2">Click on the particle canvas to create explosions!</p>
                                    <button class="btn btn-success" onclick="toggleParticles()">Toggle Particles</button>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Enhanced status update function
function updateStatus() {
    if (window.themeManager) {
        // Theme settings
        document.getElementById('currentThemeDisplay').textContent = window.themeManager.getCurrentTheme();
        document.getElementById('currentDeviceDisplay').textContent = window.themeManager.getCurrentDevice();
        document.getElementById('particleEffectDisplay').textContent = window.themeManager.particleEffects.currentType;
        
        // Accessibility
        document.getElementById('highContrastDisplay').textContent = window.themeManager.accessibility.highContrast ? 'Enabled' : 'Disabled';
        document.getElementById('reducedMotionDisplay').textContent = window.themeManager.accessibility.reducedMotion ? 'Enabled' : 'Disabled';
        document.getElementById('fontSizeDisplay').textContent = window.themeManager.accessibility.fontSize + 'px';
        document.getElementById('voiceControlDisplay').textContent = window.themeManager.accessibility.voiceControl ? 'Active' : 'Disabled';
        
        // Performance
        document.getElementById('fpsDisplay').textContent = window.themeManager.performanceMetrics.fps + ' fps';
        document.getElementById('memoryDisplay').textContent = Math.round(window.themeManager.performanceMetrics.memoryUsage / 1024 / 1024) + 'MB';
        document.getElementById('particleCountDisplay').textContent = window.themeManager.particleSystem ? window.themeManager.particleSystem.particles.length : 0;
        document.getElementById('audioVolumeDisplay').textContent = Math.round(window.themeManager.audioEffects.volume * 100) + '%';
        
        // Update volume slider
        const volumeSlider = document.querySelector('.volume-slider');
        const volumeDisplay = document.getElementById('volumeDisplay');
        if (volumeSlider && volumeDisplay) {
            volumeSlider.value = window.themeManager.audioEffects.volume;
            volumeDisplay.textContent = Math.round(window.themeManager.audioEffects.volume * 100) + '%';
        }
        
        // Update active buttons
        updateActiveButtons();
    }
}

function updateActiveButtons() {
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
    
    // Update active particle effect button
    document.querySelectorAll('.particle-effect-btn').forEach(btn => {
        btn.classList.remove('active');
        const effectName = btn.textContent.toLowerCase().split(' ')[1];
        if (effectName === window.themeManager.particleEffects.currentType) {
            btn.classList.add('active');
        }
    });
}

// Custom theme creation
function createThemeFromInputs() {
    const name = document.getElementById('themeName').value.trim();
    if (!name) {
        window.themeManager.showNotification('Please enter a theme name', 'error');
        return;
    }
    
    const colors = {
        'primary-color': document.getElementById('primaryColor').value,
        'bg-color': document.getElementById('bgColor').value,
        'text-color': document.getElementById('textColor').value,
        'accent-color': document.getElementById('accentColor').value,
        'border-color': document.getElementById('borderColor').value
    };
    
    const themeId = createCustomTheme(name, colors);
    if (themeId) {
        applyCustomTheme(themeId);
    }
}

function previewTheme() {
    const colors = {
        'primary-color': document.getElementById('primaryColor').value,
        'bg-color': document.getElementById('bgColor').value,
        'text-color': document.getElementById('textColor').value,
        'accent-color': document.getElementById('accentColor').value,
        'border-color': document.getElementById('borderColor').value
    };
    
    // Temporarily apply colors for preview
    const root = document.documentElement;
    Object.entries(colors).forEach(([key, value]) => {
        root.style.setProperty(`--${key}`, value);
    });
    
    window.themeManager.showNotification('Theme preview applied! Create to save.', 'info');
}

// Color input synchronization
function setupColorInputs() {
    const colorInputs = [
        { color: 'primaryColor', text: 'primaryColorText' },
        { color: 'bgColor', text: 'bgColorText' },
        { color: 'textColor', text: 'textColorText' },
        { color: 'accentColor', text: 'accentColorText' },
        { color: 'borderColor', text: 'borderColorText' }
    ];
    
    colorInputs.forEach(({ color, text }) => {
        const colorInput = document.getElementById(color);
        const textInput = document.getElementById(text);
        
        if (colorInput && textInput) {
            colorInput.addEventListener('input', () => {
                textInput.value = colorInput.value;
            });
            
            textInput.addEventListener('input', () => {
                if (/^#[0-9A-Fa-f]{6}$/.test(textInput.value)) {
                    colorInput.value = textInput.value;
                }
            });
        }
    });
}

// Enhanced accessibility toggle
function toggleColorBlindSupport() {
    if (window.themeManager) {
        window.themeManager.accessibility.colorBlindSupport = !window.themeManager.accessibility.colorBlindSupport;
        
        if (window.themeManager.accessibility.colorBlindSupport) {
            document.body.classList.add('color-blind-support');
            window.themeManager.showNotification('Color blind support enabled!', 'success');
        } else {
            document.body.classList.remove('color-blind-support');
            window.themeManager.showNotification('Color blind support disabled!', 'info');
        }
        
        localStorage.setItem('colorBlindSupport', window.themeManager.accessibility.colorBlindSupport);
    }
}

// Initialize enhanced features
document.addEventListener('DOMContentLoaded', () => {
    setupColorInputs();
    
    // Add hover sound effects to interactive elements
    document.querySelectorAll('.interactive-element').forEach(el => {
        el.addEventListener('mouseenter', () => {
            if (window.themeManager && window.themeManager.features.audio) {
                window.themeManager.playHoverSound();
            }
        });
    });
    
    // Smart suggestions demo
    setTimeout(() => {
        if (window.themeManager && window.themeManager.features.smartSuggestions) {
            window.themeManager.showNotification('💡 Try voice control! Say "switch to dark theme"', 'info', 10000);
        }
    }, 5000);
});

// Update status every second
setInterval(updateStatus, 1000);

// Initial update
setTimeout(updateStatus, 500);
</script>

<?php include 'includes/footer.php'; ?>