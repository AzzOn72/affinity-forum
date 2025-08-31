/**
 * Affinity Forum - Premium Theme System v2.0
 * Advanced theme switching with visual effects and customization
 */

// Global theme functions for HTML onclick
function switchTheme(themeName) {
    if (window.themeManager) {
        window.themeManager.setTheme(themeName);
    } else {
        // Fallback if themeManager not loaded
        document.body.setAttribute('data-theme', themeName);
        localStorage.setItem('affinity_theme', themeName);
        showThemeNotification(themeName);
    }
}

function openThemePreview() {
    if (window.themeManager) {
        window.themeManager.showThemePreview();
    }
}

function openCustomization() {
    if (window.themeManager) {
        window.themeManager.openCustomization();
    }
}

function previewTheme(themeName) {
    if (window.themeManager) {
        window.themeManager.previewTheme(themeName);
    }
}

function applyTheme(themeName) {
    if (window.themeManager) {
        window.themeManager.setTheme(themeName);
    }
}

function closeThemePreview() {
    const modal = document.getElementById('theme-preview-modal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// Global notification function
function showThemeNotification(themeName) {
    const themeNames = {
        light: 'Light Theme',
        dark: 'Dark Theme',
        cs2: 'CS2 Theme',
        premium: 'Premium Theme'
    };
    
    const notification = document.createElement('div');
    notification.className = 'theme-change-notification';
    notification.innerHTML = `
        <div class="theme-notification-content">
            <i class="fas fa-palette"></i>
            <span>Switched to ${themeNames[themeName]}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
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

class ThemeManager {
    constructor() {
        this.currentTheme = 'light';
        this.availableThemes = ['light', 'dark', 'cs2', 'premium'];
        this.themeColors = {
            light: {
                primary: '#007bff',
                secondary: '#6c757d',
                success: '#28a745',
                warning: '#ffc107',
                danger: '#dc3545',
                info: '#17a2b8'
            },
            dark: {
                primary: '#58a6ff',
                secondary: '#8b949e',
                success: '#3fb950',
                warning: '#d29922',
                danger: '#f85149',
                info: '#58a6ff'
            },
            cs2: {
                primary: '#ff6b35',
                secondary: '#7a8ba1',
                success: '#48bb78',
                warning: '#ed8936',
                danger: '#f56565',
                info: '#4299e1'
            },
            premium: {
                primary: '#ffd700',
                secondary: '#a0a0a0',
                success: '#00ff88',
                warning: '#ffaa00',
                danger: '#ff4444',
                info: '#00aaff'
            }
        };
        
        this.init();
    }
    
    init() {
        this.loadSavedTheme();
        this.setupThemeToggle();
        this.setupThemePreview();
        this.setupCustomization();
        this.applyTheme(this.currentTheme);
        
        // Make themeManager globally accessible
        window.themeManager = this;
        
        // Add theme change event listener
        document.addEventListener('themeChanged', (e) => {
            this.onThemeChange(e.detail.theme);
        });
    }
    
    loadSavedTheme() {
        const savedTheme = localStorage.getItem('affinity_theme');
        if (savedTheme && this.availableThemes.includes(savedTheme)) {
            this.currentTheme = savedTheme;
        } else {
            // Default to light theme
            this.currentTheme = 'light';
        }
    }
    
    setupThemeToggle() {
        // Update the theme name display
        this.updateThemeName();
        
        // Add keyboard shortcut (Ctrl/Cmd + T)
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key === 't') {
                e.preventDefault();
                this.cycleTheme();
            }
        });
    }
    
    updateThemeName() {
        const themeNameSpan = document.querySelector('.theme-name');
        if (themeNameSpan) {
            themeNameSpan.textContent = this.currentTheme.charAt(0).toUpperCase() + this.currentTheme.slice(1);
        }
    }
    
    setupThemePreview() {
        // Create theme preview modal
        const previewModal = document.createElement('div');
        previewModal.className = 'theme-preview-modal';
        previewModal.innerHTML = `
            <div class="theme-preview-content">
                <div class="theme-preview-header">
                    <h3><i class="fas fa-palette"></i> Theme Preview</h3>
                    <button class="btn-close" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="theme-preview-grid">
                    ${this.availableThemes.map(theme => this.createThemePreview(theme)).join('')}
                </div>
            </div>
        `;
        
        document.body.appendChild(previewModal);
        
        // Add preview button to theme toggle
        const themeToggle = document.getElementById('theme-toggle');
        if (themeToggle) {
            const previewBtn = document.createElement('button');
            previewBtn.className = 'btn btn-sm btn-outline-secondary ms-2';
            previewBtn.innerHTML = '<i class="fas fa-eye"></i>';
            previewBtn.title = 'Preview Themes';
            previewBtn.addEventListener('click', () => {
                this.showThemePreview();
            });
            themeToggle.parentNode.appendChild(previewBtn);
        }
    }
    
    createThemePreview(theme) {
        const colors = this.themeColors[theme];
        const isActive = theme === this.currentTheme;
        
        return `
            <div class="theme-preview-item ${isActive ? 'active' : ''}" data-theme="${theme}">
                <div class="theme-preview-header">
                    <h4>${theme.charAt(0).toUpperCase() + theme.slice(1)}</h4>
                    ${isActive ? '<span class="badge bg-success">Active</span>' : ''}
                </div>
                <div class="theme-preview-colors">
                    <div class="color-swatch" style="background: ${colors.primary}" title="Primary"></div>
                    <div class="color-swatch" style="background: ${colors.secondary}" title="Secondary"></div>
                    <div class="color-swatch" style="background: ${colors.success}" title="Success"></div>
                    <div class="color-swatch" style="background: ${colors.warning}" title="Warning"></div>
                    <div class="color-swatch" style="background: ${colors.danger}" title="Danger"></div>
                </div>
                <div class="theme-preview-actions">
                    <button class="btn btn-sm btn-primary preview-theme-btn" data-theme="${theme}">
                        Preview
                    </button>
                    ${!isActive ? `<button class="btn btn-sm btn-success apply-theme-btn" data-theme="${theme}">Apply</button>` : ''}
                </div>
            </div>
        `;
    }
    
    setupCustomization() {
        // Create theme customization panel
        const customizationPanel = document.createElement('div');
        customizationPanel.className = 'theme-customization-panel';
        customizationPanel.innerHTML = `
            <div class="customization-header">
                <h4><i class="fas fa-sliders-h"></i> Customize Theme</h4>
                <button class="btn-close-customization">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="customization-content">
                <div class="customization-section">
                    <label>Primary Color</label>
                    <input type="color" id="custom-primary" class="form-control">
                </div>
                <div class="customization-section">
                    <label>Background Opacity</label>
                    <input type="range" id="custom-opacity" min="0.1" max="1" step="0.1" class="form-range">
                </div>
                <div class="customization-section">
                    <label>Border Radius</label>
                    <input type="range" id="custom-radius" min="0" max="20" step="1" class="form-range">
                </div>
                <div class="customization-section">
                    <label>Animation Speed</label>
                    <select id="custom-animation" class="form-select">
                        <option value="fast">Fast</option>
                        <option value="normal" selected>Normal</option>
                        <option value="slow">Slow</option>
                    </select>
                </div>
                <div class="customization-actions">
                    <button class="btn btn-primary" id="save-customization">Save Changes</button>
                    <button class="btn btn-secondary" id="reset-customization">Reset to Default</button>
                </div>
            </div>
        `;
        
        document.body.appendChild(customizationPanel);
        
        // Add customization button
        const themeToggle = document.getElementById('theme-toggle');
        if (themeToggle) {
            const customBtn = document.createElement('button');
            customBtn.className = 'btn btn-sm btn-outline-secondary ms-2';
            customBtn.innerHTML = '<i class="fas fa-sliders-h"></i>';
            customBtn.title = 'Customize Theme';
            customBtn.addEventListener('click', () => {
                this.toggleCustomization();
            });
            themeToggle.parentNode.appendChild(customBtn);
        }
        
        // Bind customization events
        this.bindCustomizationEvents();
    }
    
    bindCustomizationEvents() {
        // Color picker
        const primaryColor = document.getElementById('custom-primary');
        if (primaryColor) {
            primaryColor.addEventListener('change', (e) => {
                this.updateCustomColor('primary', e.target.value);
            });
        }
        
        // Opacity slider
        const opacitySlider = document.getElementById('custom-opacity');
        if (opacitySlider) {
            opacitySlider.addEventListener('input', (e) => {
                this.updateCustomOpacity(e.target.value);
            });
        }
        
        // Border radius slider
        const radiusSlider = document.getElementById('custom-radius');
        if (radiusSlider) {
            radiusSlider.addEventListener('input', (e) => {
                this.updateCustomRadius(e.target.value);
            });
        }
        
        // Animation speed
        const animationSelect = document.getElementById('custom-animation');
        if (animationSelect) {
            animationSelect.addEventListener('change', (e) => {
                this.updateCustomAnimation(e.target.value);
            });
        }
        
        // Save button
        const saveBtn = document.getElementById('save-customization');
        if (saveBtn) {
            saveBtn.addEventListener('click', () => {
                this.saveCustomization();
            });
        }
        
        // Reset button
        const resetBtn = document.getElementById('reset-customization');
        if (resetBtn) {
            resetBtn.addEventListener('click', () => {
                this.resetCustomization();
            });
        }
        
        // Close button
        const closeBtn = document.querySelector('.btn-close-customization');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                this.toggleCustomization();
            });
        }
    }
    
    cycleTheme() {
        const currentIndex = this.availableThemes.indexOf(this.currentTheme);
        const nextIndex = (currentIndex + 1) % this.availableThemes.length;
        const nextTheme = this.availableThemes[nextIndex];
        
        this.setTheme(nextTheme);
    }
    
    setTheme(themeName) {
        if (!this.availableThemes.includes(themeName)) {
            console.error(`Invalid theme: ${themeName}`);
            return;
        }
        
        console.log(`🎨 Switching to theme: ${themeName}`);
        
        // Remove all existing theme classes
        document.body.classList.remove(...this.availableThemes.map(t => `theme-${t}`));
        
        // Set new theme
        document.body.setAttribute('data-theme', themeName);
        this.currentTheme = themeName;
        
        // Save to localStorage
        localStorage.setItem('affinity_theme', themeName);
        
        // Update theme name display
        this.updateThemeName();
        
        // Apply theme-specific customizations
        this.applyThemeCustomizations(themeName);
        
        // Trigger theme change event
        document.dispatchEvent(new CustomEvent('themeChanged', {
            detail: { theme: themeName }
        }));
        
        // Show notification
        this.showThemeNotification(themeName);
        
        // Update any theme-dependent components
        this.updateThemeComponents(themeName);
    }
    
    applyTheme(themeName) {
        // Set initial theme
        document.body.setAttribute('data-theme', themeName);
        
        // Apply theme-specific styles
        this.applyThemeStyles(themeName);
        
        // Update components
        this.updateThemeComponents(themeName);
    }
    
    applyThemeStyles(themeName) {
        const colors = this.themeColors[themeName];
        
        // Update CSS custom properties
        Object.entries(colors).forEach(([key, value]) => {
            document.documentElement.style.setProperty(`--accent-${key}`, value);
        });
        
        // Apply theme-specific animations
        this.applyThemeAnimations(themeName);
    }
    
    applyThemeAnimations(themeName) {
        const animationSpeeds = {
            light: 'normal',
            dark: 'normal',
            cs2: 'fast',
            premium: 'slow'
        };
        
        const speed = animationSpeeds[themeName];
        document.documentElement.style.setProperty('--transition-speed', speed);
        
        // Add theme-specific animation classes
        document.body.classList.add(`theme-${themeName}-animations`);
    }
    
    updateThemeToggle() {
        const themeToggle = document.getElementById('theme-toggle');
        if (themeToggle) {
            const icon = themeToggle.querySelector('i');
            if (icon) {
                icon.className = this.getThemeIcon(this.currentTheme);
            }
            
            // Update tooltip
            themeToggle.title = `Current: ${this.currentTheme.charAt(0).toUpperCase() + this.currentTheme.slice(1)} (Click to change)`;
        }
    }
    
    getThemeIcon(theme) {
        const icons = {
            light: 'fas fa-sun',
            dark: 'fas fa-moon',
            cs2: 'fas fa-crosshairs',
            premium: 'fas fa-crown'
        };
        return icons[theme] || 'fas fa-palette';
    }
    
    showThemeNotification(themeName) {
        const themeNames = {
            light: 'Light Theme',
            dark: 'Dark Theme',
            cs2: 'CS2 Theme',
            premium: 'Premium Theme'
        };
        
        const notification = document.createElement('div');
        notification.className = 'theme-change-notification';
        notification.innerHTML = `
            <div class="theme-notification-content">
                <i class="fas fa-palette"></i>
                <span>Switched to ${themeNames[themeName]}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);
        
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
    
    updateThemeComponents(themeName) {
        // Update charts and graphs if they exist
        this.updateCharts(themeName);
        
        // Update code syntax highlighting
        this.updateCodeHighlighting(themeName);
        
        // Update any custom components
        this.updateCustomComponents(themeName);
    }
    
    updateCharts(themeName) {
        // Update Chart.js charts if they exist
        if (window.Chart && window.Chart.instances) {
            Object.values(window.Chart.instances).forEach(chart => {
                const colors = this.themeColors[themeName];
                chart.data.datasets.forEach(dataset => {
                    if (dataset.backgroundColor === 'rgba(0, 123, 255, 0.2)') {
                        dataset.backgroundColor = this.hexToRgba(colors.primary, 0.2);
                        dataset.borderColor = colors.primary;
                    }
                });
                chart.update();
            });
        }
    }
    
    updateCodeHighlighting(themeName) {
        // Update Prism.js if it exists
        if (window.Prism) {
            Prism.highlightAll();
        }
        
        // Update custom code blocks
        const codeBlocks = document.querySelectorAll('pre code');
        codeBlocks.forEach(block => {
            block.style.background = `var(--code-bg)`;
            block.style.color = `var(--code-text)`;
        });
    }
    
    updateCustomComponents(themeName) {
        // Update any custom components that need theme awareness
        const customComponents = document.querySelectorAll('[data-theme-aware]');
        customComponents.forEach(component => {
            component.setAttribute('data-current-theme', themeName);
        });
    }
    
    hexToRgba(hex, alpha) {
        const r = parseInt(hex.slice(1, 3), 16);
        const g = parseInt(hex.slice(3, 5), 16);
        const b = parseInt(hex.slice(5, 7), 16);
        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    }
    
    showThemePreview() {
        const modal = document.getElementById('theme-preview-modal');
        if (modal) {
            modal.style.display = 'block';
        }
    }
    
    openCustomization() {
        // Open theme customization panel
        console.log('Opening theme customization...');
        // This can be expanded later
    }
    
    previewTheme(themeName) {
        // Temporarily apply theme for preview
        const originalTheme = this.currentTheme;
        document.body.setAttribute('data-theme', themeName);
        
        // Revert after 3 seconds
        setTimeout(() => {
            document.body.setAttribute('data-theme', originalTheme);
        }, 3000);
    }
    
    hideThemePreview() {
        const modal = document.querySelector('.theme-preview-modal');
        if (modal) {
            modal.classList.remove('show');
        }
    }
    
    toggleCustomization() {
        const panel = document.querySelector('.theme-customization-panel');
        if (panel) {
            panel.classList.toggle('show');
            
            if (panel.classList.contains('show')) {
                this.loadCustomizationValues();
            }
        }
    }
    
    loadCustomizationValues() {
        const saved = this.getCustomization();
        
        // Load saved values
        const primaryColor = document.getElementById('custom-primary');
        if (primaryColor) {
            primaryColor.value = saved.primaryColor || this.themeColors[this.currentTheme].primary;
        }
        
        const opacitySlider = document.getElementById('custom-opacity');
        if (opacitySlider) {
            opacitySlider.value = saved.opacity || 1;
        }
        
        const radiusSlider = document.getElementById('custom-radius');
        if (radiusSlider) {
            radiusSlider.value = saved.borderRadius || 8;
        }
        
        const animationSelect = document.getElementById('custom-animation');
        if (animationSelect) {
            animationSelect.value = saved.animationSpeed || 'normal';
        }
    }
    
    updateCustomColor(type, value) {
        document.documentElement.style.setProperty(`--custom-${type}`, value);
    }
    
    updateCustomOpacity(value) {
        document.documentElement.style.setProperty('--custom-opacity', value);
    }
    
    updateCustomRadius(value) {
        document.documentElement.style.setProperty('--custom-border-radius', `${value}px`);
    }
    
    updateCustomAnimation(value) {
        const speeds = {
            fast: '150ms',
            normal: '250ms',
            slow: '400ms'
        };
        document.documentElement.style.setProperty('--custom-transition', speeds[value]);
    }
    
    saveCustomization() {
        const customization = {
            primaryColor: document.getElementById('custom-primary')?.value,
            opacity: document.getElementById('custom-opacity')?.value,
            borderRadius: document.getElementById('custom-radius')?.value,
            animationSpeed: document.getElementById('custom-animation')?.value
        };
        
        localStorage.setItem('affinity_customization', JSON.stringify(customization));
        
        // Show success message
        this.showCustomizationNotification('Customization saved!', 'success');
        
        // Hide panel
        this.toggleCustomization();
    }
    
    resetCustomization() {
        // Reset to default values
        this.updateCustomColor('primary', this.themeColors[this.currentTheme].primary);
        this.updateCustomOpacity(1);
        this.updateCustomRadius(8);
        this.updateCustomAnimation('normal');
        
        // Clear saved customization
        localStorage.removeItem('affinity_customization');
        
        // Show reset message
        this.showCustomizationNotification('Customization reset to default', 'info');
        
        // Reload values
        this.loadCustomizationValues();
    }
    
    getCustomization() {
        const saved = localStorage.getItem('affinity_customization');
        return saved ? JSON.parse(saved) : {};
    }
    
    showCustomizationNotification(message, type = 'info') {
        // Create notification
        const notification = document.createElement('div');
        notification.className = `customization-notification ${type}`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Show and hide
        setTimeout(() => notification.classList.add('show'), 100);
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
    
    onThemeChange(themeName) {
        // Handle any additional theme change logic
        console.log(`Theme changed to: ${themeName}`);
        
        // Update any external components
        this.updateExternalComponents(themeName);
    }
    
    updateExternalComponents(themeName) {
        // Update any external libraries or components
        // This can be extended based on what's used in the forum
        
        // Example: Update TinyMCE if it exists
        if (window.tinymce) {
            window.tinymce.editors.forEach(editor => {
                editor.theme.resizeTo(editor.getContainer().offsetWidth, editor.getContainer().offsetHeight);
            });
        }
        
        // Example: Update Summernote if it exists
        if (window.summernote) {
            // Summernote specific updates
        }
    }
}

// Initialize theme manager when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎨 Initializing Theme Manager...');
    window.themeManager = new ThemeManager();
    console.log('✅ Theme Manager initialized successfully!');
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ThemeManager;
}