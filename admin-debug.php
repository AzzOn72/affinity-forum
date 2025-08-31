<?php
// Admin debug file - place this in the root directory
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Admin Access Debug</h2>";

// Test 1: Basic PHP
echo "<h3>1. Basic PHP Test</h3>";
echo "✓ PHP is working<br>";
echo "PHP Version: " . PHP_VERSION . "<br><br>";

// Test 2: Session
echo "<h3>2. Session Test</h3>";
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "✓ Sessions are active<br>";
} else {
    echo "✗ Sessions are NOT active<br>";
    session_start();
    echo "✓ Sessions started<br>";
}

echo "Session ID: " . session_id() . "<br>";
echo "Session data: <pre>" . print_r($_SESSION, true) . "</pre><br>";

// Test 3: Config file
echo "<h3>3. Config File Test</h3>";
try {
    require_once 'config.php';
    echo "✓ Config file loaded<br>";
    echo "Site name: " . SITE_NAME . "<br>";
    echo "DB host: " . DB_HOST . "<br><br>";
} catch (Exception $e) {
    echo "✗ Config file error: " . $e->getMessage() . "<br><br>";
    exit;
}

// Test 4: Database connection
echo "<h3>4. Database Connection Test</h3>";
try {
    $pdo = getDBConnection();
    echo "✓ Database connected<br>";
    
    // Test basic query
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $user_count = $stmt->fetchColumn();
    echo "✓ Users table accessible: $user_count users<br><br>";
} catch (Exception $e) {
    echo "✗ Database error: " . $e->getMessage() . "<br><br>";
    exit;
}

// Test 5: Admin check
echo "<h3>5. Admin Check Test</h3>";
if (isLoggedIn()) {
    echo "✓ User is logged in<br>";
    echo "User ID: " . $_SESSION['user_id'] . "<br>";
    echo "User Role: " . ($_SESSION['user_role'] ?? 'NOT SET') . "<br>";
    
    if (isAdmin()) {
        echo "✓ User is admin<br>";
    } else {
        echo "✗ User is NOT admin<br>";
        
        // Check what's in the database
        try {
            $stmt = $pdo->prepare("SELECT username, rank FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
            
            if ($user) {
                echo "Database shows user rank: " . $user['rank'] . "<br>";
                echo "Session shows user_role: " . ($_SESSION['user_role'] ?? 'NOT SET') . "<br>";
                
                if ($user['rank'] === 'admin') {
                    echo "✓ User has admin rank in database<br>";
                    echo "✗ But session user_role is not set correctly<br>";
                } else {
                    echo "✗ User does not have admin rank in database<br>";
                }
            } else {
                echo "✗ User not found in database<br>";
            }
        } catch (Exception $e) {
            echo "✗ Database query error: " . $e->getMessage() . "<br>";
        }
    }
} else {
    echo "✗ User is NOT logged in<br>";
    echo "<a href='login.php'>Go to login</a><br>";
}

echo "<br>";

// Test 6: Try to access admin
echo "<h3>6. Admin Access Test</h3>";
if (isAdmin()) {
    echo "✓ Admin access granted<br>";
    echo "<a href='admin/index-simple.php'>Access Simple Admin Panel</a><br>";
    echo "<a href='admin/index.php'>Access Full Admin Panel</a><br>";
} else {
    echo "✗ Admin access denied<br>";
    echo "You need to be logged in as an admin user.<br>";
}

echo "<br><hr>";
echo "<h3>Debug Information</h3>";
echo "Current file: " . __FILE__ . "<br>";
echo "Document root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Request URI: " . $_SERVER['REQUEST_URI'] . "<br>";
echo "Script name: " . $_SERVER['SCRIPT_NAME'] . "<br>";
?>
