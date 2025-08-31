// Simple device selection test
console.log('🔍 Debug device selection loaded');

document.addEventListener('DOMContentLoaded', () => {
    console.log('🔍 DOM loaded, checking device selection...');
    
    const deviceOverlay = document.getElementById('deviceOverlay');
    const deviceOptions = document.querySelectorAll('.device-option');
    
    console.log('🔍 Device overlay found:', !!deviceOverlay);
    console.log('🔍 Device options found:', deviceOptions.length);
    
    if (deviceOptions.length > 0) {
        deviceOptions.forEach((option, index) => {
            console.log(`🔍 Device option ${index}:`, option.dataset.device);
            
            // Add simple click handler
            option.addEventListener('click', function(e) {
                console.log('🎯 CLICKED device option:', this.dataset.device);
                
                const device = this.dataset.device;
                
                // Apply device class
                document.body.classList.remove('device-desktop', 'device-tablet', 'device-mobile');
                document.body.classList.add(`device-${device}`);
                document.body.setAttribute('data-device', device);
                
                // Hide overlay
                if (deviceOverlay) {
                    deviceOverlay.style.display = 'none';
                }
                
                // Show simple alert
                alert(`Device set to ${device}!`);
                
                // Save to localStorage
                localStorage.setItem('selectedDevice', device);
                
                console.log('✅ Device selection completed');
            });
        });
        
        console.log('✅ Device selection handlers attached');
    } else {
        console.error('❌ No device options found!');
    }
});
