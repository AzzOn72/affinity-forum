/**
 * Performance Optimizer
 * Intelligent performance optimization for the Advanced Theme System
 */

class PerformanceOptimizer {
    constructor(themeManager) {
        this.themeManager = themeManager;
        this.metrics = {
            fps: [],
            memory: [],
            renderTime: [],
            particleCount: []
        };
        this.optimizations = {
            particleReduction: false,
            animationDisabled: false,
            audioDisabled: false,
            lowQualityMode: false
        };
        this.monitoringInterval = null;
        
        this.init();
    }
    
    init() {
        console.log('🚀 Performance Optimizer initialized');
        this.startMonitoring();
        this.setupAutoOptimization();
    }
    
    startMonitoring() {
        this.monitoringInterval = setInterval(() => {
            this.collectMetrics();
            this.analyzePerformance();
        }, 1000);
    }
    
    collectMetrics() {
        if (!this.themeManager) return;
        
        // Collect FPS
        this.metrics.fps.push(this.themeManager.performanceMetrics.fps);
        if (this.metrics.fps.length > 60) this.metrics.fps.shift();
        
        // Collect memory usage
        if (performance.memory) {
            const memoryMB = performance.memory.usedJSHeapSize / 1024 / 1024;
            this.metrics.memory.push(memoryMB);
            if (this.metrics.memory.length > 60) this.metrics.memory.shift();
        }
        
        // Collect render time
        this.metrics.renderTime.push(this.themeManager.performanceMetrics.renderTime || 0);
        if (this.metrics.renderTime.length > 60) this.metrics.renderTime.shift();
        
        // Collect particle count
        const particleCount = this.themeManager.particleSystem ? 
            this.themeManager.particleSystem.particles.length : 0;
        this.metrics.particleCount.push(particleCount);
        if (this.metrics.particleCount.length > 60) this.metrics.particleCount.shift();
    }
    
    analyzePerformance() {
        const avgFps = this.getAverage(this.metrics.fps);
        const avgMemory = this.getAverage(this.metrics.memory);
        const avgRenderTime = this.getAverage(this.metrics.renderTime);
        
        // Check for performance issues
        if (avgFps < 30 && !this.optimizations.particleReduction) {
            this.optimizeParticles();
        }
        
        if (avgFps < 20 && !this.optimizations.animationDisabled) {
            this.disableAnimations();
        }
        
        if (avgMemory > 100 && !this.optimizations.lowQualityMode) {
            this.enableLowQualityMode();
        }
        
        // Check battery level if available
        if (navigator.getBattery) {
            navigator.getBattery().then(battery => {
                if (battery.level < 0.2 && !this.optimizations.audioDisabled) {
                    this.optimizeForBattery();
                }
            });
        }
    }
    
    getAverage(array) {
        if (array.length === 0) return 0;
        return array.reduce((sum, value) => sum + value, 0) / array.length;
    }
    
    optimizeParticles() {
        if (!this.themeManager.particleSystem) return;
        
        console.log('🔧 Auto-optimizing particles for better performance');
        
        // Reduce particle count by 50%
        const currentCount = this.themeManager.particleSystem.particles.length;
        this.themeManager.particleSystem.particles = 
            this.themeManager.particleSystem.particles.slice(0, Math.floor(currentCount * 0.5));
        
        // Reduce max particles
        this.themeManager.particleSystem.maxParticles = Math.floor(
            this.themeManager.particleSystem.maxParticles * 0.5
        );
        
        this.optimizations.particleReduction = true;
        this.themeManager.showNotification('🔧 Auto-optimized particles for better performance', 'info');
    }
    
    disableAnimations() {
        console.log('🔧 Auto-disabling animations for better performance');
        
        document.body.classList.add('reduced-motion');
        this.themeManager.accessibility.reducedMotion = true;
        this.themeManager.features.animations = false;
        
        this.optimizations.animationDisabled = true;
        this.themeManager.showNotification('🔧 Auto-disabled animations for better performance', 'warning');
    }
    
    enableLowQualityMode() {
        console.log('🔧 Enabling low quality mode for memory optimization');
        
        // Disable particle trails and glow effects
        if (this.themeManager.particleEffects) {
            this.themeManager.particleEffects.trails = false;
        }
        
        // Reduce notification duration
        this.themeManager.notificationDuration = 3000;
        
        this.optimizations.lowQualityMode = true;
        this.themeManager.showNotification('🔧 Enabled low quality mode for memory optimization', 'warning');
    }
    
    optimizeForBattery() {
        console.log('🔧 Optimizing for low battery');
        
        // Disable audio
        this.themeManager.features.audio = false;
        if (this.themeManager.audioContext) {
            this.themeManager.audioContext.suspend();
        }
        
        // Reduce particle count further
        if (this.themeManager.particleSystem) {
            this.themeManager.particleSystem.maxParticles = 10;
        }
        
        this.optimizations.audioDisabled = true;
        this.themeManager.showNotification('🔋 Optimized for low battery mode', 'info');
    }
    
    getPerformanceReport() {
        return {
            averages: {
                fps: this.getAverage(this.metrics.fps),
                memory: this.getAverage(this.metrics.memory),
                renderTime: this.getAverage(this.metrics.renderTime),
                particleCount: this.getAverage(this.metrics.particleCount)
            },
            current: {
                fps: this.metrics.fps[this.metrics.fps.length - 1] || 0,
                memory: this.metrics.memory[this.metrics.memory.length - 1] || 0,
                renderTime: this.metrics.renderTime[this.metrics.renderTime.length - 1] || 0,
                particleCount: this.metrics.particleCount[this.metrics.particleCount.length - 1] || 0
            },
            optimizations: this.optimizations,
            recommendations: this.getRecommendations(),
            timestamp: Date.now()
        };
    }
    
    getRecommendations() {
        const recommendations = [];
        const avgFps = this.getAverage(this.metrics.fps);
        const avgMemory = this.getAverage(this.metrics.memory);
        
        if (avgFps < 45) {
            recommendations.push({
                type: 'performance',
                priority: 'high',
                message: 'Consider reducing particle effects or switching to mobile device mode',
                action: 'optimizeParticles'
            });
        }
        
        if (avgMemory > 80) {
            recommendations.push({
                type: 'memory',
                priority: 'medium',
                message: 'High memory usage detected. Consider clearing browser cache.',
                action: 'clearCache'
            });
        }
        
        if (window.innerWidth < 768 && this.themeManager.currentDevice !== 'mobile') {
            recommendations.push({
                type: 'device',
                priority: 'low',
                message: 'Switch to mobile device mode for better performance on small screens',
                action: 'switchToMobile'
            });
        }
        
        return recommendations;
    }
    
    applyRecommendation(recommendation) {
        switch (recommendation.action) {
            case 'optimizeParticles':
                this.optimizeParticles();
                break;
            case 'clearCache':
                this.suggestCacheClear();
                break;
            case 'switchToMobile':
                this.themeManager.selectDevice('mobile');
                break;
        }
    }
    
    suggestCacheClear() {
        this.themeManager.showNotification(
            '💡 Consider clearing your browser cache for better performance', 
            'info', 
            8000
        );
    }
    
    resetOptimizations() {
        console.log('🔄 Resetting performance optimizations');
        
        // Reset all optimizations
        this.optimizations = {
            particleReduction: false,
            animationDisabled: false,
            audioDisabled: false,
            lowQualityMode: false
        };
        
        // Re-enable features
        this.themeManager.features.animations = true;
        this.themeManager.features.audio = true;
        document.body.classList.remove('reduced-motion');
        
        // Reset particle system
        if (this.themeManager.particleSystem) {
            this.themeManager.particleSystem.maxParticles = this.themeManager.getMaxParticleCount();
        }
        
        this.themeManager.showNotification('🔄 Performance optimizations reset', 'success');
    }
    
    destroy() {
        if (this.monitoringInterval) {
            clearInterval(this.monitoringInterval);
        }
        console.log('🛑 Performance Optimizer destroyed');
    }
}

// Auto-initialize when theme manager is ready
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        if (window.themeManager) {
            window.performanceOptimizer = new PerformanceOptimizer(window.themeManager);
            console.log('✅ Performance Optimizer attached to theme manager');
        }
    }, 2000);
});

console.log('📈 Performance Optimizer loaded successfully!');