<?php
require_once 'config.php';

// Only allow logged-in users to logout
if (!isLoggedIn()) {
    redirect('index.php');
}

// Get database connection
$pdo = getDBConnection();

// Update user's online status
$stmt = $pdo->prepare("UPDATE users SET is_online = 0 WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);

// Remove remember me token if exists
if (isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];
    
    // Remove from database
    $stmt = $pdo->prepare("DELETE FROM user_sessions WHERE session_token = ?");
    $stmt->execute([$token]);
    
    // Remove cookie
    setcookie('remember_token', '', time() - 3600, '/', '', true, true);
}

// Clear all session data
$_SESSION = array();

// Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to home page with success message
$message = urlencode('You have been successfully logged out.');
redirect('index.php?message=' . $message);
?>
