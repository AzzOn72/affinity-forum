<?php
/**
 * Database Update Script
 * Add missing fields to existing database
 */

define('SECURE_ACCESS', true);
require_once 'config.php';

echo "<h1>Database Update Script</h1>";
echo "<p>Adding missing fields to existing database...</p>";

try {
    // Check if reputation field exists
    $stmt = $pdo->prepare("SHOW COLUMNS FROM users LIKE 'reputation'");
    $stmt->execute();
    $reputationExists = $stmt->rowCount() > 0;
    
    if (!$reputationExists) {
        echo "<p>Adding reputation field...</p>";
        $pdo->exec("ALTER TABLE users ADD COLUMN reputation INT UNSIGNED NOT NULL DEFAULT 0 AFTER like_count");
        echo "<p>✓ Reputation field added successfully</p>";
    } else {
        echo "<p>✓ Reputation field already exists</p>";
    }
    
    // Check if level field exists
    $stmt = $pdo->prepare("SHOW COLUMNS FROM users LIKE 'level'");
    $stmt->execute();
    $levelExists = $stmt->rowCount() > 0;
    
    if (!$levelExists) {
        echo "<p>Adding level field...</p>";
        $pdo->exec("ALTER TABLE users ADD COLUMN level INT UNSIGNED NOT NULL DEFAULT 1 AFTER reputation");
        echo "<p>✓ Level field added successfully</p>";
    } else {
        echo "<p>✓ Level field already exists</p>";
    }
    
    // Check if experience field exists
    $stmt = $pdo->prepare("SHOW COLUMNS FROM users LIKE 'experience'");
    $stmt->execute();
    $experienceExists = $stmt->rowCount() > 0;
    
    if (!$experienceExists) {
        echo "<p>Adding experience field...</p>";
        $pdo->exec("ALTER TABLE users ADD COLUMN experience INT UNSIGNED NOT NULL DEFAULT 0 AFTER level");
        echo "<p>✓ Experience field added successfully</p>";
    } else {
        echo "<p>✓ Experience field already exists</p>";
    }
    
    // Check if is_premium field exists
    $stmt = $pdo->prepare("SHOW COLUMNS FROM users LIKE 'is_premium'");
    $stmt->execute();
    $premiumExists = $stmt->rowCount() > 0;
    
    if (!$premiumExists) {
        echo "<p>Adding is_premium field...</p>";
        $pdo->exec("ALTER TABLE users ADD COLUMN is_premium TINYINT(1) NOT NULL DEFAULT 0 AFTER is_banned");
        echo "<p>✓ Premium field added successfully</p>";
    } else {
        echo "<p>✓ Premium field already exists</p>";
    }
    
    // Update existing users with default values
    echo "<p>Updating existing users with default values...</p>";
    $pdo->exec("UPDATE users SET reputation = 0 WHERE reputation IS NULL");
    $pdo->exec("UPDATE users SET level = 1 WHERE level IS NULL");
    $pdo->exec("UPDATE users SET experience = 0 WHERE experience IS NULL");
    $pdo->exec("UPDATE users SET is_premium = 0 WHERE is_premium IS NULL");
    
    echo "<p>✓ All users updated with default values</p>";
    
    // Calculate initial reputation based on post and thread counts
    echo "<p>Calculating initial reputation for existing users...</p>";
    $pdo->exec("
        UPDATE users 
        SET reputation = (post_count * 5) + (thread_count * 10) + (like_count * 2)
        WHERE reputation = 0
    ");
    
    echo "<p>✓ Initial reputation calculated</p>";
    
    // Calculate initial levels based on experience
    echo "<p>Calculating initial levels for existing users...</p>";
    $pdo->exec("
        UPDATE users 
        SET experience = (post_count * 10) + (thread_count * 50) + (reputation * 2)
        WHERE experience = 0
    ");
    
    $pdo->exec("
        UPDATE users 
        SET level = FLOOR(experience / 1000) + 1
        WHERE level = 1
    ");
    
    echo "<p>✓ Initial levels calculated</p>";
    
    echo "<h2>Database Update Complete!</h2>";
    echo "<p>All missing fields have been added and existing data has been updated.</p>";
    
    // Show summary
    $stmt = $pdo->prepare("SELECT COUNT(*) as total_users, AVG(reputation) as avg_reputation, AVG(level) as avg_level FROM users WHERE is_active = 1");
    $stmt->execute();
    $stats = $stmt->fetch();
    
    echo "<h3>Current Statistics:</h3>";
    echo "<ul>";
    echo "<li>Total Users: " . number_format($stats['total_users']) . "</li>";
    echo "<li>Average Reputation: " . number_format($stats['avg_reputation'], 1) . "</li>";
    echo "<li>Average Level: " . number_format($stats['avg_level'], 1) . "</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database connection and try again.</p>";
}

echo "<p><a href='index.php'>Return to Homepage</a></p>";
?>
