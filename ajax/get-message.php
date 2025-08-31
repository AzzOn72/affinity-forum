<?php
require_once '../config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

// Check CSRF token
if (!validateCSRFToken($_GET['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid token']);
    exit;
}

$pdo = getDBConnection();
$user_id = $_SESSION['user_id'];
$message_id = intval($_GET['message_id'] ?? 0);
$type = $_GET['type'] ?? 'inbox';

if (!$message_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid message ID']);
    exit;
}

// Get message based on type
if ($type === 'inbox') {
    $stmt = $pdo->prepare("
        SELECT pm.*, u.username as sender_username, u.avatar as sender_avatar
        FROM private_messages pm
        JOIN users u ON pm.sender_id = u.id
        WHERE pm.id = ? AND pm.recipient_id = ? AND pm.is_deleted_recipient = 0
    ");
    $stmt->execute([$message_id, $user_id]);
} else {
    $stmt = $pdo->prepare("
        SELECT pm.*, u.username as recipient_username, u.avatar as recipient_avatar
        FROM private_messages pm
        JOIN users u ON pm.recipient_id = u.id
        WHERE pm.id = ? AND pm.sender_id = ? AND pm.is_deleted_sender = 0
    ");
    $stmt->execute([$message_id, $user_id]);
}

$message = $stmt->fetch();

if (!$message) {
    echo json_encode(['success' => false, 'message' => 'Message not found']);
    exit;
}

// Mark as read if it's an inbox message
if ($type === 'inbox' && !$message['is_read']) {
    $stmt = $pdo->prepare("UPDATE private_messages SET is_read = 1 WHERE id = ? AND recipient_id = ?");
    $stmt->execute([$message_id, $user_id]);
}

// Format the message for display
$html = '<div class="message-content">';
$html .= '<div class="message-header mb-3">';
$html .= '<h5>' . htmlspecialchars($message['subject']) . '</h5>';

if ($type === 'inbox') {
    $html .= '<p class="text-muted">From: <strong>' . htmlspecialchars($message['sender_username']) . '</strong></p>';
} else {
    $html .= '<p class="text-muted">To: <strong>' . htmlspecialchars($message['recipient_username']) . '</strong></p>';
}

$html .= '<p class="text-muted">Date: ' . formatDate($message['created_at']) . '</p>';
$html .= '</div>';

$html .= '<div class="message-body">';
$html .= '<p>' . nl2br(htmlspecialchars($message['message'])) . '</p>';
$html .= '</div>';
$html .= '</div>';

echo json_encode([
    'success' => true,
    'data' => [
        'subject' => $message['subject'],
        'html' => $html,
        'sender_id' => $message['sender_id']
    ]
]);
?>
