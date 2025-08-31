<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Connection Test</h1>";

try {
    // Test config loading
    echo "<h2>1. Loading Config</h2>";
    require_once 'config.php';
    echo "✅ Config loaded successfully<br>";
    
    // Test database connection
    echo "<h2>2. Database Connection</h2>";
    $pdo = getDBConnection();
    echo "✅ Database connection successful<br>";
    
    // Test basic query
    echo "<h2>3. Basic Query Test</h2>";
    $stmt = $pdo->query("SELECT 1 as test");
    $result = $stmt->fetch();
    echo "✅ Basic query successful: " . $result['test'] . "<br>";
    
    // Test if users table exists
    echo "<h2>4. Table Existence Check</h2>";
    $tables = ['users', 'threads', 'posts', 'downloads', 'private_messages'];
    
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
            $result = $stmt->fetch();
            echo "✅ Table '$table' exists - Count: " . $result['count'] . "<br>";
        } catch (Exception $e) {
            echo "❌ Table '$table' error: " . $e->getMessage() . "<br>";
        }
    }
    
    // Test users table structure
    echo "<h2>5. Users Table Structure</h2>";
    try {
        $stmt = $pdo->query("DESCRIBE users");
        $columns = $stmt->fetchAll();
        echo "Users table columns:<br>";
        foreach ($columns as $column) {
            echo "- " . $column['Field'] . " (" . $column['Type'] . ")<br>";
        }
    } catch (Exception $e) {
        echo "❌ Error getting table structure: " . $e->getMessage() . "<br>";
    }
    
    // Test session functions
    echo "<h2>6. Function Tests</h2>";
    $functions = ['isLoggedIn', 'isAdmin', 'redirect', 'generateCSRFToken'];
    
    foreach ($functions as $func) {
        if (function_exists($func)) {
            echo "✅ Function '$func' exists<br>";
        } else {
            echo "❌ Function '$func' not found<br>";
        }
    }
    
    // Test session
    echo "<h2>7. Session Test</h2>";
    if (session_status() === PHP_SESSION_ACTIVE) {
        echo "✅ Session is active<br>";
        if (isset($_SESSION)) {
            echo "Session variables: <pre>" . print_r($_SESSION, true) . "</pre>";
        } else {
            echo "No session variables<br>";
        }
    } else {
        echo "❌ Session is not active<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Fatal error: " . $e->getMessage() . "<br>";
    echo "Stack trace: <pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<p>Test complete. Check the output above for any errors.</p>";
?>
