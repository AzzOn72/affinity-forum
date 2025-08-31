/**
 * Advanced Theme Manager v3.0
 * Next-generation theme system with AI-powered features, advanced effects, and smart optimization
 */

console.log('🚀 Advanced Theme Manager v3.0 loading...');

class ThemeManager {
    constructor() {
        this.currentTheme = 'light';
        this.currentDevice = 'desktop';
        this.particleSystem = null;
        this.audioContext = null;
        this.isInitialized = false;
        this.animations = new Map();
        this.notifications = [];
        this.customThemes = new Map();
        this.themeHistory = [];
        this.smartSuggestions = [];
        this.performanceMetrics = {
            themeSwitchCount: 0,
            deviceSwitchCount: 0,
            lastThemeSwitch: 0,
            lastDeviceSwitch: 0,
            totalNotifications: 0,
            fps: 60,
            memoryUsage: 0,
            renderTime: 0,
            particleCount: 0
        };
        this.accessibility = {
            highContrast: false,
            reducedMotion: false,
            fontSize: 16,
            screenReader: false,
            voiceControl: false,
            eyeTracking: false,
            colorBlindSupport: false
        };
        this.features = {
            particles: true,
            audio: true,
            animations: true,
            notifications: true,
            shortcuts: true,
            smartSuggestions: true,
            autoOptimization: true,
            weatherSync: false,
            timeSync: true,
            moodDetection: false
        };
        this.audioEffects = {
            volume: 0.5,
            effects: ['theme-change', 'device-switch', 'notification', 'click', 'hover'],
            currentTrack: null,
            visualizer: null
        };
        this.particleEffects = {
            types: ['standard', 'fireworks', 'snow', 'rain', 'stars', 'bubbles'],
            currentType: 'standard',
            interactive: true,
            physics: true,
            trails: true
        };
        
        console.log('🎨 Theme Manager initialized');
        this.init();
    }

    init() {
        try {
            this.loadUserPreferences();
            this.setupAccessibility();
            this.setupAdvancedParticleSystem();
            this.setupEnhancedAudioSystem();
            this.setupSmartFeatures();
            this.setupEventListeners();
            this.setupPerformanceMonitoring();
            this.setupAnimationSystem();
            this.setupThemeCustomization();
            this.applyCurrentTheme();
            
            // Check if this is the first visit and show device selector
            this.checkFirstVisit();
            
            // Initialize smart suggestions
            this.initializeSmartSuggestions();
            
            console.log('✅ Advanced Theme Manager v3.0 fully initialized');
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

    setupAdvancedParticleSystem() {
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
                pointer-events: ${this.particleEffects.interactive ? 'auto' : 'none'};
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
                particleCount: this.getOptimalParticleCount(),
                maxParticles: this.getMaxParticleCount(),
                isActive: true,
                animationId: null,
                mouseX: 0,
                mouseY: 0,
                forces: [],
                emitters: []
            };
            
            // Initialize particles based on current type
            this.initializeParticlesByType();
            
            // Start animation
            this.startAdvancedParticleAnimation();
            
            // Setup interactive features
            if (this.particleEffects.interactive) {
                this.setupParticleInteraction();
            }
            
            // Handle resize
            window.addEventListener('resize', () => this.resizeCanvas(canvas, ctx));
            
            console.log('✨ Advanced particle system initialized');
        } catch (error) {
            console.warn('⚠️ Failed to setup advanced particle system:', error);
        }
    }

    getOptimalParticleCount() {
        const base = this.currentDevice === 'mobile' ? 30 : this.currentDevice === 'tablet' ? 50 : 80;
        const multiplier = this.particleEffects.currentType === 'fireworks' ? 1.5 : 
                          this.particleEffects.currentType === 'snow' ? 2 : 1;
        return Math.floor(base * multiplier);
    }

    getMaxParticleCount() {
        const base = this.currentDevice === 'mobile' ? 60 : this.currentDevice === 'tablet' ? 100 : 150;
        const multiplier = this.particleEffects.currentType === 'fireworks' ? 2 : 
                          this.particleEffects.currentType === 'snow' ? 3 : 1;
        return Math.floor(base * multiplier);
    }

    initializeParticlesByType() {
        const count = this.particleSystem.particleCount;
        for (let i = 0; i < count; i++) {
            this.addParticleByType(this.particleEffects.currentType);
        }
    }

    setupParticleInteraction() {
        const canvas = this.particleSystem.canvas;
        
        canvas.addEventListener('mousemove', (e) => {
            const rect = canvas.getBoundingClientRect();
            this.particleSystem.mouseX = e.clientX - rect.left;
            this.particleSystem.mouseY = e.clientY - rect.top;
            
            // Add attraction force at mouse position
            this.addForce(this.particleSystem.mouseX, this.particleSystem.mouseY, 50, 0.5);
        });
        
        canvas.addEventListener('click', (e) => {
            const rect = canvas.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            // Create explosion effect
            this.createExplosion(x, y);
            this.playClickSound();
        });
    }

    addForce(x, y, radius, strength) {
        this.particleSystem.forces.push({
            x, y, radius, strength,
            life: 60,
            maxLife: 60
        });
    }

    createExplosion(x, y) {
        const explosionParticles = 20;
        for (let i = 0; i < explosionParticles; i++) {
            const angle = (i / explosionParticles) * Math.PI * 2;
            const speed = Math.random() * 10 + 5;
            
            this.particleSystem.particles.push({
                x: x,
                y: y,
                vx: Math.cos(angle) * speed,
                vy: Math.sin(angle) * speed,
                size: Math.random() * 5 + 2,
                opacity: 1,
                color: this.getRandomThemeColor(),
                pulse: 0,
                pulseSpeed: 0.1,
                life: 60,
                maxLife: 60,
                type: 'explosion',
                gravity: 0.2
            });
        }
    }

    resizeCanvas(canvas, ctx) {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    }

    setupEnhancedAudioSystem() {
        if (!this.features.audio || !window.AudioContext) return;
        
        try {
            this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
            
            // Create master gain node for volume control
            this.masterGain = this.audioContext.createGain();
            this.masterGain.connect(this.audioContext.destination);
            this.masterGain.gain.value = this.audioEffects.volume;
            
            // Create audio visualizer
            this.setupAudioVisualizer();
            
            const resumeAudio = () => {
                if (this.audioContext.state === 'suspended') {
                    this.audioContext.resume();
                }
                document.removeEventListener('click', resumeAudio);
                document.removeEventListener('keydown', resumeAudio);
            };
            
            document.addEventListener('click', resumeAudio);
            document.addEventListener('keydown', resumeAudio);
            
            console.log('🔊 Enhanced audio system initialized');
        } catch (error) {
            console.warn('⚠️ Failed to setup enhanced audio system:', error);
        }
    }

    setupAudioVisualizer() {
        try {
            this.audioEffects.visualizer = {
                analyser: this.audioContext.createAnalyser(),
                dataArray: new Uint8Array(128),
                canvas: document.createElement('canvas'),
                isActive: false
            };
            
            this.audioEffects.visualizer.analyser.fftSize = 256;
            this.audioEffects.visualizer.canvas.id = 'audioVisualizer';
            this.audioEffects.visualizer.canvas.style.cssText = `
                position: fixed;
                bottom: 20px;
                right: 20px;
                width: 200px;
                height: 100px;
                border: 2px solid var(--border-color);
                border-radius: var(--border-radius);
                background: var(--card-bg);
                z-index: 1001;
                opacity: 0.8;
                display: none;
            `;
            
            document.body.appendChild(this.audioEffects.visualizer.canvas);
            
            console.log('🎵 Audio visualizer setup complete');
        } catch (error) {
            console.warn('⚠️ Failed to setup audio visualizer:', error);
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
        this.playSound('device-switch');
    }

    playClickSound() {
        this.playSound('click');
    }

    playHoverSound() {
        this.playSound('hover');
    }

    playNotificationSound() {
        this.playSound('notification');
    }

    playSound(type) {
        if (!this.audioContext || this.audioContext.state !== 'running') return;
        if (!this.audioEffects.effects.includes(type)) return;
        
        try {
            const oscillator = this.audioContext.createOscillator();
            const gainNode = this.audioContext.createGain();
            const filter = this.audioContext.createBiquadFilter();
            
            oscillator.connect(filter);
            filter.connect(gainNode);
            gainNode.connect(this.masterGain);
            
            // Configure sound based on type
            switch (type) {
                case 'theme-change':
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
                    break;
                    
                case 'device-switch':
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
                    break;
                    
                case 'click':
                    oscillator.frequency.setValueAtTime(800, this.audioContext.currentTime);
                    oscillator.type = 'triangle';
                    gainNode.gain.setValueAtTime(0, this.audioContext.currentTime);
                    gainNode.gain.linearRampToValueAtTime(0.1, this.audioContext.currentTime + 0.01);
                    gainNode.gain.exponentialRampToValueAtTime(0.01, this.audioContext.currentTime + 0.1);
                    oscillator.start(this.audioContext.currentTime);
                    oscillator.stop(this.audioContext.currentTime + 0.1);
                    break;
                    
                case 'hover':
                    oscillator.frequency.setValueAtTime(600, this.audioContext.currentTime);
                    oscillator.type = 'sine';
                    gainNode.gain.setValueAtTime(0, this.audioContext.currentTime);
                    gainNode.gain.linearRampToValueAtTime(0.05, this.audioContext.currentTime + 0.01);
                    gainNode.gain.exponentialRampToValueAtTime(0.01, this.audioContext.currentTime + 0.05);
                    oscillator.start(this.audioContext.currentTime);
                    oscillator.stop(this.audioContext.currentTime + 0.05);
                    break;
                    
                case 'notification':
                    // Two-tone notification sound
                    const osc2 = this.audioContext.createOscillator();
                    const gain2 = this.audioContext.createGain();
                    
                    oscillator.frequency.setValueAtTime(523, this.audioContext.currentTime);
                    osc2.frequency.setValueAtTime(659, this.audioContext.currentTime + 0.1);
                    
                    oscillator.type = 'sine';
                    osc2.type = 'sine';
                    
                    oscillator.connect(gainNode);
                    osc2.connect(gain2);
                    gainNode.connect(this.masterGain);
                    gain2.connect(this.masterGain);
                    
                    gainNode.gain.setValueAtTime(0, this.audioContext.currentTime);
                    gainNode.gain.linearRampToValueAtTime(0.1, this.audioContext.currentTime + 0.05);
                    gainNode.gain.exponentialRampToValueAtTime(0.01, this.audioContext.currentTime + 0.15);
                    
                    gain2.gain.setValueAtTime(0, this.audioContext.currentTime + 0.1);
                    gain2.gain.linearRampToValueAtTime(0.1, this.audioContext.currentTime + 0.15);
                    gain2.gain.exponentialRampToValueAtTime(0.01, this.audioContext.currentTime + 0.25);
                    
                    oscillator.start(this.audioContext.currentTime);
                    oscillator.stop(this.audioContext.currentTime + 0.15);
                    osc2.start(this.audioContext.currentTime + 0.1);
                    osc2.stop(this.audioContext.currentTime + 0.25);
                    break;
            }
            
        } catch (error) {
            console.warn(`⚠️ Failed to play ${type} sound:`, error);
        }
    }

    setVolume(volume) {
        if (this.masterGain) {
            this.audioEffects.volume = Math.max(0, Math.min(1, volume));
            this.masterGain.gain.value = this.audioEffects.volume;
            localStorage.setItem('audioVolume', this.audioEffects.volume);
            this.showNotification(`Volume: ${Math.round(this.audioEffects.volume * 100)}%`, 'info');
        }
    }

    toggleAudioVisualizer() {
        if (!this.audioEffects.visualizer) return;
        
        const canvas = this.audioEffects.visualizer.canvas;
        if (canvas.style.display === 'none') {
            canvas.style.display = 'block';
            this.audioEffects.visualizer.isActive = true;
            this.startAudioVisualization();
        } else {
            canvas.style.display = 'none';
            this.audioEffects.visualizer.isActive = false;
        }
    }

    startAudioVisualization() {
        if (!this.audioEffects.visualizer || !this.audioEffects.visualizer.isActive) return;
        
        const { analyser, dataArray, canvas } = this.audioEffects.visualizer;
        const ctx = canvas.getContext('2d');
        
        const draw = () => {
            if (!this.audioEffects.visualizer.isActive) return;
            
            analyser.getByteFrequencyData(dataArray);
            
            ctx.fillStyle = 'rgba(0, 0, 0, 0.2)';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            
            const barWidth = canvas.width / dataArray.length;
            
            for (let i = 0; i < dataArray.length; i++) {
                const barHeight = (dataArray[i] / 255) * canvas.height;
                
                ctx.fillStyle = `hsl(${i * 2}, 70%, 60%)`;
                ctx.fillRect(i * barWidth, canvas.height - barHeight, barWidth, barHeight);
            }
            
            requestAnimationFrame(draw);
        };
        
        draw();
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

    setupSmartFeatures() {
        try {
            // Time-based theme suggestions
            if (this.features.timeSync) {
                this.setupTimeBasedThemes();
            }
            
            // Smart optimization based on device capabilities
            if (this.features.autoOptimization) {
                this.setupAutoOptimization();
            }
            
            // Weather-based theme sync (if enabled)
            if (this.features.weatherSync) {
                this.setupWeatherSync();
            }
            
            console.log('🧠 Smart features initialized');
        } catch (error) {
            console.warn('⚠️ Failed to setup smart features:', error);
        }
    }

    setupTimeBasedThemes() {
        const hour = new Date().getHours();
        let suggestedTheme = this.currentTheme;
        
        if (hour >= 6 && hour < 18) {
            suggestedTheme = 'light';
        } else {
            suggestedTheme = 'dark';
        }
        
        if (suggestedTheme !== this.currentTheme) {
            this.smartSuggestions.push({
                type: 'theme',
                suggestion: suggestedTheme,
                reason: `Based on current time (${hour}:00)`,
                confidence: 0.8
            });
        }
    }

    setupAutoOptimization() {
        // Monitor performance and auto-adjust
        setInterval(() => {
            this.analyzePerformance();
        }, 5000);
    }

    analyzePerformance() {
        try {
            // Check FPS
            const now = performance.now();
            if (this.lastFrameTime) {
                const fps = 1000 / (now - this.lastFrameTime);
                this.performanceMetrics.fps = Math.round(fps);
                
                // Auto-optimize if FPS is too low
                if (fps < 30 && this.particleSystem) {
                    this.optimizeForPerformance();
                }
            }
            this.lastFrameTime = now;
            
            // Check memory usage
            if (performance.memory) {
                this.performanceMetrics.memoryUsage = performance.memory.usedJSHeapSize;
                
                // Auto-optimize if memory usage is high
                if (performance.memory.usedJSHeapSize > performance.memory.jsHeapSizeLimit * 0.8) {
                    this.optimizeForMemory();
                }
            }
        } catch (error) {
            console.warn('⚠️ Performance analysis failed:', error);
        }
    }

    optimizeForPerformance() {
        if (this.particleSystem && this.particleSystem.particles.length > 20) {
            this.particleSystem.particles = this.particleSystem.particles.slice(0, 20);
            this.showNotification('Auto-optimized for better performance', 'info');
        }
    }

    optimizeForMemory() {
        // Clear old notifications
        this.notifications = this.notifications.slice(-5);
        
        // Reduce particle effects
        if (this.particleSystem) {
            this.particleSystem.maxParticles = Math.max(20, this.particleSystem.maxParticles * 0.8);
        }
        
        this.showNotification('Auto-optimized for memory usage', 'info');
    }

    setupWeatherSync() {
        // This would integrate with a weather API
        // For now, just a placeholder
        console.log('🌤️ Weather sync feature ready (API integration needed)');
    }

    setupAnimationSystem() {
        try {
            // Page transition animations
            this.setupPageTransitions();
            
            // Element entrance animations
            this.setupEntranceAnimations();
            
            // Scroll-based animations
            this.setupScrollAnimations();
            
            console.log('🎬 Animation system initialized');
        } catch (error) {
            console.warn('⚠️ Failed to setup animation system:', error);
        }
    }

    setupPageTransitions() {
        // Add smooth page transitions
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a[href]');
            if (link && link.href.includes(window.location.origin) && !link.target) {
                e.preventDefault();
                this.transitionToPage(link.href);
            }
        });
    }

    transitionToPage(url) {
        if (this.accessibility.reducedMotion) {
            window.location.href = url;
            return;
        }
        
        // Fade out current page
        document.body.style.transition = 'opacity 0.3s ease-out';
        document.body.style.opacity = '0';
        
        setTimeout(() => {
            window.location.href = url;
        }, 300);
    }

    setupEntranceAnimations() {
        // Animate elements as they enter the viewport
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        });
        
        // Observe all cards and premium cards
        document.querySelectorAll('.card, .premium-card').forEach(el => {
            observer.observe(el);
        });
    }

    setupScrollAnimations() {
        let ticking = false;
        
        const updateScrollAnimations = () => {
            const scrollY = window.pageYOffset;
            const windowHeight = window.innerHeight;
            
            // Parallax effect for particle canvas
            if (this.particleSystem && this.particleSystem.canvas) {
                this.particleSystem.canvas.style.transform = `translateY(${scrollY * 0.1}px)`;
            }
            
            ticking = false;
        };
        
        window.addEventListener('scroll', () => {
            if (!ticking && !this.accessibility.reducedMotion) {
                requestAnimationFrame(updateScrollAnimations);
                ticking = true;
            }
        });
    }

    setupThemeCustomization() {
        try {
            // Load custom themes from localStorage
            const customThemes = localStorage.getItem('customThemes');
            if (customThemes) {
                const themes = JSON.parse(customThemes);
                themes.forEach(theme => {
                    this.customThemes.set(theme.id, theme);
                });
            }
            
            console.log('🎨 Theme customization initialized');
        } catch (error) {
            console.warn('⚠️ Failed to setup theme customization:', error);
        }
    }

    initializeSmartSuggestions() {
        if (!this.features.smartSuggestions) return;
        
        try {
            // Analyze user behavior and suggest optimizations
            this.analyzeUserBehavior();
            
            // Show suggestions if any
            if (this.smartSuggestions.length > 0) {
                setTimeout(() => {
                    this.showSmartSuggestions();
                }, 3000);
            }
            
            console.log('🧠 Smart suggestions initialized');
        } catch (error) {
            console.warn('⚠️ Failed to initialize smart suggestions:', error);
        }
    }

    analyzeUserBehavior() {
        // Analyze theme switching patterns
        const switchCount = localStorage.getItem('themeSwitchCount') || 0;
        const lastSwitch = localStorage.getItem('lastThemeSwitch') || 0;
        
        if (switchCount > 10 && lastSwitch > 100) {
            this.smartSuggestions.push({
                type: 'performance',
                suggestion: 'Consider using keyboard shortcuts for faster theme switching',
                reason: 'You switch themes frequently',
                confidence: 0.9
            });
        }
        
        // Analyze device usage
        const deviceSwitches = localStorage.getItem('deviceSwitchCount') || 0;
        if (deviceSwitches === 0 && window.innerWidth < 768) {
            this.smartSuggestions.push({
                type: 'device',
                suggestion: 'mobile',
                reason: 'Your screen size suggests mobile optimization would be better',
                confidence: 0.85
            });
        }
    }

    showSmartSuggestions() {
        if (this.smartSuggestions.length === 0) return;
        
        const suggestion = this.smartSuggestions[0];
        const message = `💡 Smart suggestion: ${suggestion.suggestion} (${suggestion.reason})`;
        
        this.showNotification(message, 'info', 8000);
    }

    setupPerformanceMonitoring() {
        try {
            // Real-time FPS monitoring
            this.startFPSMonitoring();
            
            // Memory usage tracking
            this.startMemoryMonitoring();
            
            // Render time tracking
            this.startRenderTimeMonitoring();
            
            console.log('📊 Advanced performance monitoring initialized');
        } catch (error) {
            console.warn('⚠️ Failed to setup performance monitoring:', error);
        }
    }

    startFPSMonitoring() {
        let frames = 0;
        let lastTime = performance.now();
        
        const measureFPS = () => {
            frames++;
            const currentTime = performance.now();
            
            if (currentTime >= lastTime + 1000) {
                this.performanceMetrics.fps = Math.round((frames * 1000) / (currentTime - lastTime));
                frames = 0;
                lastTime = currentTime;
                
                // Update performance display if visible
                this.updatePerformanceDisplay();
            }
            
            requestAnimationFrame(measureFPS);
        };
        
        measureFPS();
    }

    startMemoryMonitoring() {
        if (!performance.memory) return;
        
        setInterval(() => {
            this.performanceMetrics.memoryUsage = performance.memory.usedJSHeapSize;
            this.performanceMetrics.memoryLimit = performance.memory.jsHeapSizeLimit;
        }, 1000);
    }

    startRenderTimeMonitoring() {
        const originalRequestAnimationFrame = window.requestAnimationFrame;
        
        window.requestAnimationFrame = (callback) => {
            return originalRequestAnimationFrame((timestamp) => {
                const start = performance.now();
                callback(timestamp);
                const end = performance.now();
                this.performanceMetrics.renderTime = end - start;
            });
        };
    }

    updatePerformanceDisplay() {
        const indicator = document.getElementById('performanceIndicator');
        if (indicator && indicator.style.display !== 'none') {
            const fpsElement = document.getElementById('currentFps');
            const memoryElement = document.getElementById('currentMemory');
            const particleElement = document.getElementById('currentParticles');
            
            if (fpsElement) fpsElement.textContent = this.performanceMetrics.fps;
            if (memoryElement && this.performanceMetrics.memoryUsage) {
                const usedMB = Math.round(this.performanceMetrics.memoryUsage / 1024 / 1024);
                memoryElement.textContent = `${usedMB}MB`;
            }
            if (particleElement && this.particleSystem) {
                particleElement.textContent = this.particleSystem.particles.length;
            }
        }
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

    startAdvancedParticleAnimation() {
        if (!this.particleSystem || !this.particleSystem.isActive) return;
        
        const animate = () => {
            if (!this.particleSystem.isActive) return;
            
            const { ctx, particles, maxParticles, forces } = this.particleSystem;
            
            // Clear canvas with fade effect
            ctx.fillStyle = 'rgba(0, 0, 0, 0.05)';
            ctx.fillRect(0, 0, ctx.canvas.width, ctx.canvas.height);
            
            // Update forces
            for (let i = forces.length - 1; i >= 0; i--) {
                const force = forces[i];
                force.life--;
                
                if (force.life <= 0) {
                    forces.splice(i, 1);
                }
            }
            
            // Update and draw particles
            for (let i = particles.length - 1; i >= 0; i--) {
                const particle = particles[i];
                
                // Apply forces
                forces.forEach(force => {
                    const dx = force.x - particle.x;
                    const dy = force.y - particle.y;
                    const distance = Math.sqrt(dx * dx + dy * dy);
                    
                    if (distance < force.radius) {
                        const forceStrength = (1 - distance / force.radius) * force.strength;
                        particle.vx += (dx / distance) * forceStrength;
                        particle.vy += (dy / distance) * forceStrength;
                    }
                });
                
                // Update position based on particle type
                this.updateParticleByType(particle);
                
                // Update common properties
                particle.pulse += particle.pulseSpeed;
                particle.life--;
                
                // Remove dead particles or out-of-bounds
                if (this.shouldRemoveParticle(particle, ctx)) {
                    particles.splice(i, 1);
                    continue;
                }
                
                // Draw particle based on type
                this.drawParticleByType(ctx, particle);
            }
            
            // Add new particles if needed
            if (particles.length < maxParticles && Math.random() < 0.1) {
                this.addNewParticle();
            }
            
            // Update performance metrics
            this.performanceMetrics.particleCount = particles.length;
            
            this.particleSystem.animationId = requestAnimationFrame(animate);
        };
        
        animate();
    }

    updateParticleByType(particle) {
        switch (particle.type) {
            case 'standard':
            case 'explosion':
                particle.x += particle.vx;
                particle.y += particle.vy;
                if (particle.gravity) {
                    particle.vy += particle.gravity;
                }
                if (particle.friction) {
                    particle.vx *= particle.friction;
                    particle.vy *= particle.friction;
                }
                break;
                
            case 'fireworks':
                particle.x += particle.vx;
                particle.y += particle.vy;
                particle.vy += particle.gravity || 0.1;
                particle.vx *= particle.friction || 0.98;
                particle.vy *= particle.friction || 0.98;
                break;
                
            case 'snow':
                particle.x += particle.vx;
                particle.y += particle.vy;
                particle.rotation += particle.rotationSpeed;
                // Add slight horizontal drift
                particle.vx += (Math.random() - 0.5) * 0.1;
                break;
                
            case 'rain':
                particle.x += particle.vx;
                particle.y += particle.vy;
                // Add wind effect
                particle.vx += Math.sin(Date.now() * 0.001) * 0.1;
                break;
                
            case 'stars':
                // Stars don't move, just twinkle
                particle.twinkle += particle.twinkleSpeed;
                break;
                
            case 'bubbles':
                particle.x += particle.vx + Math.sin(particle.wobble) * 0.5;
                particle.y += particle.vy;
                particle.wobble += particle.wobbleSpeed;
                break;
        }
    }

    shouldRemoveParticle(particle, ctx) {
        if (particle.life <= 0) return true;
        
        switch (particle.type) {
            case 'snow':
            case 'rain':
                return particle.y > ctx.canvas.height + 10;
                
            case 'bubbles':
                return particle.y < -10;
                
            case 'stars':
                return false; // Stars persist
                
            default:
                return particle.x < -10 || particle.x > ctx.canvas.width + 10 ||
                       particle.y < -10 || particle.y > ctx.canvas.height + 10;
        }
    }

    drawParticleByType(ctx, particle) {
        ctx.save();
        ctx.globalAlpha = particle.opacity * (particle.life / particle.maxLife);
        
        switch (particle.type) {
            case 'standard':
            case 'explosion':
                this.drawStandardParticle(ctx, particle);
                break;
                
            case 'fireworks':
                this.drawFireworkParticle(ctx, particle);
                break;
                
            case 'snow':
                this.drawSnowflake(ctx, particle);
                break;
                
            case 'rain':
                this.drawRaindrop(ctx, particle);
                break;
                
            case 'stars':
                this.drawStar(ctx, particle);
                break;
                
            case 'bubbles':
                this.drawBubble(ctx, particle);
                break;
        }
        
        ctx.restore();
    }

    drawStandardParticle(ctx, particle) {
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
        
        // Glow effect
        ctx.shadowColor = particle.color;
        ctx.shadowBlur = size * 2;
        ctx.beginPath();
        ctx.arc(particle.x, particle.y, size * 0.5, 0, Math.PI * 2);
        ctx.fill();
    }

    drawFireworkParticle(ctx, particle) {
        ctx.fillStyle = particle.color;
        ctx.beginPath();
        ctx.arc(particle.x, particle.y, particle.size, 0, Math.PI * 2);
        ctx.fill();
        
        // Trail effect
        if (this.particleEffects.trails) {
            ctx.strokeStyle = particle.color;
            ctx.lineWidth = particle.size * 0.5;
            ctx.beginPath();
            ctx.moveTo(particle.x, particle.y);
            ctx.lineTo(particle.x - particle.vx * 3, particle.y - particle.vy * 3);
            ctx.stroke();
        }
    }

    drawSnowflake(ctx, particle) {
        ctx.save();
        ctx.translate(particle.x, particle.y);
        ctx.rotate(particle.rotation);
        
        ctx.strokeStyle = particle.color;
        ctx.lineWidth = 1;
        
        // Draw snowflake shape
        for (let i = 0; i < 6; i++) {
            ctx.beginPath();
            ctx.moveTo(0, 0);
            ctx.lineTo(0, particle.size);
            ctx.stroke();
            ctx.rotate(Math.PI / 3);
        }
        
        ctx.restore();
    }

    drawRaindrop(ctx, particle) {
        ctx.strokeStyle = particle.color;
        ctx.lineWidth = particle.size;
        ctx.lineCap = 'round';
        ctx.beginPath();
        ctx.moveTo(particle.x, particle.y);
        ctx.lineTo(particle.x, particle.y + particle.length);
        ctx.stroke();
    }

    drawStar(ctx, particle) {
        const twinkleScale = 0.5 + Math.sin(particle.twinkle) * 0.5;
        const size = particle.size * twinkleScale;
        
        ctx.fillStyle = particle.color;
        ctx.shadowColor = particle.color;
        ctx.shadowBlur = size * 3;
        
        // Draw star shape
        ctx.beginPath();
        for (let i = 0; i < 5; i++) {
            const angle = (i * Math.PI * 2) / 5 - Math.PI / 2;
            const x = particle.x + Math.cos(angle) * size;
            const y = particle.y + Math.sin(angle) * size;
            
            if (i === 0) ctx.moveTo(x, y);
            else ctx.lineTo(x, y);
        }
        ctx.closePath();
        ctx.fill();
    }

    drawBubble(ctx, particle) {
        // Main bubble
        ctx.strokeStyle = particle.color;
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.arc(particle.x, particle.y, particle.size, 0, Math.PI * 2);
        ctx.stroke();
        
        // Bubble highlight
        ctx.fillStyle = 'rgba(255, 255, 255, 0.3)';
        ctx.beginPath();
        ctx.arc(particle.x - particle.size * 0.3, particle.y - particle.size * 0.3, particle.size * 0.2, 0, Math.PI * 2);
        ctx.fill();
    }

    addNewParticle() {
        if (!this.particleSystem) return;
        this.addParticleByType(this.particleEffects.currentType);
    }

    addParticleByType(type) {
        if (!this.particleSystem) return;
        
        const { ctx, particles } = this.particleSystem;
        const baseParticle = {
            x: Math.random() * ctx.canvas.width,
            y: Math.random() * ctx.canvas.height,
            size: Math.random() * 3 + 1,
            opacity: Math.random() * 0.5 + 0.3,
            color: this.getRandomThemeColor(),
            pulse: Math.random() * Math.PI * 2,
            pulseSpeed: Math.random() * 0.02 + 0.01,
            life: Math.random() * 100 + 50,
            maxLife: 150,
            type: type
        };
        
        switch (type) {
            case 'standard':
                particles.push({
                    ...baseParticle,
                    vx: (Math.random() - 0.5) * 2,
                    vy: (Math.random() - 0.5) * 2
                });
                break;
                
            case 'fireworks':
                particles.push({
                    ...baseParticle,
                    vx: (Math.random() - 0.5) * 8,
                    vy: (Math.random() - 0.5) * 8,
                    gravity: 0.1,
                    friction: 0.98,
                    size: Math.random() * 2 + 0.5,
                    sparkles: []
                });
                break;
                
            case 'snow':
                particles.push({
                    ...baseParticle,
                    x: Math.random() * ctx.canvas.width,
                    y: -10,
                    vx: (Math.random() - 0.5) * 1,
                    vy: Math.random() * 2 + 1,
                    size: Math.random() * 4 + 2,
                    color: '#ffffff',
                    rotation: Math.random() * Math.PI * 2,
                    rotationSpeed: (Math.random() - 0.5) * 0.1
                });
                break;
                
            case 'rain':
                particles.push({
                    ...baseParticle,
                    x: Math.random() * ctx.canvas.width,
                    y: -10,
                    vx: Math.random() * 2 - 1,
                    vy: Math.random() * 5 + 8,
                    size: Math.random() * 2 + 1,
                    color: '#4a90e2',
                    length: Math.random() * 10 + 5
                });
                break;
                
            case 'stars':
                particles.push({
                    ...baseParticle,
                    vx: 0,
                    vy: 0,
                    size: Math.random() * 2 + 0.5,
                    color: '#ffd700',
                    twinkle: Math.random() * Math.PI * 2,
                    twinkleSpeed: Math.random() * 0.05 + 0.02
                });
                break;
                
            case 'bubbles':
                particles.push({
                    ...baseParticle,
                    y: ctx.canvas.height + 10,
                    vx: (Math.random() - 0.5) * 1,
                    vy: -(Math.random() * 2 + 1),
                    size: Math.random() * 8 + 4,
                    color: 'rgba(100, 200, 255, 0.6)',
                    wobble: Math.random() * Math.PI * 2,
                    wobbleSpeed: Math.random() * 0.03 + 0.01
                });
                break;
        }
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
                
                // Import accessibility settings
                if (settings.accessibility) {
                    this.accessibility = { ...this.accessibility, ...settings.accessibility };
                }
                
                // Import feature settings
                if (settings.features) {
                    this.features = { ...this.features, ...settings.features };
                }
                
                // Import audio settings
                if (settings.audioEffects) {
                    this.audioEffects = { ...this.audioEffects, ...settings.audioEffects };
                }
                
                // Import particle settings
                if (settings.particleEffects) {
                    this.particleEffects = { ...this.particleEffects, ...settings.particleEffects };
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

    // Advanced theme customization
    createCustomTheme(name, colors) {
        try {
            const customTheme = {
                id: `custom-${Date.now()}`,
                name: name,
                colors: colors,
                timestamp: Date.now(),
                author: 'User'
            };
            
            this.customThemes.set(customTheme.id, customTheme);
            this.saveCustomThemes();
            
            this.showNotification(`Custom theme "${name}" created!`, 'success');
            return customTheme.id;
        } catch (error) {
            console.error('❌ Failed to create custom theme:', error);
            return null;
        }
    }

    saveCustomThemes() {
        try {
            const themes = Array.from(this.customThemes.values());
            localStorage.setItem('customThemes', JSON.stringify(themes));
        } catch (error) {
            console.warn('⚠️ Failed to save custom themes:', error);
        }
    }

    applyCustomTheme(themeId) {
        const theme = this.customThemes.get(themeId);
        if (!theme) return false;
        
        try {
            const root = document.documentElement;
            
            // Apply custom colors
            Object.entries(theme.colors).forEach(([key, value]) => {
                root.style.setProperty(`--${key}`, value);
            });
            
            document.body.classList.add('theme-custom');
            this.currentTheme = themeId;
            
            this.showNotification(`Applied custom theme "${theme.name}"`, 'success');
            return true;
        } catch (error) {
            console.error('❌ Failed to apply custom theme:', error);
            return false;
        }
    }

    // Particle effect switching
    switchParticleEffect(type) {
        if (!this.particleEffects.types.includes(type)) return;
        
        this.particleEffects.currentType = type;
        localStorage.setItem('particleEffectType', type);
        
        // Clear existing particles and create new ones
        if (this.particleSystem) {
            this.particleSystem.particles = [];
            this.initializeParticlesByType();
        }
        
        this.showNotification(`Particle effect changed to ${type}`, 'success');
    }

    // Advanced accessibility features
    enableVoiceControl() {
        if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
            this.showNotification('Voice control not supported in this browser', 'error');
            return;
        }
        
        try {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            this.speechRecognition = new SpeechRecognition();
            
            this.speechRecognition.continuous = true;
            this.speechRecognition.interimResults = false;
            this.speechRecognition.lang = 'en-US';
            
            this.speechRecognition.onresult = (event) => {
                const command = event.results[event.results.length - 1][0].transcript.toLowerCase();
                this.processVoiceCommand(command);
            };
            
            this.speechRecognition.start();
            this.accessibility.voiceControl = true;
            this.showNotification('Voice control enabled! Try saying "switch to dark theme"', 'success');
        } catch (error) {
            console.error('❌ Failed to enable voice control:', error);
        }
    }

    processVoiceCommand(command) {
        if (command.includes('switch to') && command.includes('theme')) {
            if (command.includes('light')) this.switchTheme('light');
            else if (command.includes('dark')) this.switchTheme('dark');
            else if (command.includes('cs2') || command.includes('gaming')) this.switchTheme('cs2');
            else if (command.includes('premium')) this.switchTheme('premium');
        } else if (command.includes('select') && command.includes('device')) {
            if (command.includes('desktop')) this.selectDevice('desktop');
            else if (command.includes('tablet')) this.selectDevice('tablet');
            else if (command.includes('mobile')) this.selectDevice('mobile');
        } else if (command.includes('enable') || command.includes('disable')) {
            if (command.includes('particles')) this.toggleParticles();
            else if (command.includes('audio')) this.toggleAudio();
            else if (command.includes('high contrast')) this.toggleHighContrast();
        }
    }

    // Performance dashboard
    showPerformanceDashboard() {
        const dashboard = document.createElement('div');
        dashboard.id = 'performanceDashboard';
        dashboard.className = 'performance-dashboard';
        dashboard.innerHTML = `
            <div class="dashboard-header">
                <h3>📊 Performance Dashboard</h3>
                <button onclick="this.parentElement.parentElement.remove()">×</button>
            </div>
            <div class="dashboard-content">
                <div class="metric">
                    <label>FPS:</label>
                    <span id="dashboardFps">${this.performanceMetrics.fps}</span>
                </div>
                <div class="metric">
                    <label>Memory:</label>
                    <span id="dashboardMemory">${Math.round(this.performanceMetrics.memoryUsage / 1024 / 1024)}MB</span>
                </div>
                <div class="metric">
                    <label>Particles:</label>
                    <span id="dashboardParticles">${this.particleSystem ? this.particleSystem.particles.length : 0}</span>
                </div>
                <div class="metric">
                    <label>Theme Switches:</label>
                    <span>${this.performanceMetrics.themeSwitchCount}</span>
                </div>
                <div class="metric">
                    <label>Notifications:</label>
                    <span>${this.performanceMetrics.totalNotifications}</span>
                </div>
                <div class="dashboard-actions">
                    <button onclick="window.themeManager.optimizePerformance()">Optimize</button>
                    <button onclick="window.themeManager.toggleAudioVisualizer()">Audio Visualizer</button>
                    <button onclick="window.themeManager.exportPerformanceReport()">Export Report</button>
                </div>
            </div>
        `;
        
        document.body.appendChild(dashboard);
        
        // Update dashboard every second
        const updateInterval = setInterval(() => {
            if (!document.getElementById('performanceDashboard')) {
                clearInterval(updateInterval);
                return;
            }
            
            const fpsEl = document.getElementById('dashboardFps');
            const memoryEl = document.getElementById('dashboardMemory');
            const particlesEl = document.getElementById('dashboardParticles');
            
            if (fpsEl) fpsEl.textContent = this.performanceMetrics.fps;
            if (memoryEl) memoryEl.textContent = Math.round(this.performanceMetrics.memoryUsage / 1024 / 1024) + 'MB';
            if (particlesEl) particlesEl.textContent = this.particleSystem ? this.particleSystem.particles.length : 0;
        }, 1000);
    }

    exportPerformanceReport() {
        try {
            const report = {
                ...this.getPerformanceReport(),
                browserInfo: {
                    userAgent: navigator.userAgent,
                    platform: navigator.platform,
                    hardwareConcurrency: navigator.hardwareConcurrency,
                    deviceMemory: navigator.deviceMemory,
                    connection: navigator.connection ? {
                        effectiveType: navigator.connection.effectiveType,
                        downlink: navigator.connection.downlink
                    } : null
                },
                timestamp: new Date().toISOString()
            };
            
            const dataStr = JSON.stringify(report, null, 2);
            const dataBlob = new Blob([dataStr], { type: 'application/json' });
            
            const link = document.createElement('a');
            link.href = URL.createObjectURL(dataBlob);
            link.download = `performance-report-${Date.now()}.json`;
            link.click();
            
            this.showNotification('Performance report exported!', 'success');
        } catch (error) {
            console.error('❌ Failed to export performance report:', error);
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

// Enhanced global functions
window.switchParticleEffect = (type) => {
    if (window.themeManager) {
        window.themeManager.switchParticleEffect(type);
    }
};

window.setVolume = (volume) => {
    if (window.themeManager) {
        window.themeManager.setVolume(volume);
    }
};

window.toggleAudioVisualizer = () => {
    if (window.themeManager) {
        window.themeManager.toggleAudioVisualizer();
    }
};

window.showPerformanceDashboard = () => {
    if (window.themeManager) {
        window.themeManager.showPerformanceDashboard();
    }
};

window.enableVoiceControl = () => {
    if (window.themeManager) {
        window.themeManager.enableVoiceControl();
    }
};

window.createCustomTheme = (name, colors) => {
    if (window.themeManager) {
        return window.themeManager.createCustomTheme(name, colors);
    }
};

window.applyCustomTheme = (themeId) => {
    if (window.themeManager) {
        return window.themeManager.applyCustomTheme(themeId);
    }
};

window.toggleColorBlindSupport = () => {
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
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    console.log('🚀 DOM ready - Theme manager initialized');
    
    if (window.themeManager) {
        window.themeManager.optimizePerformance();
    }
});

console.log('🎨 Unified Theme Manager loaded successfully!');