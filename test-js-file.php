<?php
require_once 'config.php';
require_once 'includes/header.php';
?>

<div class="container mt-5">
    <h1>JavaScript File Test</h1>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>File Accessibility Test</h5>
                </div>
                <div class="card-body">
                    <p><strong>JS File Path:</strong> js/themes-ultra-premium.js</p>
                    <p><strong>File Exists:</strong> 
                        <?php echo file_exists('js/themes-ultra-premium.js') ? '✅ Yes' : '❌ No'; ?>
                    </p>
                    <p><strong>File Size:</strong> 
                        <?php 
                        if (file_exists('js/themes-ultra-premium.js')) {
                            echo number_format(filesize('js/themes-ultra-premium.js')) . ' bytes';
                        } else {
                            echo 'N/A';
                        }
                        ?>
                    </p>
                    <p><strong>File Permissions:</strong> 
                        <?php 
                        if (file_exists('js/themes-ultra-premium.js')) {
                            echo substr(sprintf('%o', fileperms('js/themes-ultra-premium.js')), -4);
                        } else {
                            echo 'N/A';
                        }
                        ?>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Manual Script Test</h5>
                </div>
                <div class="card-body">
                    <button class="btn btn-primary mb-2" onclick="loadScriptManually()">Load Script Manually</button>
                    <button class="btn btn-success mb-2" onclick="checkFunctions()">Check Functions</button>
                    <div id="manualTestResult" class="mt-3"></div>
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
function loadScriptManually() {
    console.log('Loading script manually...');
    
    const script = document.createElement('script');
    script.src = 'js/themes-ultra-premium.js';
    script.onload = function() {
        console.log('✅ Script loaded manually!');
        document.getElementById('manualTestResult').innerHTML = '<div class="alert alert-success">Script loaded successfully!</div>';
        
        // Check functions after loading
        setTimeout(checkFunctions, 1000);
    };
    script.onerror = function() {
        console.error('❌ Failed to load script manually');
        document.getElementById('manualTestResult').innerHTML = '<div class="alert alert-danger">Failed to load script!</div>';
    };
    
    document.head.appendChild(script);
}

function checkFunctions() {
    console.log('Checking functions...');
    let result = '<div class="alert alert-info"><h6>Function Status:</h6>';
    
    if (typeof switchTheme === 'function') {
        result += '<p>✅ switchTheme: Available</p>';
    } else {
        result += '<p>❌ switchTheme: Missing</p>';
    }
    
    if (typeof selectDevice === 'function') {
        result += '<p>✅ selectDevice: Available</p>';
    } else {
        result += '<p>❌ selectDevice: Missing</p>';
    }
    
    if (window.themeManager) {
        result += '<p>✅ themeManager: Available</p>';
    } else {
        result += '<p>❌ themeManager: Missing</p>';
    }
    
    result += '</div>';
    document.getElementById('manualTestResult').innerHTML = result;
}

// Log when page loads
console.log('Test page loaded');
console.log('Current functions status:');
console.log('switchTheme:', typeof switchTheme);
console.log('selectDevice:', typeof selectDevice);
console.log('themeManager:', window.themeManager);
</script>

<?php require_once 'includes/footer.php'; ?>
