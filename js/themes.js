/**
 * Affinity Forum - Modern Theme System v3.0
 * Beautiful, functional theme switching with smooth animations
 */

class ModernThemeManager {
    constructor() {
        this.currentTheme = 'light';
        this.availableThemes = ['light', 'dark', 'cs2', 'premium', 'midnight', 'sunset'];
        this.themeColors = {
            light: {
                primary: '#007bff',
                secondary: '#6c757d',
                success: '#28a745',
                warning: '#ffc107',
                danger: '#dc3545',
                info: '#17a2b8',
                background: '#ffffff',
                surface: '#f8f9fa',
                text: '#212529',
                textSecondary: '#6c757d',
                border: '#dee2e6',
                shadow: 'rgba(0, 0, 0, 0.1)'
            },
            dark: {
                primary: '#58a6ff',
                secondary: '#8b949e',
                success: '#3fb950',
                warning: '#d29922',
                danger: '#f85149',
                info: '#58a6ff',
                background: '#0d1117',
                surface: '#161b22',
                text: '#f0f6fc',
                textSecondary: '#8b949e',
                border: '#30363d',
                shadow: 'rgba(0, 0, 0, 0.3)'
            },
            cs2: {
                primary: '#ff6b35',
                secondary: '#f7931e',
                success: '#00d4aa',
                warning: '#ffd23f',
                danger: '#ff6b6b',
                info: '#4ecdc4',
                background: '#1a1a1a',
                surface: '#2d2d2d',
                text: '#ffffff',
                textSecondary: '#cccccc',
                border: '#404040',
                shadow: 'rgba(255, 107, 53, 0.2)'
            },
            premium: {
                primary: '#6366f1',
                secondary: '#8b5cf6',
                success: '#10b981',
                warning: '#f59e0b',
                danger: '#ef4444',
                info: '#06b6d4',
                background: '#ffffff',
                surface: '#f8fafc',
                text: '#1e293b',
                textSecondary: '#64748b',
                border: '#e2e8f0',
                shadow: 'rgba(99, 102, 241, 0.1)'
            },
            midnight: {
                primary: '#8b5cf6',
                secondary: '#a855f7',
                success: '#10b981',
                warning: '#f59e0b',
                danger: '#ef4444',
                info: '#06b6d4',
                background: '#0f0f23',
                surface: '#1a1a2e',
                text: '#ffffff',
                textSecondary: '#a0a0a0',
                border: '#2d2d44',
                shadow: 'rgba(139, 92, 246, 0.2)'
            },
            sunset: {
                primary: '#f97316',
                secondary: '#ea580c',
                success: '#16a34a',
                warning: '#ca8a04',
                danger: '#dc2626',
                info: '#0891b2',
                background: '#fef7ed',
                surface: '#fff7ed',
                text: '#451a03',
                textSecondary: '#92400e',
                border: '#fed7aa',
                shadow: 'rgba(249, 115, 22, 0.1)'
            }
        };
        
        this.init();
    }
    
    init() {
        // Load saved theme
        this.loadSavedTheme();
        
        // Apply current theme
        this.applyTheme(this.currentTheme);
        
        // Create theme switcher UI
        this.createThemeSwitcher();
        
        // Add event listeners
        this.addEventListeners();
        
        console.log('🎨 ModernThemeManager initialized with theme:', this.currentTheme);
    }
    
    loadSavedTheme() {
        const savedTheme = localStorage.getItem('affinity_theme');
        if (savedTheme && this.availableThemes.includes(savedTheme)) {
            this.currentTheme = savedTheme;
        }
    }
    
    setTheme(themeName) {
        if (!this.availableThemes.includes(themeName)) {
            console.warn('Invalid theme:', themeName);
            return;
        }
        
        // Add transition effect
        document.body.style.transition = 'all 0.3s ease';
        
        // Apply theme
        this.applyTheme(themeName);
        
        // Save preference
        localStorage.setItem('affinity_theme', themeName);
        this.currentTheme = themeName;
        
        // Show notification
        this.showThemeNotification(themeName);
        
        // Update UI
        this.updateThemeSwitcher();
        
        // Remove transition after animation
        setTimeout(() => {
            document.body.style.transition = '';
        }, 300);
    }
    
    applyTheme(themeName) {
        const colors = this.themeColors[themeName];
        if (!colors) return;
        
        // Apply CSS variables
        Object.entries(colors).forEach(([key, value]) => {
            document.documentElement.style.setProperty(`--${key}`, value);
        });
        
        // Set body attribute
        document.body.setAttribute('data-theme', themeName);
        
        // Update meta theme color
        const metaThemeColor = document.querySelector('meta[name="theme-color"]');
        if (metaThemeColor) {
            metaThemeColor.setAttribute('content', colors.primary);
        }
    }
    
    createThemeSwitcher() {
        // Remove existing switcher
        const existingSwitcher = document.getElementById('theme-switcher');
        if (existingSwitcher) {
            existingSwitcher.remove();
        }
        
        // Create new switcher
        const switcher = document.createElement('div');
        switcher.id = 'theme-switcher';
        switcher.className = 'theme-switcher';
        switcher.innerHTML = `
            <div class="theme-switcher-header">
                <h4><i class="fas fa-palette"></i> Choose Theme</h4>
                <button class="theme-close-btn" onclick="window.themeManager.closeThemeSwitcher()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="theme-grid">
                ${this.availableThemes.map(theme => `
                    <div class="theme-option ${theme === this.currentTheme ? 'active' : ''}" 
                         data-theme="${theme}" 
                         onclick="window.themeManager.selectTheme('${theme}')">
                        <div class="theme-preview" style="background: ${this.themeColors[theme].background}">
                            <div class="theme-preview-header" style="background: ${this.themeColors[theme].primary}"></div>
                            <div class="theme-preview-content">
                                <div class="preview-line" style="background: ${this.themeColors[theme].text}"></div>
                                <div class="preview-line short" style="background: ${this.themeColors[theme].textSecondary}"></div>
                            </div>
                        </div>
                        <div class="theme-name">${this.getThemeDisplayName(theme)}</div>
                        ${theme === this.currentTheme ? '<div class="theme-active-badge"><i class="fas fa-check"></i></div>' : ''}
                    </div>
                `).join('')}
            </div>
        `;
        
        document.body.appendChild(switcher);
    }
    
    getThemeDisplayName(theme) {
        const names = {
            light: 'Light',
            dark: 'Dark',
            cs2: 'CS2',
            premium: 'Premium',
            midnight: 'Midnight',
            sunset: 'Sunset'
        };
        return names[theme] || theme;
    }
    
    selectTheme(themeName) {
        this.setTheme(themeName);
        this.closeThemeSwitcher();
    }
    
    openThemeSwitcher() {
        const switcher = document.getElementById('theme-switcher');
        if (switcher) {
            switcher.classList.add('open');
            document.body.classList.add('theme-switcher-open');
        }
    }
    
    closeThemeSwitcher() {
        const switcher = document.getElementById('theme-switcher');
        if (switcher) {
            switcher.classList.remove('open');
            document.body.classList.remove('theme-switcher-open');
        }
    }
    
    updateThemeSwitcher() {
        const switcher = document.getElementById('theme-switcher');
        if (switcher) {
            // Update active theme
            switcher.querySelectorAll('.theme-option').forEach(option => {
                option.classList.remove('active');
                if (option.dataset.theme === this.currentTheme) {
                    option.classList.add('active');
                }
            });
            
            // Update active badges
            switcher.querySelectorAll('.theme-active-badge').forEach(badge => {
                badge.remove();
            });
            
            const activeOption = switcher.querySelector(`[data-theme="${this.currentTheme}"]`);
            if (activeOption) {
                const badge = document.createElement('div');
                badge.className = 'theme-active-badge';
                badge.innerHTML = '<i class="fas fa-check"></i>';
                activeOption.appendChild(badge);
            }
        }
    }
    
    addEventListeners() {
        // Close switcher when clicking outside
        document.addEventListener('click', (e) => {
            const switcher = document.getElementById('theme-switcher');
            if (switcher && !switcher.contains(e.target) && !e.target.closest('.theme-toggle-btn')) {
                this.closeThemeSwitcher();
            }
        });
        
        // Escape key to close
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeThemeSwitcher();
            }
        });
    }
    
    showThemeNotification(themeName) {
        const notification = document.createElement('div');
        notification.className = 'theme-notification';
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-palette"></i>
                <span>Switched to ${this.getThemeDisplayName(themeName)} theme</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => notification.classList.add('show'), 100);
        
        // Remove after delay
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 300);
        }, 3000);
    }
}

// Global functions for HTML onclick
function switchTheme(themeName) {
    if (window.themeManager) {
        window.themeManager.setTheme(themeName);
    } else {
        console.warn('ThemeManager not initialized');
    }
}

function openThemeSwitcher() {
    if (window.themeManager) {
        window.themeManager.openThemeSwitcher();
    } else {
        console.warn('ThemeManager not initialized');
    }
}

function closeThemeSwitcher() {
    if (window.themeManager) {
        window.themeManager.closeThemeSwitcher();
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.themeManager = new ModernThemeManager();
    console.log('🎨 Theme system ready!');
});

// Fallback initialization
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        if (!window.themeManager) {
            window.themeManager = new ModernThemeManager();
        }
    });
} else {
    if (!window.themeManager) {
        window.themeManager = new ModernThemeManager();
    }
}