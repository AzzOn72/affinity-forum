<?php
$page_title = "Advanced Features Showcase";
include 'includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="premium-card animate-in">
                <div class="text-center mb-5">
                    <h1 class="display-4 text-gradient">🚀 Advanced Features Showcase</h1>
                    <p class="lead">Experience the next-generation theme system with AI-powered features</p>
                </div>
                
                <!-- Feature Grid -->
                <div class="row g-4">
                    
                    <!-- Particle Effects Demo -->
                    <div class="col-lg-6">
                        <div class="premium-card h-100">
                            <div class="card-header">
                                <h3>✨ Interactive Particle Effects</h3>
                            </div>
                            <div class="card-body">
                                <p>Click anywhere on the screen to create explosions! Choose from multiple particle types:</p>
                                
                                <div class="particle-effects-selector mb-3">
                                    <button class="particle-effect-btn" onclick="switchParticleEffect('standard')">⚪ Standard</button>
                                    <button class="particle-effect-btn" onclick="switchParticleEffect('fireworks')">🎆 Fireworks</button>
                                    <button class="particle-effect-btn" onclick="switchParticleEffect('snow')">❄️ Snow</button>
                                </div>
                                <div class="particle-effects-selector mb-3">
                                    <button class="particle-effect-btn" onclick="switchParticleEffect('rain')">🌧️ Rain</button>
                                    <button class="particle-effect-btn" onclick="switchParticleEffect('stars')">⭐ Stars</button>
                                    <button class="particle-effect-btn" onclick="switchParticleEffect('bubbles')">🫧 Bubbles</button>
                                </div>
                                
                                <div class="d-flex gap-2 mt-3">
                                    <button class="btn btn-success" onclick="toggleParticles()">Toggle Effects</button>
                                    <button class="btn btn-info" onclick="createFireworkShow()">🎆 Firework Show</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Audio System Demo -->
                    <div class="col-lg-6">
                        <div class="premium-card h-100">
                            <div class="card-header">
                                <h3>🎵 Enhanced Audio System</h3>
                            </div>
                            <div class="card-body">
                                <p>Experience immersive audio feedback with volume control and visualization:</p>
                                
                                <div class="volume-control mb-3">
                                    <label>🔉 Master Volume:</label>
                                    <input type="range" class="volume-slider" min="0" max="1" step="0.1" value="0.5" 
                                           onchange="setVolume(this.value); updateVolumeDisplay(this.value)">
                                    <span id="masterVolumeDisplay">50%</span>
                                </div>
                                
                                <div class="d-flex gap-2 flex-wrap mb-3">
                                    <button class="btn btn-primary" onclick="window.themeManager.playSound('theme-change')">🎵 Theme Sound</button>
                                    <button class="btn btn-info" onclick="window.themeManager.playSound('notification')">🔔 Notification</button>
                                    <button class="btn btn-success" onclick="window.themeManager.playSound('click')">👆 Click Sound</button>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button class="btn btn-gradient" onclick="toggleAudioVisualizer()">📊 Audio Visualizer</button>
                                    <button class="btn btn-secondary" onclick="toggleAudio()">🔇 Toggle Audio</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Smart Features Demo -->
                    <div class="col-lg-6">
                        <div class="premium-card h-100">
                            <div class="card-header">
                                <h3>🧠 AI-Powered Smart Features</h3>
                            </div>
                            <div class="card-body">
                                <p>Intelligent theme suggestions and automatic optimization:</p>
                                
                                <div class="mb-3">
                                    <h5>🕐 Time-Based Suggestions</h5>
                                    <p class="small text-muted">Automatically suggests themes based on time of day</p>
                                    <button class="btn btn-info" onclick="showTimeBasedSuggestion()">Get Suggestion</button>
                                </div>
                                
                                <div class="mb-3">
                                    <h5>🎤 Voice Control</h5>
                                    <p class="small text-muted">Control themes with voice commands</p>
                                    <button class="btn btn-warning" onclick="enableVoiceControl()">Enable Voice Control</button>
                                </div>
                                
                                <div class="mb-3">
                                    <h5>⚡ Auto-Optimization</h5>
                                    <p class="small text-muted">Automatically optimizes performance based on device capabilities</p>
                                    <button class="btn btn-success" onclick="window.themeManager.optimizePerformance()">Optimize Now</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Performance Dashboard -->
                    <div class="col-lg-6">
                        <div class="premium-card h-100">
                            <div class="card-header">
                                <h3>📊 Real-Time Performance</h3>
                            </div>
                            <div class="card-body">
                                <p>Monitor system performance in real-time:</p>
                                
                                <div class="row text-center mb-3">
                                    <div class="col-6">
                                        <div class="metric-display">
                                            <div class="metric-value" id="liveFps">60</div>
                                            <div class="metric-label">FPS</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="metric-display">
                                            <div class="metric-value" id="liveMemory">0</div>
                                            <div class="metric-label">Memory (MB)</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row text-center mb-3">
                                    <div class="col-6">
                                        <div class="metric-display">
                                            <div class="metric-value" id="liveParticles">0</div>
                                            <div class="metric-label">Particles</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="metric-display">
                                            <div class="metric-value" id="liveThemeSwitches">0</div>
                                            <div class="metric-label">Theme Switches</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-center">
                                    <button class="btn btn-gradient" onclick="showPerformanceDashboard()">📊 Full Dashboard</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Accessibility Showcase -->
                    <div class="col-lg-6">
                        <div class="premium-card h-100">
                            <div class="card-header">
                                <h3>♿ Accessibility Excellence</h3>
                            </div>
                            <div class="card-body">
                                <p>Comprehensive accessibility features for all users:</p>
                                
                                <div class="accessibility-grid">
                                    <div class="accessibility-feature">
                                        <button class="btn btn-outline-primary w-100 mb-2" onclick="toggleHighContrast()">
                                            🔲 High Contrast Mode
                                        </button>
                                    </div>
                                    <div class="accessibility-feature">
                                        <button class="btn btn-outline-primary w-100 mb-2" onclick="toggleReducedMotion()">
                                            ⏸️ Reduced Motion
                                        </button>
                                    </div>
                                    <div class="accessibility-feature">
                                        <button class="btn btn-outline-primary w-100 mb-2" onclick="toggleFontSize()">
                                            🔤 Font Size Control
                                        </button>
                                    </div>
                                    <div class="accessibility-feature">
                                        <button class="btn btn-outline-primary w-100 mb-2" onclick="toggleColorBlindSupport()">
                                            👁️ Color Blind Support
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Theme Customization -->
                    <div class="col-lg-6">
                        <div class="premium-card h-100">
                            <div class="card-header">
                                <h3>🎨 Theme Customization</h3>
                            </div>
                            <div class="card-body">
                                <p>Create and share your own custom themes:</p>
                                
                                <div class="quick-themes mb-3">
                                    <h6>Quick Themes:</h6>
                                    <button class="btn btn-sm btn-outline-primary me-2" onclick="applyQuickTheme('ocean')">🌊 Ocean</button>
                                    <button class="btn btn-sm btn-outline-primary me-2" onclick="applyQuickTheme('sunset')">🌅 Sunset</button>
                                    <button class="btn btn-sm btn-outline-primary" onclick="applyQuickTheme('forest')">🌲 Forest</button>
                                </div>
                                
                                <div class="text-center">
                                    <button class="btn btn-gradient" onclick="showThemeCreator()">🎨 Custom Theme Creator</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Advanced Controls -->
                <div class="row mt-5">
                    <div class="col-12">
                        <div class="premium-card">
                            <div class="card-header">
                                <h3>⚙️ Advanced Controls</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <h5>🎮 Gaming Features</h5>
                                        <button class="btn btn-primary w-100 mb-2" onclick="enableGamingMode()">🎮 Gaming Mode</button>
                                        <button class="btn btn-secondary w-100 mb-2" onclick="toggleFullscreen()">📺 Fullscreen</button>
                                    </div>
                                    <div class="col-md-4">
                                        <h5>🔧 Developer Tools</h5>
                                        <button class="btn btn-info w-100 mb-2" onclick="showPerformanceDashboard()">📊 Performance</button>
                                        <button class="btn btn-warning w-100 mb-2" onclick="exportDebugInfo()">🐛 Debug Export</button>
                                    </div>
                                    <div class="col-md-4">
                                        <h5>💾 Data Management</h5>
                                        <button class="btn btn-success w-100 mb-2" onclick="exportSettings()">📤 Export All</button>
                                        <button class="btn btn-danger w-100 mb-2" onclick="resetToDefaults()">🔄 Factory Reset</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.metric-display {
    background: var(--card-bg);
    border: 2px solid var(--border-color);
    border-radius: var(--border-radius);
    padding: 1rem;
    transition: all 0.3s ease;
}

.metric-display:hover {
    border-color: var(--primary-color);
    transform: scale(1.05);
}

.metric-value {
    font-size: 2rem;
    font-weight: bold;
    color: var(--primary-color);
    font-family: monospace;
}

.metric-label {
    font-size: 0.9rem;
    color: var(--text-color);
    text-transform: uppercase;
    letter-spacing: 1px;
}

.accessibility-grid {
    display: grid;
    gap: 0.5rem;
}

.quick-themes h6 {
    color: var(--text-color);
    margin-bottom: 0.5rem;
}

.text-gradient {
    background: linear-gradient(45deg, var(--primary-color), var(--accent-color));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
</style>

<script>
// Quick theme presets
function applyQuickTheme(preset) {
    const themes = {
        ocean: {
            'primary-color': '#0077be',
            'bg-color': '#f0f8ff',
            'text-color': '#1e3a8a',
            'accent-color': '#06b6d4',
            'border-color': '#7dd3fc'
        },
        sunset: {
            'primary-color': '#f97316',
            'bg-color': '#fef3c7',
            'text-color': '#92400e',
            'accent-color': '#ef4444',
            'border-color': '#fbbf24'
        },
        forest: {
            'primary-color': '#16a34a',
            'bg-color': '#f0fdf4',
            'text-color': '#14532d',
            'accent-color': '#22c55e',
            'border-color': '#86efac'
        }
    };
    
    if (themes[preset]) {
        const themeId = createCustomTheme(`Quick ${preset.charAt(0).toUpperCase() + preset.slice(1)}`, themes[preset]);
        if (themeId) {
            applyCustomTheme(themeId);
        }
    }
}

// Gaming mode
function enableGamingMode() {
    // Switch to CS2 theme
    switchTheme('cs2');
    
    // Enable fireworks particles
    switchParticleEffect('fireworks');
    
    // Increase audio volume
    setVolume(0.8);
    
    // Enable audio visualizer
    toggleAudioVisualizer();
    
    window.themeManager.showNotification('🎮 Gaming mode activated! Ready to dominate!', 'success');
}

// Fullscreen toggle
function toggleFullscreen() {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen().then(() => {
            window.themeManager.showNotification('Entered fullscreen mode', 'info');
        });
    } else {
        document.exitFullscreen().then(() => {
            window.themeManager.showNotification('Exited fullscreen mode', 'info');
        });
    }
}

// Show theme creator modal
function showThemeCreator() {
    // Redirect to the test page with theme creator
    window.location.href = 'test-themes.php#theme-creator';
}

// Create firework show
function createFireworkShow() {
    if (!window.themeManager.particleSystem) return;
    
    switchParticleEffect('fireworks');
    
    // Create multiple firework bursts
    const bursts = 5;
    for (let i = 0; i < bursts; i++) {
        setTimeout(() => {
            const x = Math.random() * window.innerWidth;
            const y = Math.random() * window.innerHeight * 0.6;
            window.themeManager.createExplosion(x, y);
            window.themeManager.playSound('theme-change');
        }, i * 1000);
    }
    
    window.themeManager.showNotification('🎆 Firework show started!', 'success');
}

// Export debug information
function exportDebugInfo() {
    const debugInfo = {
        themeManager: window.themeManager ? 'Available' : 'Missing',
        userAgent: navigator.userAgent,
        platform: navigator.platform,
        languages: navigator.languages,
        cookieEnabled: navigator.cookieEnabled,
        onLine: navigator.onLine,
        hardwareConcurrency: navigator.hardwareConcurrency,
        deviceMemory: navigator.deviceMemory,
        connection: navigator.connection ? {
            effectiveType: navigator.connection.effectiveType,
            downlink: navigator.connection.downlink,
            rtt: navigator.connection.rtt
        } : null,
        screen: {
            width: screen.width,
            height: screen.height,
            colorDepth: screen.colorDepth,
            pixelDepth: screen.pixelDepth
        },
        window: {
            innerWidth: window.innerWidth,
            innerHeight: window.innerHeight,
            devicePixelRatio: window.devicePixelRatio
        },
        performance: window.themeManager ? window.themeManager.getPerformanceReport() : null,
        localStorage: {
            available: typeof Storage !== 'undefined',
            itemCount: localStorage.length
        },
        features: {
            canvas: !!window.CanvasRenderingContext2D,
            webGL: !!window.WebGLRenderingContext,
            audioContext: !!(window.AudioContext || window.webkitAudioContext),
            speechRecognition: !!window.SpeechRecognition || !!window.webkitSpeechRecognition,
            fullscreen: !!document.fullscreenEnabled,
            serviceWorker: 'serviceWorker' in navigator
        },
        timestamp: new Date().toISOString()
    };
    
    const dataStr = JSON.stringify(debugInfo, null, 2);
    const dataBlob = new Blob([dataStr], { type: 'application/json' });
    
    const link = document.createElement('a');
    link.href = URL.createObjectURL(dataBlob);
    link.download = `debug-info-${Date.now()}.json`;
    link.click();
    
    window.themeManager.showNotification('Debug information exported!', 'success');
}

// Show time-based suggestion
function showTimeBasedSuggestion() {
    const hour = new Date().getHours();
    let suggestion = '';
    let reason = '';
    
    if (hour >= 6 && hour < 12) {
        suggestion = 'light';
        reason = 'Morning time - light theme is easier on the eyes';
    } else if (hour >= 12 && hour < 18) {
        suggestion = 'premium';
        reason = 'Afternoon productivity - premium theme for focus';
    } else if (hour >= 18 && hour < 22) {
        suggestion = 'cs2';
        reason = 'Evening gaming time - CS2 theme for immersion';
    } else {
        suggestion = 'dark';
        reason = 'Night time - dark theme reduces eye strain';
    }
    
    const message = `💡 Suggested theme: ${suggestion} (${reason})`;
    window.themeManager.showNotification(message, 'info', 8000);
}

// Update volume display
function updateVolumeDisplay(value) {
    const display = document.getElementById('masterVolumeDisplay');
    if (display) {
        display.textContent = Math.round(value * 100) + '%';
    }
}

// Live performance updates
function updateLiveMetrics() {
    if (window.themeManager) {
        document.getElementById('liveFps').textContent = window.themeManager.performanceMetrics.fps;
        document.getElementById('liveMemory').textContent = Math.round(window.themeManager.performanceMetrics.memoryUsage / 1024 / 1024);
        document.getElementById('liveParticles').textContent = window.themeManager.particleSystem ? window.themeManager.particleSystem.particles.length : 0;
        document.getElementById('liveThemeSwitches').textContent = window.themeManager.performanceMetrics.themeSwitchCount;
    }
}

// Initialize showcase
document.addEventListener('DOMContentLoaded', () => {
    // Update live metrics every second
    setInterval(updateLiveMetrics, 1000);
    
    // Initial update
    setTimeout(updateLiveMetrics, 500);
    
    // Demo notification after 3 seconds
    setTimeout(() => {
        if (window.themeManager) {
            window.themeManager.showNotification('🎉 Welcome to the Advanced Features Showcase!', 'success');
        }
    }, 3000);
});
</script>

<?php include 'includes/footer.php'; ?>