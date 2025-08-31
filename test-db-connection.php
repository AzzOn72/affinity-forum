<?php
/**
 * Simple Database Connection Test
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html><head><title>Database Connection Test</title></head><body>";
echo "<h1>🗄️ Database Connection Test</h1>";

// Test 1: Load config
echo "<h2>📁 Test 1: Load Config</h2>";
try {
    require_once 'config.php';
    echo "<p style='color: green;'>✅ Config loaded successfully</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Config failed: " . $e->getMessage() . "</p>";
    exit;
}

// Test 2: Database connection
echo "<h2>🔌 Test 2: Database Connection</h2>";
try {
    $pdo = getDBConnection();
    echo "<p style='color: green;'>✅ Database connected successfully</p>";
    echo "<p><strong>Host:</strong> " . DB_HOST . "</p>";
    echo "<p><strong>Database:</strong> " . DB_NAME . "</p>";
    echo "<p><strong>User:</strong> " . DB_USER . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
    exit;
}

// Test 3: Basic query
echo "<h2>🔍 Test 3: Basic Query</h2>";
try {
    $stmt = $pdo->query("SELECT 1 as test");
    $result = $stmt->fetch();
    echo "<p style='color: green;'>✅ Basic query successful: " . $result['test'] . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Basic query failed: " . $e->getMessage() . "</p>";
}

// Test 4: Check tables
echo "<h2>📋 Test 4: Check Tables</h2>";
try {
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($tables)) {
        echo "<p style='color: orange;'>⚠️ No tables found in database</p>";
    } else {
        echo "<p style='color: green;'>✅ Found " . count($tables) . " tables:</p>";
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>$table</li>";
        }
        echo "</ul>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Table check failed: " . $e->getMessage() . "</p>";
}

// Test 5: Check specific tables
echo "<h2>🎯 Test 5: Check Required Tables</h2>";
$required_tables = ['users', 'threads', 'posts', 'categories'];
foreach ($required_tables as $table) {
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "<p style='color: green;'>✅ Table '$table' exists</p>";
            
            // Check if table has data
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM `$table`");
            $count = $stmt->fetch()['count'];
            echo "<p style='color: blue;'>   - Records: $count</p>";
        } else {
            echo "<p style='color: red;'>❌ Table '$table' missing</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error checking table '$table': " . $e->getMessage() . "</p>";
    }
}

echo "<h2>🎯 Summary</h2>";
echo "<p>If you see any red ❌ marks, those are the problems causing the 500 error.</p>";
echo "<p>If tables are missing, you may need to run the database setup files.</p>";

echo "</body></html>";
?>