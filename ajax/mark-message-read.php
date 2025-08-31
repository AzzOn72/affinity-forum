<?php
require_once '../config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

// Check CSRF token
if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid token']);
    exit;
}

$pdo = getDBConnection();
$user_id = $_SESSION['user_id'];
$message_id = intval($_POST['message_id'] ?? 0);

if (!$message_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid message ID']);
    exit;
}

// Mark message as read
$stmt = $pdo->prepare("UPDATE private_messages SET is_read = 1 WHERE id = ? AND recipient_id = ?");
if ($stmt->execute([$message_id, $user_id])) {
    echo json_encode(['success' => true, 'message' => 'Message marked as read']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to mark message as read']);
}
?>
