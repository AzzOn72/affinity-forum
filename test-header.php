<?php
/**
 * Test Header Include
 */

echo "<!DOCTYPE html>";
echo "<html><head><title>Header Test</title></head><body>";
echo "<h1>🔍 Testing Header Include</h1>";

// Test 1: Direct config include
echo "<h2>✅ Test 1: Direct Config Include</h2>";
try {
    require_once 'config.php';
    echo "<p style='color: green;'>✅ Config loaded successfully</p>";
    echo "<p>SECURE_ACCESS: " . (defined('SECURE_ACCESS') ? 'YES' : 'NO') . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Config failed: " . $e->getMessage() . "</p>";
}

// Test 2: Header include
echo "<h2>🔧 Test 2: Header Include</h2>";
try {
    include 'includes/header.php';
    echo "<p style='color: green;'>✅ Header included successfully</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Header failed: " . $e->getMessage() . "</p>";
}

// Test 3: Test the path calculation
echo "<h2>🧮 Test 3: Path Calculation</h2>";
$includes_dir = __DIR__ . '/includes';
$config_from_includes = dirname($includes_dir) . '/config.php';
echo "<p><strong>Includes directory:</strong> $includes_dir</p>";
echo "<p><strong>Config path from includes:</strong> $config_from_includes</p>";
echo "<p><strong>Config exists:</strong> " . (file_exists($config_from_includes) ? 'YES' : 'NO') . "</p>";

echo "<h2>🎯 Next Steps</h2>";
echo "<p>If all tests pass, your forum should work!</p>";
echo "<p><a href='index.php'>🏠 Try Homepage</a></p>";

echo "</body></html>";
?>
