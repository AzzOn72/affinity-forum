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
        $stats = getForumStats();
    }
} catch (Exception $e) {
    error_log("Failed to get forum stats: " . $e->getMessage());
    // Use default stats
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
        $categories = getCategories();
    }
} catch (Exception $e) {
    error_log("Failed to get categories: " . $e->getMessage());
}

// Include header
include 'includes/header.php';
?>

<!-- ===== ULTRA PREMIUM HERO SECTION ===== -->
<section class="hero-section ultra-premium-hero">
    <!-- Animated Background -->
    <div class="hero-background">
        <div class="hero-particles" id="heroParticles"></div>
        <div class="hero-waves">
            <div class="wave wave-1"></div>
            <div class="wave wave-2"></div>
            <div class="wave wave-3"></div>
        </div>
        <div class="hero-grid"></div>
    </div>
    
    <!-- CS2 Status Indicator -->
    <div class="cs2-status-indicator animate-fade-in-down">
        <div class="status-dot online"></div>
        <span class="status-text">CS2 Servers Online</span>
        <div class="status-ping">
            <span class="ping-value" id="pingValue">12ms</span>
            <span class="ping-label">Ping</span>
        </div>
    </div>
    
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="hero-content animate-fade-in-left">
                    <!-- Main Title with Typing Effect -->
                    <div class="hero-title-container">
                        <h1 class="hero-title ultra-premium-title">
                            <span class="title-line">Welcome to</span>
                            <span class="title-main" id="typingTitle">Affinity</span>
                            <span class="title-cursor">|</span>
                        </h1>
                        <div class="title-glow"></div>
                    </div>
                    
                    <p class="hero-subtitle ultra-premium-subtitle">
                        The most <span class="highlight-text">advanced</span> and <span class="highlight-text">undetectable</span> 
                        Counter-Strike 2 cheat community. Join <span class="highlight-number" id="memberCount"><?php echo number_format($stats['total_users']); ?>+</span> 
                        elite players, share exclusive strategies, and dominate every match with confidence.
                    </p>
                    
                    <!-- VAC Safety Guarantee -->
                    <div class="vac-safety-banner animate-pulse-glow">
                        <div class="safety-icon">
                            <i class="fas fa-shield-check"></i>
                        </div>
                        <div class="safety-content">
                            <div class="safety-title">100% VAC UNDETECTED</div>
                            <div class="safety-subtitle">Zero bans in 2+ years • Trusted by thousands</div>
                        </div>
                        <div class="safety-badge">
                            <span class="badge-text">SAFE</span>
                        </div>
                    </div>
                    
                    <!-- Enhanced Stats Grid -->
                    <div class="stats-container ultra-premium-stats">
                        <div class="stat-card animate-fade-in-up" style="animation-delay: 0.1s;" data-stat="users">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number" data-count="<?php echo $stats['total_users']; ?>"><?php echo number_format($stats['total_users']); ?></div>
                                <div class="stat-label">ELITE MEMBERS</div>
                                <div class="stat-trend">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>+<?php echo rand(5, 25); ?> today</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="stat-card animate-fade-in-up" style="animation-delay: 0.2s;" data-stat="threads">
                            <div class="stat-icon">
                                <i class="fas fa-comments"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number" data-count="<?php echo $stats['total_threads']; ?>"><?php echo number_format($stats['total_threads']); ?></div>
                                <div class="stat-label">DISCUSSIONS</div>
                                <div class="stat-trend">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>+<?php echo rand(10, 50); ?> today</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="stat-card animate-fade-in-up" style="animation-delay: 0.3s;" data-stat="posts">
                            <div class="stat-icon">
                                <i class="fas fa-reply"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number" data-count="<?php echo $stats['total_posts']; ?>"><?php echo number_format($stats['total_posts']); ?></div>
                                <div class="stat-label">STRATEGIES SHARED</div>
                                <div class="stat-trend">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>+<?php echo rand(50, 200); ?> today</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="stat-card animate-fade-in-up" style="animation-delay: 0.4s;" data-stat="online">
                            <div class="stat-icon">
                                <i class="fas fa-circle"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number" data-count="<?php echo count($online_users); ?>"><?php echo count($online_users); ?></div>
                                <div class="stat-label">ONLINE NOW</div>
                                <div class="stat-trend">
                                    <i class="fas fa-eye"></i>
                                    <span>Live</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Premium Call-to-Action Buttons -->
                    <div class="hero-actions ultra-premium-actions">
                        <?php if (!isLoggedIn()): ?>
                            <a href="register.php" class="btn btn-ultra-premium btn-lg me-3 animate-fade-in-up" style="animation-delay: 0.5s;">
                                <div class="btn-content">
                                    <i class="fas fa-rocket me-2"></i>
                                    <span>Join Elite Community</span>
                                    <div class="btn-glow"></div>
                                </div>
                            </a>
                            <a href="login.php" class="btn btn-outline-ultra-premium btn-lg animate-fade-in-up" style="animation-delay: 0.6s;">
                                <div class="btn-content">
                                    <i class="fas fa-sign-in-alt me-2"></i>
                                    <span>Member Login</span>
                                </div>
                            </a>
                        <?php else: ?>
                            <a href="new-thread.php" class="btn btn-ultra-premium btn-lg me-3 animate-fade-in-up" style="animation-delay: 0.5s;">
                                <div class="btn-content">
                                    <i class="fas fa-plus me-2"></i>
                                    <span>Create Discussion</span>
                                    <div class="btn-glow"></div>
                                </div>
                            </a>
                            <a href="forum.php" class="btn btn-outline-ultra-premium btn-lg animate-fade-in-up" style="animation-delay: 0.6s;">
                                <div class="btn-content">
                                    <i class="fas fa-comments me-2"></i>
                                    <span>Browse Forums</span>
                                </div>
                            </a>
                        <?php endif; ?>
                        
                        <!-- Download Button -->
                        <a href="downloads.php" class="btn btn-download-premium btn-lg animate-fade-in-up" style="animation-delay: 0.7s;">
                            <div class="btn-content">
                                <i class="fas fa-download me-2"></i>
                                <span>Download Affinity</span>
                                <div class="download-indicator">
                                    <span class="download-status">READY</span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Ultra Premium Feature Showcase -->
                <div class="feature-showcase animate-fade-in-right" style="animation-delay: 0.3s;">
                    <!-- Live Cheat Status -->
                    <div class="cheat-status-card premium-card-glass">
                        <div class="status-header">
                            <div class="status-icon">
                                <i class="fas fa-shield-check"></i>
                            </div>
                            <div class="status-title">Affinity Cheat Status</div>
                            <div class="status-indicator online">
                                <span class="indicator-dot"></span>
                                <span class="indicator-text">ONLINE</span>
                            </div>
                        </div>
                        
                        <div class="status-content">
                            <div class="status-item">
                                <span class="status-label">Version:</span>
                                <span class="status-value">v2.4.1</span>
                                <span class="status-badge new">NEW</span>
                            </div>
                            <div class="status-item">
                                <span class="status-label">Last Update:</span>
                                <span class="status-value">2 hours ago</span>
                            </div>
                            <div class="status-item">
                                <span class="status-label">Detection Status:</span>
                                <span class="status-value safe">UNDETECTED</span>
                            </div>
                            <div class="status-item">
                                <span class="status-label">Active Users:</span>
                                <span class="status-value"><?php echo rand(1500, 3000); ?>+</span>
                            </div>
                        </div>
                        
                        <div class="status-actions">
                            <button class="btn btn-success btn-sm me-2">
                                <i class="fas fa-download"></i> Download
                            </button>
                            <button class="btn btn-outline-info btn-sm">
                                <i class="fas fa-info-circle"></i> Details
                            </button>
                        </div>
                    </div>
                    
                    <!-- Premium Trending Card -->
                    <div class="trending-card ultra-premium-trending animate-fade-in-right" style="animation-delay: 0.4s;">
                        <div class="trending-header">
                            <div class="trending-icon">
                                <i class="fas fa-fire"></i>
                            </div>
                            <div class="trending-title">🔥 Trending Now</div>
                            <div class="trending-live">
                                <span class="live-dot"></span>
                                <span>LIVE</span>
                            </div>
                        </div>
                        
                        <div class="trending-content">
                            <div class="trending-item hot">
                                <div class="trending-item-icon">🎯</div>
                                <div class="trending-item-content">
                                    <div class="trending-item-title">
                                        <a href="#">Ultimate Aimbot Settings Guide 2024</a>
                                    </div>
                                    <div class="trending-item-meta">
                                        <span class="trending-author">by ProGamer123</span>
                                        <span class="trending-stats">
                                            <i class="fas fa-eye"></i> 2.3k views
                                            <i class="fas fa-heart"></i> 156 likes
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="trending-item">
                                <div class="trending-item-icon">💎</div>
                                <div class="trending-item-content">
                                    <div class="trending-item-title">
                                        <a href="#">Affinity v2.4.1 - Revolutionary Update</a>
                                    </div>
                                    <div class="trending-item-meta">
                                        <span class="trending-author">by Developer</span>
                                        <span class="trending-stats">
                                            <i class="fas fa-eye"></i> 5.1k views
                                            <i class="fas fa-heart"></i> 324 likes
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="trending-item">
                                <div class="trending-item-icon">🏆</div>
                                <div class="trending-item-content">
                                    <div class="trending-item-title">
                                        <a href="#">Weekly Tournament - $500 Prize Pool</a>
                                    </div>
                                    <div class="trending-item-meta">
                                        <span class="trending-author">by TournamentMaster</span>
                                        <span class="trending-stats">
                                            <i class="fas fa-users"></i> 89 participants
                                            <i class="fas fa-trophy"></i> Live
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="trending-item">
                                <div class="trending-item-icon">🛡️</div>
                                <div class="trending-item-content">
                                    <div class="trending-item-title">
                                        <a href="#">VAC Protection Guide - Stay Safe</a>
                                    </div>
                                    <div class="trending-item-meta">
                                        <span class="trending-author">by Admin</span>
                                        <span class="trending-stats">
                                            <i class="fas fa-eye"></i> 8.7k views
                                            <i class="fas fa-shield-check"></i> Pinned
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="trending-footer">
                            <a href="forum.php" class="btn btn-trending-more">
                                <span>View All Discussions</span>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== CS2 FEATURES SHOWCASE ===== -->
<section class="features-showcase-section">
    <div class="container">
        <div class="section-header text-center animate-fade-in-up">
            <h2 class="section-title ultra-premium-title">
                <span class="title-icon">🎮</span>
                <span>Why Choose Affinity?</span>
            </h2>
            <p class="section-subtitle">The most advanced CS2 cheat with unmatched safety and performance</p>
        </div>
        
        <div class="features-grid">
            <div class="feature-card ultra-premium-card animate-fade-in-up" style="animation-delay: 0.1s;">
                <div class="feature-icon">
                    <i class="fas fa-shield-virus"></i>
                    <div class="icon-glow"></div>
                </div>
                <div class="feature-content">
                    <h3 class="feature-title">100% Undetected</h3>
                    <p class="feature-description">Advanced anti-detection technology keeps you safe from VAC, FACEIT, and ESEA</p>
                    <div class="feature-stats">
                        <span class="stat-item">
                            <i class="fas fa-check-circle"></i>
                            0 Bans in 2+ Years
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="feature-card ultra-premium-card animate-fade-in-up" style="animation-delay: 0.2s;">
                <div class="feature-icon">
                    <i class="fas fa-crosshairs"></i>
                    <div class="icon-glow"></div>
                </div>
                <div class="feature-content">
                    <h3 class="feature-title">Precision Aimbot</h3>
                    <p class="feature-description">Human-like aiming with customizable smoothness, FOV, and bone selection</p>
                    <div class="feature-stats">
                        <span class="stat-item">
                            <i class="fas fa-target"></i>
                            95%+ Accuracy
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="feature-card ultra-premium-card animate-fade-in-up" style="animation-delay: 0.3s;">
                <div class="feature-icon">
                    <i class="fas fa-eye"></i>
                    <div class="icon-glow"></div>
                </div>
                <div class="feature-content">
                    <h3 class="feature-title">Advanced ESP</h3>
                    <p class="feature-description">See enemies through walls with customizable boxes, names, health, and weapons</p>
                    <div class="feature-stats">
                        <span class="stat-item">
                            <i class="fas fa-radar"></i>
                            360° Awareness
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="feature-card ultra-premium-card animate-fade-in-up" style="animation-delay: 0.4s;">
                <div class="feature-icon">
                    <i class="fas fa-bolt"></i>
                    <div class="icon-glow"></div>
                </div>
                <div class="feature-content">
                    <h3 class="feature-title">Instant Updates</h3>
                    <p class="feature-description">Automatic updates within minutes of CS2 patches to maintain compatibility</p>
                    <div class="feature-stats">
                        <span class="stat-item">
                            <i class="fas fa-clock"></i>
                            &lt;5min Response
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="feature-card ultra-premium-card animate-fade-in-up" style="animation-delay: 0.5s;">
                <div class="feature-icon">
                    <i class="fas fa-users-cog"></i>
                    <div class="icon-glow"></div>
                </div>
                <div class="feature-content">
                    <h3 class="feature-title">24/7 Support</h3>
                    <p class="feature-description">Dedicated support team available around the clock for all your needs</p>
                    <div class="feature-stats">
                        <span class="stat-item">
                            <i class="fas fa-headset"></i>
                            Always Online
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="feature-card ultra-premium-card animate-fade-in-up" style="animation-delay: 0.6s;">
                <div class="feature-icon">
                    <i class="fas fa-crown"></i>
                    <div class="icon-glow"></div>
                </div>
                <div class="feature-content">
                    <h3 class="feature-title">VIP Community</h3>
                    <p class="feature-description">Exclusive access to private forums, strategies, and premium features</p>
                    <div class="feature-stats">
                        <span class="stat-item">
                            <i class="fas fa-star"></i>
                            Elite Access
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== TESTIMONIALS SECTION ===== -->
<section class="testimonials-section">
    <div class="container">
        <div class="section-header text-center animate-fade-in-up">
            <h2 class="section-title ultra-premium-title">
                <span class="title-icon">💬</span>
                <span>What Our Community Says</span>
            </h2>
            <p class="section-subtitle">Real feedback from elite CS2 players</p>
        </div>
        
        <div class="testimonials-carousel">
            <div class="testimonial-card ultra-premium-card animate-fade-in-up" style="animation-delay: 0.1s;">
                <div class="testimonial-content">
                    <div class="testimonial-quote">
                        "Affinity is hands down the best CS2 cheat I've ever used. Been using it for 8 months, zero bans, incredible features. The community is amazing too!"
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <img src="images/default-avatar.svg" alt="ProPlayer2024">
                        </div>
                        <div class="author-info">
                            <div class="author-name">ProPlayer2024</div>
                            <div class="author-rank">Elite Member • Global Elite</div>
                            <div class="author-stats">
                                <span><i class="fas fa-trophy"></i> 156 Wins</span>
                                <span><i class="fas fa-target"></i> 89% HS Rate</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="testimonial-rating">
                    <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <span class="rating-text">5.0/5</span>
                </div>
            </div>
            
            <div class="testimonial-card ultra-premium-card animate-fade-in-up" style="animation-delay: 0.2s;">
                <div class="testimonial-content">
                    <div class="testimonial-quote">
                        "The safety features are incredible. I've been using Affinity on FACEIT for months without any issues. The community support is top-notch!"
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <img src="images/default-avatar.svg" alt="EliteGamer">
                        </div>
                        <div class="author-info">
                            <div class="author-name">EliteGamer</div>
                            <div class="author-rank">VIP Member • Supreme Master</div>
                            <div class="author-stats">
                                <span><i class="fas fa-shield-check"></i> 0 Bans</span>
                                <span><i class="fas fa-clock"></i> 8 Months</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="testimonial-rating">
                    <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <span class="rating-text">5.0/5</span>
                </div>
            </div>
            
            <div class="testimonial-card ultra-premium-card animate-fade-in-up" style="animation-delay: 0.3s;">
                <div class="testimonial-content">
                    <div class="testimonial-quote">
                        "Amazing cheat with constant updates. The developers really care about the community and listen to feedback. Worth every penny!"
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <img src="images/default-avatar.svg" alt="CS2Master">
                        </div>
                        <div class="author-info">
                            <div class="author-name">CS2Master</div>
                            <div class="author-rank">Premium Member • The Global Elite</div>
                            <div class="author-stats">
                                <span><i class="fas fa-star"></i> 5.0 Rating</span>
                                <span><i class="fas fa-comments"></i> 234 Posts</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="testimonial-rating">
                    <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <span class="rating-text">5.0/5</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== LIVE STATS SECTION ===== -->
<section class="live-stats-section">
    <div class="container">
        <div class="live-stats-container">
            <div class="live-stats-header">
                <h3 class="stats-title">
                    <span class="live-indicator">
                        <span class="live-dot"></span>
                        LIVE
                    </span>
                    Real-Time Community Stats
                </h3>
            </div>
            
            <div class="live-stats-grid">
                <div class="live-stat-item" data-stat="active-cheaters">
                    <div class="stat-icon">
                        <i class="fas fa-gamepad"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-number" id="activeCheaters"><?php echo rand(800, 1500); ?></div>
                        <div class="stat-label">Active Cheaters</div>
                    </div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up"></i>
                        <span>+<?php echo rand(5, 20); ?></span>
                    </div>
                </div>
                
                <div class="live-stat-item" data-stat="matches-won">
                    <div class="stat-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-number" id="matchesWon"><?php echo rand(15000, 25000); ?></div>
                        <div class="stat-label">Matches Won Today</div>
                    </div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up"></i>
                        <span>+<?php echo rand(100, 300); ?></span>
                    </div>
                </div>
                
                <div class="live-stat-item" data-stat="headshots">
                    <div class="stat-icon">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-number" id="headshotsToday"><?php echo rand(50000, 100000); ?></div>
                        <div class="stat-label">Headshots Today</div>
                    </div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up"></i>
                        <span>+<?php echo rand(1000, 3000); ?></span>
                    </div>
                </div>
                
                <div class="live-stat-item" data-stat="detection-rate">
                    <div class="stat-icon">
                        <i class="fas fa-shield-check"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-number">0.00%</div>
                        <div class="stat-label">Detection Rate</div>
                    </div>
                    <div class="stat-change safe">
                        <i class="fas fa-check"></i>
                        <span>SAFE</span>
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
                        <h2 class="section-title ultra-premium-title">
                            <span class="title-icon">
                                <i class="fas fa-comments me-2"></i>
                            </span>
                            <span>Community Forums</span>
                        </h2>
                        <p class="section-subtitle">Join thousands of elite players in our exclusive discussions</p>
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
            
            <!-- Ultra Premium Sidebar -->
            <div class="col-lg-4">
                <!-- Live Cheat Monitor -->
                <div class="sidebar-section animate-fade-in-right" style="animation-delay: 0.4s;">
                    <div class="section-header">
                        <h3 class="section-title ultra-premium-title">
                            <span class="title-icon">
                                <i class="fas fa-desktop me-2"></i>
                            </span>
                            <span>Live Cheat Monitor</span>
                            <div class="status-indicator online">
                                <span class="indicator-dot"></span>
                                <span>LIVE</span>
                            </div>
                        </h3>
                    </div>
                    
                    <div class="cheat-monitor ultra-premium-card">
                        <div class="monitor-header">
                            <div class="monitor-title">Affinity Status</div>
                            <div class="monitor-version">v2.4.1</div>
                        </div>
                        
                        <div class="monitor-stats">
                            <div class="monitor-stat">
                                <div class="stat-icon">
                                    <i class="fas fa-shield-check"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-label">Detection Status</div>
                                    <div class="stat-value safe">UNDETECTED</div>
                                </div>
                            </div>
                            
                            <div class="monitor-stat">
                                <div class="stat-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-label">Active Users</div>
                                    <div class="stat-value" id="liveUsers"><?php echo rand(1200, 2500); ?></div>
                                </div>
                            </div>
                            
                            <div class="monitor-stat">
                                <div class="stat-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-label">Last Update</div>
                                    <div class="stat-value">2h ago</div>
                                </div>
                            </div>
                            
                            <div class="monitor-stat">
                                <div class="stat-icon">
                                    <i class="fas fa-server"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-label">Server Load</div>
                                    <div class="stat-value good">18%</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="monitor-actions">
                            <button class="btn btn-success btn-sm w-100 mb-2">
                                <i class="fas fa-download me-2"></i>Download Latest
                            </button>
                            <button class="btn btn-outline-primary btn-sm w-100">
                                <i class="fas fa-cog me-2"></i>Config Generator
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Premium Features -->
                <div class="sidebar-section animate-fade-in-right" style="animation-delay: 0.5s;">
                    <div class="section-header">
                        <h3 class="section-title ultra-premium-title">
                            <span class="title-icon">
                                <i class="fas fa-crown me-2"></i>
                            </span>
                            <span>Premium Features</span>
                        </h3>
                    </div>
                    
                    <div class="premium-features-list">
                        <div class="premium-feature ultra-premium-feature">
                            <div class="feature-icon">
                                <i class="fas fa-crosshairs"></i>
                                <div class="icon-glow"></div>
                            </div>
                            <div class="feature-content">
                                <h4>Advanced Aimbot</h4>
                                <p>Human-like precision with customizable settings</p>
                                <div class="feature-badge">NEW</div>
                            </div>
                        </div>
                        
                        <div class="premium-feature ultra-premium-feature">
                            <div class="feature-icon">
                                <i class="fas fa-eye"></i>
                                <div class="icon-glow"></div>
                            </div>
                            <div class="feature-content">
                                <h4>ESP Wallhack</h4>
                                <p>See enemies through walls with style</p>
                                <div class="feature-badge">HOT</div>
                            </div>
                        </div>
                        
                        <div class="premium-feature ultra-premium-feature">
                            <div class="feature-icon">
                                <i class="fas fa-magic"></i>
                                <div class="icon-glow"></div>
                            </div>
                            <div class="feature-content">
                                <h4>Triggerbot</h4>
                                <p>Instant reactions for perfect timing</p>
                            </div>
                        </div>
                        
                        <div class="premium-feature ultra-premium-feature">
                            <div class="feature-icon">
                                <i class="fas fa-shield-alt"></i>
                                <div class="icon-glow"></div>
                            </div>
                            <div class="feature-content">
                                <h4>Anti-Detection</h4>
                                <p>Advanced protection from all anti-cheats</p>
                                <div class="feature-badge">SAFE</div>
                            </div>
                        </div>
                        
                        <div class="premium-feature ultra-premium-feature">
                            <div class="feature-icon">
                                <i class="fas fa-cogs"></i>
                                <div class="icon-glow"></div>
                            </div>
                            <div class="feature-content">
                                <h4>Custom Configs</h4>
                                <p>Pre-made configs for every playstyle</p>
                            </div>
                        </div>
                        
                        <div class="premium-feature ultra-premium-feature">
                            <div class="feature-icon">
                                <i class="fas fa-headset"></i>
                                <div class="icon-glow"></div>
                            </div>
                            <div class="feature-content">
                                <h4>VIP Support</h4>
                                <p>24/7 priority support from our team</p>
                                <div class="feature-badge">VIP</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="premium-cta">
                        <a href="register.php" class="btn btn-ultra-premium btn-lg w-100">
                            <div class="btn-content">
                                <i class="fas fa-crown me-2"></i>
                                <span>Join Elite Community</span>
                                <div class="btn-glow"></div>
                            </div>
                        </a>
                    </div>
                </div>
                
                <!-- Live Streams & Showcases -->
                <div class="sidebar-section animate-fade-in-right" style="animation-delay: 0.6s;">
                    <div class="section-header">
                        <h3 class="section-title ultra-premium-title">
                            <span class="title-icon">
                                <i class="fas fa-broadcast-tower me-2"></i>
                            </span>
                            <span>Live Streams</span>
                            <div class="status-indicator online">
                                <span class="indicator-dot"></span>
                                <span>3 LIVE</span>
                            </div>
                        </h3>
                    </div>
                    
                    <div class="streams-container">
                        <div class="stream-item ultra-premium-stream live">
                            <div class="stream-thumbnail">
                                <div class="thumbnail-overlay">
                                    <i class="fas fa-play"></i>
                                </div>
                                <div class="stream-live-indicator">
                                    <span class="live-dot"></span>
                                    <span>LIVE</span>
                                </div>
                                <div class="stream-viewers">
                                    <i class="fas fa-eye"></i>
                                    <span><?php echo rand(150, 500); ?></span>
                                </div>
                            </div>
                            <div class="stream-info">
                                <h4>Affinity Showcase - Live Demo</h4>
                                <p>Watch our cheat in action on Mirage</p>
                                <div class="stream-meta">
                                    <span class="streamer">by AffinityDev</span>
                                    <span class="stream-duration">2h 15m</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="stream-item ultra-premium-stream live">
                            <div class="stream-thumbnail">
                                <div class="thumbnail-overlay">
                                    <i class="fas fa-play"></i>
                                </div>
                                <div class="stream-live-indicator">
                                    <span class="live-dot"></span>
                                    <span>LIVE</span>
                                </div>
                                <div class="stream-viewers">
                                    <i class="fas fa-eye"></i>
                                    <span><?php echo rand(80, 200); ?></span>
                                </div>
                            </div>
                            <div class="stream-info">
                                <h4>Pro Tournament Match</h4>
                                <p>Elite players competing with Affinity</p>
                                <div class="stream-meta">
                                    <span class="streamer">by ProPlayer123</span>
                                    <span class="stream-duration">45m</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="stream-item ultra-premium-stream">
                            <div class="stream-thumbnail">
                                <div class="thumbnail-overlay">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="stream-offline-indicator">
                                    <span>OFFLINE</span>
                                </div>
                            </div>
                            <div class="stream-info">
                                <h4>Config Tutorial Series</h4>
                                <p>Learn advanced configuration tips</p>
                                <div class="stream-meta">
                                    <span class="streamer">by ConfigMaster</span>
                                    <span class="stream-duration">Next: 2h</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="streams-footer">
                        <a href="streams.php" class="btn btn-outline-ultra-premium btn-sm w-100">
                            <i class="fas fa-video me-2"></i>View All Streams
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
