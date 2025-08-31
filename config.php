<?php
/**
 * Affinity Forum - Configuration File
 * Professional Counter-Strike 2 Community Forum
 */

// Include error handler first
require_once __DIR__ . '/error-handler-simple.php';

// Security constant
define('SECURE_ACCESS', true);

// Enable error reporting for development (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session with enhanced security
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');
session_start();

// Security constants
define('CSRF_TOKEN_NAME', 'affinity_csrf_token');
define('SESSION_TIMEOUT', 3600); // 1 hour
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_TIMEOUT', 900); // 15 minutes
define('PASSWORD_MIN_LENGTH', 8);
define('USERNAME_MIN_LENGTH', 3);
define('USERNAME_MAX_LENGTH', 25);
define('CONTENT_MAX_LENGTH', 700);
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('MAX_ATTACHMENTS_PER_POST', 3);

// Database configuration
define('DB_HOST', 'sql112.infinityfree.com');
define('DB_USER', 'if0_39775345');
define('DB_PASS', 'Mateut2018');
define('DB_NAME', 'if0_39775345_database');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', 'utf8mb4_unicode_ci');

// Site configuration
define('SITE_NAME', 'Affinity');
define('SITE_DESCRIPTION', 'Premium Counter-Strike 2 Community');
define('SITE_URL', 'https://yourdomain.com'); // Update with your domain
define('ADMIN_EMAIL', 'admin@affinity.com');
define('VERSION', '2.0.0');

// Feature flags
define('ENABLE_NOTIFICATIONS', true);
define('ENABLE_REACTIONS', true);
define('ENABLE_BADGES', true);
define('ENABLE_ACHIEVEMENTS', true);
define('ENABLE_LEVELS', true);
define('ENABLE_POINTS', true);
define('ENABLE_SHOP', true);
define('ENABLE_TOURNAMENTS', true);
define('ENABLE_STREAMING', true);
define('ENABLE_DISCORD_INTEGRATION', true);

// Cache configuration
define('CACHE_ENABLED', true);
define('CACHE_DURATION', 300); // 5 minutes

// Rate limiting
define('RATE_LIMIT_ENABLED', true);
define('RATE_LIMIT_POSTS', 10); // posts per minute
define('RATE_LIMIT_SEARCH', 20); // searches per minute
define('RATE_LIMIT_LOGIN', 5); // login attempts per minute

// Advanced security features
define('ENABLE_2FA', true);
define('ENABLE_EMAIL_VERIFICATION', true);
define('ENABLE_ACCOUNT_LOCKOUT', true);
define('ENABLE_IP_WHITELIST', false);
define('ENABLE_CONTENT_MODERATION', true);
define('ENABLE_SPAM_PROTECTION', true);

// Database connection with enhanced error handling
function getDBConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET . " COLLATE " . DB_COLLATE,
                PDO::ATTR_PERSISTENT => false,
                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
            ];
            
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            
            // Set timezone
            $pdo->exec("SET time_zone = '+00:00'");
            
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            http_response_code(500);
            die('Database connection failed. Please try again later.');
        }
    }
    
    return $pdo;
}

// Enhanced input sanitization
function sanitizeInput($input, $type = 'string') {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    
    $input = trim($input);
    
    switch ($type) {
        case 'email':
            return filter_var($input, FILTER_SANITIZE_EMAIL);
        case 'url':
            return filter_var($input, FILTER_SANITIZE_URL);
        case 'int':
            return intval($input);
        case 'float':
            return floatval($input);
        case 'html':
            return strip_tags($input, '<p><br><strong><em><u><ol><ul><li><code><pre><blockquote>');
        case 'username':
            return preg_replace('/[^a-zA-Z0-9_-]/', '', $input);
        case 'filename':
            return preg_replace('/[^a-zA-Z0-9._-]/', '', $input);
        default:
            return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }
}

// Enhanced CSRF protection
function generateCSRFToken() {
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

function validateCSRFToken($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

// Rate limiting
function checkRateLimit($action, $limit, $timeout = 60) {
    if (!RATE_LIMIT_ENABLED) return true;
    
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $key = "rate_limit_{$action}_{$ip}";
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 0, 'reset_time' => time() + $timeout];
    }
    
    if (time() > $_SESSION[$key]['reset_time']) {
        $_SESSION[$key] = ['count' => 0, 'reset_time' => time() + $timeout];
    }
    
    if ($_SESSION[$key]['count'] >= $limit) {
        return false;
    }
    
    $_SESSION[$key]['count']++;
    return true;
}

// Enhanced password validation
function validatePassword($password) {
    $errors = [];
    
    if (strlen($password) < PASSWORD_MIN_LENGTH) {
        $errors[] = "Password must be at least " . PASSWORD_MIN_LENGTH . " characters long";
    }
    
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password must contain at least one uppercase letter";
    }
    
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Password must contain at least one lowercase letter";
    }
    
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least one number";
    }
    
    if (!preg_match('/[^A-Za-z0-9]/', $password)) {
        $errors[] = "Password must contain at least one special character";
    }
    
    return $errors;
}

// Enhanced username validation
function validateUsername($username) {
    $errors = [];
    
    if (strlen($username) < USERNAME_MIN_LENGTH) {
        $errors[] = "Username must be at least " . USERNAME_MIN_LENGTH . " characters long";
    }
    
    if (strlen($username) > USERNAME_MAX_LENGTH) {
        $errors[] = "Username cannot exceed " . USERNAME_MAX_LENGTH . " characters";
    }
    
    if (!preg_match('/^[a-zA-Z]+$/', $username)) {
        $errors[] = "Username can only contain letters";
    }
    
    // Check for reserved usernames
    $reserved = ['admin', 'administrator', 'mod', 'moderator', 'staff', 'support', 'help', 'info', 'test', 'guest', 'anonymous'];
    if (in_array(strtolower($username), $reserved)) {
        $errors[] = "This username is reserved and cannot be used";
    }
    
    return $errors;
}

// Enhanced content validation
function validateContent($content, $type = 'post') {
    $errors = [];
    
    if (empty(trim($content))) {
        $errors[] = "Content cannot be empty";
    }
    
    if (strlen($content) > CONTENT_MAX_LENGTH) {
        $errors[] = "Content cannot exceed " . CONTENT_MAX_LENGTH . " characters";
    }
    
    // Check for spam patterns
    $spam_patterns = [
        '/\b(buy\s+now|click\s+here|free\s+offer|limited\s+time|act\s+now)\b/i',
        '/\b(viagra|cialis|casino|poker|loan|debt|credit)\b/i',
        '/\b(www\.|http:\/\/|https:\/\/)\S+/i',
        '/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}\b/',
    ];
    
    foreach ($spam_patterns as $pattern) {
        if (preg_match($pattern, $content)) {
            $errors[] = "Content contains prohibited patterns";
            break;
        }
    }
    
    return $errors;
}

// Enhanced time formatting
function formatTimeAgo($date) {
    if (!$date) {
        return 'Never';
    }
    
    $time = time() - strtotime($date);
    
    if ($time < 60) {
        return 'Just now';
    } elseif ($time < 3600) {
        $minutes = floor($time / 60);
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
    } elseif ($time < 86400) {
        $hours = floor($time / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($time < 604800) {
        $days = floor($time / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } elseif ($time < 2592000) {
        $weeks = floor($time / 604800);
        return $weeks . ' week' . ($weeks > 1 ? 's' : '') . ' ago';
    } elseif ($time < 31536000) {
        $months = floor($time / 2592000);
        return $months . ' month' . ($months > 1 ? 's' : '') . ' ago';
    } else {
        $years = floor($time / 31536000);
        return $years . ' year' . ($years > 1 ? 's' : '') . ' ago';
    }
}

// Enhanced file upload handling
function handleFileUpload($file, $allowedTypes = null, $maxSize = null) {
    if ($allowedTypes === null) $allowedTypes = ALLOWED_IMAGE_TYPES;
    if ($maxSize === null) $maxSize = MAX_FILE_SIZE;
    
    $errors = [];
    
    if (!isset($file['error']) || is_array($file['error'])) {
        $errors[] = 'Invalid file parameter';
        return ['success' => false, 'errors' => $errors];
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        switch ($file['error']) {
            case UPLOAD_ERR_INI_SIZE:
                $errors[] = 'File exceeds server upload limit';
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $errors[] = 'File exceeds form upload limit';
                break;
            case UPLOAD_ERR_PARTIAL:
                $errors[] = 'File was only partially uploaded';
                break;
            case UPLOAD_ERR_NO_FILE:
                $errors[] = 'No file was uploaded';
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $errors[] = 'Missing temporary folder';
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $errors[] = 'Failed to write file to disk';
                break;
            case UPLOAD_ERR_EXTENSION:
                $errors[] = 'File upload stopped by extension';
                break;
            default:
                $errors[] = 'Unknown upload error';
        }
        return ['success' => false, 'errors' => $errors];
    }
    
    if ($file['size'] > $maxSize) {
        $errors[] = 'File size exceeds limit of ' . formatFileSize($maxSize);
    }
    
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($fileExtension, $allowedTypes)) {
        $errors[] = 'File type not allowed. Allowed types: ' . implode(', ', $allowedTypes);
    }
    
    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }
    
    // Generate unique filename
    $filename = uniqid() . '_' . time() . '.' . $fileExtension;
    
    return ['success' => true, 'filename' => $filename, 'extension' => $fileExtension];
}

// File size formatting
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    
    $bytes /= pow(1024, $pow);
    
    return round($bytes, 2) . ' ' . $units[$pow];
}

// Enhanced user authentication
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function isAdmin() {
    return isLoggedIn() && isset($_SESSION['user_rank']) && $_SESSION['user_rank'] === 'admin';
}

function isModerator() {
    return isLoggedIn() && isset($_SESSION['user_rank']) && in_array($_SESSION['user_rank'], ['admin', 'moderator']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: login.php');
        exit;
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        http_response_code(403);
        die('Access denied. Admin privileges required.');
    }
}

function requireModerator() {
    requireLogin();
    if (!isModerator()) {
        http_response_code(403);
        die('Access denied. Moderator privileges required.');
    }
}

// Enhanced session management
function regenerateSession() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }
}

function destroySession() {
    $_SESSION = array();
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
}

// Enhanced logging
function logActivity($user_id, $action, $details = '', $ip = null) {
    if ($ip === null) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
    
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            INSERT INTO user_activity (user_id, action, details, ip_address, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$user_id, $action, $details, $ip]);
    } catch (Exception $e) {
        error_log("Failed to log activity: " . $e->getMessage());
    }
}

function logSystemEvent($event, $details = '', $level = 'info') {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            INSERT INTO system_logs (event, details, level, ip_address, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$event, $details, $_SERVER['REMOTE_ADDR'] ?? 'unknown', $level]);
    } catch (Exception $e) {
        error_log("Failed to log system event: " . $e->getMessage());
    }
}

// Enhanced notification system
function sendNotification($user_id, $type, $title, $message, $data = []) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            INSERT INTO notifications (user_id, type, title, message, data, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$user_id, $type, $title, $message, json_encode($data)]);
        
        // Update unread count
        $stmt = $pdo->prepare("
            UPDATE users SET unread_notifications = unread_notifications + 1 WHERE id = ?
        ");
        $stmt->execute([$user_id]);
        
        return true;
    } catch (Exception $e) {
        error_log("Failed to send notification: " . $e->getMessage());
        return false;
    }
}

// Enhanced search functionality
function performSearch($query, $type = 'all', $filters = [], $page = 1, $per_page = 20) {
    try {
        $pdo = getDBConnection();
        
        // Log search
        if (isLoggedIn()) {
            logActivity($_SESSION['user_id'], 'search', $query);
        }
        
        $conditions = [];
        $params = [];
        
        if (!empty($query)) {
            $conditions[] = "(t.title LIKE ? OR t.content LIKE ? OR p.content LIKE ? OR u.username LIKE ?)";
            $search_term = "%{$query}%";
            $params = array_merge($params, [$search_term, $search_term, $search_term, $search_term]);
        }
        
        // Apply filters
        if (!empty($filters['category'])) {
            $conditions[] = "c.id = ?";
            $params[] = $filters['category'];
        }
        
        if (!empty($filters['subforum'])) {
            $conditions[] = "sf.id = ?";
            $params[] = $filters['subforum'];
        }
        
        if (!empty($filters['author'])) {
            $conditions[] = "u.username LIKE ?";
            $params[] = "%{$filters['author']}%";
        }
        
        if (!empty($filters['date_from'])) {
            $conditions[] = "t.created_at >= ?";
            $params[] = $filters['date_from'] . ' 00:00:00';
        }
        
        if (!empty($filters['date_to'])) {
            $conditions[] = "t.created_at <= ?";
            $params[] = $filters['date_to'] . ' 23:59:59';
        }
        
        $where_clause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
        
        // Build search query
        $sql = "
            SELECT 
                'thread' as type,
                t.id,
                t.title,
                t.content,
                t.created_at,
                t.views,
                t.replies,
                t.is_pinned,
                t.is_locked,
                u.username,
                u.avatar,
                u.rank,
                sf.name as subforum_name,
                c.name as category_name
            FROM threads t
            JOIN users u ON t.user_id = u.id
            JOIN subforums sf ON t.subforum_id = sf.id
            JOIN categories c ON sf.category_id = c.id
            {$where_clause}
            ORDER BY 
                t.is_pinned DESC,
                t.created_at DESC
            LIMIT ? OFFSET ?
        ";
        
        $offset = ($page - 1) * $per_page;
        $params[] = $per_page;
        $params[] = $offset;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll();
        
        // Get total count
        $count_sql = "
            SELECT COUNT(*) as total
            FROM threads t
            JOIN users u ON t.user_id = u.id
            JOIN subforums sf ON t.subforum_id = sf.id
            JOIN categories c ON sf.category_id = c.id
            {$where_clause}
        ";
        
        $stmt = $pdo->prepare($count_sql);
        $stmt->execute(array_slice($params, 0, -2));
        $total = $stmt->fetch()['total'];
        
        return [
            'results' => $results,
            'total' => $total,
            'pages' => ceil($total / $per_page),
            'current_page' => $page
        ];
        
    } catch (Exception $e) {
        error_log("Search failed: " . $e->getMessage());
        return ['results' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
    }
}

// Enhanced user management
function getUserById($user_id) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            SELECT u.*, 
                   COUNT(DISTINCT t.id) as thread_count,
                   COUNT(DISTINCT p.id) as post_count,
                   COUNT(DISTINCT l.id) as given_likes,
                   (SELECT COUNT(*) FROM likes WHERE target_type = 'post' AND target_id IN (SELECT id FROM posts WHERE user_id = u.id)) as received_likes
            FROM users u
            LEFT JOIN threads t ON u.id = t.user_id
            LEFT JOIN posts p ON u.id = p.user_id
            LEFT JOIN likes l ON u.id = l.user_id AND l.target_type = 'post'
            WHERE u.id = ? AND u.is_active = 1
            GROUP BY u.id
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetch();
    } catch (Exception $e) {
        error_log("Failed to get user: " . $e->getMessage());
        return false;
    }
}

function getUserByUsername($username) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND is_active = 1");
        $stmt->execute([$username]);
        return $stmt->fetch();
    } catch (Exception $e) {
        error_log("Failed to get user by username: " . $e->getMessage());
        return false;
    }
}

function updateUserProfile($user_id, $data) {
    try {
        $pdo = getDBConnection();
        
        $allowed_fields = ['bio', 'location', 'website', 'signature', 'avatar'];
        $updates = [];
        $params = [];
        
        foreach ($allowed_fields as $field) {
            if (isset($data[$field])) {
                $updates[] = "{$field} = ?";
                $params[] = $data[$field];
            }
        }
        
        if (empty($updates)) {
            return false;
        }
        
        $params[] = $user_id;
        $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
        
    } catch (Exception $e) {
        error_log("Failed to update user profile: " . $e->getMessage());
        return false;
    }
}

// Enhanced forum management
function getCategories() {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            SELECT c.*, 
                   COUNT(DISTINCT sf.id) as subforum_count,
                   COUNT(DISTINCT t.id) as thread_count,
                   COUNT(DISTINCT p.id) as post_count
            FROM categories c
            LEFT JOIN subforums sf ON c.id = sf.category_id
            LEFT JOIN threads t ON sf.id = t.subforum_id
            LEFT JOIN posts p ON t.id = p.thread_id
            WHERE c.is_active = 1
            GROUP BY c.id
            ORDER BY c.order_index, c.name
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Failed to get categories: " . $e->getMessage());
        return [];
    }
}

function getSubforums($category_id = null) {
    try {
        $pdo = getDBConnection();
        
        $where_clause = $category_id ? "WHERE sf.category_id = ? AND sf.is_active = 1" : "WHERE sf.is_active = 1";
        $params = $category_id ? [$category_id] : [];
        
        $sql = "
            SELECT sf.*, 
                   c.name as category_name,
                   COUNT(DISTINCT t.id) as thread_count,
                   COUNT(DISTINCT p.id) as post_count,
                   (SELECT p.created_at FROM posts p 
                    JOIN threads t ON p.thread_id = t.id 
                    WHERE t.subforum_id = sf.id 
                    ORDER BY p.created_at DESC LIMIT 1) as last_post_date
            FROM subforums sf
            JOIN categories c ON sf.category_id = c.id
            LEFT JOIN threads t ON sf.id = t.subforum_id
            LEFT JOIN posts p ON t.id = p.thread_id
            {$where_clause}
            GROUP BY sf.id
            ORDER BY sf.order_index, sf.name
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
        
    } catch (Exception $e) {
        error_log("Failed to get subforums: " . $e->getMessage());
        return [];
    }
}

// Enhanced thread management
function getThread($thread_id) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            SELECT t.*, 
                   u.username, u.avatar, u.rank, u.created_at as user_created,
                   sf.name as subforum_name, sf.id as subforum_id,
                   c.name as category_name, c.id as category_id
            FROM threads t
            JOIN users u ON t.user_id = u.id
            JOIN subforums sf ON t.subforum_id = sf.id
            JOIN categories c ON sf.category_id = c.id
            WHERE t.id = ? AND t.is_active = 1
        ");
        $stmt->execute([$thread_id]);
        return $stmt->fetch();
    } catch (Exception $e) {
        error_log("Failed to get thread: " . $e->getMessage());
        return false;
    }
}

function getThreadPosts($thread_id, $page = 1, $per_page = 20) {
    try {
        $pdo = getDBConnection();
        
        $offset = ($page - 1) * $per_page;
        
        $sql = "
            SELECT p.*, 
                   u.username, u.avatar, u.rank, u.created_at as user_created,
                   u.post_count, u.thread_count,
                   (SELECT COUNT(*) FROM likes WHERE target_type = 'post' AND target_id = p.id) as like_count,
                   (SELECT COUNT(*) FROM likes WHERE target_type = 'post' AND target_id = p.id AND user_id = ?) as user_liked
            FROM posts p
            JOIN users u ON p.user_id = u.id
            WHERE p.thread_id = ? AND p.is_active = 1
            ORDER BY p.created_at ASC
            LIMIT ? OFFSET ?
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_SESSION['user_id'] ?? 0, $thread_id, $per_page, $offset]);
        $posts = $stmt->fetchAll();
        
        // Get total count
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM posts WHERE thread_id = ? AND is_active = 1");
        $stmt->execute([$thread_id]);
        $total = $stmt->fetch()['total'];
        
        return [
            'posts' => $posts,
            'total' => $total,
            'pages' => ceil($total / $per_page),
            'current_page' => $page
        ];
        
    } catch (Exception $e) {
        error_log("Failed to get thread posts: " . $e->getMessage());
        return ['posts' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
    }
}

// Enhanced like system
function toggleLike($user_id, $target_type, $target_id) {
    try {
        $pdo = getDBConnection();
        
        // Check if already liked
        $stmt = $pdo->prepare("
            SELECT id FROM likes 
            WHERE user_id = ? AND target_type = ? AND target_id = ?
        ");
        $stmt->execute([$user_id, $target_type, $target_id]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            // Unlike
            $stmt = $pdo->prepare("
                DELETE FROM likes 
                WHERE user_id = ? AND target_type = ? AND target_id = ?
            ");
            $stmt->execute([$user_id, $target_type, $target_id]);
            
            // Update target like count
            if ($target_type === 'post') {
                $stmt = $pdo->prepare("UPDATE posts SET like_count = like_count - 1 WHERE id = ?");
                $stmt->execute([$target_id]);
            } elseif ($target_type === 'thread') {
                $stmt = $pdo->prepare("UPDATE threads SET like_count = like_count - 1 WHERE id = ?");
                $stmt->execute([$target_id]);
            }
            
            return ['action' => 'unliked', 'liked' => false];
        } else {
            // Like
            $stmt = $pdo->prepare("
                INSERT INTO likes (user_id, target_type, target_id, created_at)
                VALUES (?, ?, ?, NOW())
            ");
            $stmt->execute([$user_id, $target_type, $target_id]);
            
            // Update target like count
            if ($target_type === 'post') {
                $stmt = $pdo->prepare("UPDATE posts SET like_count = like_count + 1 WHERE id = ?");
                $stmt->execute([$target_id]);
            } elseif ($target_type === 'thread') {
                $stmt = $pdo->prepare("UPDATE threads SET like_count = like_count + 1 WHERE id = ?");
                $stmt->execute([$target_id]);
            }
            
            return ['action' => 'liked', 'liked' => true];
        }
        
    } catch (Exception $e) {
        error_log("Failed to toggle like: " . $e->getMessage());
        return false;
    }
}

// Enhanced notification system
function getUnreadNotifications($user_id) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            SELECT * FROM notifications 
            WHERE user_id = ? AND is_read = 0 
            ORDER BY created_at DESC 
            LIMIT 50
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Failed to get notifications: " . $e->getMessage());
        return [];
    }
}

function markNotificationAsRead($notification_id, $user_id) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            UPDATE notifications 
            SET is_read = 1, read_at = NOW() 
            WHERE id = ? AND user_id = ?
        ");
        return $stmt->execute([$notification_id, $user_id]);
    } catch (Exception $e) {
        error_log("Failed to mark notification as read: " . $e->getMessage());
        return false;
    }
}

// Enhanced security functions
function generateSecureToken($length = 32) {
    return bin2hex(random_bytes($length));
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_ARGON2ID, [
        'memory_cost' => 65536,
        'time_cost' => 4,
        'threads' => 3
    ]);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

function generateRememberMeToken() {
    return bin2hex(random_bytes(32));
}

function validateRememberMeToken($token) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            SELECT user_id, expires_at 
            FROM user_sessions 
            WHERE token = ? AND expires_at > NOW()
        ");
        $stmt->execute([$token]);
        return $stmt->fetch();
    } catch (Exception $e) {
        error_log("Failed to validate remember me token: " . $e->getMessage());
        return false;
    }
}

// Enhanced file management
function uploadFile($file, $directory = 'uploads', $allowedTypes = null) {
    if ($allowedTypes === null) {
        $allowedTypes = ALLOWED_IMAGE_TYPES;
    }
    
    $result = handleFileUpload($file, $allowedTypes);
    
    if (!$result['success']) {
        return $result;
    }
    
    $upload_dir = $directory . '/' . date('Y/m');
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $filepath = $upload_dir . '/' . $result['filename'];
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return [
            'success' => true,
            'filename' => $result['filename'],
            'filepath' => $filepath,
            'url' => $filepath
        ];
    } else {
        return [
            'success' => false,
            'errors' => ['Failed to move uploaded file']
        ];
    }
}

// Enhanced statistics
function getForumStats() {
    try {
        $pdo = getDBConnection();
        
        $stats = [];
        
        // Basic counts
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM users WHERE is_active = 1");
        $stmt->execute();
        $stats['total_users'] = $stmt->fetch()['total'];
        
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM threads WHERE is_active = 1");
        $stmt->execute();
        $stats['total_threads'] = $stmt->fetch()['total'];
        
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM posts WHERE is_active = 1");
        $stmt->execute();
        $stats['total_posts'] = $stmt->fetch()['total'];
        
        // Online users
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total 
            FROM users 
            WHERE last_seen > DATE_SUB(NOW(), INTERVAL 15 MINUTE)
        ");
        $stmt->execute();
        $stats['online_users'] = $stmt->fetch()['total'];
        
        // Today's activity
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total 
            FROM posts 
            WHERE DATE(created_at) = CURDATE()
        ");
        $stmt->execute();
        $stats['today_posts'] = $stmt->fetch()['total'];
        
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total 
            FROM threads 
            WHERE DATE(created_at) = CURDATE()
        ");
        $stmt->execute();
        $stats['today_threads'] = $stmt->fetch()['total'];
        
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total 
            FROM users 
            WHERE DATE(created_at) = CURDATE()
        ");
        $stmt->execute();
        $stats['today_users'] = $stmt->fetch()['total'];
        
        return $stats;
        
    } catch (Exception $e) {
        error_log("Failed to get forum stats: " . $e->getMessage());
        return [
            'total_users' => 0,
            'total_threads' => 0,
            'total_posts' => 0,
            'online_users' => 0,
            'today_posts' => 0,
            'today_threads' => 0,
            'today_users' => 0
        ];
    }
}

// Enhanced caching
function getCache($key) {
    if (!CACHE_ENABLED) return false;
    
    $cache_file = "cache/{$key}.cache";
    
    if (file_exists($cache_file) && (time() - filemtime($cache_file)) < CACHE_DURATION) {
        return unserialize(file_get_contents($cache_file));
    }
    
    return false;
}

function setCache($key, $data) {
    if (!CACHE_ENABLED) return false;
    
    $cache_dir = "cache";
    if (!is_dir($cache_dir)) {
        mkdir($cache_dir, 0755, true);
    }
    
    $cache_file = "{$cache_dir}/{$key}.cache";
    return file_put_contents($cache_file, serialize($data));
}

function clearCache($pattern = null) {
    if (!CACHE_ENABLED) return false;
    
    $cache_dir = "cache";
    if (!is_dir($cache_dir)) return false;
    
    if ($pattern === null) {
        $files = glob("{$cache_dir}/*.cache");
    } else {
        $files = glob("{$cache_dir}/{$pattern}.cache");
    }
    
    foreach ($files as $file) {
        unlink($file);
    }
    
    return true;
}

// Enhanced validation
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validateURL($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

function validateIP($ip) {
    return filter_var($ip, FILTER_VALIDATE_IP) !== false;
}

// Enhanced formatting
function formatNumber($number) {
    if ($number >= 1000000) {
        return round($number / 1000000, 1) . 'M';
    } elseif ($number >= 1000) {
        return round($number / 1000, 1) . 'K';
    }
    return $number;
}

function formatDate($date, $format = 'F j, Y') {
    if (!$date) return 'Never';
    return date($format, strtotime($date));
}

function formatDateTime($date, $format = 'F j, Y \a\t g:i A') {
    if (!$date) return 'Never';
    return date($format, strtotime($date));
}

// Enhanced security headers
function setSecurityHeaders() {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\' \'unsafe-eval\'; style-src \'self\' \'unsafe-inline\'; img-src \'self\' data: https:; font-src \'self\' data:;');
}

/**
 * Get user avatar URL
 * @param string $username
 * @return string
 */
function getUserAvatar($username) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT avatar FROM users WHERE username = ? AND is_active = 1");
        $stmt->execute([$username]);
        $avatar = $stmt->fetchColumn();
        
        if ($avatar && file_exists("uploads/avatars/$avatar")) {
            return "uploads/avatars/$avatar";
        }
        
        // Return default avatar
        return "images/default-avatar.svg";
    } catch (Exception $e) {
        error_log("Failed to get user avatar: " . $e->getMessage());
        return "images/default-avatar.svg";
    }
}

/**
 * Get total users count
 * @return int
 */
function getTotalUsers() {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE is_active = 1");
        $stmt->execute();
        return $stmt->fetchColumn();
    } catch (Exception $e) {
        error_log("Failed to get total users: " . $e->getMessage());
        return 0;
    }
}

/**
 * Get total posts count
 * @return int
 */
function getTotalPosts() {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE is_active = 1");
        $stmt->execute();
        return $stmt->fetchColumn();
    } catch (Exception $e) {
        error_log("Failed to get total posts: " . $e->getMessage());
        return 0;
    }
}

/**
 * Get online users count
 * @return int
 */
function getOnlineUsers() {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE last_seen > DATE_SUB(NOW(), INTERVAL 15 MINUTE) AND is_active = 1");
        $stmt->execute();
        return $stmt->fetchColumn();
    } catch (Exception $e) {
        error_log("Failed to get online users: " . $e->getMessage());
        return 0;
    }
}

/**
 * Get total achievements count
 * @return int
 */
function getTotalAchievements() {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM badges WHERE is_active = 1");
        $stmt->execute();
        return $stmt->fetchColumn();
    } catch (Exception $e) {
        error_log("Failed to get total achievements: " . $e->getMessage());
        return 0;
    }
}

/**
 * Get unread notification count for a user
 * @param int $user_id
 * @return int
 */
function getUnreadNotificationCount($user_id = null) {
    global $pdo;
    
    if (!$user_id && isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    }
    
    if (!$user_id) {
        return 0;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0 AND is_active = 1");
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn();
    } catch (Exception $e) {
        error_log("Failed to get unread notification count: " . $e->getMessage());
        return 0;
    }
}

/**
 * Get unread message count for a user
 * @param int $user_id
 * @return int
 */
function getUnreadMessageCount($user_id = null) {
    global $pdo;
    
    if (!$user_id && isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    }
    
    if (!$user_id) {
        return 0;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM private_messages WHERE recipient_id = ? AND is_read = 0 AND is_active = 1");
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn();
    } catch (Exception $e) {
        error_log("Failed to get unread message count: " . $e->getMessage());
        return 0;
    }
}

/**
 * Mark all notifications as read for a user
 * @param int $user_id
 * @return bool
 */
function markAllNotificationsAsRead($user_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_active = 1");
        return $stmt->execute([$user_id]);
    } catch (Exception $e) {
        error_log("Failed to mark all notifications as read: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete a notification
 * @param int $notification_id
 * @return bool
 */
function deleteNotification($notification_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("UPDATE notifications SET is_active = 0 WHERE id = ?");
        return $stmt->execute([$notification_id]);
    } catch (Exception $e) {
        error_log("Failed to delete notification: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete all notifications for a user
 * @param int $user_id
 * @return bool
 */
function deleteAllNotifications($user_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("UPDATE notifications SET is_active = 0 WHERE user_id = ?");
        return $stmt->execute([$user_id]);
    } catch (Exception $e) {
        error_log("Failed to delete all notifications: " . $e->getMessage());
        return false;
    }
}
?>
