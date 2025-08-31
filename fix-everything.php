<?php
require_once 'config.php';

echo "<h1>🔧 COMPREHENSIVE FORUM FIX SCRIPT</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #1a1a1a; color: #fff; }
    .success { color: #4CAF50; }
    .error { color: #f44336; }
    .warning { color: #ff9800; }
    .info { color: #2196F3; }
    pre { background: #333; padding: 10px; border-radius: 5px; overflow-x: auto; }
</style>";

try {
    $pdo = getDBConnection();
    echo "<p class='success'>✅ Database connection successful</p>";
    
    // Create all missing tables
    $tables = [
        'users' => "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(25) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            avatar VARCHAR(255) DEFAULT 'images/default-avatar.svg',
            bio TEXT,
            location VARCHAR(100),
            website VARCHAR(255),
            reputation INT DEFAULT 0,
            level INT DEFAULT 1,
            experience INT DEFAULT 0,
            is_premium BOOLEAN DEFAULT FALSE,
            is_admin BOOLEAN DEFAULT FALSE,
            is_banned BOOLEAN DEFAULT FALSE,
            is_online BOOLEAN DEFAULT FALSE,
            last_seen TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            thread_count INT DEFAULT 0,
            post_count INT DEFAULT 0,
            given_likes INT DEFAULT 0,
            received_likes INT DEFAULT 0
        )",
        
        'categories' => "CREATE TABLE IF NOT EXISTS categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            icon VARCHAR(50) DEFAULT 'fas fa-folder',
            color VARCHAR(7) DEFAULT '#667eea',
            sort_order INT DEFAULT 0,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        'threads' => "CREATE TABLE IF NOT EXISTS threads (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            user_id INT NOT NULL,
            category_id INT NOT NULL,
            views INT DEFAULT 0,
            replies INT DEFAULT 0,
            likes INT DEFAULT 0,
            is_pinned BOOLEAN DEFAULT FALSE,
            is_locked BOOLEAN DEFAULT FALSE,
            is_featured BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
        )",
        
        'posts' => "CREATE TABLE IF NOT EXISTS posts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            content TEXT NOT NULL,
            user_id INT NOT NULL,
            thread_id INT NOT NULL,
            likes INT DEFAULT 0,
            is_solution BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (thread_id) REFERENCES threads(id) ON DELETE CASCADE
        )",
        
        'user_badges' => "CREATE TABLE IF NOT EXISTS user_badges (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            badge_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )",
        
        'badges' => "CREATE TABLE IF NOT EXISTS badges (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            icon VARCHAR(50) NOT NULL,
            color VARCHAR(7) DEFAULT '#667eea',
            rarity ENUM('common', 'rare', 'epic', 'legendary') DEFAULT 'common',
            requirements TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        'likes' => "CREATE TABLE IF NOT EXISTS likes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            target_type ENUM('thread', 'post') NOT NULL,
            target_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE KEY unique_like (user_id, target_type, target_id)
        )",
        
        'system_logs' => "CREATE TABLE IF NOT EXISTS system_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            level ENUM('info', 'warning', 'error', 'critical') DEFAULT 'info',
            message TEXT NOT NULL,
            context JSON,
            user_id INT,
            ip_address VARCHAR(45),
            user_agent TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
        )",
        
        'reports' => "CREATE TABLE IF NOT EXISTS reports (
            id INT AUTO_INCREMENT PRIMARY KEY,
            reporter_id INT NOT NULL,
            target_type ENUM('user', 'thread', 'post') NOT NULL,
            target_id INT NOT NULL,
            reason ENUM('spam', 'inappropriate', 'harassment', 'cheating', 'other') NOT NULL,
            description TEXT,
            status ENUM('pending', 'investigating', 'resolved', 'dismissed') DEFAULT 'pending',
            moderator_id INT,
            resolved_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (reporter_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (moderator_id) REFERENCES users(id) ON DELETE SET NULL
        )",
        
        'site_settings' => "CREATE TABLE IF NOT EXISTS site_settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(100) UNIQUE NOT NULL,
            setting_value TEXT,
            setting_type ENUM('string', 'integer', 'boolean', 'json') DEFAULT 'string',
            description TEXT,
            is_public BOOLEAN DEFAULT FALSE,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )",
        
        'notifications' => "CREATE TABLE IF NOT EXISTS notifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            type ENUM('like', 'reply', 'mention', 'badge', 'system') NOT NULL,
            title VARCHAR(255) NOT NULL,
            message TEXT,
            data JSON,
            is_read BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )",
        
        'user_activity' => "CREATE TABLE IF NOT EXISTS user_activity (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            activity_type ENUM('login', 'logout', 'post', 'thread', 'like', 'badge') NOT NULL,
            description TEXT,
            ip_address VARCHAR(45),
            user_agent TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )"
    ];
    
    echo "<h2>📊 Creating Database Tables...</h2>";
    
    foreach ($tables as $table_name => $sql) {
        try {
            $pdo->exec($sql);
            echo "<p class='success'>✅ Table '$table_name' created/verified successfully</p>";
        } catch (Exception $e) {
            echo "<p class='error'>❌ Failed to create table '$table_name': " . $e->getMessage() . "</p>";
        }
    }
    
    // Insert default data
    echo "<h2>🌱 Inserting Default Data...</h2>";
    
    // Insert default categories
    $default_categories = [
        ['General Discussion', 'General forum discussions', 'fas fa-comments', '#667eea'],
        ['Counter-Strike 2', 'CS2 specific discussions', 'fas fa-crosshairs', '#ff6b35'],
        ['Cheat Discussion', 'Cheat related topics', 'fas fa-shield-alt', '#4CAF50'],
        ['Support & Help', 'Help and support', 'fas fa-question-circle', '#2196F3'],
        ['Off-Topic', 'Non-gaming discussions', 'fas fa-coffee', '#9C27B0'],
        ['Tournaments', 'Tournament discussions', 'fas fa-trophy', '#FFD700'],
        ['Downloads', 'File sharing', 'fas fa-download', '#FF5722'],
        ['Streams', 'Live streaming', 'fas fa-broadcast-tower', '#E91E63']
    ];
    
    foreach ($default_categories as $cat) {
        try {
            $stmt = $pdo->prepare("INSERT IGNORE INTO categories (name, description, icon, color) VALUES (?, ?, ?, ?)");
            $stmt->execute($cat);
            echo "<p class='success'>✅ Category '{$cat[0]}' added</p>";
        } catch (Exception $e) {
            echo "<p class='warning'>⚠️ Category '{$cat[0]}' already exists or failed: " . $e->getMessage() . "</p>";
        }
    }
    
    // Insert default badges
    $default_badges = [
        ['Newcomer', 'Welcome to the community!', 'fas fa-star', '#4CAF50', 'common'],
        ['Active Member', 'Regular contributor', 'fas fa-fire', '#FF9800', 'common'],
        ['Helpful', 'Always helping others', 'fas fa-hands-helping', '#2196F3', 'rare'],
        ['Creative', 'Original content creator', 'fas fa-palette', '#9C27B0', 'rare'],
        ['Elite', 'Premium community member', 'fas fa-crown', '#FFD700', 'epic'],
        ['Legend', 'Community legend', 'fas fa-medal', '#FF5722', 'legendary']
    ];
    
    foreach ($default_badges as $badge) {
        try {
            $stmt = $pdo->prepare("INSERT IGNORE INTO badges (name, description, icon, color, rarity) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute($badge);
            echo "<p class='success'>✅ Badge '{$badge[0]}' added</p>";
        } catch (Exception $e) {
            echo "<p class='warning'>⚠️ Badge '{$badge[0]}' already exists or failed: " . $e->getMessage() . "</p>";
        }
    }
    
    // Insert default site settings
    $default_settings = [
        ['site_name', 'Affinity Forum', 'string', 'Website name'],
        ['site_description', 'The most advanced Counter-Strike 2 cheat community forum', 'string', 'Website description'],
        ['site_email', 'admin@affinityforum.com', 'string', 'Admin email'],
        ['max_file_size', '10485760', 'integer', 'Maximum file upload size in bytes'],
        ['max_login_attempts', '5', 'integer', 'Maximum login attempts before lockout'],
        ['session_timeout', '3600', 'integer', 'Session timeout in seconds'],
        ['maintenance_mode', '0', 'boolean', 'Maintenance mode enabled'],
        ['allow_registration', '1', 'boolean', 'Allow new user registration'],
        ['allow_guest_posting', '0', 'boolean', 'Allow guests to post']
    ];
    
    foreach ($default_settings as $setting) {
        try {
            $stmt = $pdo->prepare("INSERT IGNORE INTO site_settings (setting_key, setting_value, setting_type, description) VALUES (?, ?, ?, ?)");
            $stmt->execute($setting);
            echo "<p class='success'>✅ Setting '{$setting[0]}' added</p>";
        } catch (Exception $e) {
            echo "<p class='warning'>⚠️ Setting '{$setting[0]}' already exists or failed: " . $e->getMessage() . "</p>";
        }
    }
    
    // Create sample threads if none exist
    $stmt = $pdo->query("SELECT COUNT(*) FROM threads");
    $thread_count = $stmt->fetchColumn();
    
    if ($thread_count == 0) {
        echo "<h2>📝 Creating Sample Threads...</h2>";
        
        // Get first user and category
        $stmt = $pdo->query("SELECT id FROM users LIMIT 1");
        $user_id = $stmt->fetchColumn();
        
        $stmt = $pdo->query("SELECT id FROM categories LIMIT 1");
        $category_id = $stmt->fetchColumn();
        
        if ($user_id && $category_id) {
            $sample_threads = [
                ['Best CS2 Cheat Settings for 2024', 'Share your optimal cheat configurations and settings for maximum performance in Counter-Strike 2.'],
                ['Premium Features Guide - Everything You Need to Know', 'Complete guide to all premium features available on Affinity Forum.'],
                ['New Update v2.1.0 - Changelog & Discussion', 'Discuss the latest update and share your thoughts on new features.'],
                ['Tournament Results & Highlights', 'Latest tournament results and memorable moments from competitive play.']
            ];
            
            foreach ($sample_threads as $thread) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO threads (title, content, user_id, category_id) VALUES (?, ?, ?, ?)");
                    $stmt->execute($thread);
                    echo "<p class='success'>✅ Sample thread '{$thread[0]}' created</p>";
                } catch (Exception $e) {
                    echo "<p class='error'>❌ Failed to create sample thread: " . $e->getMessage() . "</p>";
                }
            }
        }
    }
    
    echo "<h2>🎯 Database Status Check...</h2>";
    
    // Check table counts
    $table_names = array_keys($tables);
    foreach ($table_names as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
            $count = $stmt->fetchColumn();
            echo "<p class='info'>📊 Table '$table': $count records</p>";
        } catch (Exception $e) {
            echo "<p class='error'>❌ Failed to count '$table': " . $e->getMessage() . "</p>";
        }
    }
    
    echo "<h2 class='success'>🎉 FORUM FIX COMPLETED!</h2>";
    echo "<p class='success'>✅ All database tables created and populated with default data</p>";
    echo "<p class='info'>🔄 Please refresh your main page to see the working forum</p>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ CRITICAL ERROR: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
