<?php
define('SECURE_ACCESS', true);
require_once 'config.php';

// Get forum categories and subforums
$categories = [];
try {
    $stmt = $pdo->prepare("
        SELECT c.*, 
               COUNT(sf.id) as subforum_count,
               COUNT(DISTINCT t.id) as thread_count,
               COUNT(DISTINCT p.id) as post_count
        FROM categories c
        LEFT JOIN subforums sf ON c.id = sf.category_id AND sf.is_active = 1
        LEFT JOIN threads t ON sf.id = t.subforum_id AND t.is_active = 1
        LEFT JOIN posts p ON t.id = p.thread_id AND p.is_active = 1
        WHERE c.is_active = 1
        GROUP BY c.id
        ORDER BY c.display_order, c.name
    ");
    $stmt->execute();
    $categories = $stmt->fetchAll();
} catch (Exception $e) {
    error_log("Failed to get categories: " . $e->getMessage());
}

// Get recent activity
$recent_activity = [];
try {
    $stmt = $pdo->prepare("
        SELECT 'thread' as type, t.id, t.title, t.slug, t.created_at, t.view_count,
               u.username, u.avatar, u.rank,
               sf.name as subforum_name, c.name as category_name
        FROM threads t
        JOIN users u ON t.user_id = u.id
        JOIN subforums sf ON t.subforum_id = sf.id
        JOIN categories c ON sf.category_id = c.id
        WHERE t.is_active = 1
        UNION ALL
        SELECT 'post' as type, p.id, CONCAT('Re: ', t.title) as title, t.slug, p.created_at, 0 as view_count,
               u.username, u.avatar, u.rank,
               sf.name as subforum_name, c.name as category_name
        FROM posts p
        JOIN threads t ON p.thread_id = t.id
        JOIN users u ON p.user_id = u.id
        JOIN subforums sf ON t.subforum_id = sf.id
        JOIN categories c ON sf.category_id = c.id
        WHERE p.is_active = 1 AND p.is_first_post = 0
        ORDER BY created_at DESC
        LIMIT 20
    ");
    $stmt->execute();
    $recent_activity = $stmt->fetchAll();
} catch (Exception $e) {
    error_log("Failed to get recent activity: " . $e->getMessage());
}

// Get forum statistics
$stats = getForumStats();

// Set page variables
$page_title = 'Forums - ' . SITE_NAME;
$page_description = 'Browse all forum categories and discussions';
$breadcrumbs = [
    ['text' => 'Forums']
];

// Include header
include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section forum-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="hero-title">
                    <i class="fas fa-comments"></i>
                    Welcome to Affinity Forums
                </h1>
                <p class="hero-subtitle">
                    Join the ultimate Counter-Strike 2 community. Share strategies, discuss cheats, 
                    and connect with players from around the world.
                </p>
                <div class="hero-actions">
                    <?php if (isLoggedIn()): ?>
                        <a href="new-thread.php" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-plus"></i> Create Thread
                        </a>
                        <a href="tournaments.php" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-trophy"></i> Join Tournament
                        </a>
                    <?php else: ?>
                        <a href="register.php" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-user-plus"></i> Join Community
                        </a>
                        <a href="login.php" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-sign-in-alt"></i> Sign In
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="hero-stats">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo number_format($stats['total_users']); ?></div>
                        <div class="stat-label">Members</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?php echo number_format($stats['total_threads']); ?></div>
                        <div class="stat-label">Threads</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?php echo number_format($stats['total_posts']); ?></div>
                        <div class="stat-label">Posts</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Stats Section -->
<section class="quick-stats-section">
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo number_format($stats['online_users']); ?></div>
                        <div class="stat-label">Online Now</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo number_format($stats['new_users_today']); ?></div>
                        <div class="stat-label">New Today</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-comment"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo number_format($stats['new_posts_today']); ?></div>
                        <div class="stat-label">Posts Today</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo number_format($stats['total_views']); ?></div>
                        <div class="stat-label">Total Views</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Content Section -->
<section class="main-content-section">
    <div class="container">
        <div class="row">
            <!-- Forum Categories -->
            <div class="col-lg-8">
                <div class="content-card">
                    <div class="card-header">
                        <h2><i class="fas fa-layer-group"></i> Forum Categories</h2>
                        <div class="card-actions">
                            <button class="btn btn-sm btn-outline-primary" onclick="toggleView('grid')">
                                <i class="fas fa-th"></i> Grid
                            </button>
                            <button class="btn btn-sm btn-outline-primary active" onclick="toggleView('list')">
                                <i class="fas fa-list"></i> List
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="forum-categories" id="forum-view">
                            <?php foreach ($categories as $category): ?>
                                <div class="category-card">
                                    <div class="category-header">
                                        <div class="category-icon">
                                            <i class="<?php echo getCategoryIcon($category['name']); ?>"></i>
                                        </div>
                                        <div class="category-info">
                                            <h3 class="category-title">
                                                <a href="category.php?slug=<?php echo urlencode($category['slug']); ?>">
                                                    <?php echo htmlspecialchars($category['name']); ?>
                                                </a>
                                            </h3>
                                            <p class="category-description">
                                                <?php echo htmlspecialchars($category['description']); ?>
                                            </p>
                                        </div>
                                        <div class="category-stats">
                                            <div class="stat">
                                                <span class="stat-number"><?php echo number_format($category['subforum_count']); ?></span>
                                                <span class="stat-label">Forums</span>
                                            </div>
                                            <div class="stat">
                                                <span class="stat-number"><?php echo number_format($category['thread_count']); ?></span>
                                                <span class="stat-label">Threads</span>
                                            </div>
                                            <div class="stat">
                                                <span class="stat-number"><?php echo number_format($category['post_count']); ?></span>
                                                <span class="stat-label">Posts</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <?php
                                    // Get subforums for this category
                                    $subforums = [];
                                    try {
                                        $stmt = $pdo->prepare("
                                            SELECT sf.*, 
                                                   COUNT(t.id) as thread_count,
                                                   COUNT(p.id) as post_count,
                                                   t.title as last_thread_title,
                                                   t.slug as last_thread_slug,
                                                   t.created_at as last_thread_date,
                                                   u.username as last_thread_user
                                            FROM subforums sf
                                            LEFT JOIN threads t ON sf.id = t.subforum_id AND t.is_active = 1
                                            LEFT JOIN posts p ON t.id = p.thread_id AND p.is_active = 1
                                            WHERE sf.category_id = ? AND sf.is_active = 1
                                            GROUP BY sf.id
                                            ORDER BY sf.display_order, sf.name
                                        ");
                                        $stmt->execute([$category['id']]);
                                        $subforums = $stmt->fetchAll();
                                    } catch (Exception $e) {
                                        error_log("Failed to get subforums: " . $e->getMessage());
                                    }
                                    ?>
                                    
                                    <?php if (!empty($subforums)): ?>
                                        <div class="subforums-list">
                                            <?php foreach ($subforums as $subforum): ?>
                                                <div class="subforum-item">
                                                    <div class="subforum-info">
                                                        <h4 class="subforum-title">
                                                            <a href="subforum.php?slug=<?php echo urlencode($subforum['slug']); ?>">
                                                                <?php echo htmlspecialchars($subforum['name']); ?>
                                                            </a>
                                                        </h4>
                                                        <p class="subforum-description">
                                                            <?php echo htmlspecialchars($subforum['description']); ?>
                                                        </p>
                                                    </div>
                                                    <div class="subforum-stats">
                                                        <div class="stat">
                                                            <span class="stat-number"><?php echo number_format($subforum['thread_count']); ?></span>
                                                            <span class="stat-label">Threads</span>
                                                        </div>
                                                        <div class="stat">
                                                            <span class="stat-number"><?php echo number_format($subforum['post_count']); ?></span>
                                                            <span class="stat-label">Posts</span>
                                                        </div>
                                                    </div>
                                                    <?php if ($subforum['last_thread_title']): ?>
                                                        <div class="subforum-last-activity">
                                                            <small class="text-muted">
                                                                Last: <a href="thread.php?slug=<?php echo urlencode($subforum['last_thread_slug']); ?>">
                                                                    <?php echo htmlspecialchars(substr($subforum['last_thread_title'], 0, 50)); ?>
                                                                </a>
                                                                by <?php echo htmlspecialchars($subforum['last_thread_user']); ?>
                                                                <span class="time-ago"><?php echo formatTimeAgo($subforum['last_thread_date']); ?></span>
                                                            </small>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <?php include 'includes/sidebar.php'; ?>
                
                <!-- Premium Features -->
                <div class="content-card">
                    <div class="card-header">
                        <h3><i class="fas fa-crown"></i> Premium Features</h3>
                    </div>
                    <div class="card-body">
                        <div class="premium-features">
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div class="feature-content">
                                    <h4>VIP Access</h4>
                                    <p>Exclusive content and early access to new features</p>
                                </div>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-palette"></i>
                                </div>
                                <div class="feature-content">
                                    <h4>Custom Themes</h4>
                                    <p>Personalize your forum experience with custom colors</p>
                                </div>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-bell"></i>
                                </div>
                                <div class="feature-content">
                                    <h4>Priority Notifications</h4>
                                    <p>Get instant alerts for important updates</p>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <a href="premium.php" class="btn btn-warning btn-sm">
                                <i class="fas fa-crown"></i> Upgrade to Premium
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Live Streams -->
                <div class="content-card">
                    <div class="card-header">
                        <h3><i class="fas fa-broadcast-tower"></i> Live Streams</h3>
                    </div>
                    <div class="card-body">
                        <div class="live-streams">
                            <div class="stream-item">
                                <div class="stream-thumbnail">
                                    <img src="https://via.placeholder.com/300x200/ff6b35/ffffff?text=CS2+Stream" alt="CS2 Stream" class="img-fluid">
                                    <div class="stream-live-badge">LIVE</div>
                                </div>
                                <div class="stream-info">
                                    <h5>CS2 Pro Tournament</h5>
                                    <p class="streamer">by ProPlayer123</p>
                                    <p class="viewers">1.2K viewers</p>
                                </div>
                            </div>
                            <div class="stream-item">
                                <div class="stream-thumbnail">
                                    <img src="https://via.placeholder.com/300x200/48bb78/ffffff?text=Cheat+Showcase" alt="Cheat Showcase" class="img-fluid">
                                    <div class="stream-live-badge">LIVE</div>
                                </div>
                                <div class="stream-info">
                                    <h5>Cheat Showcase</h5>
                                    <p class="streamer">by CheatMaster</p>
                                    <p class="viewers">856 viewers</p>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <a href="streams.php" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-play"></i> View All Streams
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="content-card">
                    <div class="card-header">
                        <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
                    </div>
                    <div class="card-body">
                        <div class="quick-actions">
                            <a href="new-thread.php" class="btn btn-primary btn-block mb-2">
                                <i class="fas fa-plus"></i> Create Thread
                            </a>
                            <a href="search.php" class="btn btn-outline-primary btn-block mb-2">
                                <i class="fas fa-search"></i> Search Forums
                            </a>
                            <a href="tournaments.php" class="btn btn-outline-success btn-block mb-2">
                                <i class="fas fa-trophy"></i> Join Tournament
                            </a>
                            <a href="download.php" class="btn btn-outline-warning btn-block">
                                <i class="fas fa-download"></i> Download Cheats
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Recent Activity Section -->
<section class="recent-activity-section">
    <div class="container">
        <div class="content-card">
            <div class="card-header">
                <h2><i class="fas fa-history"></i> Recent Activity</h2>
                <div class="card-actions">
                    <button class="btn btn-sm btn-outline-primary" onclick="refreshActivity()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="recent-activity" id="activity-feed">
                    <?php foreach ($recent_activity as $activity): ?>
                        <div class="timeline-item">
                            <div class="timeline-icon">
                                <i class="fas fa-<?php echo $activity['type'] === 'thread' ? 'file-alt' : 'comment'; ?>"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="timeline-header">
                                    <h5 class="timeline-title">
                                        <a href="thread.php?slug=<?php echo urlencode($activity['slug']); ?>">
                                            <?php echo htmlspecialchars($activity['title']); ?>
                                        </a>
                                    </h5>
                                    <span class="timeline-time"><?php echo formatTimeAgo($activity['created_at']); ?></span>
                                </div>
                                <div class="timeline-meta">
                                    <span class="user-info">
                                        <img src="<?php echo getUserAvatar($activity['username']); ?>" alt="Avatar" class="avatar-sm">
                                        <a href="profile.php?user=<?php echo urlencode($activity['username']); ?>">
                                            <?php echo htmlspecialchars($activity['username']); ?>
                                        </a>
                                        <?php if ($activity['rank']): ?>
                                            <span class="user-rank"><?php echo htmlspecialchars($activity['rank']); ?></span>
                                        <?php endif; ?>
                                    </span>
                                    <span class="forum-info">
                                        in <a href="subforum.php?slug=<?php echo urlencode($activity['subforum_name']); ?>">
                                            <?php echo htmlspecialchars($activity['subforum_name']); ?>
                                        </a>
                                    </span>
                                </div>
                                <?php if ($activity['type'] === 'thread' && $activity['view_count'] > 0): ?>
                                    <div class="timeline-stats">
                                        <small class="text-muted">
                                            <i class="fas fa-eye"></i> <?php echo number_format($activity['view_count']); ?> views
                                        </small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="cta-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2>Ready to Join the Discussion?</h2>
                <p class="lead">
                    Connect with thousands of CS2 players, share your strategies, and stay updated with the latest cheats and tournaments.
                </p>
                <div class="cta-actions">
                    <?php if (isLoggedIn()): ?>
                        <a href="new-thread.php" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-plus"></i> Start a Thread
                        </a>
                        <a href="tournaments.php" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-trophy"></i> Join Tournament
                        </a>
                    <?php else: ?>
                        <a href="register.php" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-user-plus"></i> Join Now
                        </a>
                        <a href="login.php" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-sign-in-alt"></i> Sign In
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Forum view toggle functionality
function toggleView(view) {
    const forumView = document.getElementById('forum-view');
    const buttons = document.querySelectorAll('.card-actions .btn');
    
    // Update button states
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    
    // Apply view class
    forumView.className = `forum-categories view-${view}`;
    
    // Save preference
    localStorage.setItem('forumView', view);
}

// Refresh activity feed
function refreshActivity() {
    const button = event.target;
    const icon = button.querySelector('i');
    
    // Add loading state
    button.disabled = true;
    icon.className = 'fas fa-spinner fa-spin';
    
    // Simulate refresh (replace with actual AJAX call)
    setTimeout(() => {
        button.disabled = false;
        icon.className = 'fas fa-sync-alt';
        
        // Show success notification
        showNotification('Activity feed refreshed!', 'success');
    }, 1000);
}

// Initialize forum view
document.addEventListener('DOMContentLoaded', function() {
    const savedView = localStorage.getItem('forumView') || 'list';
    toggleView(savedView);
    
    // Add smooth animations
    const categoryCards = document.querySelectorAll('.category-card');
    categoryCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.classList.add('fadeInUp');
    });
    
    // Add hover effects
    const subforumItems = document.querySelectorAll('.subforum-item');
    subforumItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(10px)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
    });
});

// Enhanced search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="q"]');
    if (searchInput) {
        let searchTimeout;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length >= 2) {
                searchTimeout = setTimeout(() => {
                    performForumSearch(query);
                }, 300);
            }
        });
    }
});

function performForumSearch(query) {
    // Implement live forum search
    console.log('Searching forums for:', query);
    
    // Show search suggestions
    const suggestions = [
        'CS2 cheats',
        'Tournament discussion',
        'Strategy guides',
        'Cheat updates'
    ];
    
    // Filter suggestions based on query
    const filtered = suggestions.filter(s => s.toLowerCase().includes(query.toLowerCase()));
    
    if (filtered.length > 0) {
        showSearchSuggestions(filtered);
    }
}

function showSearchSuggestions(suggestions) {
    // Remove existing suggestions
    const existing = document.querySelector('.search-suggestions');
    if (existing) {
        existing.remove();
    }
    
    // Create suggestions dropdown
    const dropdown = document.createElement('div');
    dropdown.className = 'search-suggestions dropdown-menu show';
    dropdown.style.cssText = `
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 1000;
        max-height: 200px;
        overflow-y: auto;
    `;
    
    suggestions.forEach(suggestion => {
        const item = document.createElement('a');
        item.className = 'dropdown-item';
        item.href = `search.php?q=${encodeURIComponent(suggestion)}`;
        item.textContent = suggestion;
        dropdown.appendChild(item);
    });
    
    // Add to search container
    const searchContainer = document.querySelector('.input-group');
    searchContainer.appendChild(dropdown);
    
    // Auto-hide on outside click
    document.addEventListener('click', function hideSuggestions(e) {
        if (!searchContainer.contains(e.target)) {
            dropdown.remove();
            document.removeEventListener('click', hideSuggestions);
        }
    });
}

// Performance monitoring
document.addEventListener('DOMContentLoaded', function() {
    // Monitor forum load performance
    const startTime = performance.now();
    
    window.addEventListener('load', function() {
        const loadTime = performance.now() - startTime;
        
        if (loadTime > 2000) {
            console.warn('Forum page load time is slow:', loadTime.toFixed(2) + 'ms');
        }
        
        // Send performance data to analytics
        if (typeof gtag !== 'undefined') {
            gtag('event', 'timing_complete', {
                name: 'forum_load',
                value: Math.round(loadTime)
            });
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
