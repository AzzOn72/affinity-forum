<div class="sidebar">
    <!-- Forum Statistics -->
    <div class="sidebar-widget mb-4">
        <div class="widget-header">
            <h5><i class="fas fa-chart-bar"></i> Forum Statistics</h5>
        </div>
        <div class="widget-content">
            <?php
            // Ensure $pdo exists
            if (!isset($pdo)) { $pdo = getDBConnection(); }

            // Safely compute stats if not provided by page
            if (!isset($total_users)) {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE is_active = 1");
                $stmt->execute();
                $total_users = (int)$stmt->fetchColumn();
            }
            if (!isset($total_threads)) {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM threads WHERE is_active = 1");
                $stmt->execute();
                $total_threads = (int)$stmt->fetchColumn();
            }
            if (!isset($total_posts)) {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE is_active = 1");
                $stmt->execute();
                $total_posts = (int)$stmt->fetchColumn();
            }
            if (!isset($online_users)) {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE is_online = 1 AND is_active = 1");
                $stmt->execute();
                $online_users = (int)$stmt->fetchColumn();
            }
            ?>
            <div class="stat-item">
                <span class="stat-label">Total Members</span>
                <span class="stat-value"><?php echo number_format($total_users); ?></span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Total Threads</span>
                <span class="stat-value"><?php echo number_format($total_threads); ?></span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Total Posts</span>
                <span class="stat-value"><?php echo number_format($total_posts); ?></span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Online Now</span>
                <span class="stat-value text-success"><?php echo number_format($online_users); ?></span>
            </div>
        </div>
    </div>

    <!-- Online Users -->
    <div class="sidebar-widget mb-4">
        <div class="widget-header">
            <h5><i class="fas fa-users"></i> Online Users</h5>
        </div>
        <div class="widget-content">
            <?php
            // Get online users
            $stmt = $pdo->prepare("
                SELECT username, avatar, last_login 
                FROM users 
                WHERE is_online = 1 AND is_active = 1
                ORDER BY last_login DESC 
                LIMIT 10
            ");
            $stmt->execute();
            $online_users_list = $stmt->fetchAll();
            
            if (empty($online_users_list)) {
                echo '<p class="text-muted">No users online</p>';
            } else {
                foreach ($online_users_list as $user) {
                    echo '<div class="online-user">';
                    if ($user['avatar']) {
                        echo '<img src="' . htmlspecialchars($user['avatar']) . '" alt="Avatar" class="avatar-xs me-2">';
                    } else {
                        echo '<div class="avatar-placeholder-xs me-2"><i class="fas fa-user"></i></div>';
                    }
                    echo '<span class="username">' . htmlspecialchars($user['username']) . '</span>';
                    echo '<span class="online-indicator"></span>';
                    echo '</div>';
                }
            }
            ?>
        </div>
    </div>

    <!-- Quick Actions -->
    <?php if (isLoggedIn()): ?>
    <div class="sidebar-widget mb-4">
        <div class="widget-header">
            <h5><i class="fas fa-bolt"></i> Quick Actions</h5>
        </div>
        <div class="widget-content">
            <a href="new-thread.php" class="btn btn-primary btn-sm w-100 mb-2">
                <i class="fas fa-plus"></i> New Thread
            </a>
            <a href="messages.php?compose=1" class="btn btn-outline-primary btn-sm w-100 mb-2">
                <i class="fas fa-envelope"></i> Send Message
            </a>
            <a href="profile.php?user=<?php echo urlencode($_SESSION['username']); ?>" class="btn btn-outline-secondary btn-sm w-100">
                <i class="fas fa-user"></i> My Profile
            </a>
        </div>
    </div>
    <?php endif; ?>

    <!-- Recent Threads -->
    <div class="sidebar-widget mb-4">
        <div class="widget-header">
            <h5><i class="fas fa-clock"></i> Recent Threads</h5>
        </div>
        <div class="widget-content">
            <?php
            // Get recent threads (use id, no slug in schema)
            $stmt = $pdo->prepare("
                SELECT t.id, t.title, t.created_at, u.username, s.name as subforum_name
                FROM threads t
                JOIN users u ON t.user_id = u.id
                JOIN subforums s ON t.subforum_id = s.id
                WHERE t.is_active = 1
                ORDER BY t.created_at DESC
                LIMIT 5
            ");
            $stmt->execute();
            $recent_threads = $stmt->fetchAll();
            
            if (empty($recent_threads)) {
                echo '<p class="text-muted">No threads yet</p>';
            } else {
                foreach ($recent_threads as $thread) {
                    echo '<div class="recent-thread">';
                    echo '<a href="thread.php?id=' . (int)$thread['id'] . '" class="thread-title">' . htmlspecialchars($thread['title']) . '</a>';
                    echo '<div class="thread-meta">';
                    echo '<small class="text-muted">by ' . htmlspecialchars($thread['username']) . '</small><br>';
                    echo '<small class="text-muted">in ' . htmlspecialchars($thread['subforum_name']) . '</small><br>';
                    echo '<small class="text-muted">' . formatTimeAgo($thread['created_at']) . '</small>';
                    echo '</div>';
                    echo '</div>';
                }
            }
            ?>
        </div>
    </div>

    <!-- Forum Rules -->
    <div class="sidebar-widget mb-4">
        <div class="widget-header">
            <h5><i class="fas fa-gavel"></i> Forum Rules</h5>
        </div>
        <div class="widget-content">
            <ul class="rules-list">
                <li>Be respectful to all members</li>
                <li>No spam or advertising</li>
                <li>Keep discussions relevant to CS2</li>
                <li>No sharing of malicious software</li>
                <li>Follow community guidelines</li>
            </ul>
        </div>
    </div>

    <!-- Discord Widget -->
    <div class="sidebar-widget mb-4">
        <div class="widget-header">
            <h5><i class="fab fa-discord"></i> Join Our Discord</h5>
        </div>
        <div class="widget-content text-center">
            <p>Connect with the community on Discord!</p>
            <a href="#" class="btn btn-discord btn-sm">
                <i class="fab fa-discord"></i> Join Discord
            </a>
        </div>
    </div>
</div>
