<?php
/**
 * Debug 500 Error - Minimal Test
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html><head><title>Debug 500</title></head><body>";
echo "<h1>🔍 Debug 500 Error</h1>";

// Test 1: Basic PHP
echo "<h2>✅ Test 1: Basic PHP</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Current Directory: " . __DIR__ . "</p>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";

// Test 2: Check if config.php exists
echo "<h2>📁 Test 2: File Existence</h2>";
$config_paths = [
    'config.php',
    __DIR__ . '/config.php',
    dirname(__DIR__) . '/config.php',
    $_SERVER['DOCUMENT_ROOT'] . '/affinity-forum/config.php'
];

foreach ($config_paths as $path) {
    if (file_exists($path)) {
        echo "<p style='color: green;'>✅ File exists: $path</p>";
    } else {
        echo "<p style='color: red;'>❌ File missing: $path</p>";
    }
}

// Test 3: Try to include config
echo "<h2>🔧 Test 3: Include Config</h2>";
try {
    require_once 'config.php';
    echo "<p style='color: green;'>✅ Config loaded successfully</p>";
    echo "<p>SECURE_ACCESS: " . (defined('SECURE_ACCESS') ? 'YES' : 'NO') . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Config failed: " . $e->getMessage() . "</p>";
} catch (Error $e) {
    echo "<p style='color: red;'>❌ Config failed (Error): " . $e->getMessage() . "</p>";
}

echo "<h2>🎯 Next Steps</h2>";
echo "<p>Check the output above to identify the issue.</p>";

echo "</body></html>";
?>
