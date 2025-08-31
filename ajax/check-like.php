<?php
require_once '../config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'message' => 'Invalid security token']);
    exit;
}

$post_id = intval($_POST['post_id'] ?? 0);

if (!$post_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid post ID']);
    exit;
}

try {
    $pdo = getDBConnection();
    
    // Check if user has liked this post
    $stmt = $pdo->prepare("SELECT id FROM likes WHERE user_id = ? AND post_id = ? AND is_active = 1");
    $stmt->execute([$_SESSION['user_id'], $post_id]);
    $like = $stmt->fetch();
    
    echo json_encode([
        'success' => true,
        'liked' => $like ? true : false
    ]);
    
} catch (Exception $e) {
    error_log('Check like error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to check like status']);
}
?>
