<?php
// Minimal admin test - bypass all complex logic
echo "<!DOCTYPE html>";
echo "<html><head><title>Admin Test</title></head><body>";
echo "<h1>Admin Access Test</h1>";

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

echo "<p>Session status: " . session_status() . "</p>";
echo "<p>Session ID: " . session_id() . "</p>";

if (isset($_SESSION['user_id'])) {
    echo "<p>User ID: " . $_SESSION['user_id'] . "</p>";
} else {
    echo "<p>No user ID in session</p>";
}

if (isset($_SESSION['user_role'])) {
    echo "<p>User Role: " . $_SESSION['user_role'] . "</p>";
} else {
    echo "<p>No user role in session</p>";
}

if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
    echo "<h2 style='color: green;'>✓ ADMIN ACCESS GRANTED!</h2>";
    echo "<p>You can access the admin panel.</p>";
    echo "<a href='index.php'>Go to Full Admin Panel</a><br>";
    echo "<a href='index-simple.php'>Go to Simple Admin Panel</a><br>";
} else {
    echo "<h2 style='color: red;'>✗ ADMIN ACCESS DENIED!</h2>";
    echo "<p>You need admin privileges to access this area.</p>";
}

echo "<br><a href='../'>Back to Forum</a>";
echo "</body></html>";
?>

