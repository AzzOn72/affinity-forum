/**
 * Minimal Ultra Premium Theme Manager Test
 */

console.log('🚀 Minimal themes file loading...');

// Simple class definition
class UltraPremiumThemeManager {
    constructor() {
        this.currentTheme = 'light';
        this.currentDevice = 'desktop';
        console.log('🎨 Minimal Theme Manager initialized');
    }
    
    switchTheme(themeName) {
        console.log('🎨 Switching theme to:', themeName);
        this.currentTheme = themeName;
        document.body.setAttribute('data-theme', themeName);
        console.log('✅ Theme switched successfully');
    }
    
    selectDevice(device) {
        console.log('📱 Selecting device:', device);
        this.currentDevice = device;
        document.body.setAttribute('data-device', device);
        console.log('✅ Device selected successfully');
    }
}

// Create global instance
window.themeManager = new UltraPremiumThemeManager();
console.log('✅ Global themeManager instance created');

// Global functions
window.switchTheme = (themeName) => {
    console.log('🌍 Global switchTheme called with:', themeName);
    if (window.themeManager) {
        window.themeManager.switchTheme(themeName);
    } else {
        console.error('❌ Theme manager not available');
    }
};

window.selectDevice = (device) => {
    console.log('🌍 Global selectDevice called with:', device);
    if (window.themeManager) {
        window.themeManager.selectDevice(device);
    } else {
        console.error('❌ Theme manager not available');
    }
};

window.showDeviceOverlay = () => {
    console.log('🌍 Global showDeviceOverlay called');
    const overlay = document.getElementById('deviceOverlay');
    if (overlay) {
        overlay.style.display = 'flex';
        console.log('✅ Device overlay shown');
    } else {
        console.error('❌ Device overlay not found');
    }
};

console.log('🎨 Minimal Theme Manager loaded successfully!');
console.log('🌍 All global functions created successfully!');
