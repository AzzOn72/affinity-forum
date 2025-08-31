/**
 * Unified Theme Manager
 * Advanced theme system with particle effects, audio feedback, and accessibility features
 */

console.log('🚀 Unified Theme Manager loading...');

class ThemeManager {
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
        
        console.log('🎨 Theme Manager initialized');
        this.init();
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
            
            // Check if this is the first visit and show device selector
            this.checkFirstVisit();
            
            console.log('✅ Theme Manager fully initialized');
        } catch (error) {
            console.error('❌ Error in init:', error);
        }
    }
    
    checkFirstVisit() {
        const hasSelectedDevice = localStorage.getItem('hasSelectedDevice');
        
        if (!hasSelectedDevice) {
            console.log('🎯 First visit detected - showing device selector');
            if (document.body) {
                setTimeout(() => {
                    this.showDeviceSelector();
                }, 1000);
            } else {
                setTimeout(() => this.checkFirstVisit(), 100);
            }
        } else {
            console.log('✅ Returning user - device already selected');
        }
    }
    
    showDeviceSelector() {
        console.log('🎯 Showing device selector for first visit');
        const overlay = document.getElementById('deviceOverlay');
        if (overlay) {
            overlay.style.display = 'flex';
            console.log('✅ Device selector shown for first visit');
        }
    }

    loadUserPreferences() {
        try {
            // Load theme preference
            const savedTheme = localStorage.getItem('selectedTheme');
            if (savedTheme && ['light', 'dark', 'cs2', 'premium'].includes(savedTheme)) {
                this.currentTheme = savedTheme;
            } else {
                this.currentTheme = 'light';
                localStorage.setItem('selectedTheme', 'light');
            }
            
            // Load device preference
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

    setupAccessibility() {
        try {
            if (this.accessibility.highContrast) {
                document.body.classList.add('high-contrast');
            }
            if (this.accessibility.reducedMotion) {
                document.body.classList.add('reduced-motion');
            }
            if (this.accessibility.screenReader) {
                document.body.classList.add('screen-reader');
            }
            
            document.documentElement.style.fontSize = this.accessibility.fontSize + 'px';
            
            console.log('♿ Accessibility features configured');
        } catch (error) {
            console.warn('⚠️ Failed to setup accessibility:', error);
        }
    }

    setupParticleSystem() {
        if (!this.features.particles) return;
        
        try {
            const canvas = document.createElement('canvas');
            canvas.id = 'particleCanvas';
            canvas.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                pointer-events: none;
                z-index: 1000;
                opacity: 0.6;
            `;
            
            document.body.appendChild(canvas);
            
            const ctx = canvas.getContext('2d');
            this.resizeCanvas(canvas, ctx);
            
            this.particleSystem = {
                canvas,
                ctx,
                particles: [],
                particleCount: this.currentDevice === 'mobile' ? 20 : this.currentDevice === 'tablet' ? 35 : 50,
                maxParticles: this.currentDevice === 'mobile' ? 40 : this.currentDevice === 'tablet' ? 70 : 100,
                isActive: true,
                animationId: null
            };
            
            // Initialize particles
            for (let i = 0; i < this.particleSystem.particleCount; i++) {
                this.addNewParticle();
            }
            
            // Start animation
            this.startParticleAnimation();
            
            // Handle resize
            window.addEventListener('resize', () => this.resizeCanvas(canvas, ctx));
            
            console.log('✨ Particle system initialized');
        } catch (error) {
            console.warn('⚠️ Failed to setup particle system:', error);
        }
    }

    resizeCanvas(canvas, ctx) {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    }

    setupAudioContext() {
        if (!this.features.audio || !window.AudioContext) return;
        
        try {
            this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
            
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

    playThemeChangeSound() {
        if (!this.audioContext || this.audioContext.state !== 'running') return;
        
        try {
            const oscillator = this.audioContext.createOscillator();
            const gainNode = this.audioContext.createGain();
            const filter = this.audioContext.createBiquadFilter();
            
            oscillator.connect(filter);
            filter.connect(gainNode);
            gainNode.connect(this.audioContext.destination);
            
            filter.type = 'lowpass';
            filter.frequency.setValueAtTime(800, this.audioContext.currentTime);
            filter.frequency.exponentialRampToValueAtTime(200, this.audioContext.currentTime + 0.5);
            
            oscillator.frequency.setValueAtTime(440, this.audioContext.currentTime);
            oscillator.frequency.exponentialRampToValueAtTime(880, this.audioContext.currentTime + 0.3);
            oscillator.type = 'sine';
            
            gainNode.gain.setValueAtTime(0, this.audioContext.currentTime);
            gainNode.gain.linearRampToValueAtTime(0.3, this.audioContext.currentTime + 0.1);
            gainNode.gain.exponentialRampToValueAtTime(0.01, this.audioContext.currentTime + 0.5);
            
            oscillator.start(this.audioContext.currentTime);
            oscillator.stop(this.audioContext.currentTime + 0.5);
            
        } catch (error) {
            console.warn('⚠️ Failed to play theme change sound:', error);
        }
    }

    playDeviceSelectionSound() {
        if (!this.audioContext || this.audioContext.state !== 'running') return;
        
        try {
            const oscillator = this.audioContext.createOscillator();
            const gainNode = this.audioContext.createGain();
            const filter = this.audioContext.createBiquadFilter();
            
            oscillator.connect(filter);
            filter.connect(gainNode);
            gainNode.connect(this.audioContext.destination);
            
            filter.type = 'highpass';
            filter.frequency.setValueAtTime(200, this.audioContext.currentTime);
            filter.frequency.exponentialRampToValueAtTime(800, this.audioContext.currentTime + 0.3);
            
            oscillator.frequency.setValueAtTime(220, this.audioContext.currentTime);
            oscillator.frequency.exponentialRampToValueAtTime(440, this.audioContext.currentTime + 0.2);
            oscillator.type = 'square';
            
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
            // Theme switching
            document.addEventListener('click', (e) => {
                const themeButton = e.target.closest('[data-theme]');
                if (themeButton) {
                    e.preventDefault();
                    const theme = themeButton.dataset.theme;
                    this.switchTheme(theme);
                }
            });

            // Device selection
            document.addEventListener('click', (e) => {
                const deviceOption = e.target.closest('.device-option');
                if (deviceOption) {
                    e.preventDefault();
                    const device = deviceOption.dataset.device;
                    this.selectDevice(device);
                }
            });

            // Keyboard shortcuts
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
            
            // Performance monitoring
            window.addEventListener('beforeunload', () => {
                this.savePerformanceMetrics();
            });
            
            console.log('🎯 Event listeners configured');
        } catch (error) {
            console.error('❌ Failed to setup event listeners:', error);
        }
    }

    setupPerformanceMonitoring() {
        console.log('📊 Performance monitoring initialized');
    }

    switchTheme(themeName) {
        if (this.currentTheme === themeName) return;
        
        try {
            const startTime = performance.now();
            
            this.currentTheme = themeName;
            localStorage.setItem('selectedTheme', themeName);
            
            this.applyCurrentTheme();
            this.updateParticleColors();
            this.playThemeChangeSound();
            
            const switchTime = performance.now() - startTime;
            this.performanceMetrics.lastThemeSwitch = switchTime;
            this.performanceMetrics.themeSwitchCount++;
            
            this.showNotification(`Theme switched to ${themeName}!`, 'success');
            
            document.dispatchEvent(new CustomEvent('themeChanged', {
                detail: { theme: themeName, switchTime }
            }));
            
            console.log(`🎨 Theme switched to ${themeName} in ${switchTime.toFixed(2)}ms`);
        } catch (error) {
            console.error('❌ Failed to switch theme:', error);
            this.showNotification('Failed to switch theme!', 'error');
        }
    }

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

    selectDevice(device) {
        console.log('📱 Selecting device:', device);
        this.currentDevice = device;
        this.applyCurrentTheme();
        localStorage.setItem('selectedDevice', device);
        localStorage.setItem('hasSelectedDevice', 'true');
        
        const overlay = document.getElementById('deviceOverlay');
        if (overlay) {
            overlay.style.display = 'none';
            console.log('✅ Device overlay hidden after selection');
        }
        
        this.updateParticleSystemForDevice(device);
        this.playDeviceSelectionSound();
        
        console.log('✅ Device selected successfully');
    }

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

    applyCurrentTheme() {
        try {
            document.body.classList.remove('theme-light', 'theme-dark', 'theme-cs2', 'theme-premium');
            document.body.classList.add(`theme-${this.currentTheme}`);
            document.body.setAttribute('data-theme', this.currentTheme);
            document.body.setAttribute('data-device', this.currentDevice);
            
            this.updateCSSVariables();
            
            if (this.particleSystem) {
                this.updateParticleColors();
            }
            
            console.log(`🎨 Applied theme: ${this.currentTheme}, device: ${this.currentDevice}`);
        } catch (error) {
            console.error('❌ Failed to apply current theme:', error);
        }
    }

    updateCSSVariables() {
        try {
            const root = document.documentElement;
            
            const themeColors = this.getThemeColors(this.currentTheme);
            Object.entries(themeColors).forEach(([key, value]) => {
                root.style.setProperty(`--${key}`, value);
            });
            
            const deviceVars = this.getDeviceVariables(this.currentDevice);
            Object.entries(deviceVars).forEach(([key, value]) => {
                root.style.setProperty(`--${key}`, value);
            });
            
        } catch (error) {
            console.warn('⚠️ Failed to update CSS variables:', error);
        }
    }

    getThemeColors(theme) {
        const themes = {
            light: {
                'primary-color': '#007bff',
                'secondary-color': '#6c757d',
                'accent-color': '#28a745',
                'text-color': '#212529',
                'bg-color': '#ffffff',
                'border-color': '#dee2e6'
            },
            dark: {
                'primary-color': '#0d6efd',
                'secondary-color': '#6c757d',
                'accent-color': '#198754',
                'text-color': '#f8f9fa',
                'bg-color': '#212529',
                'border-color': '#495057'
            },
            cs2: {
                'primary-color': '#ff6b35',
                'secondary-color': '#2c3e50',
                'accent-color': '#e74c3c',
                'text-color': '#ecf0f1',
                'bg-color': '#1a1a1a',
                'border-color': '#34495e'
            },
            premium: {
                'primary-color': '#d4af37',
                'secondary-color': '#2c3e50',
                'accent-color': '#e74c3c',
                'text-color': '#f8f9fa',
                'bg-color': '#1a1a1a',
                'border-color': '#d4af37'
            }
        };
        
        return themes[theme] || themes.light;
    }

    getDeviceVariables(device) {
        const devices = {
            desktop: {
                'container-width': '1200px',
                'font-size': '16px',
                'spacing': '1.5rem',
                'border-radius': '10px'
            },
            tablet: {
                'container-width': '768px',
                'font-size': '14px',
                'spacing': '1rem',
                'border-radius': '8px'
            },
            mobile: {
                'container-width': '100%',
                'font-size': '12px',
                'spacing': '0.75rem',
                'border-radius': '6px'
            }
        };
        
        return devices[device] || devices.desktop;
    }

    startParticleAnimation() {
        if (!this.particleSystem || !this.particleSystem.isActive) return;
        
        const animate = () => {
            if (!this.particleSystem.isActive) return;
            
            const { ctx, particles, maxParticles } = this.particleSystem;
            
            ctx.fillStyle = 'rgba(0, 0, 0, 0.05)';
            ctx.fillRect(0, 0, ctx.canvas.width, ctx.canvas.height);
            
            for (let i = particles.length - 1; i >= 0; i--) {
                const particle = particles[i];
                
                particle.x += particle.vx;
                particle.y += particle.vy;
                particle.pulse += particle.pulseSpeed;
                particle.life--;
                
                if (particle.life <= 0 || 
                    particle.x < 0 || particle.x > ctx.canvas.width ||
                    particle.y < 0 || particle.y > ctx.canvas.height) {
                    particles.splice(i, 1);
                    continue;
                }
                
                ctx.save();
                ctx.globalAlpha = particle.opacity * (particle.life / particle.maxLife);
                
                const pulseScale = 1 + Math.sin(particle.pulse) * 0.3;
                const size = particle.size * pulseScale;
                
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
                
                ctx.shadowColor = particle.color;
                ctx.shadowBlur = size * 2;
                ctx.beginPath();
                ctx.arc(particle.x, particle.y, size * 0.5, 0, Math.PI * 2);
                ctx.fill();
                
                ctx.restore();
            }
            
            if (particles.length < maxParticles && Math.random() < 0.1) {
                this.addNewParticle();
            }
            
            this.particleSystem.animationId = requestAnimationFrame(animate);
        };
        
        animate();
    }

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

    getRandomThemeColor() {
        const colors = ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14'];
        return colors[Math.floor(Math.random() * colors.length)];
    }

    showNotification(message, type = 'info', duration = 5000) {
        if (!this.features.notifications) return;
        
        try {
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.innerHTML = `
                <div class="notification-content">
                    <div class="notification-icon">${this.getNotificationIcon(type)}</div>
                    <div class="notification-message">${message}</div>
                    <button class="notification-close" onclick="this.parentElement.parentElement.remove()">×</button>
                </div>
                <div class="notification-progress"></div>
            `;
            
            const container = document.getElementById('notificationContainer');
            if (container) {
                container.appendChild(notification);
                
                if (container.children.length > 5) {
                    container.removeChild(container.firstChild);
                }
                
                if (duration > 0) {
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.remove();
                        }
                    }, duration);
                }
                
                const progress = notification.querySelector('.notification-progress');
                if (progress && duration > 0) {
                    progress.style.transition = `width ${duration}ms linear`;
                    setTimeout(() => {
                        progress.style.width = '0%';
                    }, 100);
                }
                
                this.performanceMetrics.totalNotifications++;
                
                setTimeout(() => {
                    notification.style.transform = 'translateX(0)';
                    notification.style.opacity = '1';
                }, 100);
            }
        } catch (error) {
            console.error('❌ Failed to show notification:', error);
        }
    }

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

    // Accessibility toggles
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

    resetToDefaults() {
        try {
            localStorage.clear();
            
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
            
            this.applyCurrentTheme();
            this.setupAccessibility();
            
            this.showNotification('Settings reset to defaults!', 'success');
        } catch (error) {
            console.error('❌ Failed to reset to defaults:', error);
        }
    }

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
                
                if (settings.theme && ['light', 'dark', 'cs2', 'premium'].includes(settings.theme)) {
                    this.currentTheme = settings.theme;
                    localStorage.setItem('selectedTheme', settings.theme);
                }
                
                if (settings.device && ['desktop', 'tablet', 'mobile'].includes(settings.device)) {
                    this.currentDevice = settings.device;
                    localStorage.setItem('selectedDevice', settings.device);
                }
                
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
window.themeManager = new ThemeManager();
console.log('✅ Global themeManager instance created:', window.themeManager);

// Global functions for HTML onclick
window.switchTheme = (themeName) => {
    if (window.themeManager) {
        window.themeManager.switchTheme(themeName);
    }
};

window.selectDevice = (device) => {
    if (window.themeManager) {
        window.themeManager.selectDevice(device);
    }
};

window.toggleHighContrast = () => {
    if (window.themeManager) {
        window.themeManager.toggleHighContrast();
    }
};

window.toggleReducedMotion = () => {
    if (window.themeManager) {
        window.themeManager.toggleReducedMotion();
    }
};

window.toggleFontSize = () => {
    if (window.themeManager) {
        window.themeManager.toggleFontSize();
    }
};

window.toggleParticles = () => {
    if (window.themeManager) {
        window.themeManager.toggleParticles();
    }
};

window.toggleAudio = () => {
    if (window.themeManager) {
        window.themeManager.toggleAudio();
    }
};

window.exportSettings = () => {
    if (window.themeManager) {
        window.themeManager.exportSettings();
    }
};

window.importSettings = (file) => {
    if (window.themeManager) {
        window.themeManager.importSettings(file);
    }
};

window.resetToDefaults = () => {
    if (window.themeManager) {
        window.themeManager.resetToDefaults();
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    console.log('🚀 DOM ready - Theme manager initialized');
    
    if (window.themeManager) {
        window.themeManager.optimizePerformance();
    }
});

console.log('🎨 Unified Theme Manager loaded successfully!');