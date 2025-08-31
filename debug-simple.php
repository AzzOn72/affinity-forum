<?php
/**
 * Simple Debug File - Test Basic Functionality
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html><head><title>Simple Debug</title></head><body>";
echo "<h1>🔍 Simple Debug Test</h1>";

// Test 1: Basic PHP
echo "<h2>✅ Test 1: Basic PHP</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Current Directory: " . __DIR__ . "</p>";

// Test 2: Check if config.php exists and can be included
echo "<h2>📁 Test 2: Config File</h2>";
if (file_exists('config.php')) {
    echo "<p style='color: green;'>✅ config.php exists</p>";
    
    try {
        require_once 'config.php';
        echo "<p style='color: green;'>✅ Config loaded successfully</p>";
        echo "<p>SECURE_ACCESS: " . (defined('SECURE_ACCESS') ? 'YES' : 'NO') . "</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Config failed (Exception): " . $e->getMessage() . "</p>";
    } catch (Error $e) {
        echo "<p style='color: red;'>❌ Config failed (Error): " . $e->getMessage() . "</p>";
    } catch (ParseError $e) {
        echo "<p style='color: red;'>❌ Config failed (ParseError): " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ config.php not found</p>";
}

// Test 3: Database connection
echo "<h2>🗄️ Test 3: Database Connection</h2>";
if (function_exists('getDBConnection')) {
    try {
        $pdo = getDBConnection();
        echo "<p style='color: green;'>✅ Database connected successfully</p>";
        
        // Test basic query
        $stmt = $pdo->query("SELECT 1");
        echo "<p style='color: green;'>✅ Basic query successful</p>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ getDBConnection function not found</p>";
}

// Test 4: Check if required functions exist
echo "<h2>🔧 Test 4: Required Functions</h2>";
$required_functions = [
    'getForumStats',
    'getCategories',
    'formatTimeAgo',
    'isLoggedIn'
];

foreach ($required_functions as $func) {
    if (function_exists($func)) {
        echo "<p style='color: green;'>✅ $func function exists</p>";
    } else {
        echo "<p style='color: red;'>❌ $func function missing</p>";
    }
}

// Test 5: Check if includes directory exists
echo "<h2>📁 Test 5: Includes Directory</h2>";
if (is_dir('includes')) {
    echo "<p style='color: green;'>✅ includes directory exists</p>";
    
    $required_includes = ['header.php', 'footer.php'];
    foreach ($required_includes as $file) {
        if (file_exists("includes/$file")) {
            echo "<p style='color: green;'>✅ includes/$file exists</p>";
        } else {
            echo "<p style='color: red;'>❌ includes/$file missing</p>";
                        }
    }
} else {
    echo "<p style='color: red;'>❌ includes directory not found</p>";
}

echo "<h2>🎯 Summary</h2>";
echo "<p>Check the output above to identify any issues.</p>";
echo "<p>If you see any red ❌ marks, those are the problems causing the 500 error.</p>";

echo "</body></html>";
?>