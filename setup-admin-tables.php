<?php
define('SECURE_ACCESS', true);
require_once 'config.php';

// Require admin access
requireAdmin();

echo "<h1>Setting up Admin Tables for Affinity Forum v2.0</h1>";

try {
    $pdo = getDBConnection();
    
    // Create system_logs table
    echo "<h2>Creating system_logs table...</h2>";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS system_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            event VARCHAR(100) NOT NULL,
            details TEXT,
            level ENUM('debug', 'info', 'warning', 'error', 'critical') DEFAULT 'info',
            ip_address VARCHAR(45),
            user_agent TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_event (event),
            INDEX idx_level (level),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ system_logs table created successfully<br>";
    
    // Create reports table
    echo "<h2>Creating reports table...</h2>";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS reports (
            id INT AUTO_INCREMENT PRIMARY KEY,
            reporter_id INT NOT NULL,
            reported_user_id INT NULL,
            target_type ENUM('post', 'thread', 'user', 'message') NOT NULL,
            target_id INT NOT NULL,
            reason VARCHAR(100) NOT NULL,
            details TEXT,
            status ENUM('pending', 'investigating', 'resolved', 'dismissed') DEFAULT 'pending',
            moderator_id INT NULL,
            moderator_notes TEXT,
            resolved_at DATETIME NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (reporter_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (reported_user_id) REFERENCES users(id) ON DELETE SET NULL,
            FOREIGN KEY (moderator_id) REFERENCES users(id) ON DELETE SET NULL,
            INDEX idx_reporter (reporter_id),
            INDEX idx_reported_user (reported_user_id),
            INDEX idx_target (target_type, target_id),
            INDEX idx_status (status),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ reports table created successfully<br>";
    
    // Create site_settings table
    echo "<h2>Creating site_settings table...</h2>";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS site_settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(100) UNIQUE NOT NULL,
            setting_value TEXT,
            setting_type ENUM('string', 'integer', 'boolean', 'json') DEFAULT 'string',
            description TEXT,
            is_public BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_key (setting_key),
            INDEX idx_is_public (is_public)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ site_settings table created successfully<br>";
    
    // Create thread_subscriptions table
    echo "<h2>Creating thread_subscriptions table...</h2>";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS thread_subscriptions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            thread_id INT NOT NULL,
            notification_type ENUM('instant', 'daily', 'weekly') DEFAULT 'instant',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (thread_id) REFERENCES threads(id) ON DELETE CASCADE,
            UNIQUE KEY unique_subscription (user_id, thread_id),
            INDEX idx_user (user_id),
            INDEX idx_thread (thread_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ thread_subscriptions table created successfully<br>";
    
    // Create user_follows table
    echo "<h2>Creating user_follows table...</h2>";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS user_follows (
            id INT AUTO_INCREMENT PRIMARY KEY,
            follower_id INT NOT NULL,
            following_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (follower_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (following_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE KEY unique_follow (follower_id, following_id),
            INDEX idx_follower (follower_id),
            INDEX idx_following (following_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ user_follows table created successfully<br>";
    
    // Create polls table
    echo "<h2>Creating polls table...</h2>";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS polls (
            id INT AUTO_INCREMENT PRIMARY KEY,
            thread_id INT NOT NULL,
            question VARCHAR(255) NOT NULL,
            options JSON NOT NULL,
            allow_multiple BOOLEAN DEFAULT FALSE,
            max_choices INT DEFAULT 1,
            is_public BOOLEAN DEFAULT TRUE,
            ends_at DATETIME NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (thread_id) REFERENCES threads(id) ON DELETE CASCADE,
            INDEX idx_thread (thread_id),
            INDEX idx_ends_at (ends_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ polls table created successfully<br>";
    
    // Create poll_votes table
    echo "<h2>Creating poll_votes table...</h2>";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS poll_votes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            poll_id INT NOT NULL,
            user_id INT NOT NULL,
            selected_options JSON NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (poll_id) REFERENCES polls(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE KEY unique_poll_vote (poll_id, user_id),
            INDEX idx_poll (poll_id),
            INDEX idx_user (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ poll_votes table created successfully<br>";
    
    // Create user_levels table
    echo "<h2>Creating user_levels table...</h2>";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS user_levels (
            id INT AUTO_INCREMENT PRIMARY KEY,
            level INT UNIQUE NOT NULL,
            name VARCHAR(100) NOT NULL,
            min_points INT NOT NULL,
            color VARCHAR(7) DEFAULT '#007bff',
            icon VARCHAR(255),
            benefits JSON,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_level (level),
            INDEX idx_min_points (min_points)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ user_levels table created successfully<br>";
    
    // Create shop_items table
    echo "<h2>Creating shop_items table...</h2>";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS shop_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            icon VARCHAR(255),
            price_coins INT NOT NULL,
            price_real DECIMAL(10,2) NULL,
            category VARCHAR(50),
            item_type ENUM('badge', 'title', 'color', 'feature', 'custom') NOT NULL,
            item_data JSON,
            is_active BOOLEAN DEFAULT TRUE,
            stock INT DEFAULT -1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_category (category),
            INDEX idx_item_type (item_type),
            INDEX idx_is_active (is_active)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ shop_items table created successfully<br>";
    
    // Create user_purchases table
    echo "<h2>Creating user_purchases table...</h2>";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS user_purchases (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            item_id INT NOT NULL,
            price_coins INT NOT NULL,
            price_real DECIMAL(10,2) NULL,
            purchased_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (item_id) REFERENCES shop_items(id) ON DELETE CASCADE,
            INDEX idx_user (user_id),
            INDEX idx_item (item_id),
            INDEX idx_purchased_at (purchased_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ user_purchases table created successfully<br>";
    
    // Create tournaments table
    echo "<h2>Creating tournaments table...</h2>";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS tournaments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            game VARCHAR(100) NOT NULL,
            start_date DATETIME NOT NULL,
            end_date DATETIME NOT NULL,
            max_participants INT DEFAULT 0,
            current_participants INT DEFAULT 0,
            prize_pool DECIMAL(10,2) DEFAULT 0.00,
            status ENUM('upcoming', 'registration', 'active', 'completed', 'cancelled') DEFAULT 'upcoming',
            rules TEXT,
            created_by INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_game (game),
            INDEX idx_status (status),
            INDEX idx_start_date (start_date),
            INDEX idx_created_by (created_by)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ tournaments table created successfully<br>";
    
    // Create tournament_participants table
    echo "<h2>Creating tournament_participants table...</h2>";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS tournament_participants (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tournament_id INT NOT NULL,
            user_id INT NOT NULL,
            team_name VARCHAR(100) NULL,
            status ENUM('registered', 'confirmed', 'eliminated', 'winner') DEFAULT 'registered',
            registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (tournament_id) REFERENCES tournaments(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE KEY unique_participation (tournament_id, user_id),
            INDEX idx_tournament (tournament_id),
            INDEX idx_user (user_id),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ tournament_participants table created successfully<br>";
    
    // Create streaming table
    echo "<h2>Creating streaming table...</h2>";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS streaming (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            stream_url VARCHAR(500),
            platform ENUM('twitch', 'youtube', 'facebook', 'discord', 'other') NOT NULL,
            is_live BOOLEAN DEFAULT FALSE,
            viewers_count INT DEFAULT 0,
            started_at DATETIME NULL,
            ended_at DATETIME NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_user (user_id),
            INDEX idx_platform (platform),
            INDEX idx_is_live (is_live),
            INDEX idx_started_at (started_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ streaming table created successfully<br>";
    
    // Create discord_integration table
    echo "<h2>Creating discord_integration table...</h2>";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS discord_integration (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            discord_id VARCHAR(100) UNIQUE NOT NULL,
            discord_username VARCHAR(100) NOT NULL,
            discord_avatar VARCHAR(255),
            is_verified BOOLEAN DEFAULT FALSE,
            verification_token VARCHAR(255),
            linked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_user (user_id),
            INDEX idx_discord_id (discord_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ discord_integration table created successfully<br>";
    
    // Create user_statistics table
    echo "<h2>Creating user_statistics table...</h2>";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS user_statistics (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            posts_today INT DEFAULT 0,
            posts_this_week INT DEFAULT 0,
            posts_this_month INT DEFAULT 0,
            threads_today INT DEFAULT 0,
            threads_this_week INT DEFAULT 0,
            threads_this_month INT DEFAULT 0,
            likes_given_today INT DEFAULT 0,
            likes_given_this_week INT DEFAULT 0,
            likes_given_this_month INT DEFAULT 0,
            likes_received_today INT DEFAULT 0,
            likes_received_this_week INT DEFAULT 0,
            likes_received_this_month INT DEFAULT 0,
            last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE KEY unique_user_stats (user_id),
            INDEX idx_user (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ user_statistics table created successfully<br>";
    
    // Create forum_statistics table
    echo "<h2>Creating forum_statistics table...</h2>";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS forum_statistics (
            id INT AUTO_INCREMENT PRIMARY KEY,
            stat_date DATE UNIQUE NOT NULL,
            total_users INT DEFAULT 0,
            new_users INT DEFAULT 0,
            total_threads INT DEFAULT 0,
            new_threads INT DEFAULT 0,
            total_posts INT DEFAULT 0,
            new_posts INT DEFAULT 0,
            total_views INT DEFAULT 0,
            new_views INT DEFAULT 0,
            total_likes INT DEFAULT 0,
            new_likes INT DEFAULT 0,
            active_users INT DEFAULT 0,
            peak_online INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_stat_date (stat_date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ forum_statistics table created successfully<br>";
    
    // Insert default data
    echo "<h2>Inserting default data...</h2>";
    
    // Insert default user levels
    $pdo->exec("
        INSERT IGNORE INTO user_levels (level, name, min_points, color, icon) VALUES
        (1, 'Newcomer', 0, '#6c757d', 'fas fa-seedling'),
        (2, 'Member', 100, '#007bff', 'fas fa-user'),
        (3, 'Regular', 500, '#28a745', 'fas fa-user-check'),
        (4, 'Veteran', 1000, '#ffc107', 'fas fa-user-shield'),
        (5, 'Elite', 2500, '#dc3545', 'fas fa-user-crown'),
        (6, 'Legend', 5000, '#6f42c1', 'fas fa-user-astronaut')
    ");
    echo "✅ Default user levels inserted<br>";
    
    // Insert default badges
    $pdo->exec("
        INSERT IGNORE INTO badges (name, description, icon, color, category, rarity, points_reward) VALUES
        ('New Member', 'Welcome to the community!', 'fas fa-user-plus', '#28a745', 'membership', 'common', 10),
        ('First Post', 'Made your first post', 'fas fa-comment', '#007bff', 'activity', 'common', 20),
        ('Helpful', 'Helped other members', 'fas fa-thumbs-up', '#ffc107', 'community', 'uncommon', 50),
        ('Popular', 'Post received many likes', 'fas fa-fire', '#dc3545', 'recognition', 'rare', 100),
        ('Veteran', 'Been a member for 1 year', 'fas fa-crown', '#6f42c1', 'membership', 'epic', 200)
    ");
    echo "✅ Default badges inserted<br>";
    
    // Insert default site settings
    $pdo->exec("
        INSERT IGNORE INTO site_settings (setting_key, setting_value, setting_type, description, is_public) VALUES
        ('site_name', 'Affinity', 'string', 'Site name', true),
        ('site_description', 'Premium Counter-Strike 2 Community', 'string', 'Site description', true),
        ('site_logo', 'images/logo.png', 'string', 'Site logo path', true),
        ('maintenance_mode', 'false', 'boolean', 'Maintenance mode', false),
        ('registration_enabled', 'true', 'boolean', 'Allow new registrations', true),
        ('guest_viewing', 'true', 'boolean', 'Allow guests to view content', true),
        ('max_attachments_per_post', '3', 'integer', 'Maximum attachments per post', true),
        ('max_file_size', '5242880', 'integer', 'Maximum file size in bytes', true),
        ('allowed_file_types', '[\"jpg\",\"jpeg\",\"png\",\"gif\",\"webp\"]', 'json', 'Allowed file types', true),
        ('default_theme', 'light', 'string', 'Default theme for new users', true),
        ('enable_notifications', 'true', 'boolean', 'Enable notification system', true),
        ('enable_reactions', 'true', 'boolean', 'Enable post reactions', true),
        ('enable_badges', 'true', 'boolean', 'Enable badge system', true),
        ('enable_achievements', 'true', 'boolean', 'Enable achievement system', true),
        ('enable_levels', 'true', 'boolean', 'Enable level system', true),
        ('enable_points', 'true', 'boolean', 'Enable point system', true),
        ('enable_shop', 'true', 'boolean', 'Enable shop system', true),
        ('enable_tournaments', 'true', 'boolean', 'Enable tournament system', true),
        ('enable_streaming', 'true', 'boolean', 'Enable streaming integration', true),
        ('enable_discord', 'true', 'boolean', 'Enable Discord integration', true)
    ");
    echo "✅ Default site settings inserted<br>";
    
    // Insert default categories
    $pdo->exec("
        INSERT IGNORE INTO categories (name, description, icon, color, order_index) VALUES
        ('General Discussion', 'General topics and discussions', 'fas fa-comments', '#007bff', 1),
        ('Counter-Strike 2', 'CS2 specific discussions', 'fas fa-crosshairs', '#28a745', 2),
        ('Cheat Discussion', 'Cheat related topics', 'fas fa-shield-alt', '#dc3545', 3),
        ('Support & Help', 'Get help and support', 'fas fa-question-circle', '#ffc107', 4),
        ('Off-Topic', 'Non-gaming discussions', 'fas fa-coffee', '#6c757d', 5)
    ");
    echo "✅ Default categories inserted<br>";
    
    // Insert default subforums
    $pdo->exec("
        INSERT IGNORE INTO subforums (category_id, name, description, icon, order_index) VALUES
        (1, 'Welcome & Introductions', 'Introduce yourself to the community', 'fas fa-handshake', 1),
        (1, 'General Gaming', 'General gaming discussions', 'fas fa-gamepad', 2),
        (2, 'CS2 Updates', 'Latest CS2 news and updates', 'fas fa-newspaper', 1),
        (2, 'CS2 Strategies', 'Share strategies and tips', 'fas fa-chess', 2),
        (3, 'Cheat Features', 'Discuss cheat features and capabilities', 'fas fa-star', 1),
        (3, 'Cheat Support', 'Get help with cheats', 'fas fa-life-ring', 2),
        (4, 'Technical Support', 'Technical issues and help', 'fas fa-tools', 1),
        (4, 'Account Support', 'Account related issues', 'fas fa-user-cog', 2)
    ");
    echo "✅ Default subforums inserted<br>";
    
    // Create indexes for better performance
    echo "<h2>Creating performance indexes...</h2>";
    
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_threads_subforum_user ON threads(subforum_id, user_id)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_posts_thread_user ON posts(thread_id, user_id)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_likes_target ON likes(target_type, target_id)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_notifications_user_type ON notifications(user_id, type)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_private_messages_users ON private_messages(sender_id, recipient_id)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_user_activity_user_action ON user_activity(user_id, action)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_search_log_user_query ON search_log(user_id, query)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_system_logs_level_event ON system_logs(level, event)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_reports_status_target ON reports(status, target_type, target_id)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_thread_subscriptions_user ON thread_subscriptions(user_id)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_user_follows_users ON user_follows(follower_id, following_id)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_poll_votes_poll_user ON poll_votes(poll_id, user_id)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_user_purchases_user ON user_purchases(user_id)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_tournament_participants_tournament ON tournament_participants(tournament_id)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_streaming_user_live ON streaming(user_id, is_live)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_user_statistics_user ON user_statistics(user_id)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_forum_statistics_date ON forum_statistics(stat_date)");
    
    echo "✅ Performance indexes created successfully<br>";
    
    echo "<h2>🎉 All admin tables and features have been set up successfully!</h2>";
    echo "<p>Your Affinity Forum v2.0 is now ready with:</p>";
    echo "<ul>";
    echo "<li>✅ Advanced user management system</li>";
    echo "<li>✅ Comprehensive reporting and moderation</li>";
    echo "<li>✅ Site settings and configuration</li>";
    echo "<li>✅ Thread subscriptions and user follows</li>";
    echo "<li>✅ Poll system for community engagement</li>";
    echo "<li>✅ User levels and progression system</li>";
    echo "<li>✅ Shop system for premium features</li>";
    echo "<li>✅ Tournament management system</li>";
    echo "<li>✅ Live streaming integration</li>";
    echo "<li>✅ Discord integration</li>";
    echo "<li>✅ Advanced statistics and analytics</li>";
    echo "<li>✅ Performance optimization indexes</li>";
    echo "</ul>";
    
    echo "<p><strong>Next steps:</strong></p>";
    echo "<ol>";
    echo "<li>Visit your <a href='index.php'>admin dashboard</a> to see all the new features</li>";
    echo "<li>Configure your site settings in the admin panel</li>";
    echo "<li>Set up your first tournament or event</li>";
    echo "<li>Customize user levels and badges</li>";
    echo "<li>Test the new moderation and reporting system</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<div style='color: red; padding: 20px; border: 1px solid red; border-radius: 5px;'>";
    echo "<h2>❌ Error occurred during setup:</h2>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p><strong>Line:</strong> " . htmlspecialchars($e->getLine()) . "</p>";
    echo "</div>";
    
    // Log the error
    error_log("Admin tables setup failed: " . $e->getMessage());
}
?>
