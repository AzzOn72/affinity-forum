<?php
/**
 * Setup Basic Database Tables
 * Creates the essential tables needed for the forum to work
 */

require_once 'config.php';

echo "<!DOCTYPE html>";
echo "<html><head><title>Setup Basic Tables</title></head><body>";
echo "<h1>🔧 Setting Up Basic Database Tables</h1>";

try {
    $pdo = getDBConnection();
    echo "<p style='color: green;'>✅ Database connected successfully</p>";
    
    // Create users table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(25) UNIQUE NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        rank ENUM('user', 'moderator', 'admin') DEFAULT 'user',
        avatar VARCHAR(255),
        bio TEXT,
        reputation INT UNSIGNED DEFAULT 0,
        level INT UNSIGNED DEFAULT 1,
        experience INT UNSIGNED DEFAULT 0,
        post_count INT UNSIGNED DEFAULT 0,
        thread_count INT UNSIGNED DEFAULT 0,
        like_count INT UNSIGNED DEFAULT 0,
        is_active BOOLEAN DEFAULT 1,
        is_premium BOOLEAN DEFAULT 0,
        theme VARCHAR(20) DEFAULT 'light',
        last_seen TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    echo "<p style='color: green;'>✅ Users table created/verified</p>";
    
    // Create categories table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        sort_order INT DEFAULT 0,
        is_active BOOLEAN DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    echo "<p style='color: green;'>✅ Categories table created/verified</p>";
    
    // Create subforums table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS subforums (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        category_id INT NOT NULL,
        sort_order INT DEFAULT 0,
        is_active BOOLEAN DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id)
    )";
    
    $pdo->exec($sql);
    echo "<p style='color: green;'>✅ Subforums table created/verified</p>";
    
    // Create threads table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS threads (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        content TEXT NOT NULL,
        user_id INT NOT NULL,
        subforum_id INT NOT NULL,
        views INT UNSIGNED DEFAULT 0,
        replies INT UNSIGNED DEFAULT 0,
        is_active BOOLEAN DEFAULT 1,
        is_pinned BOOLEAN DEFAULT 0,
        is_locked BOOLEAN DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (subforum_id) REFERENCES subforums(id)
    )";
    
    $pdo->exec($sql);
    echo "<p style='color: green;'>✅ Threads table created/verified</p>";
    
    // Create posts table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        content TEXT NOT NULL,
        user_id INT NOT NULL,
        thread_id INT NOT NULL,
        is_active BOOLEAN DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (thread_id) REFERENCES threads(id)
    )";
    
    $pdo->exec($sql);
    echo "<p style='color: green;'>✅ Posts table created/verified</p>";
    
    // Insert sample category if none exist
    $stmt = $pdo->query("SELECT COUNT(*) FROM categories");
    if ($stmt->fetchColumn() == 0) {
        $sql = "INSERT INTO categories (name, description, sort_order) VALUES 
                ('General Discussion', 'General topics and discussions', 1),
                ('Counter-Strike 2', 'CS2 related discussions', 2),
                ('Cheat Discussion', 'Cheat related topics', 3),
                ('Support & Help', 'Get help and support', 4),
                ('Off-Topic', 'Non-gaming discussions', 5)";
        $pdo->exec($sql);
        echo "<p style='color: green;'>✅ Sample categories created</p>";
    }
    
    // Insert sample subforum if none exist
    $stmt = $pdo->query("SELECT COUNT(*) FROM subforums");
    if ($stmt->fetchColumn() == 0) {
        $sql = "INSERT INTO subforums (name, description, category_id, sort_order) VALUES 
                ('Welcome', 'New member introductions', 1, 1),
                ('General Chat', 'General discussions', 1, 2),
                ('CS2 News', 'Latest CS2 updates', 2, 1),
                ('Gameplay', 'CS2 gameplay discussion', 2, 2),
                ('Cheat Features', 'Cheat feature discussions', 3, 1),
                ('Help Desk', 'Get help here', 4, 1)";
        $pdo->exec($sql);
        echo "<p style='color: green;'>✅ Sample subforums created</p>";
    }
    
    echo "<h2>🎉 Setup Complete!</h2>";
    echo "<p>All basic tables have been created successfully.</p>";
    echo "<p><a href='index.php'>🏠 Try Homepage Now</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Setup failed: " . $e->getMessage() . "</p>";
}

echo "</body></html>";
?>
