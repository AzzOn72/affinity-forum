<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Admin Debug Page</h1>";

// Test 1: Check if config loads
echo "<h2>Test 1: Config Loading</h2>";
try {
    require_once '../config.php';
    echo "✅ Config loaded successfully<br>";
} catch (Exception $e) {
    echo "❌ Config failed to load: " . $e->getMessage() . "<br>";
    exit;
}

// Test 2: Check session
echo "<h2>Test 2: Session Check</h2>";
echo "Session status: " . (session_status() === PHP_SESSION_ACTIVE ? "Active" : "Not Active") . "<br>";
if (isset($_SESSION)) {
    echo "Session variables: <pre>" . print_r($_SESSION, true) . "</pre>";
} else {
    echo "No session variables found<br>";
}

// Test 3: Check if user is logged in
echo "<h2>Test 3: Login Check</h2>";
if (function_exists('isLoggedIn')) {
    echo "isLoggedIn function exists<br>";
    echo "User logged in: " . (isLoggedIn() ? "Yes" : "No") . "<br>";
} else {
    echo "❌ isLoggedIn function not found<br>";
}

// Test 4: Check if user is admin
echo "<h2>Test 4: Admin Check</h2>";
if (function_exists('isAdmin')) {
    echo "isAdmin function exists<br>";
    echo "User is admin: " . (isAdmin() ? "Yes" : "No") . "<br>";
} else {
    echo "❌ isAdmin function not found<br>";
}

// Test 5: Database connection
echo "<h2>Test 5: Database Connection</h2>";
try {
    $pdo = getDBConnection();
    echo "✅ Database connection successful<br>";
    
    // Test if users table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Users table exists<br>";
        
        // Test if we can query users
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch();
        echo "✅ Users table query successful. Total users: " . $result['count'] . "<br>";
        
        // Check table structure
        $stmt = $pdo->query("DESCRIBE users");
        $columns = $stmt->fetchAll();
        echo "Users table columns:<br>";
        foreach ($columns as $column) {
            echo "- " . $column['Field'] . " (" . $column['Type'] . ")<br>";
        }
        
    } else {
        echo "❌ Users table does not exist<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
}

// Test 6: Check if required functions exist
echo "<h2>Test 6: Required Functions</h2>";
$required_functions = ['redirect', 'generateCSRFToken', 'sanitizeInput', 'formatDate', 'formatTimeAgo'];
foreach ($required_functions as $func) {
    if (function_exists($func)) {
        echo "✅ $func function exists<br>";
    } else {
        echo "❌ $func function not found<br>";
    }
}

echo "<hr>";
echo "<p>Debug complete. Check the output above for any errors.</p>";
?>
