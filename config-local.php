<?php
/**
 * Affinity Forum - Local Development Configuration
 * Use this for local development instead of the production config
 */

// Include error handler first
require_once __DIR__ . '/error-handler-simple.php';

// Security constant
define('SECURE_ACCESS', true);

// Enable error reporting for development
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

// Local Database configuration (SQLite)
define('DB_TYPE', 'sqlite');
define('DB_FILE', __DIR__ . '/database/affinity_forum.db');

// Site configuration
define('SITE_NAME', 'Affinity');
define('SITE_DESCRIPTION', 'Premium Counter-Strike 2 Community');
define('SITE_URL', 'http://localhost'); // Update with your local domain
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
function getLocalDBConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            // Create database directory if it doesn't exist
            $dbDir = dirname(DB_FILE);
            if (!is_dir($dbDir)) {
                mkdir($dbDir, 0755, true);
            }
            
            $dsn = "sqlite:" . DB_FILE;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $pdo = new PDO($dsn, null, null, $options);
            
            // Enable foreign keys
            $pdo->exec("PRAGMA foreign_keys = ON");
            
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }
    
    return $pdo;
}

// Helper functions
function isLocalLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isLocalAdmin() {
    return isset($_SESSION['user_rank']) && $_SESSION['user_rank'] === 'admin';
}

function isLocalModerator() {
    return isset($_SESSION['user_rank']) && in_array($_SESSION['user_rank'], ['admin', 'moderator']);
}

function getLocalCurrentUser() {
    if (!isLocalLoggedIn()) {
        return null;
    }
    
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    } catch (Exception $e) {
        error_log("Failed to get current user: " . $e->getMessage());
        return null;
    }
}

function getLocalCategories() {
    try {
        $pdo = getLocalDBConnection();
        $stmt = $pdo->prepare("
            SELECT c.*, 
                   COUNT(DISTINCT t.id) as thread_count,
                   COUNT(DISTINCT t.id) as post_count,
                   COALESCE(SUM(t.views), 0) as view_count
            FROM categories c
            LEFT JOIN subforums s ON c.id = s.category_id
            LEFT JOIN threads t ON s.id = t.subforum_id
            LEFT JOIN posts p ON t.id = p.thread_id
            WHERE c.is_active = 1
            GROUP BY c.id
            ORDER BY c.sort_order, c.name
        ");
        $stmt->execute();
        $categories = $stmt->fetchAll();
        
        // Get subforums for each category
        foreach ($categories as &$category) {
            $stmt = $pdo->prepare("
                SELECT s.*, 
                       COUNT(DISTINCT t.id) as thread_count,
                       COUNT(DISTINCT p.id) as post_count
                FROM subforums s
                LEFT JOIN threads t ON s.id = t.subforum_id
                LEFT JOIN posts p ON t.id = p.thread_id
            WHERE s.category_id = ? AND s.is_active = 1
                GROUP BY s.id
                ORDER BY s.sort_order, s.name
            ");
            $stmt->execute([$category['id']]);
            $category['subforums'] = $stmt->fetchAll();
        }
        
        return $categories;
    } catch (Exception $e) {
        error_log("Failed to get categories: " . $e->getMessage());
        return [];
    }
}

function getLocalForumStats() {
    try {
        $pdo = getLocalDBConnection();
        
        $stats = [];
        
        // Total users
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE is_active = 1");
        $stats['total_users'] = $stmt->fetch()['count'];
        
        // Total threads
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM threads WHERE is_active = 1");
        $stats['total_threads'] = $stmt->fetch()['count'];
        
        // Total posts
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM posts WHERE is_active = 1");
        $stats['total_posts'] = $stmt->fetch()['count'];
        
        // Online users (active in last 15 minutes)
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE last_seen > datetime('now', '-15 minutes')");
        $stats['online_users'] = $stmt->fetch()['count'];
        
        // Today's stats
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE date(created_at) = date('now')");
        $stats['today_users'] = $stmt->fetch()['count'];
        
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM threads WHERE date(created_at) = date('now')");
        $stats['today_threads'] = $stmt->fetch()['count'];
        
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM posts WHERE date(created_at) = date('now')");
        $stats['today_posts'] = $stmt->fetch()['count'];
        
        return $stats;
    } catch (Exception $e) {
        error_log("Failed to get forum stats: " . $e->getMessage());
        return [
            'total_users' => 0,
            'total_threads' => 0,
            'total_posts' => 0,
            'online_users' => 0,
            'today_users' => 0,
            'today_threads' => 0,
            'today_posts' => 0
        ];
    }
}

function getLocalCategoryIcon($categoryName) {
    $icons = [
        'General Discussion' => 'fas fa-comments',
        'Gaming & Entertainment' => 'fas fa-gamepad',
        'Technology & Innovation' => 'fas fa-microchip',
        'Community & Events' => 'fas fa-calendar-alt',
        'Help & Support' => 'fas fa-question-circle'
    ];
    
    return $icons[$categoryName] ?? 'fas fa-folder';
}

function formatLocalTimeAgo($timestamp) {
    $time = strtotime($timestamp);
    $now = time();
    $diff = $now - $time;
    
    if ($diff < 60) {
        return 'Just now';
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return date('M j, Y', $time);
    }
}
?>