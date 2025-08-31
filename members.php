<?php
require_once 'config.php';

// Initialize database connection
$pdo = getDBConnection();

// Get filter parameters
$search = sanitizeInput($_GET['search'] ?? '');
$rank = sanitizeInput($_GET['rank'] ?? '');
$sort = sanitizeInput($_GET['sort'] ?? 'posts');
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 24;
$offset = ($page - 1) * $per_page;

// Build query
$where_conditions = ['u.is_active = 1'];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(u.username LIKE ? OR u.bio LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

if (!empty($rank) && $rank !== 'all') {
    $where_conditions[] = "u.rank = ?";
    $params[] = $rank;
}

$where_clause = 'WHERE ' . implode(' AND ', $where_conditions);

// Get total count
$count_sql = "SELECT COUNT(*) FROM users u {$where_clause}";
$stmt = $pdo->prepare($count_sql);
$stmt->execute($params);
$total_users = $stmt->fetchColumn();

// Get users with proper sorting
$order_clause = match($sort) {
    'posts' => 'ORDER BY u.post_count DESC',
    'threads' => 'ORDER BY u.thread_count DESC',
    'likes' => 'ORDER BY u.like_count DESC',
    'recent' => 'ORDER BY u.created_at DESC',
    'online' => 'ORDER BY u.is_online DESC, u.last_seen DESC',
    default => 'ORDER BY u.post_count DESC'
};

$sql = "
    SELECT u.id, u.username, u.avatar, u.rank, u.bio, u.created_at, 
           u.post_count, u.thread_count, u.like_count, u.reputation, u.level,
           u.is_online, u.last_seen,
           (SELECT COUNT(*) FROM user_badges ub WHERE ub.user_id = u.id AND ub.is_active = 1) as badge_count
    FROM users u 
    {$where_clause}
    {$order_clause}
    LIMIT ? OFFSET ?
";
$params[] = $per_page;
$params[] = $offset;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();

// Get available ranks for filter
$ranks = [];
try {
    $stmt = $pdo->prepare("SELECT DISTINCT rank FROM users WHERE is_active = 1 AND rank IS NOT NULL ORDER BY rank");
    $stmt->execute();
    $ranks = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    error_log("Failed to get ranks: " . $e->getMessage());
}

$csrf_token = generateCSRFToken();

// Set page variables
$page_title = 'Members - ' . SITE_NAME;
$page_description = 'Browse and connect with Affinity community members';
$breadcrumbs = [
    ['text' => 'Members']
];

// Include header
include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section members-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="hero-title">
                    <i class="fas fa-users"></i>
                    Community Members
                </h1>
                <p class="hero-subtitle">
                    Connect with <?php echo number_format($total_users); ?>+ elite Counter-Strike 2 players, 
                    share strategies, and build your network.
                </p>
                <div class="hero-stats">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo number_format($total_users); ?></div>
                        <div class="stat-label">Total Members</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?php echo number_format(array_sum(array_column($users, 'is_online'))); ?></div>
                        <div class="stat-label">Online Now</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?php echo number_format(array_sum(array_column($users, 'post_count'))); ?></div>
                        <div class="stat-label">Total Posts</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="hero-actions">
                    <a href="new-thread.php" class="btn btn-primary btn-lg me-2">
                        <i class="fas fa-plus"></i> Start Discussion
                    </a>
                    <a href="tournaments.php" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-trophy"></i> Join Tournament
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Search and Filters Section -->
<section class="search-filters-section">
    <div class="container">
        <div class="content-card">
            <div class="card-header">
                <h3><i class="fas fa-search"></i> Find Members</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="members.php" class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Search Members</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="<?php echo htmlspecialchars($search); ?>" 
                                   placeholder="Search by username or bio...">
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="rank" class="form-label">Filter by Rank</label>
                        <select class="form-select" id="rank" name="rank">
                            <option value="all">All Ranks</option>
                            <?php foreach ($ranks as $rank_option): ?>
                                <option value="<?php echo htmlspecialchars($rank_option); ?>" 
                                        <?php echo $rank === $rank_option ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($rank_option); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="sort" class="form-label">Sort By</label>
                        <select class="form-select" id="sort" name="sort">
                            <option value="posts" <?php echo $sort === 'posts' ? 'selected' : ''; ?>>Most Posts</option>
                            <option value="threads" <?php echo $sort === 'threads' ? 'selected' : ''; ?>>Most Threads</option>
                            <option value="likes" <?php echo $sort === 'likes' ? 'selected' : ''; ?>>Most Liked</option>
                            <option value="recent" <?php echo $sort === 'recent' ? 'selected' : ''; ?>>Recently Joined</option>
                            <option value="online" <?php echo $sort === 'online' ? 'selected' : ''; ?>>Online Status</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </form>
                
                <?php if (!empty($search) || !empty($rank) || $sort !== 'posts'): ?>
                    <div class="mt-3">
                        <a href="members.php" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times"></i> Clear Filters
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Members Grid -->
<section class="members-grid-section">
    <div class="container">
        <div class="row">
            <?php if (empty($users)): ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h4>No members found</h4>
                    <p class="text-muted">Try adjusting your search criteria.</p>
                </div>
            <?php else: ?>
                <?php foreach ($users as $user): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="member-card content-card h-100">
                            <div class="card-body text-center">
                                <!-- User Avatar -->
                                <div class="member-avatar mb-3 position-relative">
                                    <img src="<?php echo getUserAvatar($user['username']); ?>" 
                                         alt="<?php echo htmlspecialchars($user['username']); ?>" 
                                         class="rounded-circle member-image" width="80" height="80">
                                    <?php if ($user['is_online']): ?>
                                        <span class="online-indicator position-absolute"></span>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- User Info -->
                                <h5 class="member-name mb-2">
                                    <a href="profile.php?user=<?php echo urlencode($user['username']); ?>" 
                                       class="member-link"><?php echo htmlspecialchars($user['username']); ?></a>
                                </h5>
                                
                                <div class="member-rank mb-3">
                                    <span class="badge bg-primary"><?php echo ucfirst($user['rank']); ?></span>
                                    <?php if ($user['level'] > 1): ?>
                                        <span class="badge bg-warning ms-1">Level <?php echo $user['level']; ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if (!empty($user['bio'])): ?>
                                    <p class="member-bio text-muted small mb-3">
                                        <?php echo htmlspecialchars(substr($user['bio'], 0, 100)); ?>
                                        <?php if (strlen($user['bio']) > 100): ?>...<?php endif; ?>
                                    </p>
                                <?php endif; ?>
                                
                                <!-- Member Stats -->
                                <div class="member-stats row text-center mb-3">
                                    <div class="col-4">
                                        <div class="stat-number"><?php echo number_format($user['post_count']); ?></div>
                                        <div class="stat-label">Posts</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-number"><?php echo number_format($user['thread_count']); ?></div>
                                        <div class="stat-label">Threads</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-number"><?php echo number_format($user['like_count']); ?></div>
                                        <div class="stat-label">Likes</div>
                                    </div>
                                </div>
                                
                                <!-- Additional Stats -->
                                <div class="member-extra-stats mb-3">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="stat-number small"><?php echo number_format($user['reputation']); ?></div>
                                            <div class="stat-label small">Reputation</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="stat-number small"><?php echo number_format($user['badge_count']); ?></div>
                                            <div class="stat-label small">Badges</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Member Meta -->
                                <div class="member-meta text-muted small mb-3">
                                    <div class="mb-1">
                                        <i class="fas fa-calendar me-1"></i>
                                        Joined <?php echo formatDate($user['created_at']); ?>
                                    </div>
                                    <div>
                                        <i class="fas fa-clock me-1"></i>
                                        <?php if ($user['is_online']): ?>
                                            <span class="text-success">Online Now</span>
                                        <?php else: ?>
                                            Last seen <?php echo formatTimeAgo($user['last_seen']); ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <!-- Member Actions -->
                                <div class="member-actions">
                                    <a href="profile.php?user=<?php echo urlencode($user['username']); ?>" 
                                       class="btn btn-outline-primary btn-sm me-2">
                                         <i class="fas fa-user me-1"></i> Profile
                                     </a>
                                     <?php if (isLoggedIn() && $_SESSION['user_id'] != $user['id']): ?>
                                         <a href="messages.php?user=<?php echo urlencode($user['username']); ?>" 
                                            class="btn btn-outline-success btn-sm">
                                             <i class="fas fa-envelope me-1"></i> Message
                                         </a>
                                     <?php endif; ?>
                                 </div>
                             </div>
                         </div>
                     </div>
                 <?php endforeach; ?>

                 <!-- Pagination -->
                 <?php if ($total_users > $per_page): ?>
                     <div class="col-12">
                         <nav aria-label="Members pagination" class="mt-4">
                             <ul class="pagination justify-content-center">
                                 <?php if ($page > 1): ?>
                                     <li class="page-item">
                                         <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">
                                             <i class="fas fa-chevron-left"></i> Previous
                                         </a>
                                     </li>
                                 <?php endif; ?>
                                 
                                 <?php
                                 $total_pages = ceil($total_users / $per_page);
                                 $start_page = max(1, $page - 2);
                                 $end_page = min($total_pages, $page + 2);
                                 
                                 if ($start_page > 1): ?>
                                     <li class="page-item">
                                         <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => 1])); ?>">1</a>
                                     </li>
                                     <?php if ($start_page > 2): ?>
                                         <li class="page-item disabled"><span class="page-link">...</span></li>
                                     <?php endif; ?>
                                 <?php endif; ?>
                                 
                                 <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                                     <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                         <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                                     </li>
                                 <?php endfor; ?>
                                 
                                 <?php if ($end_page < $total_pages): ?>
                                     <?php if ($end_page < $total_pages - 1): ?>
                                         <li class="page-item disabled"><span class="page-link">...</span></li>
                                     <?php endif; ?>
                                     <li class="page-item">
                                         <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $total_pages])); ?>"><?php echo $total_pages; ?></a>
                                     </li>
                                 <?php endif; ?>
                                 
                                 <?php if ($page < $total_pages): ?>
                                     <li class="page-item">
                                         <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">
                                             Next <i class="fas fa-chevron-right"></i>
                                         </a>
                                     </li>
                                 <?php endif; ?>
                             </ul>
                         </nav>
                     </div>
                 <?php endif; ?>
             <?php endif; ?>
         </div>
     </div>
 </section>

 <!-- Community Stats Section -->
 <section class="community-stats-section">
     <div class="container">
         <div class="content-card">
             <div class="card-header">
                 <h3><i class="fas fa-chart-bar"></i> Community Statistics</h3>
             </div>
             <div class="card-body">
                 <div class="row">
                     <div class="col-md-3 col-sm-6 mb-3">
                         <div class="stat-card">
                             <div class="stat-icon">
                                 <i class="fas fa-users"></i>
                             </div>
                             <div class="stat-content">
                                 <div class="stat-number"><?php echo number_format($total_users); ?></div>
                                 <div class="stat-label">Total Members</div>
                             </div>
                         </div>
                     </div>
                     <div class="col-md-3 col-sm-6 mb-3">
                         <div class="stat-card">
                             <div class="stat-icon">
                                 <i class="fas fa-clock"></i>
                             </div>
                             <div class="stat-content">
                                 <div class="stat-number"><?php echo number_format(array_sum(array_column($users, 'is_online'))); ?></div>
                                 <div class="stat-label">Online Now</div>
                             </div>
                         </div>
                     </div>
                     <div class="col-md-3 col-sm-6 mb-3">
                         <div class="stat-card">
                             <div class="stat-icon">
                                 <i class="fas fa-comment"></i>
                             </div>
                             <div class="stat-content">
                                 <div class="stat-number"><?php echo number_format(array_sum(array_column($users, 'post_count'))); ?></div>
                                 <div class="stat-label">Total Posts</div>
                             </div>
                         </div>
                     </div>
                     <div class="col-md-3 col-sm-6 mb-3">
                         <div class="stat-card">
                             <div class="stat-icon">
                                 <i class="fas fa-trophy"></i>
                             </div>
                             <div class="stat-content">
                                 <div class="stat-number"><?php echo number_format(array_sum(array_column($users, 'badge_count'))); ?></div>
                                 <div class="stat-label">Badges Earned</div>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </section>

 <!-- Sidebar -->
 <div class="col-lg-3">
     <div class="premium-sidebar">
         <div class="card mb-4">
             <div class="card-header">
                 <h6><i class="fas fa-chart-bar me-2"></i>Community Stats</h6>
             </div>
             <div class="card-body">
                 <div class="d-flex justify-content-between mb-2">
                     <span>Total Members:</span>
                     <strong><?php echo number_format($total_users); ?></strong>
                 </div>
                 <div class="d-flex justify-content-between mb-2">
                     <span>Online Now:</span>
                     <strong class="text-success">
                         <?php 
                         $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE is_online = 1 AND is_active = 1");
                         $stmt->execute();
                         echo number_format($stmt->fetchColumn());
                         ?>
                     </strong>
                 </div>
                 <div class="d-flex justify-content-between">
                     <span>New This Month:</span>
                     <strong>
                         <?php 
                         $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH) AND is_active = 1");
                         $stmt->execute();
                         echo number_format($stmt->fetchColumn());
                         ?>
                     </strong>
                 </div>
             </div>
         </div>

         <div class="card">
             <div class="card-header">
                 <h6><i class="fas fa-trophy me-2"></i>Top Contributors</h6>
             </div>
             <div class="card-body">
                 <?php
                 $stmt = $pdo->prepare("
                     SELECT username, post_count, rank 
                     FROM users 
                     WHERE is_active = 1 
                     ORDER BY post_count DESC 
                     LIMIT 5
                 ");
                 $stmt->execute();
                 $top_contributors = $stmt->fetchAll();
                 ?>
                 
                 <?php foreach ($top_contributors as $index => $contributor): ?>
                 <div class="d-flex align-items-center mb-2">
                     <div class="me-2">
                         <?php if ($index === 0): ?>
                         <i class="fas fa-crown text-warning"></i>
                         <?php elseif ($index === 1): ?>
                         <i class="fas fa-medal text-secondary"></i>
                         <?php elseif ($index === 2): ?>
                         <i class="fas fa-award text-bronze"></i>
                         <?php else: ?>
                         <span class="text-muted"><?php echo $index + 1; ?>.</span>
                         <?php endif; ?>
                     </div>
                     <div class="flex-grow-1">
                         <a href="profile.php?user=<?php echo urlencode($contributor['username']); ?>" 
                            class="premium-link"><?php echo htmlspecialchars($contributor['username']); ?></a>
                         <span class="premium-badge ms-2"><?php echo ucfirst($contributor['rank']); ?></span>
                     </div>
                     <div class="text-muted small">
                         <?php echo number_format($contributor['post_count']); ?> posts
                     </div>
                 </div>
                 <?php endforeach; ?>
             </div>
         </div>
     </div>
 </div>

 <?php include 'includes/footer.php'; ?>
     
     <script>
     // Enhanced members page functionality
     document.addEventListener('DOMContentLoaded', function() {
         // Add smooth animations to member cards
         const memberCards = document.querySelectorAll('.member-card');
         memberCards.forEach((card, index) => {
             card.style.animationDelay = `${index * 0.1}s`;
             card.classList.add('fadeInUp');
         });
         
         // Add hover effects
         memberCards.forEach(card => {
             card.addEventListener('mouseenter', function() {
                 this.style.transform = 'translateY(-5px)';
                 this.style.boxShadow = '0 10px 30px rgba(0,0,0,0.2)';
             });
             
             card.addEventListener('mouseleave', function() {
                 this.style.transform = 'translateY(0)';
                 this.style.boxShadow = '';
             });
         });
         
         // Enhanced search functionality
         const searchInput = document.getElementById('search');
         if (searchInput) {
             let searchTimeout;
             
             searchInput.addEventListener('input', function() {
                 clearTimeout(searchTimeout);
                 const query = this.value.trim();
                 
                 if (query.length >= 2) {
                     searchTimeout = setTimeout(() => {
                         performMemberSearch(query);
                     }, 300);
                 }
             });
         }
         
         // Filter functionality
         const rankFilter = document.getElementById('rank');
         const sortFilter = document.getElementById('sort');
         
         if (rankFilter) {
             rankFilter.addEventListener('change', function() {
                 this.form.submit();
             });
         }
         
         if (sortFilter) {
             sortFilter.addEventListener('change', function() {
                 this.form.submit();
             });
         }
         
         // Add loading states
         const form = document.querySelector('form');
         if (form) {
             form.addEventListener('submit', function() {
                 const submitBtn = this.querySelector('button[type="submit"]');
                 if (submitBtn) {
                     submitBtn.disabled = true;
                     submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...';
                 }
             });
         }
         
         // Initialize tooltips
         const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
         tooltipTriggerList.map(function (tooltipTriggerEl) {
             return new bootstrap.Tooltip(tooltipTriggerEl);
         });
         
         // Performance monitoring
         const startTime = performance.now();
         
         window.addEventListener('load', function() {
             const loadTime = performance.now() - startTime;
             
             if (loadTime > 2000) {
                 console.warn('Members page load time is slow:', loadTime.toFixed(2) + 'ms');
             }
             
             // Send performance data to analytics
             if (typeof gtag !== 'undefined') {
                 gtag('event', 'timing_complete', {
                     name: 'members_load',
                     value: Math.round(loadTime)
                 });
             }
         });
     });
     
     // Member search functionality
     function performMemberSearch(query) {
         // Implement live member search
         console.log('Searching members for:', query);
         
         // Show search suggestions
         const suggestions = [
             'CS2 players',
             'Cheat developers',
             'Tournament organizers',
             'Community moderators'
         ];
         
         // Filter suggestions based on query
         const filtered = suggestions.filter(s => s.toLowerCase().includes(query.toLowerCase()));
         
         if (filtered.length > 0) {
             showSearchSuggestions(filtered);
         }
     }
     
     // Show search suggestions
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
             item.href = `members.php?search=${encodeURIComponent(suggestion)}`;
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
     
     // Enhanced pagination
     function goToPage(page) {
         const currentUrl = new URL(window.location);
         currentUrl.searchParams.set('page', page);
         window.location.href = currentUrl.toString();
     }
     
     // Member card interactions
     function viewMemberProfile(username) {
         window.location.href = `profile.php?user=${encodeURIComponent(username)}`;
     }
     
     function sendMessage(username) {
         window.location.href = `messages.php?user=${encodeURIComponent(username)}`;
     }
     
     // Follow user functionality
     function followUser(username) {
         // Implement follow functionality
         console.log('Following user:', username);
         showNotification(`Now following ${username}!`, 'success');
     }
     
     // Share member profile
     function shareMemberProfile(username) {
         const shareText = `Check out ${username}'s profile on Affinity Forum!`;
         const shareUrl = `${window.location.origin}/profile.php?user=${encodeURIComponent(username)}`;
         
         if (navigator.share) {
             navigator.share({
                 title: 'Member Profile',
                 text: shareText,
                 url: shareUrl
             });
         } else {
             // Fallback to clipboard
             navigator.clipboard.writeText(shareUrl).then(() => {
                 showNotification('Profile link copied to clipboard!', 'success');
             }).catch(() => {
                 showNotification('Failed to copy link', 'error');
             });
         }
     }
     </script>
 </body>
 </html>
