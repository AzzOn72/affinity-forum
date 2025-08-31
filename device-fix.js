// Device Selection Fix
console.log('🔧 Device selection fix loading...');

// Simple device click handler
function handleDeviceClick(device) {
    console.log('🎯 Device clicked:', device);
    
    // Apply device class
    document.body.classList.remove('device-desktop', 'device-tablet', 'device-mobile');
    document.body.classList.add(`device-${device}`);
    document.body.setAttribute('data-device', device);
    
    // Save to localStorage
    localStorage.setItem('selectedDevice', device);
    
    // Hide overlay
    const overlay = document.getElementById('deviceOverlay');
    if (overlay) {
        overlay.style.display = 'none';
    }
    
    // Show success message
    alert(`Device set to ${device}!`);
    
    console.log('✅ Device selection completed');
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Setting up device selection...');
    
    const deviceOptions = document.querySelectorAll('.device-option');
    
    deviceOptions.forEach(option => {
        const device = option.dataset.device;
        console.log('🔧 Setting up device option:', device);
        
        // Add simple onclick handler
        option.onclick = function() {
            handleDeviceClick(device);
        };
    });
    
    console.log('✅ Device selection setup complete');
});

console.log('🔧 Device selection fix loaded');

