<?php
require_once 'config.php';
$pdo = getDBConnection();

// Get username from URL
$username = isset($_GET['user']) ? sanitizeInput($_GET['user']) : '';

if (empty($username)) {
    redirect('index.php');
}

// Get user information
$stmt = $pdo->prepare("
    SELECT u.*, 
           (SELECT COUNT(*) FROM threads WHERE user_id = u.id AND is_active = 1) as thread_count,
           (SELECT COUNT(*) FROM posts WHERE user_id = u.id AND is_active = 1) as post_count,
           (SELECT COUNT(*) FROM likes WHERE user_id = u.id AND is_active = 1) as given_likes,
           (SELECT COUNT(*) FROM likes l JOIN posts p ON l.post_id = p.id WHERE p.user_id = u.id AND l.is_active = 1) as received_likes
    FROM users u 
    WHERE u.username = ? AND u.is_active = 1
");
$stmt->execute([$username]);
$user = $stmt->fetch();

if (!$user) {
    redirect('index.php?error=user_not_found');
}

// Set default values for missing fields
$user['thread_count'] = $user['thread_count'] ?? 0;
$user['post_count'] = $user['post_count'] ?? 0;
$user['given_likes'] = $user['given_likes'] ?? 0;
$user['received_likes'] = $user['received_likes'] ?? 0;
$user['avatar'] = $user['avatar'] ?? null;
$user['bio'] = $user['bio'] ?? null;
$user['location'] = $user['location'] ?? null;
$user['website'] = $user['website'] ?? null;
$user['is_online'] = $user['is_online'] ?? 0;
$user['is_banned'] = $user['is_banned'] ?? 0;

// Check if viewing own profile
$is_own_profile = isLoggedIn() && $_SESSION['user_id'] == $user['id'];

// Pagination for user's posts
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Get user's recent posts
$stmt = $pdo->prepare("
    SELECT p.*, t.title as thread_title, t.id as thread_id, sf.name as subforum_name, sf.slug as subforum_slug
    FROM posts p
    JOIN threads t ON p.thread_id = t.id
    JOIN subforums sf ON t.subforum_id = sf.id
    WHERE p.user_id = ? AND p.is_active = 1
    ORDER BY p.created_at DESC
    LIMIT ? OFFSET ?
");
$stmt->execute([$user['id'], $per_page, $offset]);
$posts = $stmt->fetchAll();

// Get total post count for pagination
$stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE user_id = ? AND is_active = 1");
$stmt->execute([$user['id']]);
$total_posts = $stmt->fetchColumn();
$total_pages = ceil($total_posts / $per_page);

// Get user's recent threads
$stmt = $pdo->prepare("
    SELECT t.*, sf.name as subforum_name, sf.slug as subforum_slug
    FROM threads t
    JOIN subforums sf ON t.subforum_id = sf.id
    WHERE t.user_id = ? AND t.is_active = 1
    ORDER BY t.created_at DESC
    LIMIT 5
");
$stmt->execute([$user['id']]);
$threads = $stmt->fetchAll();

// Get user's badges/achievements (simulate if table doesn't exist)
$badges = [];
try {
    $stmt = $pdo->prepare("
        SELECT b.*, ub.created_at AS earned_at
        FROM user_badges ub
        JOIN badges b ON ub.badge_id = b.id
        WHERE ub.user_id = ?
        ORDER BY ub.created_at DESC
    ");
    $stmt->execute([$user['id']]);
    $badges = $stmt->fetchAll();
} catch (Exception $e) {
    // Simulate achievements for demo
    $badges = [
        ['name' => 'Elite Member', 'description' => 'Joined the elite community', 'icon' => 'crown', 'color' => '#ffd700', 'earned_at' => date('Y-m-d H:i:s', strtotime('-30 days'))],
        ['name' => 'Headshot Master', 'description' => 'Achieved 90%+ headshot rate', 'icon' => 'bullseye', 'color' => '#ff6b35', 'earned_at' => date('Y-m-d H:i:s', strtotime('-15 days'))],
        ['name' => 'Forum Veteran', 'description' => 'Made 100+ quality posts', 'icon' => 'medal', 'color' => '#00ff88', 'earned_at' => date('Y-m-d H:i:s', strtotime('-7 days'))],
        ['name' => 'Undetected', 'description' => '6+ months without detection', 'icon' => 'shield-check', 'color' => '#00ddff', 'earned_at' => date('Y-m-d H:i:s', strtotime('-3 days'))]
    ];
}

// Get user's recent activity (simulate if table doesn't exist)
$activities = [];
try {
    $stmt = $pdo->prepare("
        SELECT * FROM user_activity
        WHERE user_id = ?
        ORDER BY created_at DESC
        LIMIT 10
    ");
    $stmt->execute([$user['id']]);
    $activities = $stmt->fetchAll();
} catch (Exception $e) {
    // Simulate recent activity for demo
    $activities = [
        ['type' => 'post', 'description' => 'Posted in "Aimbot Settings Guide"', 'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours'))],
        ['type' => 'like', 'description' => 'Liked "Best ESP Configuration"', 'created_at' => date('Y-m-d H:i:s', strtotime('-4 hours'))],
        ['type' => 'thread', 'description' => 'Created "New Triggerbot Features"', 'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))],
        ['type' => 'achievement', 'description' => 'Earned "Forum Veteran" badge', 'created_at' => date('Y-m-d H:i:s', strtotime('-3 days'))]
    ];
}

// Calculate user stats
$user_stats = [
    'join_date' => $user['created_at'],
    'last_activity' => $user['last_activity'] ?? date('Y-m-d H:i:s'),
    'reputation' => $user['received_likes'] * 2 + $user['thread_count'] * 5 + $user['post_count'],
    'cs2_rank' => ['Global Elite', 'Supreme Master First Class', 'Legendary Eagle Master', 'Distinguished Master Guardian'][rand(0, 3)],
    'cheat_usage_days' => rand(30, 730),
    'matches_won' => rand(500, 2000),
    'headshot_rate' => rand(75, 95) . '%',
    'kd_ratio' => number_format(rand(150, 300) / 100, 2),
    'favorite_map' => ['de_mirage', 'de_dust2', 'de_inferno', 'de_cache'][rand(0, 3)]
];

$csrf_token = generateCSRFToken();
$page_title = htmlspecialchars($user['username']) . "'s Profile";

// Include header
include 'includes/header.php';
?>

<!-- ===== ULTRA PREMIUM PROFILE HERO ===== -->
<section class="profile-hero-section">
    <div class="profile-background">
        <div class="profile-particles" id="profileParticles"></div>
        <div class="profile-overlay"></div>
    </div>
    
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-4">
                <!-- Ultra Premium Profile Card -->
                <div class="profile-card-main ultra-premium-card animate-fade-in-left">
                    <div class="profile-card-header">
                        <div class="profile-avatar-container">
                            <div class="profile-avatar-frame">
                                <img src="<?php echo $user['avatar'] ?: 'images/default-avatar.svg'; ?>" 
                                     alt="<?php echo htmlspecialchars($user['username']); ?>" 
                                     class="profile-avatar-img">
                                <div class="avatar-glow"></div>
                                <?php if ($user['is_online'] ?? true): ?>
                                    <div class="online-status">
                                        <span class="status-dot"></span>
                                        <span class="status-text">ONLINE</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($is_own_profile): ?>
                            <button class="btn btn-avatar-edit" onclick="editAvatar()">
                                <i class="fas fa-camera"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                        
                        <div class="profile-title">
                            <h1 class="username"><?php echo htmlspecialchars($user['username']); ?></h1>
                            <div class="user-rank">
                                <span class="rank-badge rank-<?php echo $user['rank']; ?>">
                                    <i class="fas fa-crown"></i>
                                    <?php echo ucfirst($user['rank']); ?>
                                </span>
                            </div>
                            <div class="cs2-rank">
                                <span class="cs2-rank-badge">
                                    <i class="fas fa-medal"></i>
                                    <?php echo $user_stats['cs2_rank']; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="profile-card-content">
                        <div class="profile-bio">
                            <?php if ($user['bio']): ?>
                                <p><?php echo nl2br(htmlspecialchars($user['bio'])); ?></p>
                            <?php else: ?>
                                <p class="text-muted">No bio available</p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="profile-meta">
                            <div class="meta-item">
                                <i class="fas fa-calendar"></i>
                                <span>Joined <?php echo date('M Y', strtotime($user['created_at'])); ?></span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-clock"></i>
                                <span>Last seen <?php echo formatTimeAgo($user_stats['last_activity']); ?></span>
                            </div>
                            <?php if ($user['location']): ?>
                            <div class="meta-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?php echo htmlspecialchars($user['location']); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="profile-actions">
                            <?php if ($is_own_profile): ?>
                                <a href="settings.php" class="btn btn-ultra-premium">
                                    <div class="btn-content">
                                        <i class="fas fa-cog"></i>
                                        <span>Edit Profile</span>
                                        <div class="btn-glow"></div>
                                    </div>
                                </a>
                            <?php elseif (isLoggedIn()): ?>
                                <button class="btn btn-ultra-premium mb-2" onclick="sendMessage(<?php echo $user['id']; ?>)">
                                    <div class="btn-content">
                                        <i class="fas fa-envelope"></i>
                                        <span>Send Message</span>
                                        <div class="btn-glow"></div>
                                    </div>
                                </button>
                                <button class="btn btn-outline-ultra-premium" onclick="followUser(<?php echo $user['id']; ?>)">
                                    <div class="btn-content">
                                        <i class="fas fa-user-plus"></i>
                                        <span>Follow User</span>
                                    </div>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-8">
                <!-- CS2 Gaming Stats -->
                <div class="cs2-stats-section animate-fade-in-right">
                    <div class="section-header">
                        <h2 class="section-title ultra-premium-title">
                            <span class="title-icon">🎮</span>
                            <span>CS2 Gaming Stats</span>
                        </h2>
                    </div>
                    
                    <div class="cs2-stats-grid">
                        <div class="cs2-stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-trophy"></i>
                                <div class="icon-glow"></div>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number"><?php echo number_format($user_stats['matches_won']); ?></div>
                                <div class="stat-label">Matches Won</div>
                                <div class="stat-trend positive">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>+<?php echo rand(5, 20); ?> this week</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="cs2-stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-bullseye"></i>
                                <div class="icon-glow"></div>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number"><?php echo $user_stats['headshot_rate']; ?></div>
                                <div class="stat-label">Headshot Rate</div>
                                <div class="stat-trend positive">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>Elite Level</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="cs2-stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-crosshairs"></i>
                                <div class="icon-glow"></div>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number"><?php echo $user_stats['kd_ratio']; ?></div>
                                <div class="stat-label">K/D Ratio</div>
                                <div class="stat-trend positive">
                                    <i class="fas fa-star"></i>
                                    <span>Above Average</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="cs2-stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-calendar"></i>
                                <div class="icon-glow"></div>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number"><?php echo $user_stats['cheat_usage_days']; ?></div>
                                <div class="stat-label">Days with Affinity</div>
                                <div class="stat-trend safe">
                                    <i class="fas fa-shield-check"></i>
                                    <span>0 Bans</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Forum Stats -->
                <div class="forum-stats-section animate-fade-in-right" style="animation-delay: 0.2s;">
                    <div class="section-header">
                        <h3 class="section-title">
                            <span class="title-icon">
                                <i class="fas fa-comments"></i>
                            </span>
                            <span>Forum Activity</span>
                        </h3>
                    </div>
                    
                    <div class="forum-stats-grid">
                        <div class="forum-stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-comments"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number"><?php echo number_format($user['thread_count']); ?></div>
                                <div class="stat-label">Threads Created</div>
                            </div>
                        </div>
                        
                        <div class="forum-stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-reply"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number"><?php echo number_format($user['post_count']); ?></div>
                                <div class="stat-label">Posts Made</div>
                            </div>
                        </div>
                        
                        <div class="forum-stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-heart"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number"><?php echo number_format($user['received_likes']); ?></div>
                                <div class="stat-label">Likes Received</div>
                            </div>
                        </div>
                        
                        <div class="forum-stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number"><?php echo number_format($user_stats['reputation']); ?></div>
                                <div class="stat-label">Reputation</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== PROFILE CONTENT SECTIONS ===== -->
<section class="profile-content-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <!-- Profile Content Tabs -->
                <div class="profile-tabs ultra-premium-card animate-fade-in-up">
                    <div class="tabs-header">
                        <nav class="nav nav-tabs ultra-premium-tabs" id="profileTabs" role="tablist">
                            <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">
                                <i class="fas fa-chart-line me-2"></i>Overview
                            </button>
                            <button class="nav-link" id="posts-tab" data-bs-toggle="tab" data-bs-target="#posts" type="button" role="tab">
                                <i class="fas fa-reply me-2"></i>Recent Posts
                            </button>
                            <button class="nav-link" id="threads-tab" data-bs-toggle="tab" data-bs-target="#threads" type="button" role="tab">
                                <i class="fas fa-comments me-2"></i>Threads
                            </button>
                            <button class="nav-link" id="achievements-tab" data-bs-toggle="tab" data-bs-target="#achievements" type="button" role="tab">
                                <i class="fas fa-trophy me-2"></i>Achievements
                            </button>
                        </nav>
                    </div>
                    
                    <div class="tab-content ultra-premium-tab-content" id="profileTabsContent">
                        <!-- Overview Tab -->
                        <div class="tab-pane fade show active" id="overview" role="tabpanel">
                            <div class="overview-content">
                                <!-- Recent Activity -->
                                <div class="activity-section">
                                    <h4 class="activity-title">
                                        <i class="fas fa-clock me-2"></i>Recent Activity
                                    </h4>
                                    <div class="activity-timeline">
                                        <?php foreach ($activities as $activity): ?>
                                        <div class="activity-item">
                                            <div class="activity-icon">
                                                <i class="fas fa-<?php echo getActivityIcon($activity['type']); ?>"></i>
                                            </div>
                                            <div class="activity-content">
                                                <div class="activity-description"><?php echo $activity['description']; ?></div>
                                                <div class="activity-time"><?php echo formatTimeAgo($activity['created_at']); ?></div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Posts Tab -->
                        <div class="tab-pane fade" id="posts" role="tabpanel">
                            <div class="posts-content">
                                <h4 class="content-title">
                                    <i class="fas fa-reply me-2"></i>Recent Posts
                                </h4>
                                <?php if (!empty($posts)): ?>
                                    <div class="posts-list">
                                        <?php foreach ($posts as $post): ?>
                                        <div class="post-item ultra-premium-card">
                                            <div class="post-header">
                                                <div class="post-title">
                                                    <a href="thread.php?id=<?php echo $post['thread_id']; ?>#post-<?php echo $post['id']; ?>">
                                                        Re: <?php echo htmlspecialchars($post['thread_title']); ?>
                                                    </a>
                                                </div>
                                                <div class="post-time"><?php echo formatTimeAgo($post['created_at']); ?></div>
                                            </div>
                                            <div class="post-content">
                                                <?php echo htmlspecialchars(substr($post['content'], 0, 200)); ?>
                                                <?php if (strlen($post['content']) > 200): ?>...<?php endif; ?>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="empty-state">
                                        <i class="fas fa-reply fa-3x text-muted"></i>
                                        <h5 class="text-muted">No posts yet</h5>
                                        <p class="text-muted">This user hasn't made any posts yet.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Threads Tab -->
                        <div class="tab-pane fade" id="threads" role="tabpanel">
                            <div class="threads-content">
                                <h4 class="content-title">
                                    <i class="fas fa-comments me-2"></i>Created Threads
                                </h4>
                                <?php if (!empty($threads)): ?>
                                    <div class="threads-list">
                                        <?php foreach ($threads as $thread): ?>
                                        <div class="thread-item ultra-premium-card">
                                            <div class="thread-header">
                                                <div class="thread-title">
                                                    <a href="thread.php?id=<?php echo $thread['id']; ?>">
                                                        <?php echo htmlspecialchars($thread['title']); ?>
                                                    </a>
                                                </div>
                                                <div class="thread-meta">
                                                    <span class="thread-subforum"><?php echo htmlspecialchars($thread['subforum_name']); ?></span>
                                                    <span class="thread-time"><?php echo formatTimeAgo($thread['created_at']); ?></span>
                                                </div>
                                            </div>
                                            <div class="thread-stats">
                                                <span class="stat-item">
                                                    <i class="fas fa-reply"></i>
                                                    <?php echo number_format($thread['replies'] ?? 0); ?> replies
                                                </span>
                                                <span class="stat-item">
                                                    <i class="fas fa-eye"></i>
                                                    <?php echo number_format($thread['views'] ?? 0); ?> views
                                                </span>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="empty-state">
                                        <i class="fas fa-comments fa-3x text-muted"></i>
                                        <h5 class="text-muted">No threads yet</h5>
                                        <p class="text-muted">This user hasn't created any threads yet.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Achievements Tab -->
                        <div class="tab-pane fade" id="achievements" role="tabpanel">
                            <div class="achievements-content">
                                <h4 class="content-title">
                                    <i class="fas fa-trophy me-2"></i>Achievements & Badges
                                </h4>
                                <?php if (!empty($badges)): ?>
                                    <div class="achievements-grid">
                                        <?php foreach ($badges as $badge): ?>
                                        <div class="achievement-item ultra-premium-card">
                                            <div class="achievement-icon" style="color: <?php echo $badge['color']; ?>;">
                                                <i class="fas fa-<?php echo $badge['icon']; ?>"></i>
                                                <div class="achievement-glow" style="background: <?php echo $badge['color']; ?>;"></div>
                                            </div>
                                            <div class="achievement-content">
                                                <div class="achievement-name"><?php echo $badge['name']; ?></div>
                                                <div class="achievement-description"><?php echo $badge['description']; ?></div>
                                                <div class="achievement-date">Earned <?php echo formatTimeAgo($badge['earned_at']); ?></div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="empty-state">
                                        <i class="fas fa-trophy fa-3x text-muted"></i>
                                        <h5 class="text-muted">No achievements yet</h5>
                                        <p class="text-muted">Achievements will appear here as they are earned.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- User Rank Progress -->
                <div class="rank-progress-card ultra-premium-card animate-fade-in-right">
                    <div class="rank-header">
                        <div class="rank-icon">
                            <i class="fas fa-star"></i>
                            <div class="icon-glow"></div>
                        </div>
                        <div class="rank-title">Rank Progress</div>
                    </div>
                    
                    <div class="rank-content">
                        <div class="current-rank">
                            <div class="rank-badge-large rank-<?php echo $user['rank']; ?>">
                                <i class="fas fa-crown"></i>
                                <span><?php echo ucfirst($user['rank']); ?></span>
                            </div>
                        </div>
                        
                        <div class="rank-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo min(100, ($user_stats['reputation'] % 1000) / 10); ?>%;"></div>
                            </div>
                            <div class="progress-text">
                                <span><?php echo $user_stats['reputation'] % 1000; ?> / 1000 XP</span>
                            </div>
                        </div>
                        
                        <div class="next-rank">
                            <span class="next-rank-text">Next: Elite Member</span>
                            <span class="xp-needed"><?php echo 1000 - ($user_stats['reputation'] % 1000); ?> XP needed</span>
                        </div>
                    </div>
                </div>
                
                <!-- Cheat Statistics -->
                <div class="cheat-stats-card ultra-premium-card animate-fade-in-right" style="animation-delay: 0.2s;">
                    <div class="cheat-stats-header">
                        <div class="stats-icon">
                            <i class="fas fa-gamepad"></i>
                            <div class="icon-glow"></div>
                        </div>
                        <div class="stats-title">Cheat Performance</div>
                    </div>
                    
                    <div class="cheat-stats-content">
                        <div class="performance-meter">
                            <div class="meter-label">Overall Performance</div>
                            <div class="meter-bar">
                                <div class="meter-fill" style="width: <?php echo rand(85, 98); ?>%;"></div>
                            </div>
                            <div class="meter-value"><?php echo rand(85, 98); ?>%</div>
                        </div>
                        
                        <div class="cheat-stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-map"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label">Favorite Map</div>
                                <div class="stat-value"><?php echo $user_stats['favorite_map']; ?></div>
                            </div>
                        </div>
                        
                        <div class="cheat-stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-shield-check"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label">Safety Record</div>
                                <div class="stat-value safe">100% Clean</div>
                            </div>
                        </div>
                        
                        <div class="cheat-stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-label">Usage Time</div>
                                <div class="stat-value"><?php echo $user_stats['cheat_usage_days']; ?> days</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<script>
// Ultra Premium Profile JavaScript
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Profile page initialized');
    
    // Initialize profile features
    initializeProfileAnimations();
    setupProfileInteractions();
});

function initializeProfileAnimations() {
    // Animate progress bars
    const progressBars = document.querySelectorAll('.progress-fill, .meter-fill');
    progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = width;
        }, 500);
    });
    
    // Animate stat numbers
    const statNumbers = document.querySelectorAll('.stat-number');
    statNumbers.forEach(stat => {
        const target = parseInt(stat.textContent.replace(/[^\d]/g, ''));
        animateCounter(stat, target);
    });
}

function setupProfileInteractions() {
    // Achievement hover effects
    document.querySelectorAll('.achievement-item').forEach(item => {
        item.addEventListener('mouseenter', () => {
            const icon = item.querySelector('.achievement-icon');
            icon.style.animation = 'bounce 0.6s ease-out';
        });
        
        item.addEventListener('mouseleave', () => {
            const icon = item.querySelector('.achievement-icon');
            icon.style.animation = '';
        });
    });
    
    // Stat card interactions
    document.querySelectorAll('.cs2-stat-card, .forum-stat-item').forEach(card => {
        card.addEventListener('click', () => {
            card.style.animation = 'pulse-card 0.3s ease-out';
            setTimeout(() => {
                card.style.animation = '';
            }, 300);
        });
    });
}

function animateCounter(element, target) {
    let current = 0;
    const increment = target / 50;
    const duration = 1500;
    const stepTime = duration / 50;
    
    const counter = setInterval(() => {
        current += increment;
        if (current >= target) {
            current = target;
            clearInterval(counter);
        }
        
        element.textContent = Math.floor(current).toLocaleString();
    }, stepTime);
}

function editAvatar() {
    showNotification('Avatar editing feature coming soon!', 'info');
}

function sendMessage(userId) {
    showNotification('Messaging feature coming soon!', 'info');
}

function followUser(userId) {
    showNotification('Follow feature coming soon!', 'info');
}
</script>

<?php
// Helper functions
function getActivityIcon($type) {
    $icons = [
        'post' => 'reply',
        'thread' => 'comments',
        'like' => 'heart',
        'achievement' => 'trophy',
        'login' => 'sign-in-alt',
        'register' => 'user-plus'
    ];
    return $icons[$type] ?? 'circle';
}

function formatDate($date) {
    return date('M j, Y', strtotime($date));
}

function formatTimeAgo($date) {
    $time = time() - strtotime($date);
    
    if ($time < 60) return 'Just now';
    if ($time < 3600) return floor($time / 60) . 'm ago';
    if ($time < 86400) return floor($time / 3600) . 'h ago';
    if ($time < 2592000) return floor($time / 86400) . 'd ago';
    if ($time < 31536000) return floor($time / 2592000) . 'mo ago';
    
    return floor($time / 31536000) . 'y ago';
}
                            <button class="nav-link active" id="posts-tab" data-bs-toggle="tab" data-bs-target="#posts" type="button" role="tab">
                                <i class="fas fa-reply me-2"></i>Recent Posts
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="threads-tab" data-bs-toggle="tab" data-bs-target="#threads" type="button" role="tab">
                                <i class="fas fa-comments me-2"></i>Threads
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="badges-tab" data-bs-toggle="tab" data-bs-target="#badges" type="button" role="tab">
                                <i class="fas fa-award me-2"></i>Badges
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity" type="button" role="tab">
                                <i class="fas fa-chart-line me-2"></i>Activity
                            </button>
                        </li>
                    </ul>
                    
                    <div class="tab-content" id="profileTabsContent">
                        <!-- Posts Tab -->
                        <div class="tab-pane fade show active" id="posts" role="tabpanel">
                            <div class="tab-header d-flex justify-content-between align-items-center mb-3">
                                <h5>Recent Posts</h5>
                                <span class="text-muted"><?php echo number_format($total_posts); ?> total posts</span>
                            </div>
                            
                            <?php if (empty($posts)): ?>
                            <div class="no-content text-center py-4">
                                <i class="fas fa-comments fa-2x text-muted mb-3"></i>
                                <h5>No posts yet</h5>
                                <p class="text-muted">This user hasn't made any posts yet.</p>
                            </div>
                            <?php else: ?>
                            <div class="posts-list">
                                <?php foreach ($posts as $post): ?>
                                <div class="post-item-small">
                                    <div class="post-content">
                                        <div class="post-header">
                                            <div class="post-title">
                                                <a href="thread.php?id=<?php echo $post['thread_id']; ?>" class="thread-link">
                                                    <?php echo htmlspecialchars($post['thread_title']); ?>
                                                </a>
                                            </div>
                                            <div class="post-meta">
                                                <span class="subforum">
                                                    in <a href="subforum.php?slug=<?php echo $post['subforum_slug']; ?>"><?php echo htmlspecialchars($post['subforum_name']); ?></a>
                                                </span>
                                                <span class="date">
                                                    <i class="fas fa-clock me-1"></i>
                                                    <?php echo formatTimeAgo($post['created_at']); ?>
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="post-excerpt">
                                            <?php echo htmlspecialchars(substr(strip_tags($post['content']), 0, 200)); ?>
                                            <?php if (strlen(strip_tags($post['content'])) > 200): ?>
                                            <a href="thread.php?id=<?php echo $post['thread_id']; ?>" class="read-more">...read more</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Pagination -->
                            <?php if ($total_pages > 1): ?>
                            <nav aria-label="Posts pagination" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?user=<?php echo urlencode($username); ?>&page=<?php echo $page - 1; ?>">
                                            <i class="fas fa-chevron-left"></i> Previous
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                    
                                    <?php
                                    $start_page = max(1, $page - 2);
                                    $end_page = min($total_pages, $page + 2);
                                    
                                    if ($start_page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?user=<?php echo urlencode($username); ?>&page=1">1</a>
                                    </li>
                                    <?php if ($start_page > 2): ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?user=<?php echo urlencode($username); ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                    <?php endfor; ?>
                                    
                                    <?php if ($end_page < $total_pages): ?>
                                    <?php if ($end_page < $total_pages - 1): ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?user=<?php echo urlencode($username); ?>&page=<?php echo $total_pages; ?>"><?php echo $total_pages; ?></a>
                                    </li>
                                    <?php endif; ?>
                                    
                                    <?php if ($page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?user=<?php echo urlencode($username); ?>&page=<?php echo $page + 1; ?>">
                                            Next <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                            <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Threads Tab -->
                        <div class="tab-pane fade" id="threads" role="tabpanel">
                            <div class="tab-header mb-3">
                                <h5>Threads Started</h5>
                            </div>
                            
                            <?php if (empty($threads)): ?>
                            <div class="no-content text-center py-4">
                                <i class="fas fa-comments fa-2x text-muted mb-3"></i>
                                <h5>No threads yet</h5>
                                <p class="text-muted">This user hasn't started any threads yet.</p>
                            </div>
                            <?php else: ?>
                            <div class="threads-list">
                                <?php foreach ($threads as $thread): ?>
                                <div class="thread-item-small">
                                    <div class="thread-content">
                                        <div class="thread-title">
                                            <a href="thread.php?id=<?php echo $thread['id']; ?>" class="thread-link">
                                                <?php echo htmlspecialchars($thread['title']); ?>
                                            </a>
                                            <?php if ($thread['is_pinned']): ?>
                                            <i class="fas fa-thumbtack text-warning ms-2" title="Pinned Thread"></i>
                                            <?php endif; ?>
                                            <?php if ($thread['is_locked']): ?>
                                            <i class="fas fa-lock text-danger ms-2" title="Locked Thread"></i>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="thread-meta">
                                            <span class="subforum">
                                                in <a href="subforum.php?slug=<?php echo $thread['subforum_slug']; ?>"><?php echo htmlspecialchars($thread['subforum_name']); ?></a>
                                            </span>
                                            <span class="stats">
                                                <i class="fas fa-comments me-1"></i><?php echo number_format($thread['reply_count']); ?> replies
                                                <i class="fas fa-eye ms-2 me-1"></i><?php echo number_format($thread['view_count']); ?> views
                                            </span>
                                            <span class="date">
                                                <i class="fas fa-clock me-1"></i>
                                                <?php echo formatTimeAgo($thread['created_at']); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Badges Tab -->
                        <div class="tab-pane fade" id="badges" role="tabpanel">
                            <div class="tab-header mb-3">
                                <h5>Badges & Achievements</h5>
                            </div>
                            
                            <?php if (empty($badges)): ?>
                            <div class="no-content text-center py-4">
                                <i class="fas fa-award fa-2x text-muted mb-3"></i>
                                <h5>No badges yet</h5>
                                <p class="text-muted">This user hasn't earned any badges yet.</p>
                            </div>
                            <?php else: ?>
                            <div class="badges-grid">
                                <?php foreach ($badges as $badge): ?>
                                <div class="badge-item">
                                    <div class="badge-icon">
                                        <i class="<?php echo $badge['icon']; ?> fa-2x"></i>
                                    </div>
                                    <div class="badge-info">
                                        <h6><?php echo htmlspecialchars($badge['name']); ?></h6>
                                        <p class="text-muted small"><?php echo htmlspecialchars($badge['description']); ?></p>
                                        <small class="text-muted">Earned <?php echo formatTimeAgo($badge['earned_at']); ?></small>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Activity Tab -->
                        <div class="tab-pane fade" id="activity" role="tabpanel">
                            <div class="tab-header mb-3">
                                <h5>Recent Activity</h5>
                            </div>
                            
                            <?php if (empty($activities)): ?>
                            <div class="no-content text-center py-4">
                                <i class="fas fa-chart-line fa-2x text-muted mb-3"></i>
                                <h5>No activity yet</h5>
                                <p class="text-muted">This user hasn't had any recent activity.</p>
                            </div>
                            <?php else: ?>
                            <div class="activity-timeline">
                                <?php foreach ($activities as $activity): ?>
                                <div class="activity-item">
                                    <div class="activity-icon">
                                        <i class="fas fa-circle text-primary"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-text">
                                            <?php echo htmlspecialchars($activity['details']); ?>
                                        </div>
                                        <div class="activity-time">
                                            <?php echo formatTimeAgo($activity['created_at']); ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3">
                <!-- Profile Sidebar -->
                <div class="profile-sidebar">
                    <!-- User Info Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6><i class="fas fa-info-circle me-2"></i>User Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="user-info">
                                <div class="info-item">
                                    <strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?>
                                </div>
                                <div class="info-item">
                                    <strong>Rank:</strong> 
                                    <span class="rank-badge rank-<?php echo $user['rank']; ?>"><?php echo ucfirst($user['rank']); ?></span>
                                </div>
                                <div class="info-item">
                                    <strong>Status:</strong> 
                                    <?php if ($user['is_online']): ?>
                                    <span class="text-success"><i class="fas fa-circle me-1"></i>Online</span>
                                    <?php else: ?>
                                    <span class="text-muted"><i class="fas fa-circle me-1"></i>Offline</span>
                                    <?php endif; ?>
                                </div>
                                <div class="info-item">
                                    <strong>Joined:</strong> <?php echo formatDate($user['created_at']); ?>
                                </div>
                                <div class="info-item">
                                    <strong>Last Activity:</strong> <?php echo formatTimeAgo($user['last_seen'] ?? $user['created_at']); ?>
                                </div>
                                <?php if ($user['location']): ?>
                                <div class="info-item">
                                    <strong>Location:</strong> <?php echo htmlspecialchars($user['location']); ?>
                                </div>
                                <?php endif; ?>
                                <?php if ($user['website']): ?>
                                <div class="info-item">
                                    <strong>Website:</strong> 
                                    <a href="<?php echo htmlspecialchars($user['website']); ?>" target="_blank" rel="noopener">
                                        <?php echo htmlspecialchars($user['website']); ?>
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6><i class="fas fa-chart-bar me-2"></i>Quick Stats</h6>
                        </div>
                        <div class="card-body">
                            <div class="quick-stats">
                                <div class="stat-row">
                                    <span>Posts per day:</span>
                                    <span class="stat-value"><?php 
                                        $days_active = max(1, (time() - strtotime($user['created_at'])) / 86400);
                                        echo number_format($user['post_count'] / $days_active, 1);
                                    ?></span>
                                </div>
                                <div class="stat-row">
                                    <span>Threads per day:</span>
                                    <span class="stat-value"><?php 
                                        echo number_format($user['thread_count'] / $days_active, 1);
                                    ?></span>
                                </div>
                                <div class="stat-row">
                                    <span>Like ratio:</span>
                                    <span class="stat-value"><?php 
                                        echo $user['post_count'] > 0 ? number_format($user['received_likes'] / $user['post_count'], 1) : '0';
                                    ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Actions -->
                    <?php if (isLoggedIn() && !$is_own_profile): ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6><i class="fas fa-phone me-2"></i>Contact</h6>
                        </div>
                        <div class="card-body">
                            <div class="contact-actions">
                                <button type="button" class="btn btn-primary w-100 mb-2" onclick="sendMessage(<?php echo $user['id']; ?>)">
                                    <i class="fas fa-envelope me-2"></i>Send Message
                                </button>
                                <button type="button" class="btn btn-outline-primary w-100 mb-2" onclick="followUser(<?php echo $user['id']; ?>)">
                                    <i class="fas fa-user-plus me-2"></i>Follow User
                                </button>
                                <?php if (isAdmin()): ?>
                                <button type="button" class="btn btn-outline-warning w-100 mb-2" onclick="moderateUser(<?php echo $user['id']; ?>)">
                                    <i class="fas fa-shield-alt me-2"></i>Moderate
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Similar Users -->
                    <div class="card">
                        <div class="card-header">
                            <h6><i class="fas fa-users me-2"></i>Similar Users</h6>
                        </div>
                        <div class="card-body">
                            <?php
                            $stmt = $pdo->prepare("
                                SELECT u.username, u.rank, u.avatar
                                FROM users u
                                WHERE u.rank = ? AND u.id != ? AND u.is_active = 1
                                ORDER BY u.post_count DESC
                                LIMIT 5
                            ");
                            $stmt->execute([$user['rank'], $user['id']]);
                            $similar_users = $stmt->fetchAll();
                            ?>
                            
                            <?php if (!empty($similar_users)): ?>
                            <div class="similar-users">
                                <?php foreach ($similar_users as $similar_user): ?>
                                <div class="similar-user-item">
                                    <img src="<?php echo $similar_user['avatar'] ?: 'images/default-avatar.svg'; ?>" 
                                         alt="<?php echo htmlspecialchars($similar_user['username']); ?>" 
                                         class="avatar-img-sm">
                                    <div class="user-info">
                                        <a href="profile.php?user=<?php echo urlencode($similar_user['username']); ?>" class="user-link">
                                            <?php echo htmlspecialchars($similar_user['username']); ?>
                                        </a>
                                        <span class="rank-badge rank-<?php echo $similar_user['rank']; ?>"><?php echo ucfirst($similar_user['rank']); ?></span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <p class="text-muted small mb-0">No similar users found.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="js/main.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize profile tabs
            initializeProfileTabs();
            
            // Profile-specific functionality
            initializeProfileFeatures();
        });
        
        function initializeProfileTabs() {
            // Handle tab switching
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                const target = $(e.target).attr('data-bs-target');
                
                // Update URL hash
                if (history.pushState) {
                    history.pushState(null, null, target);
                }
                
                // Load content if needed
                loadTabContent(target);
            });
            
            // Check for hash in URL and activate corresponding tab
            const hash = window.location.hash;
            if (hash) {
                const tab = $(`button[data-bs-target="${hash}"]`);
                if (tab.length) {
                    tab.tab('show');
                }
            }
        }
        
        function initializeProfileFeatures() {
            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();
            
            // Initialize popovers
            $('[data-bs-toggle="popover"]').popover();
        }
        
        function loadTabContent(tabId) {
            // This function can be used to load content dynamically if needed
            console.log('Loading content for tab:', tabId);
        }
        
        function editAvatar() {
            // Redirect to settings page for avatar editing
            window.location.href = 'settings.php?tab=avatar';
        }
        
        function sendMessage(userId) {
            // Redirect to messaging page
            window.location.href = `messages.php?compose&to=${userId}`;
        }
        
        function followUser(userId) {
            $.post('ajax/follow-user.php', {
                user_id: userId,
                csrf_token: '<?php echo $csrf_token; ?>'
            })
            .done(function(response) {
                if (response.success) {
                    showNotification(response.message, 'success');
                    // Update UI
                    updateFollowButton(userId, response.following);
                } else {
                    showNotification(response.message, 'error');
                }
            })
            .fail(function() {
                showNotification('Failed to follow user', 'error');
            });
        }
        
        function updateFollowButton(userId, following) {
            const btn = $(`button[onclick="followUser(${userId})"]`);
            if (following) {
                btn.html('<i class="fas fa-user-check me-2"></i>Following');
                btn.removeClass('btn-outline-primary').addClass('btn-primary');
                btn.attr('onclick', `unfollowUser(${userId})`);
            } else {
                btn.html('<i class="fas fa-user-plus me-2"></i>Follow User');
                btn.removeClass('btn-primary').addClass('btn-outline-primary');
                btn.attr('onclick', `followUser(${userId})`);
            }
        }
        
        function unfollowUser(userId) {
            $.post('ajax/unfollow-user.php', {
                user_id: userId,
                csrf_token: '<?php echo $csrf_token; ?>'
            })
            .done(function(response) {
                if (response.success) {
                    showNotification(response.message, 'success');
                    updateFollowButton(userId, false);
                } else {
                    showNotification(response.message, 'error');
                }
            })
            .fail(function() {
                showNotification('Failed to unfollow user', 'error');
            });
        }
        
        function moderateUser(userId) {
            // Redirect to admin moderation page
            window.location.href = `admin/moderate-user.php?id=${userId}`;
        }
        
        // Profile statistics animation
        function animateStats() {
            $('.stat-number').each(function() {
                const $this = $(this);
                const countTo = parseInt($this.text().replace(/,/g, ''));
                
                $({ countNum: 0 }).animate({
                    countNum: countTo
                }, {
                    duration: 1000,
                    easing: 'swing',
                    step: function() {
                        $this.text(Math.floor(this.countNum).toLocaleString());
                    },
                    complete: function() {
                        $this.text(countTo.toLocaleString());
                    }
                });
            });
        }
        
        // Animate stats when they come into view
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateStats();
                    observer.unobserve(entry.target);
                }
            });
        });
        
        observer.observe(document.querySelector('.profile-stats'));
    </script>
</body>
</html>
