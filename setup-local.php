<?php
/**
 * Local Setup Script for Affinity Forum
 * Creates database, tables, and sample data for local development
 */

// Use local config
require_once 'config-local.php';

echo "<!DOCTYPE html>";
echo "<html><head><title>Local Setup - Affinity Forum</title>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
    .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    .success { color: #28a745; font-weight: bold; }
    .error { color: #dc3545; font-weight: bold; }
    .info { color: #17a2b8; }
    .step { margin: 10px 0; padding: 10px; background: #f8f9fa; border-left: 4px solid #007bff; }
</style></head><body>";

echo "<div class='container'>";
echo "<h1>🚀 Local Setup for Affinity Forum</h1>";

try {
    // Step 1: Create database if it doesn't exist
    echo "<div class='step'>";
    echo "<h3>Step 1: Creating Database</h3>";
    
    $pdo = new PDO("mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET " . DB_CHARSET . " COLLATE " . DB_COLLATE);
    echo "<p class='success'>✅ Database 'affinity_forum' created/verified</p>";
    
    // Select the database
    $pdo->exec("USE " . DB_NAME);
    echo "<p class='success'>✅ Database selected</p>";
    echo "</div>";
    
    // Step 2: Create tables
    echo "<div class='step'>";
    echo "<h3>Step 2: Creating Tables</h3>";
    
    // Users table
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
    echo "<p class='success'>✅ Users table created/verified</p>";
    
    // Categories table
    $sql = "CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        sort_order INT DEFAULT 0,
        is_active BOOLEAN DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    echo "<p class='success'>✅ Categories table created/verified</p>";
    
    // Subforums table
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
    echo "<p class='success'>✅ Subforums table created/verified</p>";
    
    // Threads table
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
    echo "<p class='success'>✅ Threads table created/verified</p>";
    
    // Posts table
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
    echo "<p class='success'>✅ Posts table created/verified</p>";
    
    echo "</div>";
    
    // Step 3: Create sample data
    echo "<div class='step'>";
    echo "<h3>Step 3: Creating Sample Data</h3>";
    
    // Sample categories
    $categories = [
        ['name' => 'General Discussion', 'description' => 'General topics and casual conversations about anything and everything', 'sort_order' => 1],
        ['name' => 'Gaming & Entertainment', 'description' => 'Video games, movies, TV shows, and all forms of entertainment', 'sort_order' => 2],
        ['name' => 'Technology & Innovation', 'description' => 'Latest tech news, programming, gadgets, and innovation discussions', 'sort_order' => 3],
        ['name' => 'Community & Events', 'description' => 'Community events, meetups, and social gatherings', 'sort_order' => 4],
        ['name' => 'Help & Support', 'description' => 'Get help with technical issues, questions, and support requests', 'sort_order' => 5]
    ];
    
    foreach ($categories as $category) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO categories (name, description, sort_order) VALUES (?, ?, ?)");
        $stmt->execute([$category['name'], $category['description'], $category['sort_order']]);
        echo "<p class='success'>✅ Created category: {$category['name']}</p>";
    }
    
    // Sample subforums
    $subforums = [
        ['name' => 'Introductions', 'description' => 'Introduce yourself to the community', 'category_id' => 1],
        ['name' => 'Random Chat', 'description' => 'Random conversations and fun topics', 'category_id' => 1],
        ['name' => 'Video Games', 'description' => 'Discuss your favorite games and gaming news', 'category_id' => 2],
        ['name' => 'Movies & TV', 'description' => 'Latest releases, reviews, and discussions', 'category_id' => 2],
        ['name' => 'Programming', 'description' => 'Coding, development, and tech discussions', 'category_id' => 3],
        ['name' => 'Gadgets & Hardware', 'description' => 'Latest gadgets, reviews, and tech news', 'category_id' => 3],
        ['name' => 'Local Events', 'description' => 'Community events and meetups', 'category_id' => 4],
        ['name' => 'Technical Support', 'description' => 'Get help with technical issues', 'category_id' => 5],
        ['name' => 'FAQ & Guides', 'description' => 'Frequently asked questions and helpful guides', 'category_id' => 5]
    ];
    
    foreach ($subforums as $subforum) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO subforums (name, description, category_id) VALUES (?, ?, ?)");
        $stmt->execute([$subforum['name'], $subforum['description'], $subforum['category_id']]);
        echo "<p class='success'>✅ Created subforum: {$subforum['name']}</p>";
    }
    
    // Sample users
    $users = [
        [
            'username' => 'Admin',
            'email' => 'admin@affinity.com',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'rank' => 'admin',
            'bio' => 'Forum Administrator - Here to help and moderate the community',
            'reputation' => 1000,
            'level' => 10
        ],
        [
            'username' => 'Moderator',
            'email' => 'mod@affinity.com',
            'password' => password_hash('mod123', PASSWORD_DEFAULT),
            'rank' => 'moderator',
            'bio' => 'Community Moderator - Keeping the forum friendly and organized',
            'reputation' => 750,
            'level' => 8
        ],
        [
            'username' => 'GamerPro',
            'email' => 'gamer@affinity.com',
            'password' => password_hash('gamer123', PASSWORD_DEFAULT),
            'rank' => 'user',
            'bio' => 'Passionate gamer and community member',
            'reputation' => 500,
            'level' => 5
        ],
        [
            'username' => 'TechGuru',
            'email' => 'tech@affinity.com',
            'password' => password_hash('tech123', PASSWORD_DEFAULT),
            'rank' => 'user',
            'bio' => 'Technology enthusiast and programming expert',
            'reputation' => 600,
            'level' => 6
        ],
        [
            'username' => 'Newbie',
            'email' => 'new@affinity.com',
            'password' => password_hash('new123', PASSWORD_DEFAULT),
            'rank' => 'user',
            'bio' => 'New member excited to be part of the community',
            'reputation' => 100,
            'level' => 1
        ]
    ];
    
    foreach ($users as $user) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO users (username, email, password, rank, bio, reputation, level) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user['username'], $user['email'], $user['password'], $user['rank'], $user['bio'], $user['reputation'], $user['level']]);
        echo "<p class='success'>✅ Created user: {$user['username']} ({$user['rank']})</p>";
    }
    
    // Sample threads
    $threads = [
        [
            'title' => 'Welcome to Affinity Forum! 🎉',
            'content' => "Hello everyone! Welcome to our amazing community forum. This is a place where we can connect, share ideas, and build lasting friendships.\n\nFeel free to introduce yourself in the Introductions section and start participating in discussions. We're excited to have you here!\n\nWhat brings you to our community today?",
            'user_id' => 1,
            'subforum_id' => 1,
            'views' => 150,
            'replies' => 8
        ],
        [
            'title' => 'What games are you playing right now? 🎮',
            'content' => "Hey gamers! I'm currently hooked on a few different titles and would love to hear what you're all playing.\n\nCurrently playing:\n- Counter-Strike 2 (of course!)\n- Cyberpunk 2077\n- Baldur's Gate 3\n\nWhat about you? Any recommendations for new games to try?",
            'user_id' => 3,
            'subforum_id' => 3,
            'views' => 89,
            'replies' => 12
        ],
        [
            'title' => 'Best programming languages for beginners? 💻',
            'content' => "I'm thinking about learning to code and would love some advice from the community.\n\nI've heard Python is great for beginners, but I'm also interested in web development. What would you recommend for someone just starting out?\n\nAny good resources or tutorials you'd suggest?",
            'user_id' => 5,
            'subforum_id' => 5,
            'views' => 67,
            'replies' => 6
        ],
        [
            'title' => 'Community meetup ideas! 🤝',
            'content' => "I think it would be awesome to organize some community meetups! We could do gaming nights, tech talks, or just casual hangouts.\n\nWhat kind of events would you be interested in? Any ideas for activities or venues?\n\nLet's make this community even more awesome!",
            'user_id' => 2,
            'subforum_id' => 7,
            'views' => 45,
            'replies' => 4
        ],
        [
            'title' => 'Forum theme suggestions and feedback 🎨',
            'content' => "We're always looking to improve the forum experience! What do you think about the current themes and design?\n\nAny specific features you'd like to see? Color schemes, layouts, or functionality improvements?\n\nYour feedback helps us make this place better for everyone!",
            'user_id' => 1,
            'subforum_id' => 1,
            'views' => 78,
            'replies' => 9
        ]
    ];
    
    foreach ($threads as $thread) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO threads (title, content, user_id, subforum_id, views, replies) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$thread['title'], $thread['content'], $thread['user_id'], $thread['subforum_id'], $thread['views'], $thread['replies']]);
        echo "<p class='success'>✅ Created thread: {$thread['title']}</p>";
    }
    
    // Sample posts
    $posts = [
        [
            'content' => "Great to be here! I'm excited to meet everyone and contribute to the community.",
            'user_id' => 3,
            'thread_id' => 1
        ],
        [
            'content' => "Welcome! I'm sure you'll love it here. The community is really friendly and helpful.",
            'user_id' => 2,
            'thread_id' => 1
        ],
        [
            'content' => "I'm currently playing Valorant and Apex Legends. Both are great competitive shooters!",
            'user_id' => 4,
            'thread_id' => 2
        ],
        [
            'content' => "Python is definitely the way to go for beginners! It's readable and has tons of resources.",
            'user_id' => 4,
            'thread_id' => 3
        ],
        [
            'content' => "I'd love to join a gaming night! Maybe we could do some CS2 tournaments?",
            'user_id' => 3,
            'thread_id' => 4
        ]
    ];
    
    foreach ($posts as $post) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO posts (content, user_id, thread_id) VALUES (?, ?, ?)");
        $stmt->execute([$post['content'], $post['user_id'], $post['thread_id']]);
        echo "<p class='success'>✅ Created post by user ID: {$post['user_id']}</p>";
    }
    
    echo "</div>";
    
    // Step 4: Update statistics
    echo "<div class='step'>";
    echo "<h3>Step 4: Updating Forum Statistics</h3>";
    
    // Update user post counts
    $stmt = $pdo->prepare("UPDATE users SET post_count = (SELECT COUNT(*) FROM posts WHERE user_id = users.id)");
    $stmt->execute();
    echo "<p class='success'>✅ Updated user post counts</p>";
    
    // Update user thread counts
    $stmt = $pdo->prepare("UPDATE users SET thread_count = (SELECT COUNT(*) FROM threads WHERE user_id = users.id)");
    $stmt->execute();
    echo "<p class='success'>✅ Updated user thread counts</p>";
    
    // Update thread reply counts
    $stmt = $pdo->prepare("UPDATE threads SET replies = (SELECT COUNT(*) FROM posts WHERE thread_id = threads.id)");
    $stmt->execute();
    echo "<p class='success'>✅ Updated thread reply counts</p>";
    
    echo "</div>";
    
    echo "<div class='step'>";
    echo "<h3>🎉 Local Setup Complete!</h3>";
    echo "<p class='success'>Your local forum now has:</p>";
    echo "<ul>";
    echo "<li>5 forum categories</li>";
    echo "<li>9 subforums</li>";
    echo "<li>5 sample users (including admin)</li>";
    echo "<li>5 sample threads</li>";
    echo "<li>5 sample posts</li>";
    echo "</ul>";
    echo "<p class='info'>Default login credentials:</p>";
    echo "<ul>";
    echo "<li><strong>Admin:</strong> admin@affinity.com / admin123</li>";
    echo "<li><strong>Moderator:</strong> mod@affinity.com / mod123</li>";
    echo "<li><strong>User:</strong> gamer@affinity.com / gamer123</li>";
    echo "</ul>";
    echo "<p><strong>Important:</strong> Update your header.php to use 'config-local.php' instead of 'config.php' for local development.</p>";
    echo "<p><a href='index.php' class='btn btn-primary'>Go to Forum</a></p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p class='error'>Stack trace: " . $e->getTraceAsString() . "</p>";
}

echo "</div></body></html>";
?>