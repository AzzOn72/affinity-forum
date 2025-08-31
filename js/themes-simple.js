/**
 * Simple Working Theme System
 */

// Simple working theme system
console.log('🎨 Simple theme system loading...');

// Global theme functions
window.switchTheme = function(themeName) {
    console.log('🎯 Switching theme to:', themeName);
    
    // Remove all theme classes
    document.body.classList.remove('theme-light', 'theme-dark', 'theme-cs2', 'theme-premium');
    
    // Add new theme class
    document.body.classList.add(`theme-${themeName}`);
    
    // Set data attribute
    document.body.setAttribute('data-theme', themeName);
    
    // Save to localStorage
    localStorage.setItem('affinity_theme', themeName);
    
    // Update theme toggle button
    const themeToggle = document.querySelector('.theme-toggle');
    if (themeToggle) {
        const buttonText = themeName === 'light' ? 'Dark' : 'Light';
        const icon = themeName === 'light' ? 'moon' : 'sun';
        themeToggle.innerHTML = `<i class="fas fa-${icon}"></i><span>${buttonText}</span>`;
    }
    
    // Show notification
    showNotification(`Theme changed to ${themeName}!`, 'success');
    
    console.log('✅ Theme switched successfully');
};

// Device selection
window.initDeviceSelection = function() {
    console.log('🚀 Initializing device selection...');
    
    const deviceOverlay = document.getElementById('deviceOverlay');
    const deviceOptions = document.querySelectorAll('.device-option');
    
    console.log('🔍 Device overlay found:', !!deviceOverlay);
    console.log('🔍 Device options found:', deviceOptions.length);
    
    if (!deviceOverlay) {
        console.warn('⚠️ Device overlay not found');
        return;
    }
    
    // Check if device is already selected
    const selectedDevice = localStorage.getItem('selectedDevice');
    if (selectedDevice) {
        applyDeviceClass(selectedDevice);
        deviceOverlay.style.display = 'none';
    }
    
    // Add click handlers to device options
    deviceOptions.forEach((option, index) => {
        console.log(`🔧 Setting up device option ${index}:`, option.dataset.device);
        
        // Remove existing listeners
        option.removeEventListener('click', handleDeviceClick);
        
        // Add new listener
        option.addEventListener('click', handleDeviceClick);
    });
    
    console.log('✅ Device selection initialized');
};

function handleDeviceClick(e) {
    console.log('🎯 Device clicked:', e.currentTarget.dataset.device);
    
    const device = e.currentTarget.dataset.device;
    
    // Apply device class
    applyDeviceClass(device);
    
    // Save to localStorage
    localStorage.setItem('selectedDevice', device);
    
    // Hide overlay
    const deviceOverlay = document.getElementById('deviceOverlay');
    if (deviceOverlay) {
        deviceOverlay.style.display = 'none';
    }
    
    // Show notification
    showNotification(`Device set to ${device}!`, 'success');
    
    console.log('✅ Device selection completed');
}

function applyDeviceClass(device) {
    console.log('🎯 Applying device class:', device);
    
    // Remove all device classes
    document.body.classList.remove('device-desktop', 'device-tablet', 'device-mobile');
    
    // Add new device class
    document.body.classList.add(`device-${device}`);
    
    // Set data attribute
    document.body.setAttribute('data-device', device);
    
    console.log('✅ Device class applied');
}

// Notification system
window.showNotification = function(message, type = 'info') {
    console.log('📢 Notification:', message, type);
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${getNotificationIcon(type)}"></i>
            <span>${message}</span>
            <button class="notification-close" onclick="this.parentElement.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    // Add to body
    document.body.appendChild(notification);
    
    // Show notification
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 300);
    }, 5000);
};

function getNotificationIcon(type) {
    const icons = {
        success: 'check-circle',
        warning: 'exclamation-triangle',
        error: 'times-circle',
        info: 'info-circle'
    };
    return icons[type] || 'info-circle';
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 DOM loaded, initializing simple theme system...');
    
    // Load saved theme
    const savedTheme = localStorage.getItem('affinity_theme') || 'light';
    switchTheme(savedTheme);
    
    // Initialize device selection
    initDeviceSelection();
    
    console.log('✅ Simple theme system initialized');
});

// Also try to initialize immediately if DOM is already loaded
if (document.readyState === 'loading') {
    console.log('📝 DOM still loading, waiting for DOMContentLoaded...');
} else {
    console.log('🚀 DOM already loaded, initializing immediately...');
    
    // Load saved theme
    const savedTheme = localStorage.getItem('affinity_theme') || 'light';
    switchTheme(savedTheme);
    
    // Initialize device selection
    initDeviceSelection();
}

console.log('🎨 Simple theme system loaded successfully!');
