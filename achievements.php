<?php
define('SECURE_ACCESS', true);
require_once 'config.php';

// Require login
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Get user achievements
$user_achievements = [];
$available_achievements = [];
$user_stats = [];

try {
    // Get user's earned achievements
    $stmt = $pdo->prepare("
        SELECT ub.*, b.*, u.username as awarded_by_username
        FROM user_badges ub
        JOIN badges b ON ub.badge_id = b.id
        LEFT JOIN users u ON ub.awarded_by = u.id
        WHERE ub.user_id = ? AND ub.is_active = 1
        ORDER BY ub.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $user_achievements = $stmt->fetchAll();
    
    // Get all available achievements
    $stmt = $pdo->prepare("
        SELECT b.*, 
               (SELECT COUNT(*) FROM user_badges WHERE badge_id = b.id AND is_active = 1) as earned_count,
               (SELECT COUNT(*) FROM users WHERE is_active = 1) as total_users
        FROM badges b
        WHERE b.is_active = 1
        ORDER BY b.rarity DESC, b.name
    ");
    $stmt->execute();
    $available_achievements = $stmt->fetchAll();
    
    // Get user statistics
    $stmt = $pdo->prepare("
        SELECT 
            u.post_count, u.thread_count, u.reputation, u.level, u.experience,
            u.created_at, u.last_seen,
            (SELECT COUNT(*) FROM user_badges WHERE user_id = u.id AND is_active = 1) as badges_earned,
            (SELECT COUNT(*) FROM badges WHERE is_active = 1) as total_badges
        FROM users u
        WHERE u.id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $user_stats = $stmt->fetch();
    
} catch (Exception $e) {
    error_log("Failed to get achievements: " . $e->getMessage());
    $error_message = "Failed to load achievements.";
}

// Calculate achievement progress
$achievement_progress = [];
foreach ($available_achievements as $achievement) {
    $earned = false;
    $earned_date = null;
    
    foreach ($user_achievements as $user_achievement) {
        if ($user_achievement['badge_id'] == $achievement['id']) {
            $earned = true;
            $earned_date = $user_achievement['created_at'];
            break;
        }
    }
    
    $achievement_progress[] = [
        'achievement' => $achievement,
        'earned' => $earned,
        'earned_date' => $earned_date,
        'progress_percentage' => $earned ? 100 : 0
    ];
}

// Set page variables
$page_title = 'Achievements - ' . SITE_NAME;
$page_description = 'Track your progress and unlock achievements in the Affinity community';
$breadcrumbs = [
    ['text' => 'Achievements']
];

// Include header
include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section achievements-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="hero-title">
                    <i class="fas fa-trophy"></i>
                    Achievement Center
                </h1>
                <p class="hero-subtitle">
                    Unlock badges, level up, and showcase your accomplishments in the Affinity community.
                </p>
                <div class="hero-stats">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo number_format($user_stats['badges_earned']); ?></div>
                        <div class="stat-label">Badges Earned</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?php echo number_format($user_stats['level']); ?></div>
                        <div class="stat-label">Level</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?php echo number_format($user_stats['reputation']); ?></div>
                        <div class="stat-label">Reputation</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="hero-actions">
                    <a href="profile.php" class="btn btn-outline-light btn-lg me-2">
                        <i class="fas fa-user"></i> View Profile
                    </a>
                    <a href="leaderboard.php" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-medal"></i> Leaderboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- User Progress Section -->
<section class="user-progress-section">
    <div class="container">
        <div class="content-card">
            <div class="card-header">
                <h2><i class="fas fa-chart-line"></i> Your Progress</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="progress-card">
                            <div class="progress-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="progress-content">
                                <div class="progress-number"><?php echo number_format($user_stats['level']); ?></div>
                                <div class="progress-label">Current Level</div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?php echo ($user_stats['experience'] % 1000) / 10; ?>%"></div>
                                </div>
                                <small class="text-muted">
                                    <?php echo number_format($user_stats['experience'] % 1000); ?> / 1,000 XP to next level
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="progress-card">
                            <div class="progress-icon">
                                <i class="fas fa-medal"></i>
                            </div>
                            <div class="progress-content">
                                <div class="progress-number"><?php echo number_format($user_stats['badges_earned']); ?></div>
                                <div class="progress-label">Badges Earned</div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?php echo ($user_stats['badges_earned'] / $user_stats['total_badges']) * 100; ?>%"></div>
                                </div>
                                <small class="text-muted">
                                    <?php echo number_format($user_stats['total_badges']); ?> total available
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="progress-card">
                            <div class="progress-icon">
                                <i class="fas fa-thumbs-up"></i>
                            </div>
                            <div class="progress-content">
                                <div class="progress-number"><?php echo number_format($user_stats['reputation']); ?></div>
                                <div class="progress-label">Reputation</div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?php echo min(($user_stats['reputation'] / 10000) * 100, 100); ?>%"></div>
                                </div>
                                <small class="text-muted">
                                    <?php echo number_format(10000 - $user_stats['reputation']); ?> to next milestone
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="progress-card">
                            <div class="progress-icon">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <div class="progress-content">
                                <div class="progress-number"><?php echo number_format((time() - strtotime($user_stats['created_at'])) / 86400); ?></div>
                                <div class="progress-label">Days Active</div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 100%"></div>
                                </div>
                                <small class="text-muted">
                                    Member since <?php echo date('M Y', strtotime($user_stats['created_at'])); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Achievements Grid Section -->
<section class="achievements-grid-section">
    <div class="container">
        <!-- Achievements Header -->
        <div class="achievements-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2>
                        <i class="fas fa-trophy"></i>
                        Available Achievements
                    </h2>
                    <p class="text-muted">
                        <?php echo number_format($user_stats['badges_earned']); ?> of <?php echo number_format($user_stats['total_badges']); ?> achievements unlocked
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="achievements-filters">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary active" onclick="filterAchievements('all')">
                                All
                            </button>
                            <button type="button" class="btn btn-outline-primary" onclick="filterAchievements('earned')">
                                Earned
                            </button>
                            <button type="button" class="btn btn-outline-primary" onclick="filterAchievements('unearned')">
                                Unearned
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Achievements Grid -->
        <div class="content-card">
            <div class="card-body">
                <div class="achievements-grid" id="achievements-grid">
                    <?php foreach ($achievement_progress as $progress): ?>
                        <?php $achievement = $progress['achievement']; ?>
                        <div class="achievement-card <?php echo $progress['earned'] ? 'earned' : 'unearned'; ?>" 
                             data-status="<?php echo $progress['earned'] ? 'earned' : 'unearned'; ?>">
                            
                            <!-- Achievement Badge -->
                            <div class="achievement-badge">
                                <div class="badge-icon">
                                    <i class="<?php echo $achievement['icon_class']; ?>"></i>
                                </div>
                                <?php if ($progress['earned']): ?>
                                    <div class="earned-overlay">
                                        <i class="fas fa-check"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Achievement Info -->
                            <div class="achievement-info">
                                <h4 class="achievement-title"><?php echo htmlspecialchars($achievement['name']); ?></h4>
                                <p class="achievement-description">
                                    <?php echo htmlspecialchars($achievement['description']); ?>
                                </p>
                                
                                <!-- Achievement Stats -->
                                <div class="achievement-stats">
                                    <div class="stat">
                                        <span class="stat-label">Rarity:</span>
                                        <span class="stat-value rarity-<?php echo strtolower($achievement['rarity']); ?>">
                                            <?php echo htmlspecialchars($achievement['rarity']); ?>
                                        </span>
                                    </div>
                                    <div class="stat">
                                        <span class="stat-label">Earned by:</span>
                                        <span class="stat-value">
                                            <?php echo number_format($achievement['earned_count']); ?> users
                                        </span>
                                    </div>
                                    <?php if ($achievement['total_users'] > 0): ?>
                                        <div class="stat">
                                            <span class="stat-label">Percentage:</span>
                                            <span class="stat-value">
                                                <?php echo number_format(($achievement['earned_count'] / $achievement['total_users']) * 100, 1); ?>%
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Achievement Progress -->
                                <div class="achievement-progress">
                                    <div class="progress">
                                        <div class="progress-bar" style="width: <?php echo $progress['progress_percentage']; ?>%"></div>
                                    </div>
                                    <small class="text-muted">
                                        <?php if ($progress['earned']): ?>
                                            Earned <?php echo formatTimeAgo($progress['earned_date']); ?>
                                        <?php else: ?>
                                            <?php echo $progress['progress_percentage']; ?>% complete
                                        <?php endif; ?>
                                    </small>
                                </div>
                                
                                <!-- Achievement Actions -->
                                <div class="achievement-actions">
                                    <?php if ($progress['earned']): ?>
                                        <button class="btn btn-sm btn-success" onclick="shareAchievement(<?php echo $achievement['id']; ?>)">
                                            <i class="fas fa-share"></i> Share
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-outline-primary" onclick="showAchievementDetails(<?php echo $achievement['id']; ?>)">
                                            <i class="fas fa-info-circle"></i> Details
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Recent Achievements Section -->
<?php if (!empty($user_achievements)): ?>
    <section class="recent-achievements-section">
        <div class="container">
            <div class="content-card">
                <div class="card-header">
                    <h3><i class="fas fa-history"></i> Recently Earned</h3>
                </div>
                <div class="card-body">
                    <div class="recent-achievements">
                        <?php foreach (array_slice($user_achievements, 0, 5) as $achievement): ?>
                            <div class="recent-achievement-item">
                                <div class="achievement-icon">
                                    <i class="<?php echo $achievement['icon_class']; ?>"></i>
                                </div>
                                <div class="achievement-details">
                                    <h5><?php echo htmlspecialchars($achievement['name']); ?></h5>
                                    <p class="text-muted">
                                        <?php echo htmlspecialchars($achievement['description']); ?>
                                    </p>
                                    <small class="text-muted">
                                        Earned <?php echo formatTimeAgo($achievement['created_at']); ?>
                                        <?php if ($achievement['awarded_by']): ?>
                                            by <?php echo htmlspecialchars($achievement['awarded_by_username']); ?>
                                        <?php endif; ?>
                                    </small>
                                </div>
                                <div class="achievement-actions">
                                    <button class="btn btn-sm btn-outline-primary" onclick="showAchievementDetails(<?php echo $achievement['badge_id']; ?>)">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Achievement Details Modal -->
<div class="modal fade" id="achievementModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="achievementModalTitle">Achievement Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="achievementModalBody">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="shareAchievement()">Share</button>
            </div>
        </div>
    </div>
</div>

<script>
// Achievement filtering
function filterAchievements(filter) {
    const achievements = document.querySelectorAll('.achievement-card');
    const filterButtons = document.querySelectorAll('.achievements-filters .btn');
    
    // Update button states
    filterButtons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    
    // Apply filter
    achievements.forEach(achievement => {
        let show = true;
        
        switch (filter) {
            case 'earned':
                show = achievement.dataset.status === 'earned';
                break;
            case 'unearned':
                show = achievement.dataset.status === 'unearned';
                break;
            case 'all':
            default:
                show = true;
                break;
        }
        
        if (show) {
            achievement.style.display = 'block';
            achievement.classList.add('fadeInUp');
        } else {
            achievement.style.display = 'none';
        }
    });
    
    // Update count
    updateAchievementCount(filter);
}

// Update achievement count
function updateAchievementCount(filter) {
    const achievements = document.querySelectorAll('.achievement-card');
    let earned = 0;
    let total = 0;
    
    achievements.forEach(achievement => {
        if (achievement.style.display !== 'none') {
            total++;
            if (achievement.dataset.status === 'earned') {
                earned++;
            }
        }
    });
    
    // Update display
    const countElement = document.querySelector('.achievements-header p');
    if (countElement) {
        if (filter === 'earned') {
            countElement.textContent = `Showing ${earned} earned achievements`;
        } else if (filter === 'unearned') {
            countElement.textContent = `Showing ${total - earned} unearned achievements`;
        } else {
            countElement.textContent = `${earned} of ${total} achievements unlocked`;
        }
    }
}

// Show achievement details
function showAchievementDetails(achievementId) {
    const modal = new bootstrap.Modal(document.getElementById('achievementModal'));
    const modalTitle = document.getElementById('achievementModalTitle');
    const modalBody = document.getElementById('achievementModalBody');
    
    // Find achievement data
    const achievementCard = document.querySelector(`[data-achievement-id="${achievementId}"]`);
    if (!achievementCard) return;
    
    // Set modal title
    modalTitle.textContent = 'Achievement Details';
    
    // Load achievement details (simulate API call)
    modalBody.innerHTML = `
        <div class="text-center">
            <div class="achievement-badge-large mb-3">
                <i class="fas fa-trophy fa-3x text-warning"></i>
            </div>
            <h4>Loading achievement details...</h4>
        </div>
    `;
    
    modal.show();
    
    // Simulate loading achievement details
    setTimeout(() => {
        modalBody.innerHTML = `
            <div class="achievement-details-content">
                <div class="row">
                    <div class="col-md-6">
                        <h4>Achievement Information</h4>
                        <ul class="list-unstyled">
                            <li><strong>Name:</strong> Sample Achievement</li>
                            <li><strong>Description:</strong> This is a sample achievement description.</li>
                            <li><strong>Category:</strong> Community</li>
                            <li><strong>Difficulty:</strong> Medium</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h4>Progress</h4>
                        <div class="progress mb-2">
                            <div class="progress-bar" style="width: 75%"></div>
                        </div>
                        <small class="text-muted">75% complete</small>
                        
                        <h5 class="mt-3">Requirements</h5>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success"></i> Create 10 threads</li>
                            <li><i class="fas fa-check text-success"></i> Make 50 posts</li>
                            <li><i class="fas fa-times text-danger"></i> Reach 1000 reputation</li>
                        </ul>
                    </div>
                </div>
            </div>
        `;
    }, 1000);
}

// Share achievement
function shareAchievement(achievementId = null) {
    if (!achievementId) {
        // Get from modal
        achievementId = document.querySelector('#achievementModal').dataset.achievementId;
    }
    
    // Create share text
    const shareText = `I just earned an achievement on Affinity Forum! Check out my progress: ${window.location.origin}/achievements.php`;
    
    // Check if Web Share API is available
    if (navigator.share) {
        navigator.share({
            title: 'Achievement Unlocked!',
            text: shareText,
            url: window.location.href
        });
    } else {
        // Fallback to clipboard
        navigator.clipboard.writeText(shareText).then(() => {
            showNotification('Achievement link copied to clipboard!', 'success');
        }).catch(() => {
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = shareText;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            showNotification('Achievement link copied to clipboard!', 'success');
        });
    }
}

// Initialize achievements page
document.addEventListener('DOMContentLoaded', function() {
    // Add smooth animations
    const achievementCards = document.querySelectorAll('.achievement-card');
    achievementCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.classList.add('fadeInUp');
    });
    
    // Add hover effects
    achievementCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 10px 30px rgba(0,0,0,0.2)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '';
        });
    });
    
    // Add progress bar animations
    const progressBars = document.querySelectorAll('.progress-bar');
    progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        
        setTimeout(() => {
            bar.style.width = width;
        }, 500);
    });
    
    // Add achievement card interactions
    achievementCards.forEach(card => {
        card.addEventListener('click', function() {
            if (this.dataset.status === 'unearned') {
                const achievementId = this.dataset.achievementId;
                showAchievementDetails(achievementId);
            }
        });
    });
});

// Performance monitoring
document.addEventListener('DOMContentLoaded', function() {
    const startTime = performance.now();
    
    window.addEventListener('load', function() {
        const loadTime = performance.now() - startTime;
        
        if (loadTime > 2000) {
            console.warn('Achievements page load time is slow:', loadTime.toFixed(2) + 'ms');
        }
        
        // Send performance data to analytics
        if (typeof gtag !== 'undefined') {
            gtag('event', 'timing_complete', {
                name: 'achievements_load',
                value: Math.round(loadTime)
            });
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
