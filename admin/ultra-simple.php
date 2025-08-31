<?php
// Ultra simple admin test - no includes, no complex logic
echo "Admin test - step 1: Basic PHP works<br>";

// Test session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
echo "Admin test - step 2: Session started<br>";

// Check if user is logged in
if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])) {
    echo "Admin test - step 3: User logged in (ID: " . $_SESSION['user_id'] . ", Role: " . $_SESSION['user_role'] . ")<br>";
    
    if ($_SESSION['user_role'] === 'admin') {
        echo "Admin test - step 4: User is admin - SUCCESS!<br>";
        echo "<h2>Welcome to Admin Panel</h2>";
        echo "<p>This is a basic test. If you can see this, admin access is working.</p>";
        echo "<a href='index.php'>Try Full Admin Panel</a><br>";
        echo "<a href='index-simple.php'>Try Simple Admin Panel</a><br>";
        echo "<a href='../'>Back to Forum</a>";
    } else {
        echo "Admin test - step 4: User is NOT admin<br>";
    }
} else {
    echo "Admin test - step 3: User NOT logged in<br>";
    echo "<a href='../login.php'>Login First</a>";
}
?>

