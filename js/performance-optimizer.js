/**
 * Ultra Premium Performance Optimizer
 * Ensures smooth animations and optimal performance
 */

console.log('🚀 Performance Optimizer loading...');

class UltraPremiumPerformanceOptimizer {
    constructor() {
        this.metrics = {
            fps: 0,
            memoryUsage: 0,
            loadTime: 0,
            animationFrames: 0
        };
        this.optimizations = {
            reducedMotion: false,
            lowPowerMode: false,
            adaptiveQuality: true
        };
        this.init();
    }
    
    init() {
        this.detectDeviceCapabilities();
        this.setupPerformanceMonitoring();
        this.optimizeForDevice();
        this.setupAdaptiveQuality();
        console.log('⚡ Performance Optimizer initialized');
    }
    
    detectDeviceCapabilities() {
        // Detect device performance level
        const deviceInfo = {
            cores: navigator.hardwareConcurrency || 4,
            memory: navigator.deviceMemory || 4,
            connection: navigator.connection?.effectiveType || '4g',
            isMobile: window.innerWidth < 768,
            isLowPower: navigator.getBattery ? false : true // Will be updated by battery API
        };
        
        // Update battery info if available
        if (navigator.getBattery) {
            navigator.getBattery().then(battery => {
                deviceInfo.isLowPower = !battery.charging && battery.level < 0.3;
                this.adjustForBattery(deviceInfo.isLowPower);
            });
        }
        
        // Determine performance level
        let performanceLevel = 'high';
        if (deviceInfo.cores < 4 || deviceInfo.memory < 4 || deviceInfo.isMobile) {
            performanceLevel = 'medium';
        }
        if (deviceInfo.cores < 2 || deviceInfo.memory < 2 || deviceInfo.connection === 'slow-2g') {
            performanceLevel = 'low';
        }
        
        this.applyPerformanceLevel(performanceLevel);
        console.log('📱 Device capabilities detected:', deviceInfo, 'Performance level:', performanceLevel);
    }
    
    applyPerformanceLevel(level) {
        const body = document.body;
        
        switch (level) {
            case 'low':
                body.classList.add('performance-low');
                this.optimizations.reducedMotion = true;
                this.optimizations.lowPowerMode = true;
                this.reduceParticleCount(10);
                this.disableHeavyAnimations();
                break;
                
            case 'medium':
                body.classList.add('performance-medium');
                this.reduceParticleCount(25);
                this.optimizeAnimations();
                break;
                
            case 'high':
            default:
                body.classList.add('performance-high');
                this.enableAllFeatures();
                break;
        }
        
        console.log(`⚡ Applied performance level: ${level}`);
    }
    
    reduceParticleCount(count) {
        // Reduce particle count for better performance
        if (window.themeManager && window.themeManager.particleSystem) {
            window.themeManager.particleSystem.particleCount = count;
            window.themeManager.particleSystem.maxParticles = count * 2;
        }
    }
    
    disableHeavyAnimations() {
        const style = document.createElement('style');
        style.textContent = `
            .performance-low * {
                animation-duration: 0.1s !important;
                transition-duration: 0.1s !important;
            }
            
            .performance-low .hero-particles,
            .performance-low .profile-particles {
                display: none !important;
            }
            
            .performance-low .glow-pulse,
            .performance-low .fire-flicker,
            .performance-low .premium-glow {
                animation: none !important;
            }
        `;
        document.head.appendChild(style);
    }
    
    optimizeAnimations() {
        const style = document.createElement('style');
        style.textContent = `
            .performance-medium .hero-particles canvas,
            .performance-medium .profile-particles canvas {
                opacity: 0.5 !important;
            }
            
            .performance-medium .glow-pulse {
                animation-duration: 4s !important;
            }
        `;
        document.head.appendChild(style);
    }
    
    enableAllFeatures() {
        // All features enabled for high-performance devices
        console.log('🚀 All premium features enabled');
    }
    
    setupPerformanceMonitoring() {
        // Monitor FPS
        let frameCount = 0;
        let lastTime = performance.now();
        
        const countFrames = () => {
            frameCount++;
            const currentTime = performance.now();
            
            if (currentTime - lastTime >= 1000) {
                this.metrics.fps = frameCount;
                frameCount = 0;
                lastTime = currentTime;
                
                // Adaptive quality based on FPS
                if (this.optimizations.adaptiveQuality) {
                    this.adjustQualityBasedOnFPS(this.metrics.fps);
                }
            }
            
            requestAnimationFrame(countFrames);
        };
        
        requestAnimationFrame(countFrames);
        
        // Monitor memory usage
        if ('memory' in performance) {
            setInterval(() => {
                this.metrics.memoryUsage = performance.memory.usedJSHeapSize;
                this.checkMemoryUsage();
            }, 5000);
        }
        
        // Monitor load time
        window.addEventListener('load', () => {
            this.metrics.loadTime = performance.now();
            console.log(`📊 Page loaded in ${this.metrics.loadTime.toFixed(2)}ms`);
        });
    }
    
    adjustQualityBasedOnFPS(fps) {
        if (fps < 30 && !this.optimizations.lowPowerMode) {
            console.log('⚠️ Low FPS detected, reducing quality');
            this.optimizations.lowPowerMode = true;
            this.reduceParticleCount(15);
            this.optimizeAnimations();
        } else if (fps > 50 && this.optimizations.lowPowerMode) {
            console.log('✅ FPS improved, restoring quality');
            this.optimizations.lowPowerMode = false;
            this.enableAllFeatures();
        }
    }
    
    checkMemoryUsage() {
        const memoryMB = this.metrics.memoryUsage / 1024 / 1024;
        
        if (memoryMB > 100) {
            console.log('⚠️ High memory usage detected, optimizing');
            this.optimizeMemoryUsage();
        }
    }
    
    optimizeMemoryUsage() {
        // Clean up unused animations
        document.querySelectorAll('[style*="animation"]').forEach(el => {
            if (!el.getBoundingClientRect().top < window.innerHeight) {
                el.style.animation = 'none';
            }
        });
        
        // Garbage collect if possible
        if (window.gc) {
            window.gc();
        }
    }
    
    adjustForBattery(isLowPower) {
        if (isLowPower) {
            console.log('🔋 Low battery detected, enabling power saving mode');
            this.optimizations.lowPowerMode = true;
            this.applyPerformanceLevel('low');
        }
    }
    
    setupAdaptiveQuality() {
        // Intersection observer for animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                const element = entry.target;
                if (entry.isIntersecting) {
                    // Enable animations when in view
                    element.style.animationPlayState = 'running';
                } else {
                    // Pause animations when out of view
                    element.style.animationPlayState = 'paused';
                }
            });
        }, { threshold: 0.1 });
        
        // Observe animated elements
        document.querySelectorAll('[class*="animate-"], [style*="animation"]').forEach(el => {
            observer.observe(el);
        });
    }
    
    getPerformanceReport() {
        return {
            ...this.metrics,
            optimizations: this.optimizations,
            timestamp: Date.now()
        };
    }
}

// Lazy Loading Manager
class LazyLoadManager {
    constructor() {
        this.observer = null;
        this.init();
    }
    
    init() {
        this.setupImageLazyLoading();
        this.setupContentLazyLoading();
        console.log('🖼️ Lazy Loading Manager initialized');
    }
    
    setupImageLazyLoading() {
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
    
    setupContentLazyLoading() {
        const contentObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const element = entry.target;
                    this.loadContent(element);
                    contentObserver.unobserve(element);
                }
            });
        }, { rootMargin: '50px' });
        
        document.querySelectorAll('[data-lazy-load]').forEach(el => {
            contentObserver.observe(el);
        });
    }
    
    loadContent(element) {
        const contentType = element.dataset.lazyLoad;
        
        switch (contentType) {
            case 'stats':
                this.loadUserStats(element);
                break;
            case 'activity':
                this.loadUserActivity(element);
                break;
            case 'achievements':
                this.loadUserAchievements(element);
                break;
        }
    }
    
    loadUserStats(element) {
        // Simulate loading user stats
        element.innerHTML = '<div class="loading-spinner"></div>';
        
        setTimeout(() => {
            element.innerHTML = '<div class="stats-loaded">Stats loaded!</div>';
        }, 1000);
    }
    
    loadUserActivity(element) {
        // Simulate loading user activity
        element.innerHTML = '<div class="loading-spinner"></div>';
        
        setTimeout(() => {
            element.innerHTML = '<div class="activity-loaded">Activity loaded!</div>';
        }, 800);
    }
    
    loadUserAchievements(element) {
        // Simulate loading achievements
        element.innerHTML = '<div class="loading-spinner"></div>';
        
        setTimeout(() => {
            element.innerHTML = '<div class="achievements-loaded">Achievements loaded!</div>';
        }, 600);
    }
}

// Cache Manager
class CacheManager {
    constructor() {
        this.cache = new Map();
        this.maxCacheSize = 50;
        this.init();
    }
    
    init() {
        this.setupCaching();
        console.log('💾 Cache Manager initialized');
    }
    
    setupCaching() {
        // Cache AJAX responses
        const originalFetch = window.fetch;
        window.fetch = async (...args) => {
            const url = args[0];
            const cacheKey = this.getCacheKey(url);
            
            if (this.cache.has(cacheKey)) {
                console.log('📄 Serving from cache:', url);
                return Promise.resolve(this.cache.get(cacheKey));
            }
            
            const response = await originalFetch(...args);
            
            if (response.ok) {
                const clonedResponse = response.clone();
                this.setCache(cacheKey, clonedResponse);
            }
            
            return response;
        };
    }
    
    getCacheKey(url) {
        return typeof url === 'string' ? url : url.toString();
    }
    
    setCache(key, value) {
        if (this.cache.size >= this.maxCacheSize) {
            const firstKey = this.cache.keys().next().value;
            this.cache.delete(firstKey);
        }
        
        this.cache.set(key, value);
    }
    
    clearCache() {
        this.cache.clear();
        console.log('🗑️ Cache cleared');
    }
}

// Add performance optimization styles
const performanceStyles = document.createElement('style');
performanceStyles.textContent = `
    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid var(--border-primary);
        border-radius: 50%;
        border-top-color: var(--accent-primary);
        animation: spin 1s ease-in-out infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    .lazy {
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .lazy.loaded {
        opacity: 1;
    }
    
    /* Performance optimizations */
    .performance-low .glow-pulse,
    .performance-low .fire-flicker,
    .performance-low .premium-glow,
    .performance-low .wave-animation,
    .performance-low .grid-move {
        animation: none !important;
    }
    
    .performance-low .hero-particles,
    .performance-low .profile-particles,
    .performance-low .particle-system {
        display: none !important;
    }
    
    .performance-medium .glow-pulse {
        animation-duration: 4s !important;
    }
    
    .performance-medium .hero-particles,
    .performance-medium .profile-particles {
        opacity: 0.5 !important;
    }
    
    /* Reduce motion for accessibility */
    @media (prefers-reduced-motion: reduce) {
        .animate-fade-in-up,
        .animate-fade-in-left,
        .animate-fade-in-right,
        .animate-fade-in-down,
        .animate-pulse-glow {
            animation: none !important;
        }
        
        .hero-particles,
        .profile-particles,
        .particle-system {
            display: none !important;
        }
        
        * {
            transition-duration: 0.01ms !important;
            animation-duration: 0.01ms !important;
        }
    }
    
    /* Optimize for touch devices */
    @media (hover: none) {
        .ultra-premium-card:hover,
        .stat-card:hover,
        .feature-card:hover {
            transform: none !important;
        }
        
        .btn-ultra-premium:hover,
        .btn-outline-ultra-premium:hover {
            transform: none !important;
        }
    }
`;
document.head.appendChild(performanceStyles);

// Initialize performance optimization
document.addEventListener('DOMContentLoaded', () => {
    console.log('🚀 Initializing Performance Optimization...');
    
    window.performanceOptimizer = new UltraPremiumPerformanceOptimizer();
    window.lazyLoadManager = new LazyLoadManager();
    window.cacheManager = new CacheManager();
    
    // Optimize images
    document.querySelectorAll('img').forEach(img => {
        if (!img.complete) {
            img.addEventListener('load', () => {
                img.classList.add('loaded');
            });
        }
    });
    
    // Preload critical resources
    const preloadLinks = [
        'css/ultra-premium-components.css',
        'js/ultra-premium-features.js',
        'images/default-avatar.svg'
    ];
    
    preloadLinks.forEach(href => {
        const link = document.createElement('link');
        link.rel = 'preload';
        link.as = href.endsWith('.css') ? 'style' : href.endsWith('.js') ? 'script' : 'image';
        link.href = href;
        document.head.appendChild(link);
    });
    
    console.log('✅ Performance Optimization fully initialized!');
});

// Export for debugging
window.getPerformanceReport = () => {
    if (window.performanceOptimizer) {
        return window.performanceOptimizer.getPerformanceReport();
    }
    return null;
};

console.log('⚡ Performance Optimizer loaded successfully!');