/**
 * Ultra Premium Features JavaScript
 * Advanced interactive features for the CS2 cheat forum
 */

console.log('🚀 Ultra Premium Features loading...');

// Global configuration
const ULTRA_CONFIG = {
    animations: {
        enabled: true,
        duration: 300,
        easing: 'cubic-bezier(0.4, 0, 0.2, 1)'
    },
    effects: {
        particles: true,
        typing: true,
        counters: true,
        liveUpdates: true
    },
    sounds: {
        enabled: true,
        volume: 0.3
    }
};

// Ultra Premium Animation Manager
class UltraPremiumAnimations {
    constructor() {
        this.observers = new Map();
        this.counters = new Map();
        this.typingElements = new Map();
        this.init();
    }
    
    init() {
        this.setupIntersectionObserver();
        this.setupTypingEffect();
        this.setupCounterAnimations();
        this.setupParticleEffects();
        this.setupLiveUpdates();
        console.log('✨ Ultra Premium Animations initialized');
    }
    
    setupIntersectionObserver() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.triggerAnimation(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '50px'
        });
        
        // Observe all animated elements
        document.querySelectorAll('.animate-fade-in-up, .animate-fade-in-left, .animate-fade-in-right, .animate-fade-in-down').forEach(el => {
            observer.observe(el);
        });
        
        this.observers.set('intersection', observer);
    }
    
    triggerAnimation(element) {
        element.style.opacity = '1';
        element.style.transform = 'translate(0, 0)';
        
        // Add extra effects for special elements
        if (element.classList.contains('stat-card')) {
            this.animateStatCard(element);
        }
        
        if (element.classList.contains('feature-card')) {
            this.animateFeatureCard(element);
        }
    }
    
    animateStatCard(card) {
        const number = card.querySelector('.stat-number');
        if (number && number.dataset.count) {
            this.animateCounter(number, parseInt(number.dataset.count));
        }
    }
    
    animateFeatureCard(card) {
        const icon = card.querySelector('.feature-icon');
        if (icon) {
            setTimeout(() => {
                icon.style.animation = 'bounce 0.6s ease-out';
            }, 200);
        }
    }
    
    setupTypingEffect() {
        const typingTitle = document.getElementById('typingTitle');
        if (typingTitle) {
            const originalText = typingTitle.textContent;
            typingTitle.textContent = '';
            
            let i = 0;
            const typeInterval = setInterval(() => {
                if (i < originalText.length) {
                    typingTitle.textContent += originalText.charAt(i);
                    i++;
                    
                    // Add sound effect
                    this.playTypingSound();
                } else {
                    clearInterval(typeInterval);
                    // Start cursor blinking
                    document.querySelector('.title-cursor').style.animation = 'blink 1s infinite';
                }
            }, 150);
        }
    }
    
    setupCounterAnimations() {
        document.querySelectorAll('[data-count]').forEach(element => {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const target = parseInt(entry.target.dataset.count) || 0;
                        this.animateCounter(entry.target, target);
                        observer.unobserve(entry.target);
                    }
                });
            });
            
            observer.observe(element);
        });
    }
    
    animateCounter(element, target) {
        if (this.counters.has(element)) return;
        
        let current = 0;
        const increment = target / 100;
        const duration = 2000;
        const stepTime = duration / 100;
        
        const counter = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(counter);
                this.counters.delete(element);
            }
            
            element.textContent = Math.floor(current).toLocaleString();
        }, stepTime);
        
        this.counters.set(element, counter);
    }
    
    setupParticleEffects() {
        if (!ULTRA_CONFIG.effects.particles) return;
        
        const heroParticles = document.getElementById('heroParticles');
        if (heroParticles) {
            this.createParticleSystem(heroParticles);
        }
    }
    
    createParticleSystem(container) {
        const canvas = document.createElement('canvas');
        canvas.width = container.offsetWidth;
        canvas.height = container.offsetHeight;
        canvas.style.position = 'absolute';
        canvas.style.top = '0';
        canvas.style.left = '0';
        canvas.style.pointerEvents = 'none';
        container.appendChild(canvas);
        
        const ctx = canvas.getContext('2d');
        const particles = [];
        
        // Create particles
        for (let i = 0; i < 50; i++) {
            particles.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height,
                vx: (Math.random() - 0.5) * 2,
                vy: (Math.random() - 0.5) * 2,
                size: Math.random() * 3 + 1,
                opacity: Math.random() * 0.5 + 0.3,
                color: `rgba(255, 107, 53, ${Math.random() * 0.5 + 0.3})`
            });
        }
        
        // Animate particles
        const animate = () => {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            particles.forEach(particle => {
                particle.x += particle.vx;
                particle.y += particle.vy;
                
                // Wrap around edges
                if (particle.x < 0) particle.x = canvas.width;
                if (particle.x > canvas.width) particle.x = 0;
                if (particle.y < 0) particle.y = canvas.height;
                if (particle.y > canvas.height) particle.y = 0;
                
                // Draw particle
                ctx.beginPath();
                ctx.arc(particle.x, particle.y, particle.size, 0, Math.PI * 2);
                ctx.fillStyle = particle.color;
                ctx.globalAlpha = particle.opacity;
                ctx.fill();
                
                // Add glow effect
                ctx.shadowColor = particle.color;
                ctx.shadowBlur = particle.size * 2;
                ctx.fill();
                ctx.shadowBlur = 0;
            });
            
            requestAnimationFrame(animate);
        };
        
        animate();
        
        // Handle resize
        window.addEventListener('resize', () => {
            canvas.width = container.offsetWidth;
            canvas.height = container.offsetHeight;
        });
    }
    
    setupLiveUpdates() {
        if (!ULTRA_CONFIG.effects.liveUpdates) return;
        
        // Update live stats every 5 seconds
        setInterval(() => {
            this.updateLiveStats();
        }, 5000);
        
        // Update ping every 2 seconds
        setInterval(() => {
            this.updatePing();
        }, 2000);
        
        // Update online user count
        setInterval(() => {
            this.updateOnlineUsers();
        }, 3000);
    }
    
    updateLiveStats() {
        const stats = [
            { id: 'activeCheaters', min: 800, max: 1500 },
            { id: 'matchesWon', min: 15000, max: 25000 },
            { id: 'headshotsToday', min: 50000, max: 100000 },
            { id: 'liveUsers', min: 1200, max: 2500 }
        ];
        
        stats.forEach(stat => {
            const element = document.getElementById(stat.id);
            if (element) {
                const newValue = Math.floor(Math.random() * (stat.max - stat.min) + stat.min);
                this.animateValueChange(element, newValue);
            }
        });
    }
    
    updatePing() {
        const pingElement = document.getElementById('pingValue');
        if (pingElement) {
            const newPing = Math.floor(Math.random() * 20) + 8; // 8-28ms
            pingElement.textContent = newPing + 'ms';
            
            // Color coding
            if (newPing < 15) {
                pingElement.style.color = 'var(--accent-success)';
            } else if (newPing < 25) {
                pingElement.style.color = 'var(--accent-warning)';
            } else {
                pingElement.style.color = 'var(--accent-danger)';
            }
        }
    }
    
    updateOnlineUsers() {
        const memberCount = document.getElementById('memberCount');
        if (memberCount) {
            const currentCount = parseInt(memberCount.textContent.replace(/[^\d]/g, ''));
            const change = Math.floor(Math.random() * 10) - 5; // -5 to +5
            const newCount = Math.max(0, currentCount + change);
            memberCount.textContent = newCount.toLocaleString() + '+';
        }
    }
    
    animateValueChange(element, newValue) {
        const currentValue = parseInt(element.textContent.replace(/[^\d]/g, ''));
        if (currentValue === newValue) return;
        
        element.style.transform = 'scale(1.1)';
        element.style.color = 'var(--accent-primary)';
        
        setTimeout(() => {
            element.textContent = newValue.toLocaleString();
            element.style.transform = 'scale(1)';
            element.style.color = '';
        }, 150);
    }
    
    playTypingSound() {
        if (!ULTRA_CONFIG.sounds.enabled || !window.AudioContext) return;
        
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
            oscillator.type = 'square';
            
            gainNode.gain.setValueAtTime(0, audioContext.currentTime);
            gainNode.gain.linearRampToValueAtTime(ULTRA_CONFIG.sounds.volume * 0.1, audioContext.currentTime + 0.01);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.1);
        } catch (error) {
            console.warn('Failed to play typing sound:', error);
        }
    }
}

// Enhanced Interactive Features
class UltraPremiumInteractions {
    constructor() {
        this.init();
    }
    
    init() {
        this.setupHoverEffects();
        this.setupClickEffects();
        this.setupScrollEffects();
        this.setupKeyboardShortcuts();
        console.log('🎮 Ultra Premium Interactions initialized');
    }
    
    setupHoverEffects() {
        // Enhanced hover effects for cards
        document.addEventListener('mouseenter', (e) => {
            if (e.target.matches('.ultra-premium-card, .stat-card, .feature-card')) {
                this.addHoverGlow(e.target);
            }
        }, true);
        
        document.addEventListener('mouseleave', (e) => {
            if (e.target.matches('.ultra-premium-card, .stat-card, .feature-card')) {
                this.removeHoverGlow(e.target);
            }
        }, true);
        
        // Button hover effects
        document.addEventListener('mouseenter', (e) => {
            if (e.target.matches('.btn-ultra-premium, .btn-outline-ultra-premium, .btn-download-premium')) {
                this.addButtonHoverEffect(e.target);
            }
        }, true);
    }
    
    addHoverGlow(element) {
        if (!element.querySelector('.hover-glow')) {
            const glow = document.createElement('div');
            glow.className = 'hover-glow';
            glow.style.cssText = `
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(135deg, rgba(255, 107, 53, 0.1), rgba(255, 107, 53, 0.05));
                border-radius: inherit;
                opacity: 0;
                transition: opacity 0.3s ease;
                pointer-events: none;
                z-index: 1;
            `;
            element.appendChild(glow);
            
            requestAnimationFrame(() => {
                glow.style.opacity = '1';
            });
        }
    }
    
    removeHoverGlow(element) {
        const glow = element.querySelector('.hover-glow');
        if (glow) {
            glow.style.opacity = '0';
            setTimeout(() => {
                if (glow.parentNode) {
                    glow.remove();
                }
            }, 300);
        }
    }
    
    addButtonHoverEffect(button) {
        // Create ripple effect
        const rect = button.getBoundingClientRect();
        const ripple = document.createElement('div');
        ripple.className = 'button-ripple';
        ripple.style.cssText = `
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(255, 255, 255, 0.6);
            border-radius: 50%;
            transform: scale(0);
            animation: ripple-effect 0.6s linear;
            pointer-events: none;
            z-index: 10;
            left: 50%;
            top: 50%;
            margin-left: -2px;
            margin-top: -2px;
        `;
        
        button.appendChild(ripple);
        
        setTimeout(() => {
            if (ripple.parentNode) {
                ripple.remove();
            }
        }, 600);
    }
    
    setupClickEffects() {
        document.addEventListener('click', (e) => {
            // Add click ripple to buttons
            if (e.target.closest('.btn-ultra-premium, .btn-outline-ultra-premium, .btn-download-premium')) {
                this.createClickRipple(e);
            }
            
            // Special effects for different elements
            if (e.target.closest('.stat-card')) {
                this.pulseStatCard(e.target.closest('.stat-card'));
            }
            
            if (e.target.closest('.feature-card')) {
                this.bounceFeatureCard(e.target.closest('.feature-card'));
            }
        });
    }
    
    createClickRipple(e) {
        const button = e.target.closest('button, a');
        const rect = button.getBoundingClientRect();
        const ripple = document.createElement('div');
        
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        
        ripple.style.cssText = `
            position: absolute;
            width: ${size}px;
            height: ${size}px;
            left: ${x}px;
            top: ${y}px;
            background: rgba(255, 255, 255, 0.4);
            border-radius: 50%;
            transform: scale(0);
            animation: ripple-expand 0.6s ease-out;
            pointer-events: none;
            z-index: 10;
        `;
        
        button.appendChild(ripple);
        
        setTimeout(() => {
            if (ripple.parentNode) {
                ripple.remove();
            }
        }, 600);
    }
    
    pulseStatCard(card) {
        card.style.animation = 'pulse-card 0.3s ease-out';
        setTimeout(() => {
            card.style.animation = '';
        }, 300);
    }
    
    bounceFeatureCard(card) {
        card.style.animation = 'bounce-card 0.5s ease-out';
        setTimeout(() => {
            card.style.animation = '';
        }, 500);
    }
    
    setupScrollEffects() {
        let ticking = false;
        
        const updateScrollEffects = () => {
            const scrolled = window.pageYOffset;
            const rate = scrolled * -0.5;
            
            // Parallax effect for hero background
            const heroBackground = document.querySelector('.hero-background');
            if (heroBackground) {
                heroBackground.style.transform = `translateY(${rate}px)`;
            }
            
            // Update navbar transparency
            const navbar = document.querySelector('.navbar');
            if (navbar) {
                const opacity = Math.min(scrolled / 100, 0.95);
                navbar.style.background = `rgba(26, 26, 26, ${opacity})`;
                navbar.style.backdropFilter = scrolled > 50 ? 'blur(20px)' : 'none';
            }
            
            ticking = false;
        };
        
        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(updateScrollEffects);
                ticking = true;
            }
        });
    }
    
    setupKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Premium shortcuts
            if (e.ctrlKey && e.shiftKey) {
                switch (e.key) {
                    case 'A':
                        e.preventDefault();
                        this.toggleAnimations();
                        break;
                    case 'P':
                        e.preventDefault();
                        this.toggleParticles();
                        break;
                    case 'S':
                        e.preventDefault();
                        this.toggleSounds();
                        break;
                    case 'L':
                        e.preventDefault();
                        this.toggleLiveUpdates();
                        break;
                }
            }
        });
    }
    
    toggleAnimations() {
        ULTRA_CONFIG.animations.enabled = !ULTRA_CONFIG.animations.enabled;
        document.body.classList.toggle('no-animations', !ULTRA_CONFIG.animations.enabled);
        this.showNotification(`Animations ${ULTRA_CONFIG.animations.enabled ? 'enabled' : 'disabled'}`, 'info');
    }
    
    toggleParticles() {
        ULTRA_CONFIG.effects.particles = !ULTRA_CONFIG.effects.particles;
        const particles = document.querySelectorAll('.hero-particles canvas');
        particles.forEach(canvas => {
            canvas.style.display = ULTRA_CONFIG.effects.particles ? 'block' : 'none';
        });
        this.showNotification(`Particles ${ULTRA_CONFIG.effects.particles ? 'enabled' : 'disabled'}`, 'info');
    }
    
    toggleSounds() {
        ULTRA_CONFIG.sounds.enabled = !ULTRA_CONFIG.sounds.enabled;
        this.showNotification(`Sounds ${ULTRA_CONFIG.sounds.enabled ? 'enabled' : 'disabled'}`, 'info');
    }
    
    toggleLiveUpdates() {
        ULTRA_CONFIG.effects.liveUpdates = !ULTRA_CONFIG.effects.liveUpdates;
        this.showNotification(`Live updates ${ULTRA_CONFIG.effects.liveUpdates ? 'enabled' : 'disabled'}`, 'info');
    }
    
    showNotification(message, type = 'info') {
        if (window.showNotification) {
            window.showNotification(message, type);
        } else {
            console.log(`Notification: ${message}`);
        }
    }
}

// CS2 Specific Features
class CS2Features {
    constructor() {
        this.init();
    }
    
    init() {
        this.setupStatusMonitoring();
        this.setupCheatInfo();
        this.setupVACMonitoring();
        console.log('🎯 CS2 Features initialized');
    }
    
    setupStatusMonitoring() {
        // Simulate real-time cheat status updates
        setInterval(() => {
            this.updateCheatStatus();
        }, 10000); // Every 10 seconds
    }
    
    updateCheatStatus() {
        const statusElements = document.querySelectorAll('.status-value');
        statusElements.forEach(element => {
            if (element.textContent.includes('ago')) {
                const minutes = Math.floor(Math.random() * 60) + 1;
                element.textContent = `${minutes}m ago`;
            }
        });
        
        // Update server load
        const serverLoad = document.querySelector('.stat-value.good');
        if (serverLoad && serverLoad.textContent.includes('%')) {
            const load = Math.floor(Math.random() * 30) + 10;
            serverLoad.textContent = `${load}%`;
            
            // Color coding
            if (load < 20) {
                serverLoad.className = 'stat-value good';
            } else if (load < 50) {
                serverLoad.className = 'stat-value warning';
            } else {
                serverLoad.className = 'stat-value danger';
            }
        }
    }
    
    setupCheatInfo() {
        // Add interactive cheat information
        const cheatCards = document.querySelectorAll('.cheat-status-card');
        cheatCards.forEach(card => {
            card.addEventListener('click', () => {
                this.showCheatDetails();
            });
        });
    }
    
    showCheatDetails() {
        const modal = document.createElement('div');
        modal.className = 'cheat-details-modal';
        modal.innerHTML = `
            <div class="modal-backdrop" onclick="this.parentElement.remove()"></div>
            <div class="modal-content ultra-premium-card">
                <div class="modal-header">
                    <h3>🎮 Affinity Cheat Details</h3>
                    <button class="modal-close" onclick="this.closest('.cheat-details-modal').remove()">×</button>
                </div>
                <div class="modal-body">
                    <div class="cheat-feature">
                        <div class="feature-icon"><i class="fas fa-crosshairs"></i></div>
                        <div class="feature-info">
                            <h4>Aimbot</h4>
                            <p>Advanced targeting system with human-like movement</p>
                            <div class="feature-settings">
                                <span class="setting">Smoothness: 1-100</span>
                                <span class="setting">FOV: 1-180°</span>
                                <span class="setting">Bones: Head, Chest, Stomach</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="cheat-feature">
                        <div class="feature-icon"><i class="fas fa-eye"></i></div>
                        <div class="feature-info">
                            <h4>ESP/Wallhack</h4>
                            <p>See enemies, weapons, and items through walls</p>
                            <div class="feature-settings">
                                <span class="setting">Player ESP</span>
                                <span class="setting">Weapon ESP</span>
                                <span class="setting">Item ESP</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="cheat-feature">
                        <div class="feature-icon"><i class="fas fa-bolt"></i></div>
                        <div class="feature-info">
                            <h4>Triggerbot</h4>
                            <p>Automatic shooting when crosshair is on target</p>
                            <div class="feature-settings">
                                <span class="setting">Delay: 1-500ms</span>
                                <span class="setting">Hitchance: 1-100%</span>
                                <span class="setting">RCS: Enabled</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-ultra-premium" onclick="this.closest('.cheat-details-modal').remove()">
                        <div class="btn-content">
                            <i class="fas fa-download me-2"></i>
                            <span>Download Now</span>
                        </div>
                    </button>
                </div>
            </div>
        `;
        
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        `;
        
        document.body.appendChild(modal);
        
        // Animate in
        requestAnimationFrame(() => {
            modal.style.opacity = '1';
            modal.querySelector('.modal-content').style.transform = 'scale(1)';
        });
    }
    
    setupVACMonitoring() {
        // Simulate VAC status monitoring
        setInterval(() => {
            this.updateVACStatus();
        }, 30000); // Every 30 seconds
    }
    
    updateVACStatus() {
        const vacBanners = document.querySelectorAll('.vac-safety-banner');
        vacBanners.forEach(banner => {
            // Add pulse effect to show it's actively monitoring
            banner.style.animation = 'pulse-glow 0.5s ease-out';
            setTimeout(() => {
                banner.style.animation = 'pulse-glow 2s infinite';
            }, 500);
        });
    }
}

// Enhanced Theme Integration
class UltraPremiumThemeIntegration {
    constructor() {
        this.init();
    }
    
    init() {
        this.setupThemeTransitions();
        this.setupDynamicColors();
        console.log('🎨 Ultra Premium Theme Integration initialized');
    }
    
    setupThemeTransitions() {
        document.addEventListener('themeChanged', (e) => {
            this.animateThemeChange(e.detail.theme);
        });
    }
    
    animateThemeChange(theme) {
        // Create theme transition overlay
        const overlay = document.createElement('div');
        overlay.className = 'theme-transition-overlay';
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: ${this.getThemeColor(theme)};
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        `;
        
        document.body.appendChild(overlay);
        
        // Animate transition
        requestAnimationFrame(() => {
            overlay.style.opacity = '0.8';
        });
        
        setTimeout(() => {
            overlay.style.opacity = '0';
            setTimeout(() => {
                if (overlay.parentNode) {
                    overlay.remove();
                }
            }, 300);
        }, 300);
        
        // Update particle colors
        this.updateParticleColors(theme);
    }
    
    getThemeColor(theme) {
        const colors = {
            light: '#ffffff',
            dark: '#000000',
            cs2: '#ff6b35',
            premium: '#ffd700'
        };
        return colors[theme] || colors.light;
    }
    
    updateParticleColors(theme) {
        const particles = document.querySelectorAll('.hero-particles canvas');
        particles.forEach(canvas => {
            // Re-initialize particles with new colors
            const ctx = canvas.getContext('2d');
            if (ctx) {
                // This would update the particle system colors
                console.log(`Updating particle colors for theme: ${theme}`);
            }
        });
    }
    
    setupDynamicColors() {
        // Dynamic color updates based on time of day
        const updateTimeBasedColors = () => {
            const hour = new Date().getHours();
            let intensity = 1;
            
            if (hour >= 22 || hour <= 6) {
                // Night mode - reduce intensity
                intensity = 0.7;
            } else if (hour >= 7 && hour <= 9) {
                // Morning - warm colors
                intensity = 1.1;
            }
            
            document.documentElement.style.setProperty('--glow-intensity', intensity);
        };
        
        updateTimeBasedColors();
        setInterval(updateTimeBasedColors, 300000); // Every 5 minutes
    }
}

// Premium Sound Manager
class UltraPremiumSounds {
    constructor() {
        this.audioContext = null;
        this.sounds = new Map();
        this.init();
    }
    
    init() {
        if (ULTRA_CONFIG.sounds.enabled) {
            this.setupAudioContext();
            this.preloadSounds();
        }
        console.log('🔊 Ultra Premium Sounds initialized');
    }
    
    setupAudioContext() {
        try {
            this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
            
            // Resume on user interaction
            const resume = () => {
                if (this.audioContext.state === 'suspended') {
                    this.audioContext.resume();
                }
            };
            
            document.addEventListener('click', resume, { once: true });
            document.addEventListener('keydown', resume, { once: true });
        } catch (error) {
            console.warn('Failed to setup audio context:', error);
        }
    }
    
    preloadSounds() {
        // Preload common sound effects
        this.createSound('click', { frequency: 800, type: 'sine', duration: 0.1 });
        this.createSound('hover', { frequency: 600, type: 'sine', duration: 0.05 });
        this.createSound('success', { frequency: 880, type: 'triangle', duration: 0.3 });
        this.createSound('error', { frequency: 220, type: 'sawtooth', duration: 0.5 });
    }
    
    createSound(name, config) {
        this.sounds.set(name, config);
    }
    
    playSound(name) {
        if (!this.audioContext || !ULTRA_CONFIG.sounds.enabled) return;
        
        const config = this.sounds.get(name);
        if (!config) return;
        
        try {
            const oscillator = this.audioContext.createOscillator();
            const gainNode = this.audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(this.audioContext.destination);
            
            oscillator.frequency.setValueAtTime(config.frequency, this.audioContext.currentTime);
            oscillator.type = config.type;
            
            gainNode.gain.setValueAtTime(0, this.audioContext.currentTime);
            gainNode.gain.linearRampToValueAtTime(ULTRA_CONFIG.sounds.volume, this.audioContext.currentTime + 0.01);
            gainNode.gain.exponentialRampToValueAtTime(0.01, this.audioContext.currentTime + config.duration);
            
            oscillator.start(this.audioContext.currentTime);
            oscillator.stop(this.audioContext.currentTime + config.duration);
        } catch (error) {
            console.warn('Failed to play sound:', error);
        }
    }
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple-effect {
        to {
            transform: scale(40);
            opacity: 0;
        }
    }
    
    @keyframes ripple-expand {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
    
    @keyframes pulse-card {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.02); }
    }
    
    @keyframes bounce-card {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }
    
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    
    .modal-backdrop {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(10px);
    }
    
    .modal-content {
        position: relative;
        max-width: 600px;
        width: 100%;
        max-height: 80vh;
        overflow-y: auto;
        transform: scale(0.9);
        transition: transform 0.3s ease;
    }
    
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--border-primary);
        margin-bottom: 1rem;
    }
    
    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--text-secondary);
        cursor: pointer;
        transition: color 0.3s ease;
    }
    
    .modal-close:hover {
        color: var(--accent-danger);
    }
    
    .cheat-feature {
        display: flex;
        gap: 1rem;
        padding: 1rem;
        background: var(--bg-glass);
        border-radius: var(--radius-md);
        margin-bottom: 1rem;
        border: 1px solid var(--border-primary);
    }
    
    .cheat-feature .feature-icon {
        font-size: 2rem;
        color: var(--accent-primary);
        text-shadow: var(--glow-primary);
    }
    
    .feature-info h4 {
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }
    
    .feature-info p {
        color: var(--text-secondary);
        margin-bottom: 0.5rem;
    }
    
    .feature-settings {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    .setting {
        background: var(--bg-tertiary);
        padding: 0.2rem 0.5rem;
        border-radius: var(--radius-sm);
        font-size: 0.8rem;
        color: var(--text-muted);
    }
    
    .modal-footer {
        text-align: center;
        padding-top: 1rem;
        border-top: 1px solid var(--border-primary);
        margin-top: 1rem;
    }
    
    .no-animations * {
        animation: none !important;
        transition: none !important;
    }
`;
document.head.appendChild(style);

// Initialize everything when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    console.log('🚀 Initializing Ultra Premium Features...');
    
    // Initialize all managers
    window.ultraAnimations = new UltraPremiumAnimations();
    window.ultraInteractions = new UltraPremiumInteractions();
    window.cs2Features = new CS2Features();
    window.ultraSounds = new UltraPremiumSounds();
    window.ultraThemeIntegration = new UltraPremiumThemeIntegration();
    
    // Add sound effects to existing buttons
    document.addEventListener('click', (e) => {
        if (e.target.closest('button, .btn')) {
            window.ultraSounds?.playSound('click');
        }
    });
    
    document.addEventListener('mouseenter', (e) => {
        if (e.target.matches('button, .btn, .card')) {
            window.ultraSounds?.playSound('hover');
        }
    }, true);
    
    console.log('✅ Ultra Premium Features fully initialized!');
});

console.log('🎮 Ultra Premium Features loaded successfully!');