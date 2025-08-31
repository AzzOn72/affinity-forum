<?php
// Path fix for InfinityFree - config.php is in the parent directory of ajax/
$config_path = dirname(__DIR__) . '/config.php';
if (file_exists($config_path)) {
    require_once $config_path;
} else {
    die("Config file not found at: $config_path");
}

// Set JSON content type
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Validate CSRF token
if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid CSRF token']);
    exit;
}

// Get username from request
$username = sanitizeInput($_POST['username'] ?? '');

if (empty($username)) {
    echo json_encode(['error' => 'Username is required']);
    exit;
}

// Validate username format
if (strlen($username) < 3 || strlen($username) > 20) {
    echo json_encode(['available' => false, 'message' => 'Username must be between 3 and 20 characters']);
    exit;
}

if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
    echo json_encode(['available' => false, 'message' => 'Username can only contain letters, numbers, underscores, and hyphens']);
    exit;
}

try {
    // Get database connection
    $pdo = getDBConnection();
    
    // Check if username exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $exists = $stmt->fetch();
    
    if ($exists) {
        echo json_encode(['available' => false, 'message' => 'Username is already taken']);
    } else {
        echo json_encode(['available' => true, 'message' => 'Username is available']);
    }
    
} catch (PDOException $e) {
    error_log('Username check error: ' . $e->getMessage());
    echo json_encode(['error' => 'Database error occurred']);
}
?>
