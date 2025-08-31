<?php
/**
 * Affinity Forum - SQLite Database Setup
 * Creates database tables and populates with sample data
 */

// Include local configuration
require_once 'config-local.php';

echo "<h1>Affinity Forum - SQLite Database Setup</h1>\n";

try {
    $pdo = getLocalDBConnection();
    echo "<p>✓ Database connection successful</p>\n";
    
    // Create tables
    echo "<h2>Creating Database Tables...</h2>\n";
    
    // Users table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username VARCHAR(25) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            avatar VARCHAR(255),
            rank VARCHAR(20) DEFAULT 'member',
            is_active BOOLEAN DEFAULT 1,
            is_verified BOOLEAN DEFAULT 0,
            join_date DATETIME DEFAULT CURRENT_TIMESTAMP,
            last_seen DATETIME DEFAULT CURRENT_TIMESTAMP,
            post_count INTEGER DEFAULT 0,
            thread_count INTEGER DEFAULT 0,
            reputation INTEGER DEFAULT 0,
            level INTEGER DEFAULT 1,
            experience INTEGER DEFAULT 0,
            badges TEXT,
            settings TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "<p>✓ Users table created</p>\n";
    
    // Categories table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS categories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            icon VARCHAR(50),
            color VARCHAR(7),
            sort_order INTEGER DEFAULT 0,
            is_active BOOLEAN DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "<p>✓ Categories table created</p>\n";
    
    // Subforums table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS subforums (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            category_id INTEGER NOT NULL,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            icon VARCHAR(50),
            sort_order INTEGER DEFAULT 0,
            is_active BOOLEAN DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
        )
    ");
    echo "<p>✓ Subforums table created</p>\n";
    
    // Threads table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS threads (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            subforum_id INTEGER NOT NULL,
            user_id INTEGER NOT NULL,
            title VARCHAR(200) NOT NULL,
            content TEXT NOT NULL,
            is_sticky BOOLEAN DEFAULT 0,
            is_locked BOOLEAN DEFAULT 0,
            is_active BOOLEAN DEFAULT 1,
            views INTEGER DEFAULT 0,
            likes INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (subforum_id) REFERENCES subforums(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");
    echo "<p>✓ Threads table created</p>\n";
    
    // Posts table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS posts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            thread_id INTEGER NOT NULL,
            user_id INTEGER NOT NULL,
            content TEXT NOT NULL,
            is_active BOOLEAN DEFAULT 1,
            likes INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (thread_id) REFERENCES threads(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");
    echo "<p>✓ Posts table created</p>\n";
    
    // Create indexes for better performance
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_threads_subforum ON threads(subforum_id)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_posts_thread ON posts(thread_id)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_users_username ON users(username)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_categories_sort ON categories(sort_order)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_subforums_category ON subforums(category_id)");
    
    echo "<p>✓ Database indexes created</p>\n";
    
    // Insert sample data
    echo "<h2>Inserting Sample Data...</h2>\n";
    
    // Sample categories
    $categories = [
        ['General Discussion', 'General topics and discussions about anything and everything', 'fas fa-comments', '#007bff', 1],
        ['Gaming & Entertainment', 'Video games, movies, music, and all things entertainment', 'fas fa-gamepad', '#28a745', 2],
        ['Technology & Innovation', 'Tech news, programming, and innovation discussions', 'fas fa-microchip', '#17a2b8', 3],
        ['Community & Events', 'Community events, meetups, and announcements', 'fas fa-calendar-alt', '#ffc107', 4],
        ['Help & Support', 'Get help with forum usage and technical support', 'fas fa-question-circle', '#dc3545', 5]
    ];
    
    $categoryIds = [];
    foreach ($categories as $category) {
        $stmt = $pdo->prepare("
            INSERT INTO categories (name, description, icon, color, sort_order) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute($category);
        $categoryIds[] = $pdo->lastInsertId();
        echo "<p>✓ Category '{$category[0]}' created</p>\n";
    }
    
    // Sample subforums
    $subforums = [
        [$categoryIds[0], 'Welcome & Introductions', 'Introduce yourself to the community', 'fas fa-handshake', 1],
        [$categoryIds[0], 'Off-Topic Discussion', 'Random discussions and chit-chat', 'fas fa-coffee', 2],
        [$categoryIds[1], 'Counter-Strike 2', 'CS2 discussions, strategies, and news', 'fas fa-crosshairs', 1],
        [$categoryIds[1], 'Other Games', 'Discuss other video games', 'fas fa-dice', 2],
        [$categoryIds[1], 'Entertainment', 'Movies, TV shows, music, and more', 'fas fa-film', 3],
        [$categoryIds[2], 'Tech News', 'Latest technology news and updates', 'fas fa-newspaper', 1],
        [$categoryIds[2], 'Programming', 'Programming discussions and help', 'fas fa-code', 2],
        [$categoryIds[3], 'Events', 'Community events and meetups', 'fas fa-calendar', 1],
        [$categoryIds[3], 'Announcements', 'Important community announcements', 'fas fa-bullhorn', 2],
        [$categoryIds[4], 'Forum Help', 'Help with using the forum', 'fas fa-life-ring', 1],
        [$categoryIds[4], 'Technical Support', 'Technical issues and support', 'fas fa-tools', 2]
    ];
    
    $subforumIds = [];
    foreach ($subforums as $subforum) {
        $stmt = $pdo->prepare("
            INSERT INTO subforums (category_id, name, description, icon, sort_order) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute($subforum);
        $subforumIds[] = $pdo->lastInsertId();
        echo "<p>✓ Subforum '{$subforum[1]}' created</p>\n";
    }
    
    // Sample users
    $users = [
        ['admin', 'admin@affinity.com', password_hash('admin123', PASSWORD_DEFAULT), 'admin'],
        ['moderator', 'mod@affinity.com', password_hash('mod123', PASSWORD_DEFAULT), 'moderator'],
        ['cs2player', 'cs2@affinity.com', password_hash('cs2123', PASSWORD_DEFAULT), 'member'],
        ['gamer123', 'gamer@affinity.com', password_hash('gamer123', PASSWORD_DEFAULT), 'member'],
        ['techguru', 'tech@affinity.com', password_hash('tech123', PASSWORD_DEFAULT), 'member']
    ];
    
    $userIds = [];
    foreach ($users as $user) {
        $stmt = $pdo->prepare("
            INSERT INTO users (username, email, password_hash, rank) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute($user);
        $userIds[] = $pdo->lastInsertId();
        echo "<p>✓ User '{$user[0]}' created</p>\n";
    }
    
    // Sample threads
    $threads = [
        [$subforumIds[0], $userIds[0], 'Welcome to Affinity Forum!', 'Welcome everyone to our new community! Feel free to introduce yourself and get to know other members.'],
        [$subforumIds[2], $userIds[2], 'CS2 Update Discussion', 'What do you think about the latest CS2 update? Share your thoughts and strategies!'],
        [$subforumIds[3], $userIds[1], 'Favorite Games of 2024', 'What games have you been enjoying this year? Let\'s discuss our favorites!'],
        [$subforumIds[5], $userIds[4], 'Latest Tech Trends', 'What technology trends are you most excited about? AI, VR, or something else?'],
        [$subforumIds[6], $userIds[4], 'Programming Help Thread', 'Need help with coding? Post your questions here and let\'s help each other out!']
    ];
    
    $threadIds = [];
    foreach ($threads as $thread) {
        $stmt = $pdo->prepare("
            INSERT INTO threads (subforum_id, user_id, title, content) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute($thread);
        $threadIds[] = $pdo->lastInsertId();
        echo "<p>✓ Thread '{$thread[2]}' created</p>\n";
    }
    
    // Sample posts
    $posts = [
        [$threadIds[0], $userIds[1], 'Thanks for the welcome! Looking forward to being part of this community.'],
        [$threadIds[0], $userIds[2], 'Hello everyone! I\'m a big CS2 fan and excited to meet other players.'],
        [$threadIds[1], $userIds[3], 'The new update is amazing! The graphics improvements are incredible.'],
        [$threadIds[1], $userIds[4], 'I agree! The performance optimizations are really noticeable.'],
        [$threadIds[2], $userIds[2], 'I\'ve been playing a lot of indie games lately. Any recommendations?'],
        [$threadIds[3], $userIds[0], 'AI is definitely the most exciting trend right now. The possibilities are endless!'],
        [$threadIds[4], $userIds[3], 'I\'m learning Python and would love some beginner tips!']
    ];
    
    foreach ($posts as $post) {
        $stmt = $pdo->prepare("
            INSERT INTO posts (thread_id, user_id, content) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute($post);
        echo "<p>✓ Post created</p>\n";
    }
    
    // Update user post counts
    $pdo->exec("
        UPDATE users SET post_count = (
            SELECT COUNT(*) FROM posts WHERE user_id = users.id
        )
    ");
    
    $pdo->exec("
        UPDATE users SET thread_count = (
            SELECT COUNT(*) FROM threads WHERE user_id = users.id
        )
    ");
    
    echo "<p>✓ User post counts updated</p>\n";
    
    echo "<h2>Setup Complete! 🎉</h2>\n";
    echo "<p>Your Affinity Forum database has been successfully created with sample data.</p>\n";
    echo "<p><strong>Default Login Credentials:</strong></p>\n";
    echo "<ul>\n";
    echo "<li><strong>Admin:</strong> admin / admin123</li>\n";
    echo "<li><strong>Moderator:</strong> moderator / mod123</li>\n";
    echo "<li><strong>Member:</strong> cs2player / cs2123</li>\n";
    echo "</ul>\n";
    echo "<p><a href='index.php'>Go to Forum</a></p>\n";
    
} catch (Exception $e) {
    echo "<h2>Setup Failed! ❌</h2>\n";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>\n";
    echo "<p>Please check the error logs and try again.</p>\n";
}
?>