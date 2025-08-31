<?php
require_once 'config.php';

// Initialize database connection
$pdo = getDBConnection();

// Initialize variables with safe defaults
$stats = [
    'total_users' => 0,
    'total_threads' => 0,
    'total_posts' => 0,
    'online_users' => 0,
    'today_users' => 0,
    'today_threads' => 0,
    'today_posts' => 0
];

$recent_threads = [];
$popular_threads = [];
$online_users = [];
$categories = [];

// Try to get forum statistics safely
try {
    // Check if tables exist before querying
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        $stmt = $pdo->query("SELECT COUNT(*) FROM users");
        $stats['total_users'] = $stmt->fetchColumn();
    }
} catch (Exception $e) {
    error_log("Failed to get user count: " . $e->getMessage());
}

try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'threads'");
    if ($stmt->rowCount() > 0) {
        $stmt = $pdo->query("SELECT COUNT(*) FROM threads");
        $stats['total_threads'] = $stmt->fetchColumn();
    }
} catch (Exception $e) {
    error_log("Failed to get thread count: " . $e->getMessage());
}

try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'posts'");
    if ($stmt->rowCount() > 0) {
        $stmt = $pdo->query("SELECT COUNT(*) FROM posts");
        $stats['total_posts'] = $stmt->fetchColumn();
    }
} catch (Exception $e) {
    error_log("Failed to get post count: " . $e->getMessage());
}

// Get recent threads if table exists
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'threads'");
    if ($stmt->rowCount() > 0) {
        $stmt = $pdo->query("SELECT t.*, u.username, c.name as category_name 
                             FROM threads t 
                             JOIN users u ON t.user_id = u.id 
                             JOIN categories c ON t.category_id = c.id 
                             ORDER BY t.created_at DESC 
                             LIMIT 5");
        $recent_threads = $stmt->fetchAll();
    }
} catch (Exception $e) {
    error_log("Failed to get recent threads: " . $e->getMessage());
}

// Get categories if table exists
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'categories'");
    if ($stmt->rowCount() > 0) {
        $stmt = $pdo->query("SELECT * FROM categories ORDER BY sort_order, name");
        $categories = $stmt->fetchAll();
    }
} catch (Exception $e) {
    error_log("Failed to get categories: " . $e->getMessage());
}

// Include header
include 'includes/header.php';
?>

<!-- ===== PREMIUM HERO SECTION ===== -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="hero-content animate-fade-in-left">
                    <h1 class="hero-title">Welcome to Affinity</h1>
                    <p class="hero-description">
                        The most advanced Counter-Strike 2 cheat community forum. 
                        Join thousands of players, share strategies, and dominate the competition.
                    </p>
                    <div class="hero-actions">
                        <a href="register.php" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-user-plus me-2"></i>Join Now
                        </a>
                        <a href="login.php" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>Sign In
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="hero-stats animate-fade-in-right">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo number_format($stats['total_users']); ?></div>
                        <div class="stat-label">MEMBERS</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo number_format($stats['total_threads']); ?></div>
                        <div class="stat-label">THREADS</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo number_format($stats['total_posts']); ?></div>
                        <div class="stat-label">POSTS</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== FORUM CATEGORIES SECTION ===== -->
<section class="categories-section">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title">Forum Categories</h2>
            <p class="section-description">Explore our community sections</p>
        </div>
        
        <div class="row">
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $category): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="category-card">
                            <div class="category-icon">
                                <i class="<?php echo htmlspecialchars($category['icon']); ?>"></i>
                            </div>
                            <div class="category-content">
                                <h3 class="category-title"><?php echo htmlspecialchars($category['name']); ?></h3>
                                <p class="category-description"><?php echo htmlspecialchars($category['description']); ?></p>
                                <a href="subforum.php?category=<?php echo $category['id']; ?>" class="btn btn-outline-primary">
                                    Browse <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Default categories if database is empty -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="category-card">
                        <div class="category-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <div class="category-content">
                            <h3 class="category-title">General Discussion</h3>
                            <p class="category-description">General forum discussions and community chat</p>
                            <a href="#" class="btn btn-outline-primary">Browse <i class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="category-card">
                        <div class="category-icon">
                            <i class="fas fa-crosshairs"></i>
                        </div>
                        <div class="category-content">
                            <h3 class="category-title">Counter-Strike 2</h3>
                            <p class="category-description">CS2 specific discussions and strategies</p>
                            <a href="#" class="btn btn-outline-primary">Browse <i class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="category-card">
                        <div class="category-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="category-content">
                            <h3 class="category-title">Cheat Discussion</h3>
                            <p class="category-description">Cheat related topics and configurations</p>
                            <a href="#" class="btn btn-outline-primary">Browse <i class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ===== RECENT ACTIVITY SECTION ===== -->
<section class="recent-activity-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="section-header mb-4">
                    <h2 class="section-title">Recent Activity</h2>
                    <p class="section-description">Latest discussions and updates</p>
                </div>
                
                <?php if (!empty($recent_threads)): ?>
                    <div class="thread-list">
                        <?php foreach ($recent_threads as $thread): ?>
                            <div class="thread-item">
                                <div class="thread-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="thread-content">
                                    <h4 class="thread-title">
                                        <a href="thread.php?id=<?php echo $thread['id']; ?>">
                                            <?php echo htmlspecialchars($thread['title']); ?>
                                        </a>
                                    </h4>
                                    <div class="thread-meta">
                                        <span class="thread-author">by <?php echo htmlspecialchars($thread['username']); ?></span>
                                        <span class="thread-category">in <?php echo htmlspecialchars($thread['category_name']); ?></span>
                                        <span class="thread-time"><?php echo date('M j, Y', strtotime($thread['created_at'])); ?></span>
                                    </div>
                                </div>
                                <div class="thread-stats">
                                    <div class="stat">
                                        <i class="fas fa-eye"></i>
                                        <span><?php echo number_format($thread['views']); ?></span>
                                    </div>
                                    <div class="stat">
                                        <i class="fas fa-comments"></i>
                                        <span><?php echo number_format($thread['replies']); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <!-- Sample threads if database is empty -->
                    <div class="thread-list">
                        <div class="thread-item">
                            <div class="thread-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="thread-content">
                                <h4 class="thread-title">
                                    <a href="#">Best CS2 Cheat Settings for 2024</a>
                                </h4>
                                <div class="thread-meta">
                                    <span class="thread-author">by Cheat ProGamer123</span>
                                    <span class="thread-category">in Cheat Discussion</span>
                                    <span class="thread-time">2 hours ago</span>
                                </div>
                            </div>
                            <div class="thread-stats">
                                <div class="stat">
                                    <i class="fas fa-eye"></i>
                                    <span>1.2k</span>
                                </div>
                                <div class="stat">
                                    <i class="fas fa-comments"></i>
                                    <span>45</span>
                                </div>
                            </div>
                        </div>
                        <div class="thread-item">
                            <div class="thread-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="thread-content">
                                <h4 class="thread-title">
                                    <a href="#">Premium Features Guide - Everything You Need to Know</a>
                                </h4>
                                <div class="thread-meta">
                                    <span class="thread-author">by Support Admin</span>
                                    <span class="thread-category">in Support & Help</span>
                                    <span class="thread-time">1 day ago</span>
                                </div>
                            </div>
                            <div class="thread-stats">
                                <div class="stat">
                                    <i class="fas fa-eye"></i>
                                    <span>856</span>
                                </div>
                                <div class="stat">
                                    <i class="fas fa-comments"></i>
                                    <span>23</span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="text-center mt-4">
                    <a href="forum.php" class="btn btn-primary btn-lg">
                        View All Threads <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="sidebar-widgets">
                    <!-- Trending Topics Widget -->
                    <div class="widget trending-topics">
                        <div class="widget-header">
                            <h3 class="widget-title">
                                <i class="fas fa-fire me-2"></i>Trending Now
                            </h3>
                        </div>
                        <div class="widget-content">
                            <div class="trending-item">
                                <div class="trending-icon">
                                    <i class="fas fa-fire"></i>
                                </div>
                                <div class="trending-content">
                                    <h4>Best CS2 Cheat Settings for 2024</h4>
                                    <p>by Cheat ProGamer123</p>
                                    <span class="trending-category">Discussion</span>
                                </div>
                            </div>
                            <div class="trending-item">
                                <div class="trending-icon">
                                    <i class="fas fa-gem"></i>
                                </div>
                                <div class="trending-content">
                                    <h4>Premium Features Guide - Everything You Need to Know</h4>
                                    <p>by Support Admin</p>
                                    <span class="trending-category">Help</span>
                                </div>
                            </div>
                            <div class="trending-item">
                                <div class="trending-icon">
                                    <i class="fas fa-cog"></i>
                                </div>
                                <div class="trending-content">
                                    <h4>New Update v2.1.0 - Changelog & Discussion</h4>
                                    <p>by General Developer</p>
                                    <span class="trending-category">Discussion</span>
                                </div>
                            </div>
                            <div class="trending-item">
                                <div class="trending-icon">
                                    <i class="fas fa-trophy"></i>
                                </div>
                                <div class="trending-content">
                                    <h4>Tournament Results & Highlights</h4>
                                    <p>by TournamentMaster</p>
                                    <span class="trending-category">Tournaments</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions Widget -->
                    <div class="widget quick-actions">
                        <div class="widget-header">
                            <h3 class="widget-title">
                                <i class="fas fa-bolt me-2"></i>Quick Actions
                            </h3>
                        </div>
                        <div class="widget-content">
                            <a href="new-thread.php" class="btn btn-primary btn-sm w-100 mb-2">
                                <i class="fas fa-plus me-2"></i>New Thread
                            </a>
                            <a href="search.php" class="btn btn-outline-primary btn-sm w-100 mb-2">
                                <i class="fas fa-search me-2"></i>Search Forums
                            </a>
                            <a href="members.php" class="btn btn-outline-primary btn-sm w-100 mb-2">
                                <i class="fas fa-users me-2"></i>Browse Members
                            </a>
                            <a href="downloads.php" class="btn btn-outline-primary btn-sm w-100">
                                <i class="fas fa-download me-2"></i>Downloads
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
