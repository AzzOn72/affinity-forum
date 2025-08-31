<?php
/**
 * Debug Paths - Comprehensive Test
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html><head><title>Debug Paths</title></head><body>";
echo "<h1>🔍 Debug Paths - Affinity Forum</h1>";

// Test 1: Server Variables
echo "<h2>🌐 Test 1: Server Variables</h2>";
echo "<p><strong>SCRIPT_NAME:</strong> " . $_SERVER['SCRIPT_NAME'] . "</p>";
echo "<p><strong>SCRIPT_FILENAME:</strong> " . $_SERVER['SCRIPT_FILENAME'] . "</p>";
echo "<p><strong>DOCUMENT_ROOT:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p><strong>REQUEST_URI:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";

// Test 2: PHP Constants
echo "<h2>📁 Test 2: PHP Constants</h2>";
echo "<p><strong>__FILE__:</strong> " . __FILE__ . "</p>";
echo "<p><strong>__DIR__:</strong> " . __DIR__ . "</p>";
echo "<p><strong>dirname(__DIR__):</strong> " . dirname(__DIR__) . "</p>";

// Test 3: Path Calculations
echo "<h2>🧮 Test 3: Path Calculations</h2>";
$paths_to_test = [
    'Current file' => __FILE__,
    'Current directory' => __DIR__,
    'Parent directory' => dirname(__DIR__),
    'Config from current' => __DIR__ . '/../config.php',
    'Config from parent' => dirname(__DIR__) . '/config.php',
    'Config relative' => '../config.php',
    'Config absolute' => $_SERVER['DOCUMENT_ROOT'] . '/affinity-forum/config.php'
];

foreach ($paths_to_test as $description => $path) {
    echo "<p><strong>$description:</strong> $path</p>";
}

// Test 4: File Existence
echo "<h2>✅ Test 4: File Existence</h2>";
$files_to_check = [
    'config.php' => 'config.php',
    'config.php (relative)' => '../config.php',
    'config.php (from current)' => __DIR__ . '/../config.php',
    'config.php (from parent)' => dirname(__DIR__) . '/config.php',
    'error-handler-simple.php' => __DIR__ . '/../error-handler-simple.php',
    'index.php' => __DIR__ . '/../index.php'
];

foreach ($files_to_check as $description => $file_path) {
    if (file_exists($file_path)) {
        echo "<p style='color: green;'>✅ $description: EXISTS at $file_path</p>";
    } else {
        echo "<p style='color: red;'>❌ $description: MISSING at $file_path</p>";
    }
}

// Test 5: Directory Contents
echo "<h2>📂 Test 5: Directory Contents</h2>";
$current_dir = __DIR__;
$parent_dir = dirname(__DIR__);

echo "<p><strong>Current directory ($current_dir):</strong></p>";
if (is_dir($current_dir)) {
    $files = scandir($current_dir);
    echo "<ul>";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "<li>$file</li>";
        }
    }
    echo "</ul>";
}

echo "<p><strong>Parent directory ($parent_dir):</strong></p>";
if (is_dir($parent_dir)) {
    $files = scandir($parent_dir);
    echo "<ul>";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "<li>$file</li>";
        }
    }
    echo "</ul>";
}

echo "<h2>🎯 Next Steps</h2>";
echo "<p>Based on the output above, we can determine the correct path structure.</p>";

echo "</body></html>";
?>
