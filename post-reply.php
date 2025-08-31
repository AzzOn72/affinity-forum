<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

$pdo = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
    redirect('index.php?error=invalid_token');
}

$thread_id = intval($_POST['thread_id'] ?? 0);
$content = sanitizeInput($_POST['content'] ?? '');
$quote_post_id = intval($_POST['quote_post_id'] ?? 0);
$subscribe = isset($_POST['subscribe']);

if (!$thread_id || empty($content)) {
    redirect('index.php?error=invalid_input');
}

// Get thread information
$stmt = $pdo->prepare("
    SELECT t.*, sf.slug as subforum_slug, sf.name as subforum_name
    FROM threads t
    JOIN subforums sf ON t.subforum_id = sf.id
    WHERE t.id = ? AND t.is_active = 1
");
$stmt->execute([$thread_id]);
$thread = $stmt->fetch();

if (!$thread) {
    redirect('index.php?error=thread_not_found');
}

// Check if thread is locked
if ($thread['is_locked']) {
    redirect('thread.php?id=' . $thread_id . '&error=thread_locked');
}

// Check if user is banned
$stmt = $pdo->prepare("SELECT is_banned FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if ($user['is_banned']) {
    redirect('index.php?error=user_banned');
}

// Validate content length
if (strlen($content) < 2) {
    redirect('thread.php?id=' . $thread_id . '&error=content_too_short');
}

if (strlen($content) > 700) {
    redirect('thread.php?id=' . $thread_id . '&error=content_too_long');
}

try {
    $pdo->beginTransaction();
    
    // Insert post
    $stmt = $pdo->prepare("
        INSERT INTO posts (content, user_id, thread_id, quote_post_id, created_at, updated_at)
        VALUES (?, ?, ?, ?, NOW(), NOW())
    ");
    $stmt->execute([$content, $_SESSION['user_id'], $thread_id, $quote_post_id ?: null]);
    $post_id = $pdo->lastInsertId();
    
    // Handle file attachments
    $attachments = [];
    if (!empty($_FILES['attachments']['name'][0])) {
        $upload_dir = 'uploads/attachments/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        foreach ($_FILES['attachments']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['attachments']['error'][$key] === UPLOAD_ERR_OK) {
                $filename = $_FILES['attachments']['name'][$key];
                $filesize = $_FILES['attachments']['size'][$key];
                $filetype = $_FILES['attachments']['type'][$key];
                
                // Validate file
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'text/plain', 'application/zip'];
                $max_size = 5 * 1024 * 1024; // 5MB
                
                if (in_array($filetype, $allowed_types) && $filesize <= $max_size) {
                    $unique_filename = uniqid() . '_' . $filename;
                    $filepath = $upload_dir . $unique_filename;
                    
                    if (move_uploaded_file($tmp_name, $filepath)) {
                        // Insert attachment record
                        $stmt = $pdo->prepare("
                            INSERT INTO post_attachments (post_id, filename, original_name, file_size, file_type, file_path)
                            VALUES (?, ?, ?, ?, ?, ?)
                        ");
                        $stmt->execute([$post_id, $unique_filename, $filename, $filesize, $filetype, $filepath]);
                        
                        $attachments[] = [
                            'name' => $filename,
                            'size' => $filesize,
                            'type' => $filetype
                        ];
                    }
                }
            }
        }
    }
    
    // Update thread stats
    $stmt = $pdo->prepare("
        UPDATE threads 
        SET reply_count = reply_count + 1, 
            last_post_date = NOW(), 
            last_poster_id = ?,
            updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$_SESSION['user_id'], $thread_id]);
    
    // Update subforum stats
    $stmt = $pdo->prepare("
        UPDATE subforums SET post_count = post_count + 1 WHERE id = ?
    ");
    $stmt->execute([$thread['subforum_id']]);
    
    // Update user post count
    $stmt = $pdo->prepare("
        UPDATE users SET post_count = post_count + 1 WHERE id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    
    // Subscribe to thread if requested
    if ($subscribe) {
        // Check if already subscribed
        $stmt = $pdo->prepare("
            SELECT id FROM thread_subscriptions WHERE user_id = ? AND thread_id = ?
        ");
        $stmt->execute([$_SESSION['user_id'], $thread_id]);
        
        if (!$stmt->fetch()) {
            $stmt = $pdo->prepare("
                INSERT INTO thread_subscriptions (user_id, thread_id, created_at) VALUES (?, ?, NOW())
            ");
            $stmt->execute([$_SESSION['user_id'], $thread_id]);
        }
    }
    
    // Handle mentions (@username)
    $mentions = [];
    preg_match_all('/@(\w+)/', $content, $matches);
    if (!empty($matches[1])) {
        $usernames = array_unique($matches[1]);
        $placeholders = str_repeat('?,', count($usernames) - 1) . '?';
        
        $stmt = $pdo->prepare("
            SELECT id, username FROM users WHERE username IN ($placeholders) AND id != ?
        ");
        $params = array_merge($usernames, [$_SESSION['user_id']]);
        $stmt->execute($params);
        $mentioned_users = $stmt->fetchAll();
        
        foreach ($mentioned_users as $user) {
            $mentions[] = $user['username'];
            
            // Create notification for mentioned user
            $stmt = $pdo->prepare("
                INSERT INTO notifications (user_id, type, reference_id, reference_type, message, created_at)
                VALUES (?, 'mention', ?, 'post', ?, NOW())
            ");
            $stmt->execute([
                $user['id'], 
                $post_id, 
                $_SESSION['username'] . ' mentioned you in a post'
            ]);
        }
    }
    
    // Create notifications for thread subscribers
    $stmt = $pdo->prepare("
        SELECT user_id FROM thread_subscriptions 
        WHERE thread_id = ? AND user_id != ?
    ");
    $stmt->execute([$thread_id, $_SESSION['user_id']]);
    $subscribers = $stmt->fetchAll();
    
    foreach ($subscribers as $subscriber) {
        // Check if user wants email notifications
        $stmt = $pdo->prepare("
            SELECT email_notifications FROM users WHERE id = ?
        ");
        $stmt->execute([$subscriber['user_id']]);
        $user_prefs = $stmt->fetch();
        
        if ($user_prefs['email_notifications']) {
            // Create notification
            $stmt = $pdo->prepare("
                INSERT INTO notifications (user_id, type, reference_id, reference_type, message, created_at)
                VALUES (?, 'reply', ?, 'thread', ?, NOW())
            ");
            $stmt->execute([
                $subscriber['user_id'], 
                $thread_id, 
                $_SESSION['username'] . ' replied to a thread you\'re subscribed to'
            ]);
        }
    }
    
    // Update thread tags if this is a significant post
    if (strlen($content) > 100) {
        // Extract potential new tags from content
        preg_match_all('/#(\w+)/', $content, $tag_matches);
        if (!empty($tag_matches[1])) {
            $new_tags = array_unique($tag_matches[1]);
            
            foreach ($new_tags as $tag) {
                if (strlen($tag) <= 50) {
                    // Check if tag already exists for this thread
                    $stmt = $pdo->prepare("
                        SELECT id FROM thread_tags WHERE thread_id = ? AND tag = ?
                    ");
                    $stmt->execute([$thread_id, $tag]);
                    
                    if (!$stmt->fetch()) {
                        $stmt = $pdo->prepare("
                            INSERT INTO thread_tags (thread_id, tag) VALUES (?, ?)
                        ");
                        $stmt->execute([$thread_id, $tag]);
                    }
                }
            }
        }
    }
    
    // Log activity
    $stmt = $pdo->prepare("
        INSERT INTO user_activity (user_id, action, reference_id, reference_type, details, created_at)
        VALUES (?, 'post_reply', ?, 'thread', ?, NOW())
    ");
    $stmt->execute([
        $_SESSION['user_id'], 
        $thread_id, 
        'Replied to thread: ' . $thread['title']
    ]);
    
    $pdo->commit();
    
    // Redirect to the new post
    redirect('thread.php?id=' . $thread_id . '&post=' . $post_id . '&created=1');
    
} catch (Exception $e) {
    $pdo->rollBack();
    error_log('Post reply error: ' . $e->getMessage());
    redirect('thread.php?id=' . $thread_id . '&error=post_failed');
}
?>
