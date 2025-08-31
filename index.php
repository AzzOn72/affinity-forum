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

// Try to get recent threads safely
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

// Try to get popular threads safely
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'threads'");
    if ($stmt->rowCount() > 0) {
        $stmt = $pdo->query("SELECT t.*, u.username, c.name as category_name 
                             FROM threads t 
                             JOIN users u ON t.user_id = u.id 
                             JOIN categories c ON t.category_id = c.id 
                             ORDER BY t.views DESC, t.replies DESC 
                             LIMIT 5");
        $popular_threads = $stmt->fetchAll();
    }
} catch (Exception $e) {
    error_log("Failed to get popular threads: " . $e->getMessage());
}

// Try to get online users safely
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        $stmt = $pdo->query("SELECT username, last_activity FROM users 
                             WHERE last_activity > DATE_SUB(NOW(), INTERVAL 15 MINUTE) 
                             ORDER BY last_activity DESC 
                             LIMIT 10");
        $online_users = $stmt->fetchAll();
    }
} catch (Exception $e) {
    error_log("Failed to get online users: " . $e->getMessage());
}

// Try to get categories safely
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
                    <p class="hero-subtitle">The most advanced Counter-Strike 2 cheat community forum. Join thousands of players, share strategies, and dominate the competition.</p>
                    
                    <!-- Premium Stats Grid -->
                    <div class="stats-container">
                        <div class="stat-card animate-fade-in-up" style="animation-delay: 0.1s;">
                            <div class="stat-number"><?php echo number_format($stats['total_users']); ?></div>
                            <div class="stat-label">MEMBERS</div>
                        </div>
                        <div class="stat-card animate-fade-in-up" style="animation-delay: 0.2s;">
                            <div class="stat-number"><?php echo number_format($stats['total_threads']); ?></div>
                            <div class="stat-label">THREADS</div>
                        </div>
                        <div class="stat-card animate-fade-in-up" style="animation-delay: 0.3s;">
                            <div class="stat-number"><?php echo number_format($stats['total_posts']); ?></div>
                            <div class="stat-label">POSTS</div>
                        </div>
                    </div>
                    
                    <!-- Premium Call-to-Action Buttons -->
                    <div class="hero-actions">
                        <?php if (!isLoggedIn()): ?>
                            <a href="register.php" class="btn btn-primary btn-lg me-3 animate-fade-in-up" style="animation-delay: 0.4s;">
                                <i class="fas fa-user-plus me-2"></i>Join Now
                            </a>
                            <a href="login.php" class="btn btn-outline-light btn-lg animate-fade-in-up" style="animation-delay: 0.5s;">
                                <i class="fas fa-sign-in-alt me-2"></i>Sign In
                            </a>
                        <?php else: ?>
                            <a href="new-thread.php" class="btn btn-primary btn-lg me-3 animate-fade-in-up" style="animation-delay: 0.4s;">
                                <i class="fas fa-plus me-2"></i>Create Thread
                            </a>
                            <a href="forum.php" class="btn btn-outline-light btn-lg animate-fade-in-up" style="animation-delay: 0.5s;">
                                <i class="fas fa-comments me-2"></i>Browse Forums
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Premium Trending Card -->
                <div class="trending-card animate-fade-in-right" style="animation-delay: 0.3s;">
                    <div class="trending-header">
                        <div class="trending-icon">
                            <i class="fas fa-fire"></i>
                        </div>
                        <div class="trending-title">Trending Now</div>
                    </div>
                    
                    <div class="trending-content">
                        <div class="trending-item">
                            <div class="trending-item-title">
                                <a href="#">🔥 Best CS2 Cheat Settings for 2024</a>
                            </div>
                            <div class="trending-item-meta">
                                <span class="trending-author">by ProGamer123</span>
                                <span class="trending-category">Cheat Discussion</span>
                            </div>
                        </div>
                        <div class="trending-item">
                            <div class="trending-item-title">
                                <a href="#">💎 Premium Features Guide - Everything You Need to Know</a>
                            </div>
                            <div class="trending-item-meta">
                                <span class="trending-author">by Admin</span>
                                <span class="trending-category">Support & Help</span>
                            </div>
                        </div>
                        <div class="trending-item">
                            <div class="trending-item-title">
                                <a href="#">🎯 New Update v2.1.0 - Changelog & Discussion</a>
                            </div>
                            <div class="trending-item-meta">
                                <span class="trending-author">by Developer</span>
                                <span class="trending-category">General Discussion</span>
                            </div>
                        </div>
                        <div class="trending-item">
                            <div class="trending-item-title">
                                <a href="#">🏆 Tournament Results & Highlights</a>
                            </div>
                            <div class="trending-item-meta">
                                <span class="trending-author">by TournamentMaster</span>
                                <span class="trending-category">Tournaments</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== PREMIUM MAIN CONTENT ===== -->
<section class="main-content-section">
    <div class="container">
        <div class="row">
            <!-- Main Content Area -->
            <div class="col-lg-8">
                <!-- Forum Categories -->
                <div class="content-section animate-fade-in-up" style="animation-delay: 0.6s;">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fas fa-comments me-2"></i>Forum Categories
                        </h2>
                        <p class="section-subtitle">Explore our community discussions</p>
                    </div>
                    
                    <?php if (!empty($categories)): ?>
                        <div class="categories-grid">
                            <?php foreach ($categories as $category): ?>
                                <div class="category-card">
                                    <div class="category-header">
                                        <div class="category-icon">
                                            <i class="<?php echo getCategoryIcon($category['name']); ?>"></i>
                                        </div>
                                        <div class="category-info">
                                            <h3 class="category-title">
                                                <a href="category.php?id=<?php echo $category['id']; ?>">
                                                    <?php echo htmlspecialchars($category['name']); ?>
                                                </a>
                                            </h3>
                                            <p class="category-description">
                                                <?php echo htmlspecialchars($category['description'] ?? 'No description available'); ?>
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <?php if (!empty($category['subforums'])): ?>
                                        <div class="subforums-list">
                                            <?php foreach (array_slice($category['subforums'], 0, 3) as $subforum): ?>
                                                <div class="subforum-item">
                                                    <a href="subforum.php?id=<?php echo $subforum['id']; ?>" class="subforum-link">
                                                        <i class="fas fa-folder me-2"></i>
                                                        <?php echo htmlspecialchars($subforum['name']); ?>
                                                    </a>
                                                    <span class="subforum-stats">
                                                        <?php echo number_format($subforum['thread_count'] ?? 0); ?> threads
                                                    </span>
                                                </div>
                                            <?php endforeach; ?>
                                            
                                            <?php if (count($category['subforums']) > 3): ?>
                                                <div class="subforum-more">
                                                    <a href="category.php?id=<?php echo $category['id']; ?>" class="text-muted">
                                                        +<?php echo count($category['subforums']) - 3; ?> more
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="category-stats">
                                        <div class="stat-item">
                                            <i class="fas fa-comments"></i>
                                            <span><?php echo number_format($category['thread_count'] ?? 0); ?> threads</span>
                                        </div>
                                        <div class="stat-item">
                                            <i class="fas fa-reply"></i>
                                            <span><?php echo number_format($category['post_count'] ?? 0); ?> posts</span>
                                        </div>
                                        <div class="stat-item">
                                            <i class="fas fa-eye"></i>
                                            <span><?php echo number_format($category['view_count'] ?? 0); ?> views</span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-comments"></i>
                            </div>
                            <h3>No Categories Available</h3>
                            <p>Forum categories will appear here once they are created by administrators.</p>
                            <?php if (isAdmin()): ?>
                                <a href="admin/forums.php" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Create Categories
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Recent Activity -->
                <div class="content-section animate-fade-in-up" style="animation-delay: 0.7s;">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fas fa-clock me-2"></i>Recent Activity
                        </h2>
                        <p class="section-subtitle">Latest discussions and updates</p>
                    </div>
                    
                    <?php if (!empty($recent_threads)): ?>
                        <div class="activity-timeline">
                            <?php foreach ($recent_threads as $thread): ?>
                                <div class="timeline-item">
                                    <div class="timeline-icon">
                                        <i class="fas fa-comment"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <div class="timeline-header">
                                            <h4 class="timeline-title">
                                                <a href="thread.php?id=<?php echo $thread['id']; ?>">
                                                    <?php echo htmlspecialchars($thread['title']); ?>
                                                </a>
                                            </h4>
                                            <span class="timeline-time">
                                                <?php echo formatTimeAgo($thread['created_at']); ?>
                                            </span>
                                        </div>
                                        <div class="timeline-meta">
                                            <span class="timeline-author">
                                                by <a href="profile.php?username=<?php echo urlencode($thread['username']); ?>">
                                                    <?php echo htmlspecialchars($thread['username']); ?>
                                                </a>
                                            </span>
                                            <span class="timeline-category">
                                                in <a href="category.php?id=<?php echo $thread['category_id']; ?>">
                                                    <?php echo htmlspecialchars($thread['category_name']); ?>
                                                </a>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="timeline-footer">
                            <a href="forum.php" class="btn btn-outline-primary">
                                <i class="fas fa-eye me-2"></i>View All Activity
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h3>No Recent Activity</h3>
                            <p>Recent discussions will appear here once users start creating threads.</p>
                            <?php if (isLoggedIn()): ?>
                                <a href="new-thread.php" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Start a Discussion
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Premium Sidebar -->
            <div class="col-lg-4">
                <!-- Premium Features -->
                <div class="sidebar-section animate-fade-in-right" style="animation-delay: 0.4s;">
                    <div class="section-header">
                        <h3 class="section-title">
                            <i class="fas fa-crown me-2"></i>Premium Features
                        </h3>
                    </div>
                    
                    <div class="premium-features-list">
                        <div class="premium-feature">
                            <div class="feature-icon">
                                <i class="fas fa-palette"></i>
                            </div>
                            <div class="feature-content">
                                <h4>Custom Themes</h4>
                                <p>Choose from multiple premium themes</p>
                            </div>
                        </div>
                        
                        <div class="premium-feature">
                            <div class="feature-icon">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div class="feature-content">
                                <h4>Smart Notifications</h4>
                                <p>Get notified about important updates</p>
                            </div>
                        </div>
                        
                        <div class="premium-feature">
                            <div class="feature-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="feature-content">
                                <h4>Enhanced Security</h4>
                                <p>Advanced protection for your account</p>
                            </div>
                        </div>
                        
                        <div class="premium-feature">
                            <div class="feature-icon">
                                <i class="fas fa-rocket"></i>
                            </div>
                            <div class="feature-content">
                                <h4>Priority Support</h4>
                                <p>Get help faster with premium support</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="premium-cta">
                        <a href="premium.php" class="btn btn-warning btn-lg w-100">
                            <i class="fas fa-crown me-2"></i>Upgrade to Premium
                        </a>
                    </div>
                </div>
                
                <!-- Live Streams -->
                <div class="sidebar-section animate-fade-in-right" style="animation-delay: 0.5s;">
                    <div class="section-header">
                        <h3 class="section-title">
                            <i class="fas fa-broadcast-tower me-2"></i>Live Streams
                        </h3>
                    </div>
                    
                    <div class="streams-placeholder">
                        <div class="stream-item">
                            <div class="stream-thumbnail">
                                <i class="fas fa-video"></i>
                            </div>
                            <div class="stream-info">
                                <h4>CS2 Pro Matches</h4>
                                <p>Watch live professional matches</p>
                                <span class="stream-status online">Live Now</span>
                            </div>
                        </div>
                        
                        <div class="stream-item">
                            <div class="stream-thumbnail">
                                <i class="fas fa-video"></i>
                            </div>
                            <div class="stream-info">
                                <h4>Cheat Showcase</h4>
                                <p>See our cheats in action</p>
                                <span class="stream-status offline">Offline</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="streams-footer">
                        <a href="streams.php" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-2"></i>View All Streams
                        </a>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="sidebar-section animate-fade-in-right" style="animation-delay: 0.6s;">
                    <div class="section-header">
                        <h3 class="section-title">
                            <i class="fas fa-bolt me-2"></i>Quick Actions
                        </h3>
                    </div>
                    
                    <div class="quick-actions-grid">
                        <?php if (isLoggedIn()): ?>
                            <a href="new-thread.php" class="quick-action">
                                <i class="fas fa-plus"></i>
                                <span>New Thread</span>
                            </a>
                            <a href="messages.php" class="quick-action">
                                <i class="fas fa-envelope"></i>
                                <span>Messages</span>
                            </a>
                            <a href="notifications.php" class="quick-action">
                                <i class="fas fa-bell"></i>
                                <span>Notifications</span>
                            </a>
                            <a href="profile.php" class="quick-action">
                                <i class="fas fa-user"></i>
                                <span>Profile</span>
                            </a>
                        <?php else: ?>
                            <a href="login.php" class="quick-action">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>Sign In</span>
                            </a>
                            <a href="register.php" class="quick-action">
                                <i class="fas fa-user-plus"></i>
                                <span>Join Now</span>
                            </a>
                            <a href="forum.php" class="quick-action">
                                <i class="fas fa-comments"></i>
                                <span>Browse Forums</span>
                            </a>
                            <a href="downloads.php" class="quick-action">
                                <i class="fas fa-download"></i>
                                <span>Downloads</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Online Users -->
                <?php if (!empty($online_users)): ?>
                <div class="sidebar-section animate-fade-in-right" style="animation-delay: 0.7s;">
                    <div class="section-header">
                        <h3 class="section-title">
                            <i class="fas fa-users me-2"></i>Online Users
                            <span class="online-count">(<?php echo count($online_users); ?>)</span>
                        </h3>
                    </div>
                    
                    <div class="online-users-list">
                        <?php foreach (array_slice($online_users, 0, 8) as $user): ?>
                            <div class="online-user">
                                <div class="user-avatar">
                                    <img src="images/default-avatar.png" alt="Avatar" class="rounded-circle" width="32" height="32">
                                </div>
                                <div class="user-info">
                                    <a href="profile.php?username=<?php echo urlencode($user['username']); ?>" class="username">
                                        <?php echo htmlspecialchars($user['username']); ?>
                                    </a>
                                    <span class="last-seen">
                                        <?php echo formatTimeAgo($user['last_activity']); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if (count($online_users) > 8): ?>
                        <div class="online-users-footer">
                            <a href="members.php" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-eye me-2"></i>View All Members
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- ===== PREMIUM CALL TO ACTION SECTION ===== -->
<section class="cta-section animate-fade-in-up" style="animation-delay: 0.8s;">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <div class="cta-content">
                    <h2 class="cta-title">Ready to Join the Elite?</h2>
                    <p class="cta-subtitle">Become part of the most advanced CS2 cheat community and dominate the competition.</p>
                    
                    <div class="cta-actions">
                        <?php if (!isLoggedIn()): ?>
                            <a href="register.php" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-rocket me-2"></i>Get Started Now
                            </a>
                            <a href="about.php" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-info-circle me-2"></i>Learn More
                            </a>
                        <?php else: ?>
                            <a href="new-thread.php" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-plus me-2"></i>Start Discussion
                            </a>
                            <a href="forum.php" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-comments me-2"></i>Explore Forums
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>
