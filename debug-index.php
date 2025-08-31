<?php
/**
 * Debug Index.php Step by Step
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html><head><title>Debug Index</title></head><body>";
echo "<h1>🔍 Debug Index.php Step by Step</h1>";

// Step 1: Basic PHP
echo "<h2>✅ Step 1: Basic PHP</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";

// Step 2: Include config
echo "<h2>🔧 Step 2: Include Config</h2>";
try {
    require_once 'config.php';
    echo "<p style='color: green;'>✅ Config loaded successfully</p>";
    echo "<p>SECURE_ACCESS: " . (defined('SECURE_ACCESS') ? 'YES' : 'NO') . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Config failed: " . $e->getMessage() . "</p>";
    die("Stopping here - config failed");
}

// Step 3: Database connection
echo "<h2>🗄️ Step 3: Database Connection</h2>";
try {
    $pdo = getDBConnection();
    echo "<p style='color: green;'>✅ Database connected successfully</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database failed: " . $e->getMessage() . "</p>";
    die("Stopping here - database failed");
}

// Step 4: Test required functions
echo "<h2>⚙️ Step 4: Required Functions</h2>";
$requiredFunctions = ['getForumStats', 'getCategories', 'isLoggedIn', 'formatNumber', 'formatTimeAgo'];
foreach ($requiredFunctions as $func) {
    if (function_exists($func)) {
        echo "<p style='color: green;'>✅ $func function exists</p>";
    } else {
        echo "<p style='color: red;'>❌ $func function missing</p>";
    }
}

// Step 5: Test getForumStats
echo "<h2>📊 Step 5: Test getForumStats</h2>";
try {
    $stats = getForumStats();
    echo "<p style='color: green;'>✅ getForumStats() returned data</p>";
    echo "<pre>" . print_r($stats, true) . "</pre>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ getForumStats() failed: " . $e->getMessage() . "</p>";
}

// Step 6: Test getCategories
echo "<h2>📁 Step 6: Test getCategories</h2>";
try {
    $categories = getCategories();
    echo "<p style='color: green;'>✅ getCategories() returned data</p>";
    echo "<p>Found " . count($categories) . " categories</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ getCategories() failed: " . $e->getMessage() . "</p>";
}

// Step 7: Test header include
echo "<h2>📄 Step 7: Test Header Include</h2>";
try {
    include 'includes/header.php';
    echo "<p style='color: green;'>✅ Header included successfully</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Header failed: " . $e->getMessage() . "</p>";
}

echo "<h2>🎯 Next Steps</h2>";
echo "<p>Check which step failed above.</p>";
echo "<p><a href='index.php'>🏠 Try Homepage Again</a></p>";

echo "</body></html>";
?>
