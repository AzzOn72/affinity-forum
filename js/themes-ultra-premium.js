/**
 * Ultra Premium Theme Manager
 * Advanced theme system with particle effects, audio feedback, and accessibility features
 */

console.log('🚀 themes-ultra-premium.js is loading...');

// Check if required APIs are available
if (!window.CanvasRenderingContext2D) {
    console.warn('⚠️ Canvas not supported, particle system will be disabled');
}

if (!window.AudioContext && !window.webkitAudioContext) {
    console.warn('⚠️ Audio context not supported, audio features will be disabled');
}

class UltraPremiumThemeManager {
    constructor() {
        this.currentTheme = 'light';
        this.currentDevice = 'desktop';
        this.particleSystem = null;
        this.audioContext = null;
        this.isInitialized = false;
        this.animations = new Map();
        this.notifications = [];
        this.performanceMetrics = {
            themeSwitchCount: 0,
            deviceSwitchCount: 0,
            lastThemeSwitch: 0,
            lastDeviceSwitch: 0,
            totalNotifications: 0
        };
        this.accessibility = {
            highContrast: false,
            reducedMotion: false,
            fontSize: 16,
            screenReader: false
        };
        this.features = {
            particles: true,
            audio: true,
            animations: true,
            notifications: true,
            shortcuts: true
        };
        
        console.log('🎨 Ultra Premium Theme Manager initialized');
        // Don't call init() in constructor - wait for DOM
    }

    init() {
        try {
            this.loadUserPreferences();
            this.setupAccessibility();
            this.setupParticleSystem();
            this.setupAudioContext();
            this.setupEventListeners();
            this.setupPerformanceMonitoring();
            this.applyCurrentTheme();
            this.isInitialized = true;
            console.log('✅ Ultra Premium Theme Manager fully initialized');
            
            // Check if this is the first visit and show device selector
            this.checkFirstVisit();
            
            // Show welcome notification
            this.showNotification('🎉 Welcome to Ultra Premium Theme System!', 'success', 3000);
        } catch (error) {
            console.error('❌ Error initializing theme manager:', error);
            this.showNotification('Failed to initialize theme system', 'error');
        }
    }

    loadUserPreferences() {
        try {
            // Load theme preference with fallback
            const savedTheme = localStorage.getItem('selectedTheme');
            if (savedTheme && ['light', 'dark', 'cs2', 'premium'].includes(savedTheme)) {
                this.currentTheme = savedTheme;
            } else {
                this.currentTheme = 'light';
                localStorage.setItem('selectedTheme', 'light');
            }
            
            // Load device preference with fallback
            const savedDevice = localStorage.getItem('selectedDevice');
            if (savedDevice && ['desktop', 'tablet', 'mobile'].includes(savedDevice)) {
                this.currentDevice = savedDevice;
            } else {
                this.currentDevice = 'desktop';
                localStorage.setItem('selectedDevice', 'desktop');
            }
            
            // Load accessibility preferences
            this.accessibility.highContrast = localStorage.getItem('highContrast') === 'true';
            this.accessibility.reducedMotion = localStorage.getItem('reducedMotion') === 'true';
            this.accessibility.fontSize = parseInt(localStorage.getItem('fontSize')) || 16;
            this.accessibility.screenReader = localStorage.getItem('screenReader') === 'true';
            
            // Load feature preferences
            this.features.particles = localStorage.getItem('particles') !== 'false';
            this.features.audio = localStorage.getItem('audio') !== 'false';
            this.features.animations = localStorage.getItem('animations') !== 'false';
            this.features.notifications = localStorage.getItem('notifications') !== 'false';
            this.features.shortcuts = localStorage.getItem('shortcuts') !== 'false';
            
            console.log('📱 Loaded preferences - Theme:', this.currentTheme, 'Device:', this.currentDevice);
        } catch (error) {
            console.warn('⚠️ Failed to load preferences, using defaults:', error);
        }
    }
    
    checkFirstVisit() {
        // Check if user has ever selected a device
        const hasSelectedDevice = localStorage.getItem('hasSelectedDevice');
        
        if (!hasSelectedDevice) {
            console.log('🎯 First visit detected - showing device selector');
            // Wait for DOM to be ready before showing device selector
            if (document.body) {
                // Show device selector after a short delay
                setTimeout(() => {
                    this.showDeviceSelector();
                    
                    // Monitor the overlay to see if it disappears - DISABLED
                    // this.monitorOverlayVisibility();
                }, 1000); // 1 second delay
            } else {
                // DOM not ready, wait and try again
                setTimeout(() => this.checkFirstVisit(), 100);
            }
        } else {
            console.log('✅ Returning user - device already selected');
        }
    }
    
    monitorOverlayVisibility() {
        const overlay = document.getElementById('deviceOverlay');
        if (overlay) {
            // Override any attempts to hide the overlay
            const originalStyleSetter = Object.getOwnPropertyDescriptor(HTMLElement.prototype, 'style');
            const originalDisplaySetter = Object.getOwnPropertyDescriptor(CSSStyleDeclaration.prototype, 'display');
            
            // Prevent display from being set to 'none'
            if (originalDisplaySetter && originalDisplaySetter.set) {
                const originalSet = originalDisplaySetter.set;
                Object.defineProperty(overlay.style, 'display', {
                    set: function(value) {
                        if (value === 'none') {
                            console.log('🚫 BLOCKED: Attempt to hide overlay with display: none');
                            value = 'flex';
                        }
                        originalSet.call(this, value);
                    },
                    get: originalDisplaySetter.get,
                    configurable: true
                });
            }
            
            // Check every 50ms if the overlay is still visible
            const checkInterval = setInterval(() => {
                const computedStyle = getComputedStyle(overlay);
                console.log('🔍 Overlay visibility check:', {
                    display: computedStyle.display,
                    visibility: computedStyle.visibility,
                    opacity: computedStyle.opacity,
                    zIndex: computedStyle.zIndex
                });
                
                // If overlay disappeared, force it back
                if (computedStyle.display === 'none' || computedStyle.visibility === 'hidden' || computedStyle.opacity === '0') {
                    console.log('⚠️ Overlay disappeared! Forcing it back...');
                    overlay.setAttribute('style', `
                        display: flex !important;
                        visibility: visible !important;
                        opacity: 1 !important;
                        z-index: 9999 !important;
                        position: fixed !important;
                        top: 0 !important;
                        left: 0 !important;
                        right: 0 !important;
                        bottom: 0 !important;
                        background: rgba(0, 0, 0, 0.9) !important;
                        backdrop-filter: blur(20px) !important;
                        align-items: center !important;
                        justify-content: center !important;
                        pointer-events: auto !important;
                    `);
                    overlay.classList.add('force-visible', 'device-overlay-active');
                }
            }, 50); // Check every 50ms
            
            // Stop monitoring after 30 seconds
            setTimeout(() => {
                clearInterval(checkInterval);
                console.log('🔍 Stopped monitoring overlay visibility');
                
                // Restore original style setter
                if (originalStyleSetter && originalStyleSetter.set) {
                    Object.defineProperty(HTMLElement.prototype, 'style', originalStyleSetter);
                }
            }, 30000);
        }
    }
    
    showDeviceSelector() {
        console.log('🎯 Showing device selector for first visit');
        const overlay = document.getElementById('deviceOverlay');
        if (overlay) {
            // Simple approach - just show the overlay
            overlay.style.display = 'flex';
            overlay.classList.add('force-visible');
            
            console.log('✅ Device selector shown for first visit');
        } else {
            console.error('❌ Device overlay not found');
        }
    }

    setupAccessibility() {
        try {
            // Wait for DOM to be ready
            if (!document.body) {
                console.log('⚠️ Body not ready yet, will setup accessibility later');
                setTimeout(() => this.setupAccessibility(), 100);
                return;
            }
            
            // Apply accessibility settings
            if (this.accessibility.highContrast) {
                document.body.classList.add('high-contrast');
            }
            if (this.accessibility.reducedMotion) {
                document.body.classList.add('reduced-motion');
            }
            if (this.accessibility.screenReader) {
                document.body.classList.add('screen-reader');
            }
            
            // Apply font size
            document.documentElement.style.fontSize = this.accessibility.fontSize + 'px';
            
            console.log('♿ Accessibility features configured');
        } catch (error) {
            console.warn('⚠️ Failed to setup accessibility:', error);
        }
    }

    // Particle system setup moved to enhanced version below

    setupAudioContext() {
        if (!this.features.audio || !window.AudioContext) return;
        
        try {
            this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
            
            // Resume audio context on user interaction
            const resumeAudio = () => {
                if (this.audioContext.state === 'suspended') {
                    this.audioContext.resume();
                }
                document.removeEventListener('click', resumeAudio);
                document.removeEventListener('keydown', resumeAudio);
            };
            
            document.addEventListener('click', resumeAudio);
            document.addEventListener('keydown', resumeAudio);
            
            console.log('🔊 Audio context initialized');
        } catch (error) {
            console.warn('⚠️ Failed to setup audio context:', error);
        }
    }

    // Play theme change sound
    playThemeChangeSound() {
        if (!this.audioContext || this.audioContext.state !== 'running') return;
        
        try {
            const oscillator = this.audioContext.createOscillator();
            const gainNode = this.audioContext.createGain();
            const filter = this.audioContext.createBiquadFilter();
            
            oscillator.connect(filter);
            filter.connect(gainNode);
            gainNode.connect(this.audioContext.destination);
            
            // Set up filter
            filter.type = 'lowpass';
            filter.frequency.setValueAtTime(800, this.audioContext.currentTime);
            filter.frequency.exponentialRampToValueAtTime(200, this.audioContext.currentTime + 0.5);
            
            // Set up oscillator
            oscillator.frequency.setValueAtTime(440, this.audioContext.currentTime);
            oscillator.frequency.exponentialRampToValueAtTime(880, this.audioContext.currentTime + 0.3);
            oscillator.type = 'sine';
            
            // Set up gain envelope
            gainNode.gain.setValueAtTime(0, this.audioContext.currentTime);
            gainNode.gain.linearRampToValueAtTime(0.3, this.audioContext.currentTime + 0.1);
            gainNode.gain.exponentialRampToValueAtTime(0.01, this.audioContext.currentTime + 0.5);
            
            oscillator.start(this.audioContext.currentTime);
            oscillator.stop(this.audioContext.currentTime + 0.5);
            
        } catch (error) {
            console.warn('⚠️ Failed to play theme change sound:', error);
        }
    }

    // Play device selection sound
    playDeviceSelectionSound() {
        if (!this.audioContext || this.audioContext.state !== 'running') return;
        
        try {
            const oscillator = this.audioContext.createOscillator();
            const gainNode = this.audioContext.createGain();
            const filter = this.audioContext.createBiquadFilter();
            
            oscillator.connect(filter);
            filter.connect(gainNode);
            gainNode.connect(this.audioContext.destination);
            
            // Set up filter
            filter.type = 'highpass';
            filter.frequency.setValueAtTime(200, this.audioContext.currentTime);
            filter.frequency.exponentialRampToValueAtTime(800, this.audioContext.currentTime + 0.3);
            
            // Set up oscillator
            oscillator.frequency.setValueAtTime(220, this.audioContext.currentTime);
            oscillator.frequency.exponentialRampToValueAtTime(440, this.audioContext.currentTime + 0.2);
            oscillator.type = 'square';
            
            // Set up gain envelope
            gainNode.gain.setValueAtTime(0, this.audioContext.currentTime);
            gainNode.gain.linearRampToValueAtTime(0.2, this.audioContext.currentTime + 0.05);
            gainNode.gain.exponentialRampToValueAtTime(0.01, this.audioContext.currentTime + 0.3);
            
            oscillator.start(this.audioContext.currentTime);
            oscillator.stop(this.audioContext.currentTime + 0.3);
            
        } catch (error) {
            console.warn('⚠️ Failed to play device selection sound:', error);
        }
    }

    setupEventListeners() {
        try {
            // Theme switching with better event delegation
            document.addEventListener('click', (e) => {
                const themeButton = e.target.closest('[data-theme]');
                if (themeButton) {
                    e.preventDefault();
                    const theme = themeButton.dataset.theme;
                    this.switchTheme(theme);
                }
            });

            // Device selection with better event delegation
            document.addEventListener('click', (e) => {
                const deviceOption = e.target.closest('.device-option');
                if (deviceOption) {
                    e.preventDefault();
                    const device = deviceOption.dataset.device;
                    this.selectDevice(device);
                }
            });

            // Enhanced keyboard shortcuts
            if (this.features.shortcuts) {
                document.addEventListener('keydown', (e) => {
                    if (e.ctrlKey || e.metaKey) {
                        e.preventDefault();
                        switch(e.key) {
                            case '1': this.switchTheme('light'); break;
                            case '2': this.switchTheme('dark'); break;
                            case '3': this.switchTheme('cs2'); break;
                            case '4': this.switchTheme('premium'); break;
                            case 'd': this.selectDevice('desktop'); break;
                            case 't': this.selectDevice('tablet'); break;
                            case 'm': this.selectDevice('mobile'); break;
                            case 'h': this.toggleHighContrast(); break;
                            case 'r': this.toggleReducedMotion(); break;
                            case 'f': this.toggleFontSize(); break;
                            case 'p': this.toggleParticles(); break;
                            case 'a': this.toggleAudio(); break;
                        }
                    }
                });
            }
            
            // Keyboard shortcuts
            document.addEventListener('keydown', (e) => {
                if (e.ctrlKey) {
                    switch (e.key.toLowerCase()) {
                        case 't':
                            e.preventDefault();
                            const themes = ['light', 'dark', 'cs2', 'premium'];
                            const currentIndex = themes.indexOf(this.currentTheme);
                            const nextIndex = (currentIndex + 1) % themes.length;
                            this.switchTheme(themes[nextIndex]);
                            break;
                        case 'd':
                            e.preventDefault();
                            const devices = ['desktop', 'tablet', 'mobile'];
                            const currentDeviceIndex = devices.indexOf(this.currentDevice);
                            const nextDeviceIndex = (currentDeviceIndex + 1) % devices.length;
                            this.selectDevice(devices[nextDeviceIndex]);
                            break;
                        case 'h':
                            e.preventDefault();
                            this.toggleHighContrast();
                            break;
                        case 'm':
                            e.preventDefault();
                            this.toggleReducedMotion();
                            break;
                        case 'f':
                            e.preventDefault();
                            this.toggleFontSize();
                            break;
                        case 'p':
                            e.preventDefault();
                            this.toggleParticles();
                            break;
                        case 'a':
                            e.preventDefault();
                            this.toggleAudio();
                            break;
                        case 's':
                            e.preventDefault();
                            this.exportSettings();
                            break;
                        case 'i':
                            e.preventDefault();
                            // Trigger file input for import
                            const fileInput = document.createElement('input');
                            fileInput.type = 'file';
                            fileInput.accept = '.json';
                            fileInput.onchange = (event) => {
                                if (event.target.files[0]) {
                                    this.importSettings(event.target.files[0]);
                                }
                            };
                            fileInput.click();
                            break;
                        case 'r':
                            e.preventDefault();
                            this.resetToDefaults();
                            break;
                    }
                }
                
                // F1 key for shortcuts help
                if (e.key === 'F1') {
                    e.preventDefault();
                    window.showShortcutsHelp();
                }
            });
            
            // Performance monitoring
            window.addEventListener('beforeunload', () => {
                this.savePerformanceMetrics();
            });
            
            console.log('🎯 Event listeners configured');
        } catch (error) {
            console.error('❌ Failed to setup event listeners:', error);
        }
    }

    // Particle animation moved to enhanced version below

    // Theme switching with enhanced effects
    switchTheme(themeName) {
        if (this.currentTheme === themeName) return;
        
        try {
            const startTime = performance.now();
            
            // Update current theme
            this.currentTheme = themeName;
            localStorage.setItem('selectedTheme', themeName);
            
            // Apply theme
            this.applyCurrentTheme();
            
            // Update particle colors
            this.updateParticleColors();
            
            // Play sound effect
            this.playThemeChangeSound();
            
            // Update performance metrics
            const switchTime = performance.now() - startTime;
            this.performanceMetrics.lastThemeSwitch = switchTime;
            this.performanceMetrics.themeSwitchCount++;
            
            // Show notification
            this.showNotification(`Theme switched to ${themeName}!`, 'success');
            
            // Dispatch event
            document.dispatchEvent(new CustomEvent('themeChanged', {
                detail: { theme: themeName, switchTime }
            }));
            
            console.log(`🎨 Theme switched to ${themeName} in ${switchTime.toFixed(2)}ms`);
        } catch (error) {
            console.error('❌ Failed to switch theme:', error);
            this.showNotification('Failed to switch theme!', 'error');
        }
    }

    // Update particle colors based on current theme
    updateParticleColors() {
        if (!this.particleSystem || !this.particleSystem.particles) return;
        
        try {
            this.particleSystem.particles.forEach(particle => {
                particle.color = this.getRandomThemeColor();
            });
            
            console.log('✨ Particle colors updated for new theme');
        } catch (error) {
            console.warn('⚠️ Failed to update particle colors:', error);
        }
    }

    // Device selection with enhanced effects
    selectDevice(device) {
        if (this.currentDevice === device) return;
        
        try {
            const startTime = performance.now();
            
            // Update current device
            this.currentDevice = device;
            localStorage.setItem('selectedDevice', device);
            
            // Apply device-specific styles
            document.body.setAttribute('data-device', device);
            
            // Update particle system for device
            this.updateParticleSystemForDevice(device);
            
            // Play sound effect
            this.playDeviceSelectionSound();
            
            // Update performance metrics
            const switchTime = performance.now() - startTime;
            this.performanceMetrics.lastDeviceSwitch = switchTime;
            this.performanceMetrics.deviceSwitchCount++;
            
            // Show notification
            this.showNotification(`Device mode switched to ${device}!`, 'success');
            
            // Mark as having selected a device
            localStorage.setItem('hasSelectedDevice', 'true');
            
            // DON'T hide the overlay automatically - let user close it manually
            // const overlay = document.getElementById('deviceOverlay');
            // if (overlay) {
            //     overlay.style.display = 'none';
            //     console.log('✅ Device overlay hidden after selection');
            // }
            console.log('✅ Device selected - overlay remains visible for manual closing');
            
            // Dispatch event
            document.dispatchEvent(new CustomEvent('deviceChanged', {
                detail: { device, switchTime }
            }));
            
            console.log(`📱 Device switched to ${device} in ${switchTime.toFixed(2)}ms`);
        } catch (error) {
            console.error('❌ Failed to switch device:', error);
            this.showNotification('Failed to switch device mode!', 'error');
        }
    }

    // Update particle system for device
    updateParticleSystemForDevice(device) {
        if (!this.particleSystem) return;
        
        try {
            switch (device) {
                case 'mobile':
                    this.particleSystem.particleCount = 20;
                    this.particleSystem.maxParticles = 40;
                    break;
                case 'tablet':
                    this.particleSystem.particleCount = 35;
                    this.particleSystem.maxParticles = 70;
                    break;
                case 'desktop':
                default:
                    this.particleSystem.particleCount = 50;
                    this.particleSystem.maxParticles = 100;
                    break;
            }
            
            console.log(`📱 Particle system optimized for ${device}`);
        } catch (error) {
            console.warn('⚠️ Failed to update particle system for device:', error);
        }
    }

    // Apply current theme and device
    applyCurrentTheme() {
        try {
            console.log('🎨 Applying current theme:', this.currentTheme);
            
            // Wait for DOM to be ready
            if (!document.body) {
                console.log('⚠️ Body not ready yet, will apply theme later');
                setTimeout(() => this.applyCurrentTheme(), 100);
                return;
            }
            
            // Don't apply theme if device selector is showing
            const overlay = document.getElementById('deviceOverlay');
            if (overlay && overlay.style.display === 'flex') {
                console.log('⚠️ Device selector is showing, skipping theme application');
                return;
            }
            
            // Remove all theme classes
            document.body.classList.remove('theme-light', 'theme-dark', 'theme-cs2', 'theme-premium');
            
            // Add current theme class
            document.body.classList.add(`theme-${this.currentTheme}`);
            document.body.setAttribute('data-theme', this.currentTheme);
            
            // Apply device-specific styles
            document.body.setAttribute('data-device', this.currentDevice);
            
            // Update CSS custom properties
            this.updateCSSVariables();
            
            // Update particle system colors
            if (this.particleSystem) {
                this.updateParticleColors();
            }
            
            console.log(`🎨 Applied theme: ${this.currentTheme}, device: ${this.currentDevice}`);
        } catch (error) {
            console.error('❌ Failed to apply current theme:', error);
        }
    }

    // Update CSS custom properties
    updateCSSVariables() {
        try {
            const root = document.documentElement;
            console.log('🎨 Updating CSS variables for theme:', this.currentTheme);
            
            // Get theme colors
            const themeColors = this.getThemeColors(this.currentTheme);
            console.log('🎨 Theme colors:', themeColors);
            
            // Apply theme colors
            Object.entries(themeColors).forEach(([key, value]) => {
                root.style.setProperty(`--${key}`, value);
                console.log(`🎨 Set --${key}: ${value}`);
            });
            
            // Apply device-specific variables
            const deviceVars = this.getDeviceVariables(this.currentDevice);
            console.log('📱 Device variables:', deviceVars);
            
            Object.entries(deviceVars).forEach(([key, value]) => {
                root.style.setProperty(`--${key}`, value);
                console.log(`📱 Set --${key}: ${value}`);
            });
            
            console.log('✅ CSS variables updated successfully');
        } catch (error) {
            console.warn('⚠️ Failed to update CSS variables:', error);
        }
    }

    // Get theme colors - using the correct CSS variable names
    getThemeColors(theme) {
        const themes = {
            light: {
                'accent-primary': '#007bff',
                'accent-primary-rgb': '0, 123, 255',
                'accent-secondary': '#6c757d',
                'accent-secondary-rgb': '108, 117, 125',
                'accent-success': '#28a745',
                'accent-success-rgb': '40, 167, 69',
                'accent-warning': '#ffc107',
                'accent-warning-rgb': '255, 193, 7',
                'accent-danger': '#dc3545',
                'accent-danger-rgb': '220, 53, 69',
                'accent-info': '#17a2b8',
                'accent-info-rgb': '23, 162, 184',
                'bg-primary': '#ffffff',
                'bg-secondary': '#f8f9fa',
                'bg-tertiary': '#e9ecef',
                'text-primary': '#212529',
                'text-secondary': '#6c757d',
                'text-muted': '#adb5bd',
                'border-primary': '#dee2e6',
                'border-secondary': '#ced4da',
                'border-accent': '#007bff',
                'shadow-light': 'rgba(0, 0, 0, 0.1)',
                'shadow-medium': 'rgba(0, 0, 0, 0.15)',
                'shadow-heavy': 'rgba(0, 0, 0, 0.25)'
            },
            dark: {
                'accent-primary': '#0d6efd',
                'accent-primary-rgb': '13, 110, 253',
                'accent-secondary': '#6c757d',
                'accent-secondary-rgb': '108, 117, 125',
                'accent-success': '#198754',
                'accent-success-rgb': '25, 135, 84',
                'accent-warning': '#ffc107',
                'accent-warning-rgb': '255, 193, 7',
                'accent-danger': '#dc3545',
                'accent-danger-rgb': '220, 53, 69',
                'accent-info': '#17a2b8',
                'accent-info-rgb': '23, 162, 184',
                'bg-primary': '#212529',
                'bg-secondary': '#343a40',
                'bg-tertiary': '#495057',
                'text-primary': '#f8f9fa',
                'text-secondary': '#e9ecef',
                'text-muted': '#adb5bd',
                'border-primary': '#495057',
                'border-secondary': '#6c757d',
                'border-accent': '#0d6efd',
                'shadow-light': 'rgba(0, 0, 0, 0.3)',
                'shadow-medium': 'rgba(0, 0, 0, 0.4)',
                'shadow-heavy': 'rgba(0, 0, 0, 0.5)'
            },
            cs2: {
                'accent-primary': '#ff6b35',
                'accent-primary-rgb': '255, 107, 53',
                'accent-secondary': '#2c3e50',
                'accent-secondary-rgb': '44, 62, 80',
                'accent-success': '#e74c3c',
                'accent-success-rgb': '231, 76, 60',
                'accent-warning': '#f39c12',
                'accent-warning-rgb': '243, 156, 18',
                'accent-danger': '#e74c3c',
                'accent-danger-rgb': '231, 76, 60',
                'accent-info': '#3498db',
                'accent-info-rgb': '52, 152, 219',
                'bg-primary': '#1a1a1a',
                'bg-secondary': '#2c2c2c',
                'bg-tertiary': '#3a3a3a',
                'text-primary': '#ecf0f1',
                'text-secondary': '#bdc3c7',
                'text-muted': '#95a5a6',
                'border-primary': '#34495e',
                'border-secondary': '#2c3e50',
                'border-accent': '#ff6b35',
                'shadow-light': 'rgba(0, 0, 0, 0.4)',
                'shadow-medium': 'rgba(0, 0, 0, 0.5)',
                'shadow-heavy': 'rgba(0, 0, 0, 0.6)'
            },
            premium: {
                'accent-primary': '#d4af37',
                'accent-primary-rgb': '212, 175, 55',
                'accent-secondary': '#2c3e50',
                'accent-secondary-rgb': '44, 62, 80',
                'accent-success': '#e74c3c',
                'accent-success-rgb': '231, 76, 60',
                'accent-warning': '#f39c12',
                'accent-warning-rgb': '243, 156, 18',
                'accent-danger': '#e74c3c',
                'accent-danger-rgb': '231, 76, 60',
                'accent-info': '#3498db',
                'accent-info-rgb': '52, 152, 219',
                'bg-primary': '#1a1a1a',
                'bg-secondary': '#2c2c2c',
                'bg-tertiary': '#3a3a3a',
                'text-primary': '#f8f9fa',
                'text-secondary': '#e9ecef',
                'text-muted': '#adb5bd',
                'border-primary': '#d4af37',
                'border-secondary': '#2c3e50',
                'border-accent': '#d4af37',
                'shadow-light': 'rgba(212, 175, 55, 0.2)',
                'shadow-medium': 'rgba(212, 175, 55, 0.3)',
                'shadow-heavy': 'rgba(212, 175, 55, 0.4)'
            }
        };
        
        return themes[theme] || themes.light;
    }

    // Get device variables - using the correct CSS variable names
    getDeviceVariables(device) {
        const devices = {
            desktop: {
                'spacing-xs': '0.25rem',
                'spacing-sm': '0.5rem',
                'spacing-md': '1rem',
                'spacing-lg': '1.5rem',
                'spacing-xl': '3rem',
                'radius-sm': '0.25rem',
                'radius-md': '0.5rem',
                'radius-lg': '1rem',
                'radius-xl': '1.5rem'
            },
            tablet: {
                'spacing-xs': '0.2rem',
                'spacing-sm': '0.4rem',
                'spacing-md': '0.8rem',
                'spacing-lg': '1.2rem',
                'spacing-xl': '2.5rem',
                'radius-sm': '0.2rem',
                'radius-md': '0.4rem',
                'radius-lg': '0.8rem',
                'radius-xl': '1.2rem'
            },
            mobile: {
                'spacing-xs': '0.15rem',
                'spacing-sm': '0.3rem',
                'spacing-md': '0.6rem',
                'spacing-lg': '1rem',
                'spacing-xl': '2rem',
                'radius-sm': '0.15rem',
                'radius-md': '0.3rem',
                'radius-lg': '0.6rem',
                'radius-xl': '1rem'
            }
        };
        
        return devices[device] || devices.desktop;
    }

    // Auto-save and recovery
    autoSave() {
        try {
            const autoSaveData = {
                theme: this.currentTheme,
                device: this.currentDevice,
                accessibility: this.accessibility,
                features: this.features,
                timestamp: Date.now()
            };
            
            localStorage.setItem('autoSave', JSON.stringify(autoSaveData));
            
            // Show auto-save indicator
            this.showAutoSaveIndicator();
            
        } catch (error) {
            console.warn('⚠️ Failed to auto-save:', error);
        }
    }

    // Auto-recover settings
    autoRecover() {
        try {
            const autoSaveData = localStorage.getItem('autoSave');
            if (autoSaveData) {
                const data = JSON.parse(autoSaveData);
                const timeDiff = Date.now() - data.timestamp;
                
                // Only recover if auto-save is less than 1 hour old
                if (timeDiff < 3600000) {
                    this.currentTheme = data.theme || 'light';
                    this.currentDevice = data.device || 'desktop';
                    this.accessibility = data.accessibility || this.accessibility;
                    this.features = data.features || this.features;
                    
                    this.applyCurrentTheme();
                    this.setupAccessibility();
                    
                    console.log('🔄 Auto-recovered settings');
                }
            }
        } catch (error) {
            console.warn('⚠️ Failed to auto-recover:', error);
        }
    }

    // Show auto-save indicator
    showAutoSaveIndicator() {
        let indicator = document.getElementById('autoSaveIndicator');
        
        if (!indicator) {
            indicator = document.createElement('div');
            indicator.id = 'autoSaveIndicator';
            indicator.className = 'auto-save-indicator';
            indicator.innerHTML = `
                <span class="icon">💾</span>
                <span>Settings auto-saved</span>
            `;
            document.body.appendChild(indicator);
        }
        
        indicator.classList.add('show');
        
        setTimeout(() => {
            indicator.classList.remove('show');
        }, 2000);
    }

    // Enhanced particle system
    setupParticleSystem() {
        if (!this.features.particles || !window.CanvasRenderingContext2D) return;
        
        try {
            const canvas = document.createElement('canvas');
            canvas.id = 'particleCanvas';
            canvas.className = 'particle-system';
            canvas.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                pointer-events: none;
                z-index: 9999;
                opacity: 0.3;
                transition: opacity 0.3s ease;
            `;
            
            document.body.appendChild(canvas);
            
            const ctx = canvas.getContext('2d');
            if (!ctx) return;
            
            // Resize canvas
            const resizeCanvas = () => {
                canvas.width = window.innerWidth;
                canvas.height = window.innerHeight;
            };
            
            resizeCanvas();
            window.addEventListener('resize', resizeCanvas);
            
            this.particleSystem = {
                canvas: canvas,
                ctx: ctx,
                particles: [],
                isActive: true,
                particleCount: navigator.hardwareConcurrency > 4 ? 50 : 25,
                maxParticles: navigator.hardwareConcurrency > 4 ? 100 : 50,
                animationId: null
            };
            
            // Initialize particles
            this.initParticles();
            
            // Start animation
            this.startParticleAnimation();
            
            console.log('✨ Particle system initialized');
        } catch (error) {
            console.warn('⚠️ Failed to setup particle system:', error);
        }
    }

    // Initialize particles with enhanced properties
    initParticles() {
        if (!this.particleSystem) return;
        
        const { ctx, particles, particleCount, maxParticles } = this.particleSystem;
        
        for (let i = 0; i < particleCount; i++) {
            particles.push({
                x: Math.random() * ctx.canvas.width,
                y: Math.random() * ctx.canvas.height,
                vx: (Math.random() - 0.5) * 2,
                vy: (Math.random() - 0.5) * 2,
                size: Math.random() * 3 + 1,
                opacity: Math.random() * 0.5 + 0.3,
                color: this.getRandomThemeColor(),
                pulse: Math.random() * Math.PI * 2,
                pulseSpeed: Math.random() * 0.02 + 0.01,
                life: Math.random() * 100 + 50,
                maxLife: 150
            });
        }
    }

    // Get random theme color
    getRandomThemeColor() {
        const colors = [
            'var(--primary-color)',
            'var(--secondary-color)',
            'var(--accent-color)',
            'var(--text-color)'
        ];
        return colors[Math.floor(Math.random() * colors.length)];
    }

    // Get theme color for particles
    getThemeColor(themeName) {
        const themeColors = {
            light: '#007bff',
            dark: '#0d6efd',
            cs2: '#ff6b35',
            premium: '#d4af37'
        };
        return themeColors[themeName] || themeColors.light;
    }

    // Enhanced particle animation
    startParticleAnimation() {
        if (!this.particleSystem || !this.particleSystem.isActive) return;
        
        const animate = () => {
            if (!this.particleSystem.isActive) return;
            
            const { ctx, particles, maxParticles } = this.particleSystem;
            
            // Clear canvas with fade effect
            ctx.fillStyle = 'rgba(0, 0, 0, 0.05)';
            ctx.fillRect(0, 0, ctx.canvas.width, ctx.canvas.height);
            
            // Update and draw particles
            for (let i = particles.length - 1; i >= 0; i--) {
                const particle = particles[i];
                
                // Update position
                particle.x += particle.vx;
                particle.y += particle.vy;
                
                // Update pulse
                particle.pulse += particle.pulseSpeed;
                
                // Update life
                particle.life--;
                
                // Remove dead particles
                if (particle.life <= 0 || 
                    particle.x < 0 || particle.x > ctx.canvas.width ||
                    particle.y < 0 || particle.y > ctx.canvas.height) {
                    particles.splice(i, 1);
                    continue;
                }
                
                // Draw particle with enhanced effects
                ctx.save();
                ctx.globalAlpha = particle.opacity * (particle.life / particle.maxLife);
                
                // Pulse effect
                const pulseScale = 1 + Math.sin(particle.pulse) * 0.3;
                const size = particle.size * pulseScale;
                
                // Gradient fill
                const gradient = ctx.createRadialGradient(
                    particle.x, particle.y, 0,
                    particle.x, particle.y, size
                );
                gradient.addColorStop(0, particle.color);
                gradient.addColorStop(1, 'transparent');
                
                ctx.fillStyle = gradient;
                ctx.beginPath();
                ctx.arc(particle.x, particle.y, size, 0, Math.PI * 2);
                ctx.fill();
                
                // Glow effect
                ctx.shadowColor = particle.color;
                ctx.shadowBlur = size * 2;
                ctx.beginPath();
                ctx.arc(particle.x, particle.y, size * 0.5, 0, Math.PI * 2);
                ctx.fill();
                
                ctx.restore();
            }
            
            // Add new particles if needed
            if (particles.length < maxParticles && Math.random() < 0.1) {
                this.addNewParticle();
            }
            
            this.particleSystem.animationId = requestAnimationFrame(animate);
        };
        
        animate();
    }

    // Add new particle
    addNewParticle() {
        if (!this.particleSystem) return;
        
        const { ctx, particles } = this.particleSystem;
        
        particles.push({
            x: Math.random() * ctx.canvas.width,
            y: Math.random() * ctx.canvas.height,
            vx: (Math.random() - 0.5) * 2,
            vy: (Math.random() - 0.5) * 2,
            size: Math.random() * 3 + 1,
            opacity: Math.random() * 0.5 + 0.3,
            color: this.getRandomThemeColor(),
            pulse: Math.random() * Math.PI * 2,
            pulseSpeed: Math.random() * 0.02 + 0.01,
            life: Math.random() * 100 + 50,
            maxLife: 150
        });
    }

    // Enhanced notification system
    showNotification(message, type = 'info', duration = 5000) {
        if (!this.features.notifications) return;
        
        try {
            const notification = document.createElement('div');
            notification.className = `notification notification-${type} premium-card`;
            notification.innerHTML = `
                <div class="notification-content">
                    <div class="notification-icon">${this.getNotificationIcon(type)}</div>
                    <div class="notification-message">${message}</div>
                    <button class="notification-close" onclick="this.parentElement.parentElement.remove()">×</button>
                </div>
                <div class="notification-progress"></div>
            `;
            
            // Add to container
            const container = document.getElementById('notificationContainer');
            if (container) {
                container.appendChild(notification);
                
                // Limit notifications
                if (container.children.length > 5) {
                    container.removeChild(container.firstChild);
                }
                
                // Auto-remove after duration
                if (duration > 0) {
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.remove();
                        }
                    }, duration);
                }
                
                // Progress bar animation
                const progress = notification.querySelector('.notification-progress');
                if (progress && duration > 0) {
                    progress.style.transition = `width ${duration}ms linear`;
                    setTimeout(() => {
                        progress.style.width = '0%';
                    }, 100);
                }
                
                // Add to tracking
                this.performanceMetrics.totalNotifications++;
                
                // Animate in
                setTimeout(() => {
                    notification.style.transform = 'translateX(0)';
                    notification.style.opacity = '1';
                }, 100);
            }
        } catch (error) {
            console.error('❌ Failed to show notification:', error);
        }
    }

    // Get notification icon based on type
    getNotificationIcon(type) {
        const icons = {
            success: '✅',
            error: '❌',
            warning: '⚠️',
            info: 'ℹ️'
        };
        return icons[type] || icons.info;
    }

    // Utility methods
    getCurrentTheme() {
        return this.currentTheme;
    }
    
    getCurrentDevice() {
        return this.currentDevice;
    }
    
    isThemeActive(themeName) {
        return this.currentTheme === themeName;
    }
    
    isDeviceActive(deviceName) {
        return this.currentDevice === deviceName;
    }

    // Enhanced accessibility toggles
    toggleHighContrast() {
        this.accessibility.highContrast = !this.accessibility.highContrast;
        localStorage.setItem('highContrast', this.accessibility.highContrast);
        
        if (this.accessibility.highContrast) {
            document.body.classList.add('high-contrast');
            this.showNotification('High contrast mode enabled!', 'success');
        } else {
            document.body.classList.remove('high-contrast');
            this.showNotification('High contrast mode disabled!', 'info');
        }
    }

    toggleReducedMotion() {
        this.accessibility.reducedMotion = !this.accessibility.reducedMotion;
        localStorage.setItem('reducedMotion', this.accessibility.reducedMotion);
        
        if (this.accessibility.reducedMotion) {
            document.body.classList.add('reduced-motion');
            this.showNotification('Reduced motion enabled!', 'success');
        } else {
            document.body.classList.remove('reduced-motion');
            this.showNotification('Reduced motion disabled!', 'info');
        }
    }

    toggleFontSize() {
        const sizes = [12, 14, 16, 18, 20, 22, 24];
        const currentIndex = sizes.indexOf(this.accessibility.fontSize);
        const nextIndex = (currentIndex + 1) % sizes.length;
        this.accessibility.fontSize = sizes[nextIndex];
        
        localStorage.setItem('fontSize', this.accessibility.fontSize);
        document.documentElement.style.fontSize = this.accessibility.fontSize + 'px';
        
        this.showNotification(`Font size: ${this.accessibility.fontSize}px`, 'info');
    }

    toggleParticles() {
        this.features.particles = !this.features.particles;
        localStorage.setItem('particles', this.features.particles);
        
        if (this.features.particles) {
            this.setupParticleSystem();
            this.showNotification('Particle effects enabled!', 'success');
        } else {
            if (this.particleSystem && this.particleSystem.canvas) {
                this.particleSystem.canvas.remove();
                this.particleSystem = null;
            }
            this.showNotification('Particle effects disabled!', 'warning');
        }
    }

    toggleAudio() {
        this.features.audio = !this.features.audio;
        localStorage.setItem('audio', this.features.audio);
        
        if (this.features.audio) {
            this.setupAudioContext();
            this.showNotification('Audio feedback enabled!', 'success');
        } else {
            if (this.audioContext) {
                this.audioContext.close();
                this.audioContext = null;
            }
            this.showNotification('Audio feedback disabled!', 'warning');
        }
    }

    // Performance metrics
    savePerformanceMetrics() {
        try {
            localStorage.setItem('themeSwitchCount', this.performanceMetrics.themeSwitchCount);
            localStorage.setItem('deviceSwitchCount', this.performanceMetrics.deviceSwitchCount);
            localStorage.setItem('lastThemeSwitch', this.performanceMetrics.lastThemeSwitch);
            localStorage.setItem('lastDeviceSwitch', this.performanceMetrics.lastDeviceSwitch);
            localStorage.setItem('totalNotifications', this.performanceMetrics.totalNotifications);
            console.log('📈 Performance metrics saved');
        } catch (error) {
            console.warn('⚠️ Failed to save performance metrics:', error);
        }
    }

    getPerformanceReport() {
        return {
            ...this.performanceMetrics,
            currentTheme: this.currentTheme,
            currentDevice: this.currentDevice,
            features: this.features,
            accessibility: this.accessibility,
            timestamp: Date.now()
        };
    }

    // Theme management enhancements
    getAvailableThemes() {
        return [
            { id: 'light', name: 'Light Theme', icon: '☀️', description: 'Clean and modern light interface' },
            { id: 'dark', name: 'Dark Theme', icon: '🌙', description: 'Sleek and elegant dark interface' },
            { id: 'cs2', name: 'CS2 Gaming', icon: '🎮', description: 'Gaming-inspired Counter-Strike 2 style' },
            { id: 'premium', name: 'Premium Luxury', icon: '💎', description: 'Exclusive luxury premium interface' }
        ];
    }

    getAvailableDevices() {
        return [
            { id: 'desktop', name: 'Desktop', icon: '🖥️', description: 'Full desktop experience' },
            { id: 'tablet', name: 'Tablet', icon: '📱', description: 'Optimized for tablet devices' },
            { id: 'mobile', name: 'Mobile', icon: '📱', description: 'Mobile-optimized interface' }
        ];
    }

    // Advanced features
    enableDeveloperMode() {
        try {
            // Add developer tools
            const devPanel = document.createElement('div');
            devPanel.id = 'dev-panel';
            devPanel.className = 'dev-panel';
            devPanel.innerHTML = `
                <div class="dev-header">
                    <h4>🔧 Developer Panel</h4>
                    <button onclick="this.parentElement.parentElement.remove()">×</button>
                </div>
                <div class="dev-content">
                    <button onclick="window.themeManager.exportSettings()">Export Settings</button>
                    <button onclick="window.themeManager.optimizePerformance()">Optimize Performance</button>
                    <button onclick="console.log(window.themeManager.getPerformanceReport())">Performance Report</button>
                    <button onclick="window.themeManager.resetToDefaults()">Reset to Defaults</button>
                </div>
            `;
            document.body.appendChild(devPanel);
            
            this.showNotification('Developer mode enabled!', 'success');
        } catch (error) {
            console.error('❌ Failed to enable developer mode:', error);
        }
    }

    resetToDefaults() {
        try {
            // Clear all preferences
            localStorage.clear();
            
            // Reset to defaults
            this.currentTheme = 'light';
            this.currentDevice = 'desktop';
            this.accessibility = {
                highContrast: false,
                reducedMotion: false,
                fontSize: 16,
                screenReader: false
            };
            this.features = {
                particles: true,
                audio: true,
                animations: true,
                notifications: true,
                shortcuts: true
            };
            
            // Apply defaults
            this.applyCurrentTheme();
            this.setupAccessibility();
            
            this.showNotification('Settings reset to defaults!', 'success');
        } catch (error) {
            console.error('❌ Failed to reset to defaults:', error);
        }
    }

    // Auto-save and recovery
    autoSave() {
        try {
            const autoSaveData = {
                theme: this.currentTheme,
                device: this.currentDevice,
                accessibility: this.accessibility,
                features: this.features,
                timestamp: Date.now()
            };
            
            localStorage.setItem('autoSave', JSON.stringify(autoSaveData));
            
            // Show auto-save indicator
            this.showAutoSaveIndicator();
            
        } catch (error) {
            console.warn('⚠️ Failed to auto-save:', error);
        }
    }

    // Duplicate notification method removed - using first definition above

    // Performance monitoring setup
    setupPerformanceMonitoring() {
        try {
            // Track page load performance
            if ('performance' in window) {
                const perfData = performance.getEntriesByType('navigation')[0];
                if (perfData) {
                    this.performanceMetrics.pageLoadTime = perfData.loadEventEnd - perfData.loadEventStart;
                    this.performanceMetrics.domContentLoaded = perfData.domContentLoadedEventEnd - perfData.domContentLoadedEventStart;
                }
            }

            // Monitor memory usage
            if ('memory' in performance) {
                setInterval(() => {
                    this.performanceMetrics.memoryUsage = performance.memory.usedJSHeapSize;
                    this.performanceMetrics.memoryLimit = performance.memory.jsHeapSizeLimit;
                    this.updatePerformanceIndicator();
                }, 10000);
            }

            // Monitor frame rate
            let frameCount = 0;
            let lastTime = performance.now();
            
            const countFrames = () => {
                frameCount++;
                const currentTime = performance.now();
                
                if (currentTime - lastTime >= 1000) {
                    this.performanceMetrics.fps = frameCount;
                    frameCount = 0;
                    lastTime = currentTime;
                    this.updatePerformanceIndicator();
                }
                
                requestAnimationFrame(countFrames);
            };
            
            requestAnimationFrame(countFrames);
            
            // Show performance indicator
            this.showPerformanceIndicator();
            
            console.log('📊 Performance monitoring enabled');
        } catch (error) {
            console.warn('⚠️ Failed to setup performance monitoring:', error);
        }
    }

    // Show/hide performance indicator
    showPerformanceIndicator() {
        const indicator = document.getElementById('performanceIndicator');
        if (indicator) {
            indicator.style.display = 'block';
            this.updatePerformanceIndicator();
        }
    }

    hidePerformanceIndicator() {
        const indicator = document.getElementById('performanceIndicator');
        if (indicator) {
            indicator.style.display = 'none';
        }
    }

    // Update performance indicator display
    updatePerformanceIndicator() {
        const fpsElement = document.getElementById('currentFps');
        const memoryElement = document.getElementById('currentMemory');
        const themeElement = document.getElementById('currentTheme');
        const deviceElement = document.getElementById('currentDevice');
        
        if (fpsElement) {
            fpsElement.textContent = this.performanceMetrics.fps || '--';
        }
        
        if (memoryElement && this.performanceMetrics.memoryUsage) {
            const usedMB = Math.round(this.performanceMetrics.memoryUsage / 1024 / 1024);
            const limitMB = Math.round(this.performanceMetrics.memoryLimit / 1024 / 1024);
            memoryElement.textContent = `${usedMB}MB / ${limitMB}MB`;
        }
        
        if (themeElement) {
            themeElement.textContent = this.currentTheme;
        }
        
        if (deviceElement) {
            deviceElement.textContent = this.currentDevice;
        }
    }

    // Performance optimization
    optimizePerformance() {
        try {
            // Reduce particle count on low-end devices
            if (navigator.hardwareConcurrency && navigator.hardwareConcurrency < 4) {
                if (this.particleSystem) {
                    this.particleSystem.particleCount = 25;
                    this.particleSystem.maxParticles = 50;
                }
            }
            
            // Disable animations on low-end devices
            if (navigator.deviceMemory && navigator.deviceMemory < 4) {
                this.features.animations = false;
                localStorage.setItem('animations', 'false');
                document.body.classList.add('reduced-motion');
            }
            
            // Optimize for mobile
            if (window.innerWidth < 768) {
                if (this.particleSystem) {
                    this.particleSystem.particleCount = 20;
                }
                this.features.animations = false;
                localStorage.setItem('animations', 'false');
            }
            
            // Reduce motion if user prefers
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                this.accessibility.reducedMotion = true;
                localStorage.setItem('reducedMotion', 'true');
                document.body.classList.add('reduced-motion');
            }
            
            console.log('🚀 Performance optimized');
        } catch (error) {
            console.warn('⚠️ Failed to optimize performance:', error);
        }
    }

    // Accessibility enhancements
    setupScreenReader() {
        try {
            if (this.accessibility.screenReader) {
                // Add ARIA labels
                document.querySelectorAll('[data-theme]').forEach(button => {
                    button.setAttribute('aria-label', `Switch to ${button.dataset.theme} theme`);
                });
                
                document.querySelectorAll('.device-option').forEach(option => {
                    option.setAttribute('aria-label', `Select ${option.dataset.device} device`);
                });
                
                // Announce theme changes
                const announce = (message) => {
                    const announcement = document.createElement('div');
                    announcement.setAttribute('aria-live', 'polite');
                    announcement.setAttribute('aria-atomic', 'true');
                    announcement.className = 'sr-only';
                    announcement.textContent = message;
                    document.body.appendChild(announcement);
                    
                    setTimeout(() => {
                        if (announcement.parentNode) {
                            announcement.remove();
                        }
                    }, 1000);
                };
                
                // Override showNotification for screen reader
                const originalShowNotification = this.showNotification;
                this.showNotification = (message, type, duration) => {
                    originalShowNotification.call(this, message, type, duration);
                    announce(message);
                };
                
                console.log('♿ Screen reader support enabled');
            }
        } catch (error) {
            console.warn('⚠️ Failed to setup screen reader:', error);
        }
    }

    // Theme presets system
    createThemePreset(name, colors) {
        try {
            const preset = {
                name: name,
                colors: colors,
                timestamp: Date.now()
            };
            
            const presets = JSON.parse(localStorage.getItem('themePresets') || '[]');
            presets.push(preset);
            localStorage.setItem('themePresets', JSON.stringify(presets));
            
            this.showNotification(`Theme preset "${name}" created!`, 'success');
            return true;
        } catch (error) {
            console.error('❌ Failed to create theme preset:', error);
            return false;
        }
    }

    loadThemePreset(name) {
        try {
            const presets = JSON.parse(localStorage.getItem('themePresets') || '[]');
            const preset = presets.find(p => p.name === name);
            
            if (preset) {
                // Apply custom colors
                this.applyCustomTheme(preset.colors);
                this.showNotification(`Theme preset "${name}" loaded!`, 'success');
                return true;
            } else {
                this.showNotification(`Theme preset "${name}" not found!`, 'error');
                return false;
            }
        } catch (error) {
            console.error('❌ Failed to load theme preset:', error);
            return false;
        }
    }

    applyCustomTheme(colors) {
        try {
            const root = document.documentElement;
            
            // Apply custom CSS variables
            Object.entries(colors).forEach(([key, value]) => {
                root.style.setProperty(`--custom-${key}`, value);
            });
            
            // Add custom theme class
            document.body.classList.add('theme-custom');
            
            this.showNotification('Custom theme applied!', 'success');
        } catch (error) {
            console.error('❌ Failed to apply custom theme:', error);
        }
    }

    // Export/Import settings
    exportSettings() {
        try {
            const settings = {
                theme: this.currentTheme,
                device: this.currentDevice,
                accessibility: this.accessibility,
                features: this.features,
                performanceMetrics: this.performanceMetrics,
                timestamp: Date.now()
            };
            
            const dataStr = JSON.stringify(settings, null, 2);
            const dataBlob = new Blob([dataStr], { type: 'application/json' });
            
            const link = document.createElement('a');
            link.href = URL.createObjectURL(dataBlob);
            link.download = `affinity-theme-settings-${Date.now()}.json`;
            link.click();
            
            this.showNotification('Settings exported successfully!', 'success');
            return true;
        } catch (error) {
            console.error('❌ Failed to export settings:', error);
            return false;
        }
    }

    importSettings(file) {
        try {
            const reader = new FileReader();
            reader.onload = (e) => {
                const settings = JSON.parse(e.target.result);
                
                // Validate settings
                if (settings.theme && ['light', 'dark', 'cs2', 'premium'].includes(settings.theme)) {
                    this.currentTheme = settings.theme;
                    localStorage.setItem('selectedTheme', settings.theme);
                }
                
                if (settings.device && ['desktop', 'tablet', 'mobile'].includes(settings.device)) {
                    this.currentDevice = settings.device;
                    localStorage.setItem('selectedDevice', settings.device);
                }
                
                // Apply settings
                this.applyCurrentTheme();
                this.showNotification('Settings imported successfully!', 'success');
            };
            
            reader.readAsText(file);
            return true;
        } catch (error) {
            console.error('❌ Failed to import settings:', error);
            return false;
        }
    }
}

// Create global instance
window.themeManager = new UltraPremiumThemeManager();
console.log('✅ Global themeManager instance created:', window.themeManager);

// Enhanced global functions for HTML onclick
window.switchTheme = (themeName) => {
    console.log('🌍 Global switchTheme called with:', themeName);
    if (window.themeManager) {
        window.themeManager.switchTheme(themeName);
    } else {
        console.error('❌ Theme manager not available in global switchTheme');
    }
};
console.log('✅ Global switchTheme function created:', window.switchTheme);

window.selectDevice = (device) => {
    console.log('🌍 Global selectDevice called with:', device);
    if (window.themeManager) {
        window.themeManager.selectDevice(device);
    } else {
        console.error('❌ Theme manager not available in global selectDevice');
    }
};
console.log('✅ Global selectDevice function created:', window.selectDevice);

// New global functions
window.toggleHighContrast = () => {
    console.log('🌍 Global toggleHighContrast called');
    if (window.themeManager) {
        window.themeManager.toggleHighContrast();
    } else {
        console.error('❌ Theme manager not available in global toggleHighContrast');
    }
};
console.log('✅ Global toggleHighContrast function created');

window.toggleReducedMotion = () => {
    console.log('🌍 Global toggleReducedMotion called');
    if (window.themeManager) {
        window.themeManager.toggleReducedMotion();
    } else {
        console.error('❌ Theme manager not available in global toggleReducedMotion');
    }
};
console.log('✅ Global toggleReducedMotion function created');

window.toggleFontSize = () => {
    console.log('🌍 Global toggleFontSize called');
    if (window.themeManager) {
        window.themeManager.toggleFontSize();
    } else {
        console.error('❌ Theme manager not available in global toggleFontSize');
    }
};
console.log('✅ Global toggleFontSize function created');

window.toggleParticles = () => {
    console.log('🌍 Global toggleParticles called');
    if (window.themeManager) {
        window.themeManager.toggleParticles();
    } else {
        console.error('❌ Theme manager not available in global toggleParticles');
    }
};
console.log('✅ Global toggleParticles function created');

window.toggleAudio = () => {
    console.log('🌍 Global toggleAudio called');
    if (window.themeManager) {
        window.themeManager.toggleAudio();
    } else {
        console.error('❌ Theme manager not available in global toggleAudio');
    }
};
console.log('✅ Global toggleAudio function created');

window.enableDeveloperMode = () => {
    console.log('🌍 Global enableDeveloperMode called');
    if (window.themeManager) {
        window.themeManager.enableDeveloperMode();
    } else {
        console.error('❌ Theme manager not available in global enableDeveloperMode');
    }
};
console.log('✅ Global enableDeveloperMode function created');

window.exportSettings = () => {
    console.log('🌍 Global exportSettings called');
    if (window.themeManager) {
        window.themeManager.exportSettings();
    } else {
        console.error('❌ Theme manager not available in global exportSettings');
    }
};
console.log('✅ Global exportSettings function created');

window.importSettings = (file) => {
    console.log('🌍 Global importSettings called with:', file);
    if (window.themeManager) {
        window.themeManager.importSettings(file);
    } else {
        console.error('❌ Theme manager not available in global importSettings');
    }
};
console.log('✅ Global importSettings function created');

// UI helper functions
window.showShortcutsHelp = () => {
    console.log('🌍 Global showShortcutsHelp called');
    const modal = document.getElementById('shortcutsModal');
    if (modal) {
        modal.style.display = 'block';
        // Add escape key listener
        const escapeHandler = (e) => {
            if (e.key === 'Escape') {
                hideShortcutsHelp();
                document.removeEventListener('keydown', escapeHandler);
            }
        };
        document.addEventListener('keydown', escapeHandler);
    } else {
        console.error('❌ Shortcuts modal not found');
    }
};
console.log('✅ Global showShortcutsHelp function created');

window.hideShortcutsHelp = () => {
    console.log('🌍 Global hideShortcutsHelp called');
    const modal = document.getElementById('shortcutsModal');
    if (modal) {
        modal.style.display = 'none';
    } else {
        console.error('❌ Shortcuts modal not found');
    }
};
console.log('✅ Global hideShortcutsHelp function created');

window.showDeviceOverlay = () => {
    console.log('🌍 Global showDeviceOverlay called');
    const overlay = document.getElementById('deviceOverlay');
    if (overlay) {
        overlay.style.display = 'flex';
                    // Add escape key listener
            const escapeHandler = (e) => {
                if (e.key === 'Escape') {
                    // Don't hide on escape - let user manually close
                    // overlay.style.display = 'none';
                    document.removeEventListener('keydown', escapeHandler);
                }
            };
            document.addEventListener('keydown', escapeHandler);
    } else {
        console.error('❌ Device overlay not found');
    }
};
console.log('✅ Global showDeviceOverlay function created');

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    console.log('🚀 DOM ready - Theme manager should be initialized');
    
    // Auto-recover settings
    if (window.themeManager) {
        window.themeManager.autoRecover();
        window.themeManager.optimizePerformance();
        window.themeManager.setupScreenReader();
    }
});

// Auto-save every 5 minutes
setInterval(() => {
    if (window.themeManager) {
        window.themeManager.autoSave();
    }
}, 300000);

// Performance monitoring
window.addEventListener('load', () => {
    if (window.themeManager) {
        console.log('📊 Performance Report:', window.themeManager.getPerformanceReport());
    }
});

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        console.log('🚀 DOM ready - initializing theme manager');
        window.themeManager.init();
    });
} else {
    // DOM is already ready
    console.log('🚀 DOM already ready - initializing theme manager');
    window.themeManager.init();
}

console.log('🎨 Ultra Premium Theme Manager loaded successfully!');
console.log('🔧 New features: Accessibility, Performance Monitoring, Theme Presets, Export/Import, Auto-save/Recovery');
console.log('🌍 All global functions created successfully!');
console.log('📱 Device selector should now work properly!');
