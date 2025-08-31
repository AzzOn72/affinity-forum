<?php
require_once dirname(__DIR__) . '/config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to like posts']);
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
    
    // Check if post exists and is active
    $stmt = $pdo->prepare("SELECT id, user_id FROM posts WHERE id = ? AND is_active = 1");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();
    
    if (!$post) {
        echo json_encode(['success' => false, 'message' => 'Post not found']);
        exit;
    }
    
    // Check if user is trying to like their own post
    if ($post['user_id'] == $_SESSION['user_id']) {
        echo json_encode(['success' => false, 'message' => 'You cannot like your own post']);
        exit;
    }
    
    // Check if user has already liked this post
    $stmt = $pdo->prepare("SELECT id FROM likes WHERE user_id = ? AND post_id = ? AND is_active = 1");
    $stmt->execute([$_SESSION['user_id'], $post_id]);
    $existing_like = $stmt->fetch();
    
    if ($existing_like) {
        // Unlike the post
        $stmt = $pdo->prepare("UPDATE likes SET is_active = 0, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$existing_like['id']]);
        
        // Update post like count
        $stmt = $pdo->prepare("UPDATE posts SET like_count = GREATEST(0, like_count - 1) WHERE id = ?");
        $stmt->execute([$post_id]);
        
        // Update user received likes count
        $stmt = $pdo->prepare("UPDATE users SET like_count = GREATEST(0, like_count - 1) WHERE id = ?");
        $stmt->execute([$post['user_id']]);
        
        echo json_encode([
            'success' => true, 
            'liked' => false, 
            'message' => 'Post unliked successfully'
        ]);
    } else {
        // Like the post
        $stmt = $pdo->prepare("INSERT INTO likes (user_id, post_id, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
        $stmt->execute([$_SESSION['user_id'], $post_id]);
        
        // Update post like count
        $stmt = $pdo->prepare("UPDATE posts SET like_count = like_count + 1 WHERE id = ?");
        $stmt->execute([$post_id]);
        
        // Update user received likes count
        $stmt = $pdo->prepare("UPDATE users SET like_count = like_count + 1 WHERE id = ?");
        $stmt->execute([$post['user_id']]);
        
        // Create notification for post owner
        $stmt = $pdo->prepare("
            INSERT INTO notifications (user_id, type, reference_id, reference_type, message, created_at)
            VALUES (?, 'like', ?, 'post', ?, NOW())
        ");
        $stmt->execute([
            $post['user_id'], 
            $post_id, 
            $_SESSION['username'] . ' liked your post'
        ]);
        
        echo json_encode([
            'success' => true, 
            'liked' => true, 
            'message' => 'Post liked successfully'
        ]);
    }
    
} catch (Exception $e) {
    error_log('Like toggle error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to update like status']);
}
?>
