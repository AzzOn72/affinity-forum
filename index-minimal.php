<?php
/**
 * Minimal Index.php Test
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html><head><title>Minimal Index Test</title></head><body>";
echo "<h1>🔍 Minimal Index Test</h1>";

// Step 1: Include config
echo "<h2>🔧 Step 1: Include Config</h2>";
try {
    require_once 'config.php';
    echo "<p style='color: green;'>✅ Config loaded successfully</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Config failed: " . $e->getMessage() . "</p>";
    die("Stopping here");
}

// Step 2: Database connection
echo "<h2>🗄️ Step 2: Database Connection</h2>";
try {
    $pdo = getDBConnection();
    echo "<p style='color: green;'>✅ Database connected successfully</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database failed: " . $e->getMessage() . "</p>";
    die("Stopping here");
}

// Step 3: Simple database query
echo "<h2>📊 Step 3: Simple Database Query</h2>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "<p style='color: green;'>✅ Database query successful</p>";
    echo "<p>Total users: " . $result['count'] . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database query failed: " . $e->getMessage() . "</p>";
}

// Step 4: Test getForumStats
echo "<h2>📈 Step 4: Test getForumStats</h2>";
try {
    $stats = getForumStats();
    echo "<p style='color: green;'>✅ getForumStats() successful</p>";
    echo "<p>Stats: " . json_encode($stats) . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ getForumStats() failed: " . $e->getMessage() . "</p>";
}

// Step 5: Test header include
echo "<h2>📄 Step 5: Test Header Include</h2>";
try {
    include 'includes/header.php';
    echo "<p style='color: green;'>✅ Header included successfully</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Header failed: " . $e->getMessage() . "</p>";
}

echo "<h2>🎯 Next Steps</h2>";
echo "<p>If all steps pass, the issue is in the main index.php logic.</p>";

echo "</body></html>";
?>
