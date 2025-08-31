<?php
define('SECURE_ACCESS', true);
require_once 'config.php';

// Require login
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Handle notification actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $notification_id = intval($_POST['notification_id'] ?? 0);
    
    if ($action && $notification_id) {
        try {
            switch ($action) {
                case 'mark_read':
                    markNotificationAsRead($notification_id);
                    break;
                case 'mark_all_read':
                    markAllNotificationsAsRead($_SESSION['user_id']);
                    break;
                case 'delete':
                    deleteNotification($notification_id);
                    break;
                case 'delete_all':
                    deleteAllNotifications($_SESSION['user_id']);
                    break;
            }
            
            // Redirect to prevent form resubmission
            header('Location: notifications.php?success=1');
            exit;
        } catch (Exception $e) {
            $error_message = "An error occurred while processing your request.";
        }
    }
}

// Get notifications
$notifications = [];
$unread_count = 0;

try {
    $stmt = $pdo->prepare("
        SELECT n.*, 
               CASE 
                   WHEN n.type = 'thread_reply' THEN t.title
                   WHEN n.type = 'thread_like' THEN t.title
                   WHEN n.type = 'user_follow' THEN u2.username
                   WHEN n.type = 'mention' THEN t.title
                   ELSE n.content
               END as related_content,
               CASE 
                   WHEN n.type = 'thread_reply' THEN t.slug
                   WHEN n.type = 'thread_like' THEN t.slug
                   WHEN n.type = 'mention' THEN t.slug
                   ELSE NULL
               END as related_url,
               u.username as sender_username, u.avatar as sender_avatar, u.rank as sender_rank
        FROM notifications n
        LEFT JOIN users u ON n.sender_id = u.id
        LEFT JOIN threads t ON n.thread_id = t.id
        LEFT JOIN users u2 ON n.related_user_id = u2.id
        WHERE n.user_id = ? AND n.is_active = 1
        ORDER BY n.created_at DESC
        LIMIT 100
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $notifications = $stmt->fetchAll();
    
    // Count unread notifications
    $unread_count = count(array_filter($notifications, function($n) {
        return !$n['is_read'];
    }));
    
} catch (Exception $e) {
    error_log("Failed to get notifications: " . $e->getMessage());
    $error_message = "Failed to load notifications.";
}

// Set page variables
$page_title = 'Notifications - ' . SITE_NAME;
$page_description = 'View and manage your forum notifications';
$breadcrumbs = [
    ['text' => 'Notifications']
];

// Include header
include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section notifications-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="hero-title">
                    <i class="fas fa-bell"></i>
                    Notifications Center
                </h1>
                <p class="hero-subtitle">
                    Stay updated with all the latest activity, mentions, and interactions from the Affinity community.
                </p>
                <div class="hero-stats">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo number_format($unread_count); ?></div>
                        <div class="stat-label">Unread</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?php echo number_format(count($notifications)); ?></div>
                        <div class="stat-label">Total</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="hero-actions">
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="action" value="mark_all_read">
                        <button type="submit" class="btn btn-success btn-lg me-2">
                            <i class="fas fa-check-double"></i> Mark All Read
                        </button>
                    </form>
                    <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete all notifications?')">
                        <input type="hidden" name="action" value="delete_all">
                        <button type="submit" class="btn btn-outline-danger btn-lg">
                            <i class="fas fa-trash"></i> Clear All
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Notifications Section -->
<section class="notifications-section">
    <div class="container">
        <!-- Notifications Header -->
        <div class="notifications-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2>
                        <i class="fas fa-bell"></i>
                        Your Notifications
                    </h2>
                    <p class="text-muted">
                        <?php if ($unread_count > 0): ?>
                            You have <strong><?php echo $unread_count; ?></strong> unread notification<?php echo $unread_count !== 1 ? 's' : ''; ?>
                        <?php else: ?>
                            All caught up! No unread notifications.
                        <?php endif; ?>
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="notifications-filters">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary active" onclick="filterNotifications('all')">
                                All
                            </button>
                            <button type="button" class="btn btn-outline-primary" onclick="filterNotifications('unread')">
                                Unread
                            </button>
                            <button type="button" class="btn btn-outline-primary" onclick="filterNotifications('mentions')">
                                Mentions
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Notifications Content -->
        <div class="content-card">
            <div class="card-body">
                <?php if (!empty($notifications)): ?>
                    <div class="notifications-list" id="notifications-list">
                        <?php foreach ($notifications as $notification): ?>
                            <div class="notification-item <?php echo $notification['is_read'] ? 'read' : 'unread'; ?>" 
                                 data-type="<?php echo $notification['type']; ?>"
                                 data-id="<?php echo $notification['id']; ?>">
                                
                                <!-- Notification Icon -->
                                <div class="notification-icon">
                                    <?php
                                    $icon_class = 'fas fa-bell';
                                    $icon_color = 'text-primary';
                                    
                                    switch ($notification['type']) {
                                        case 'thread_reply':
                                            $icon_class = 'fas fa-reply';
                                            $icon_color = 'text-success';
                                            break;
                                        case 'thread_like':
                                            $icon_class = 'fas fa-thumbs-up';
                                            $icon_color = 'text-warning';
                                            break;
                                        case 'user_follow':
                                            $icon_class = 'fas fa-user-plus';
                                            $icon_color = 'text-info';
                                            break;
                                        case 'mention':
                                            $icon_class = 'fas fa-at';
                                            $icon_color = 'text-danger';
                                            break;
                                        case 'achievement':
                                            $icon_class = 'fas fa-trophy';
                                            $icon_color = 'text-warning';
                                            break;
                                        case 'system':
                                            $icon_class = 'fas fa-cog';
                                            $icon_color = 'text-secondary';
                                            break;
                                    }
                                    ?>
                                    <i class="<?php echo $icon_class; ?> <?php echo $icon_color; ?>"></i>
                                </div>
                                
                                <!-- Notification Content -->
                                <div class="notification-content">
                                    <div class="notification-header">
                                        <h5 class="notification-title">
                                            <?php echo getNotificationTitle($notification); ?>
                                        </h5>
                                        <div class="notification-meta">
                                            <span class="notification-time">
                                                <?php echo formatTimeAgo($notification['created_at']); ?>
                                            </span>
                                            <?php if (!$notification['is_read']): ?>
                                                <span class="unread-badge">New</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="notification-body">
                                        <p class="notification-text">
                                            <?php echo htmlspecialchars($notification['content']); ?>
                                        </p>
                                        
                                        <?php if ($notification['related_content']): ?>
                                            <div class="notification-context">
                                                <small class="text-muted">
                                                    <?php if ($notification['type'] === 'user_follow'): ?>
                                                        Started following you
                                                    <?php elseif ($notification['type'] === 'mention'): ?>
                                                        Mentioned you in: 
                                                        <a href="thread.php?slug=<?php echo urlencode($notification['related_url']); ?>">
                                                            <?php echo htmlspecialchars($notification['related_content']); ?>
                                                        </a>
                                                    <?php else: ?>
                                                        <?php echo htmlspecialchars($notification['related_content']); ?>
                                                    <?php endif; ?>
                                                </small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Sender Info -->
                                    <?php if ($notification['sender_id']): ?>
                                        <div class="notification-sender">
                                            <img src="<?php echo getUserAvatar($notification['sender_username']); ?>" 
                                                 alt="Avatar" class="avatar-sm">
                                            <div class="sender-info">
                                                <a href="profile.php?user=<?php echo urlencode($notification['sender_username']); ?>" 
                                                   class="sender-name">
                                                    <?php echo htmlspecialchars($notification['sender_username']); ?>
                                                </a>
                                                <?php if ($notification['sender_rank']): ?>
                                                    <span class="sender-rank"><?php echo htmlspecialchars($notification['sender_rank']); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Notification Actions -->
                                <div class="notification-actions">
                                    <?php if (!$notification['is_read']): ?>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="mark_read">
                                            <input type="hidden" name="notification_id" value="<?php echo $notification['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-success" 
                                                    title="Mark as read">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    
                                    <?php if ($notification['related_url']): ?>
                                        <a href="thread.php?slug=<?php echo urlencode($notification['related_url']); ?>" 
                                           class="btn btn-sm btn-outline-primary" title="View thread">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <form method="POST" class="d-inline" 
                                          onsubmit="return confirm('Delete this notification?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="notification_id" value="<?php echo $notification['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Load More -->
                    <?php if (count($notifications) >= 100): ?>
                        <div class="text-center mt-4">
                            <button class="btn btn-outline-primary" onclick="loadMoreNotifications()">
                                <i class="fas fa-plus"></i> Load More
                            </button>
                        </div>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <div class="no-notifications">
                        <div class="text-center py-5">
                            <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                            <h3>No notifications yet</h3>
                            <p class="text-muted">
                                You're all caught up! When you receive notifications, they'll appear here.
                            </p>
                            <div class="suggestions">
                                <h5>Get started:</h5>
                                <ul class="list-unstyled">
                                    <li>• <a href="new-thread.php">Create a new thread</a></li>
                                    <li>• <a href="forum.php">Browse the forums</a></li>
                                    <li>• <a href="tournaments.php">Join a tournament</a></li>
                                    <li>• <a href="users.php">Find users to follow</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Notification Settings Section -->
<section class="notification-settings-section">
    <div class="container">
        <div class="content-card">
            <div class="card-header">
                <h3><i class="fas fa-cog"></i> Notification Preferences</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Email Notifications</h5>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="email_thread_replies" checked>
                            <label class="form-check-label" for="email_thread_replies">
                                Thread replies
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="email_mentions" checked>
                            <label class="form-check-label" for="email_mentions">
                                Mentions
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="email_follows">
                            <label class="form-check-label" for="email_follows">
                                New followers
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="email_achievements" checked>
                            <label class="form-check-label" for="email_achievements">
                                Achievements
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5>Push Notifications</h5>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="push_thread_replies" checked>
                            <label class="form-check-label" for="push_thread_replies">
                                Thread replies
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-label" type="checkbox" id="push_mentions" checked>
                            <label class="form-check-label" for="push_mentions">
                                Mentions
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="push_follows">
                            <label class="form-check-label" for="push_follows">
                                New followers
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="push_achievements" checked>
                            <label class="form-check-label" for="push_achievements">
                                Achievements
                            </label>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <button class="btn btn-primary" onclick="saveNotificationSettings()">
                        <i class="fas fa-save"></i> Save Preferences
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Notification filtering
function filterNotifications(filter) {
    const notifications = document.querySelectorAll('.notification-item');
    const filterButtons = document.querySelectorAll('.notifications-filters .btn');
    
    // Update button states
    filterButtons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    
    // Apply filter
    notifications.forEach(notification => {
        let show = true;
        
        switch (filter) {
            case 'unread':
                show = notification.classList.contains('unread');
                break;
            case 'mentions':
                show = notification.dataset.type === 'mention';
                break;
            case 'all':
            default:
                show = true;
                break;
        }
        
        if (show) {
            notification.style.display = 'flex';
            notification.classList.add('fadeInUp');
        } else {
            notification.style.display = 'none';
        }
    });
    
    // Update count
    updateNotificationCount(filter);
}

// Update notification count
function updateNotificationCount(filter) {
    const notifications = document.querySelectorAll('.notification-item');
    let count = 0;
    
    notifications.forEach(notification => {
        if (notification.style.display !== 'none') {
            count++;
        }
    });
    
    // Update display
    const countElement = document.querySelector('.notifications-header p');
    if (countElement) {
        if (filter === 'unread') {
            countElement.innerHTML = `Showing <strong>${count}</strong> unread notification${count !== 1 ? 's' : ''}`;
        } else if (filter === 'mentions') {
            countElement.innerHTML = `Showing <strong>${count}</strong> mention${count !== 1 ? 's' : ''}`;
        } else {
            countElement.innerHTML = `Showing <strong>${count}</strong> notification${count !== 1 ? 's' : ''}`;
        }
    }
}

// Load more notifications
function loadMoreNotifications() {
    const button = event.target;
    const icon = button.querySelector('i');
    
    // Add loading state
    button.disabled = true;
    icon.className = 'fas fa-spinner fa-spin';
    
    // Simulate loading more notifications
    setTimeout(() => {
        button.disabled = false;
        icon.className = 'fas fa-plus';
        
        // Show success notification
        showNotification('More notifications loaded!', 'success');
    }, 1000);
}

// Save notification settings
function saveNotificationSettings() {
    const settings = {
        email: {
            thread_replies: document.getElementById('email_thread_replies').checked,
            mentions: document.getElementById('email_mentions').checked,
            follows: document.getElementById('email_follows').checked,
            achievements: document.getElementById('email_achievements').checked
        },
        push: {
            thread_replies: document.getElementById('push_thread_replies').checked,
            mentions: document.getElementById('push_mentions').checked,
            follows: document.getElementById('push_follows').checked,
            achievements: document.getElementById('push_achievements').checked
        }
    };
    
    // Save to localStorage
    localStorage.setItem('notificationSettings', JSON.stringify(settings));
    
    // Show success message
    showNotification('Notification preferences saved!', 'success');
}

// Load notification settings
document.addEventListener('DOMContentLoaded', function() {
    // Load saved settings
    const savedSettings = localStorage.getItem('notificationSettings');
    if (savedSettings) {
        try {
            const settings = JSON.parse(savedSettings);
            
            // Apply email settings
            if (settings.email) {
                document.getElementById('email_thread_replies').checked = settings.email.thread_replies;
                document.getElementById('email_mentions').checked = settings.email.mentions;
                document.getElementById('email_follows').checked = settings.email.follows;
                document.getElementById('email_achievements').checked = settings.email.achievements;
            }
            
            // Apply push settings
            if (settings.push) {
                document.getElementById('push_thread_replies').checked = settings.push.thread_replies;
                document.getElementById('push_mentions').checked = settings.push.mentions;
                document.getElementById('push_follows').checked = settings.push.follows;
                document.getElementById('push_achievements').checked = settings.push.achievements;
            }
        } catch (e) {
            console.error('Error loading notification settings:', e);
        }
    }
    
    // Add smooth animations
    const notificationItems = document.querySelectorAll('.notification-item');
    notificationItems.forEach((item, index) => {
        item.style.animationDelay = `${index * 0.1}s`;
        item.classList.add('fadeInUp');
    });
    
    // Add hover effects
    notificationItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(5px)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
    });
    
    // Auto-mark as read on view
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && entry.target.classList.contains('unread')) {
                // Mark as read when notification comes into view
                const notificationId = entry.target.dataset.id;
                markNotificationAsRead(notificationId);
            }
        });
    }, { threshold: 0.5 });
    
    notificationItems.forEach(item => {
        observer.observe(item);
    });
});

// Mark notification as read
function markNotificationAsRead(notificationId) {
    // Send AJAX request to mark as read
    fetch('ajax/mark-notification-read.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ notification_id: notificationId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update UI
            const notification = document.querySelector(`[data-id="${notificationId}"]`);
            if (notification) {
                notification.classList.remove('unread');
                notification.classList.add('read');
                
                // Update unread count
                const unreadCount = document.querySelectorAll('.notification-item.unread').length;
                document.querySelector('.stat-number').textContent = unreadCount;
            }
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
    });
}

// Performance monitoring
document.addEventListener('DOMContentLoaded', function() {
    const startTime = performance.now();
    
    window.addEventListener('load', function() {
        const loadTime = performance.now() - startTime;
        
        if (loadTime > 2000) {
            console.warn('Notifications page load time is slow:', loadTime.toFixed(2) + 'ms');
        }
        
        // Send performance data to analytics
        if (typeof gtag !== 'undefined') {
            gtag('event', 'timing_complete', {
                name: 'notifications_load',
                value: Math.round(loadTime)
            });
        }
    });
});
</script>

<?php
// Helper function to get notification title
function getNotificationTitle($notification) {
    switch ($notification['type']) {
        case 'thread_reply':
            return 'New Reply';
        case 'thread_like':
            return 'Thread Liked';
        case 'user_follow':
            return 'New Follower';
        case 'mention':
            return 'You Were Mentioned';
        case 'achievement':
            return 'Achievement Unlocked';
        case 'system':
            return 'System Notification';
        default:
            return 'Notification';
    }
}

include 'includes/footer.php';
?>
