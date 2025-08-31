<?php
require_once '../config.php';

// Check if user is admin
if (!isAdmin()) {
    redirect('../login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('users.php');
}

if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
    redirect('users.php?error=invalid_token');
}

$username = sanitizeInput($_POST['username'] ?? '');
$email = sanitizeInput($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$rank = sanitizeInput($_POST['rank'] ?? 'user');

// Validation
$errors = [];

if (empty($username) || strlen($username) < 3) {
    $errors[] = 'Username must be at least 3 characters long';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address';
}

if (empty($password) || strlen($password) < 6) {
    $errors[] = 'Password must be at least 6 characters long';
}

if (!in_array($rank, ['user', 'moderator', 'admin'])) {
    $errors[] = 'Invalid role selected';
}

if (!empty($errors)) {
    redirect('users.php?error=' . urlencode(implode(', ', $errors)));
}

try {
    $pdo = getDBConnection();
    
    // Check if username already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        redirect('users.php?error=username_exists');
    }
    
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        redirect('users.php?error=email_exists');
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, rank, created_at, is_active) VALUES (?, ?, ?, ?, NOW(), 1)");
    $stmt->execute([$username, $email, $hashed_password, $rank]);
    
    redirect('users.php?success=user_created');
    
} catch (Exception $e) {
    redirect('users.php?error=' . urlencode('Database error: ' . $e->getMessage()));
}
?>
