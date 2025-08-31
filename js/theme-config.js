/**
 * Advanced Theme System Configuration
 * Centralized configuration for all theme system features
 */

window.THEME_CONFIG = {
    // Version information
    version: '3.0.0',
    build: Date.now(),
    
    // Default settings
    defaults: {
        theme: 'light',
        device: 'desktop',
        particleEffect: 'standard',
        volume: 0.5,
        features: {
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
        },
        accessibility: {
            highContrast: false,
            reducedMotion: false,
            fontSize: 16,
            screenReader: false,
            voiceControl: false,
            eyeTracking: false,
            colorBlindSupport: false
        }
    },
    
    // Performance settings
    performance: {
        targetFPS: 60,
        maxParticles: {
            desktop: 150,
            tablet: 100,
            mobile: 60
        },
        autoOptimize: true,
        memoryThreshold: 0.8, // 80% of available memory
        fpsThreshold: 30 // Optimize if FPS drops below this
    },
    
    // Particle system configuration
    particles: {
        types: ['standard', 'fireworks', 'snow', 'rain', 'stars', 'bubbles'],
        physics: {
            gravity: 0.1,
            friction: 0.98,
            bounce: 0.8,
            wind: 0.1
        },
        effects: {
            trails: true,
            glow: true,
            interaction: true,
            collision: false
        }
    },
    
    // Audio system configuration
    audio: {
        masterVolume: 0.5,
        effects: {
            'theme-change': { frequency: 440, duration: 0.5, type: 'sine' },
            'device-switch': { frequency: 220, duration: 0.3, type: 'square' },
            'notification': { frequency: [523, 659], duration: 0.25, type: 'sine' },
            'click': { frequency: 800, duration: 0.1, type: 'triangle' },
            'hover': { frequency: 600, duration: 0.05, type: 'sine' }
        },
        visualizer: {
            enabled: false,
            fftSize: 256,
            smoothing: 0.8
        }
    },
    
    // Theme definitions
    themes: {
        light: {
            name: 'Light Theme',
            icon: '☀️',
            description: 'Clean and modern light interface',
            colors: {
                'primary-color': '#007bff',
                'secondary-color': '#6c757d',
                'accent-color': '#28a745',
                'text-color': '#212529',
                'bg-color': '#ffffff',
                'border-color': '#dee2e6',
                'card-bg': '#f8f9fa',
                'hover-bg': '#e9ecef',
                'shadow': '0 2px 4px rgba(0, 0, 0, 0.1)'
            }
        },
        dark: {
            name: 'Dark Theme',
            icon: '🌙',
            description: 'Sleek and elegant dark interface',
            colors: {
                'primary-color': '#0d6efd',
                'secondary-color': '#6c757d',
                'accent-color': '#198754',
                'text-color': '#f8f9fa',
                'bg-color': '#212529',
                'border-color': '#495057',
                'card-bg': '#343a40',
                'hover-bg': '#495057',
                'shadow': '0 2px 4px rgba(0, 0, 0, 0.3)'
            }
        },
        cs2: {
            name: 'CS2 Gaming',
            icon: '🎮',
            description: 'Gaming-inspired Counter-Strike 2 style',
            colors: {
                'primary-color': '#ff6b35',
                'secondary-color': '#2c3e50',
                'accent-color': '#e74c3c',
                'text-color': '#ecf0f1',
                'bg-color': '#1a1a1a',
                'border-color': '#34495e',
                'card-bg': '#2c3e50',
                'hover-bg': '#34495e',
                'shadow': '0 4px 8px rgba(255, 107, 53, 0.2)'
            }
        },
        premium: {
            name: 'Premium Luxury',
            icon: '💎',
            description: 'Exclusive luxury premium interface',
            colors: {
                'primary-color': '#d4af37',
                'secondary-color': '#2c3e50',
                'accent-color': '#e74c3c',
                'text-color': '#f8f9fa',
                'bg-color': '#1a1a1a',
                'border-color': '#d4af37',
                'card-bg': '#2c3e50',
                'hover-bg': '#34495e',
                'shadow': '0 4px 12px rgba(212, 175, 55, 0.3)'
            }
        }
    },
    
    // Device configurations
    devices: {
        desktop: {
            name: 'Desktop',
            icon: '🖥️',
            description: 'Full desktop experience',
            variables: {
                'container-width': '1200px',
                'font-size': '16px',
                'spacing': '1.5rem',
                'border-radius': '10px'
            },
            particles: 80,
            maxParticles: 150
        },
        tablet: {
            name: 'Tablet',
            icon: '📱',
            description: 'Optimized for tablet devices',
            variables: {
                'container-width': '768px',
                'font-size': '14px',
                'spacing': '1rem',
                'border-radius': '8px'
            },
            particles: 50,
            maxParticles: 100
        },
        mobile: {
            name: 'Mobile',
            icon: '📱',
            description: 'Mobile-optimized interface',
            variables: {
                'container-width': '100%',
                'font-size': '12px',
                'spacing': '0.75rem',
                'border-radius': '6px'
            },
            particles: 30,
            maxParticles: 60
        }
    },
    
    // Smart features configuration
    smart: {
        suggestions: {
            enabled: true,
            confidence: 0.7,
            maxSuggestions: 3
        },
        timeBasedThemes: {
            enabled: true,
            schedule: {
                '6-12': 'light',
                '12-18': 'premium',
                '18-22': 'cs2',
                '22-6': 'dark'
            }
        },
        autoOptimization: {
            enabled: true,
            triggers: {
                lowFPS: 30,
                highMemory: 0.8,
                lowBattery: 0.2
            }
        }
    },
    
    // Accessibility configuration
    accessibility: {
        fontSizes: [12, 14, 16, 18, 20, 22, 24],
        contrastRatios: {
            normal: 4.5,
            large: 3,
            enhanced: 7
        },
        motionPreferences: {
            respect: true,
            override: false
        }
    },
    
    // Animation configuration
    animations: {
        durations: {
            fast: 150,
            normal: 300,
            slow: 600
        },
        easings: {
            ease: 'ease',
            easeIn: 'ease-in',
            easeOut: 'ease-out',
            easeInOut: 'ease-in-out',
            bounce: 'cubic-bezier(0.68, -0.55, 0.265, 1.55)'
        }
    },
    
    // Debug configuration
    debug: {
        enabled: false,
        logLevel: 'info', // error, warn, info, debug
        showPerformance: false,
        showNotifications: true
    }
};

// Configuration validation
function validateConfig() {
    const config = window.THEME_CONFIG;
    
    // Validate themes
    Object.keys(config.themes).forEach(themeId => {
        const theme = config.themes[themeId];
        if (!theme.colors || !theme.name) {
            console.warn(`⚠️ Invalid theme configuration: ${themeId}`);
        }
    });
    
    // Validate devices
    Object.keys(config.devices).forEach(deviceId => {
        const device = config.devices[deviceId];
        if (!device.variables || !device.name) {
            console.warn(`⚠️ Invalid device configuration: ${deviceId}`);
        }
    });
    
    console.log('✅ Theme configuration validated');
}

// Apply configuration to theme manager
function applyConfigToThemeManager() {
    if (window.themeManager && window.THEME_CONFIG) {
        const config = window.THEME_CONFIG;
        
        // Apply default settings
        Object.assign(window.themeManager.features, config.defaults.features);
        Object.assign(window.themeManager.accessibility, config.defaults.accessibility);
        
        // Apply performance settings
        window.themeManager.performanceConfig = config.performance;
        
        // Apply audio settings
        if (window.themeManager.audioEffects) {
            window.themeManager.audioEffects.volume = config.audio.masterVolume;
        }
        
        console.log('✅ Configuration applied to theme manager');
    }
}

// Export configuration
function exportConfiguration() {
    const dataStr = JSON.stringify(window.THEME_CONFIG, null, 2);
    const dataBlob = new Blob([dataStr], { type: 'application/json' });
    
    const link = document.createElement('a');
    link.href = URL.createObjectURL(dataBlob);
    link.download = `theme-config-${Date.now()}.json`;
    link.click();
    
    console.log('📤 Configuration exported');
}

// Import configuration
function importConfiguration(file) {
    const reader = new FileReader();
    reader.onload = (e) => {
        try {
            const config = JSON.parse(e.target.result);
            window.THEME_CONFIG = { ...window.THEME_CONFIG, ...config };
            applyConfigToThemeManager();
            console.log('📥 Configuration imported successfully');
        } catch (error) {
            console.error('❌ Failed to import configuration:', error);
        }
    };
    reader.readAsText(file);
}

// Initialize configuration
document.addEventListener('DOMContentLoaded', () => {
    validateConfig();
    
    // Apply configuration after theme manager is ready
    setTimeout(() => {
        applyConfigToThemeManager();
    }, 1000);
});

console.log('⚙️ Theme configuration loaded successfully!');