<?php
require_once 'config.php';
require_once 'includes/header.php';
?>

<div class="container mt-5">
    <h1>JavaScript Loading Test</h1>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Theme Manager Status</h5>
                </div>
                <div class="card-body">
                    <p><strong>Theme Manager:</strong> <span id="themeManagerStatus">Checking...</span></p>
                    <p><strong>selectDevice function:</strong> <span id="selectDeviceStatus">Checking...</span></p>
                    <p><strong>switchTheme function:</strong> <span id="switchThemeStatus">Checking...</span></p>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Test Functions</h5>
                </div>
                <div class="card-body">
                    <button class="btn btn-primary mb-2" onclick="testSelectDevice()">Test selectDevice</button>
                    <button class="btn btn-success mb-2" onclick="testSwitchTheme()">Test switchTheme</button>
                    <button class="btn btn-info" onclick="checkConsole()">Check Console</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Console Output</h5>
                </div>
                <div class="card-body">
                    <div id="consoleOutput" style="background: #f8f9fa; padding: 15px; border-radius: 5px; font-family: monospace; max-height: 300px; overflow-y: auto;">
                        <p>Console output will appear here...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Override console.log to capture output
const originalLog = console.log;
const originalError = console.error;
const originalWarn = console.warn;

function addToConsole(message, type = 'log') {
    const output = document.getElementById('consoleOutput');
    const timestamp = new Date().toLocaleTimeString();
    const color = type === 'error' ? 'red' : type === 'warn' ? 'orange' : 'black';
    
    output.innerHTML += `<p style="color: ${color}; margin: 2px 0;"><strong>[${timestamp}]</strong> ${message}</p>`;
    output.scrollTop = output.scrollHeight;
}

console.log = function(...args) {
    originalLog.apply(console, args);
    addToConsole(args.join(' '), 'log');
};

console.error = function(...args) {
    originalError.apply(console, args);
    addToConsole(args.join(' '), 'error');
};

console.warn = function(...args) {
    originalWarn.apply(console, args);
    addToConsole(args.join(' '), 'warn');
};

// Test functions
function testSelectDevice() {
    console.log('Testing selectDevice function...');
    if (typeof selectDevice === 'function') {
        selectDevice('tablet');
    } else {
        console.error('selectDevice is not a function');
    }
}

function testSwitchTheme() {
    console.log('Testing switchTheme function...');
    if (typeof switchTheme === 'function') {
        switchTheme('dark');
    } else {
        console.error('switchTheme is not a function');
    }
}

function checkConsole() {
    console.log('Console check requested');
    console.log('window.themeManager:', window.themeManager);
    console.log('window.selectDevice:', window.selectDevice);
    console.log('window.switchTheme:', window.switchTheme);
}

// Check status on load
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, checking JavaScript status...');
    
    setTimeout(() => {
        // Update status displays
        document.getElementById('themeManagerStatus').textContent = 
            window.themeManager ? 'Available' : 'Not Available';
        
        document.getElementById('selectDeviceStatus').textContent = 
            typeof selectDevice === 'function' ? 'Available' : 'Not Available';
        
        document.getElementById('switchThemeStatus').textContent = 
            typeof switchTheme === 'function' ? 'Available' : 'Not Available';
        
        console.log('Status check complete');
    }, 500);
});

// Log when scripts load
console.log('Test page script loaded');
</script>

<?php require_once 'includes/footer.php'; ?>
