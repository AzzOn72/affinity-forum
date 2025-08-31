<?php
require_once 'config.php';
$pdo = getDBConnection();

// Get subforum slug from URL
$subforum_slug = isset($_GET['slug']) ? sanitizeInput($_GET['slug']) : '';

if (empty($subforum_slug)) {
    redirect('index.php');
}

// Get subforum information
$stmt = $pdo->prepare("
    SELECT sf.*, c.name as category_name, c.slug as category_slug,
           (SELECT COUNT(*) FROM threads WHERE subforum_id = sf.id AND is_active = 1) as thread_count,
           (SELECT COUNT(*) FROM posts p JOIN threads t ON p.thread_id = t.id WHERE t.subforum_id = sf.id AND p.is_active = 1) as post_count
    FROM subforums sf 
    JOIN categories c ON sf.category_id = c.id 
    WHERE sf.slug = ? AND sf.is_active = 1
");
$stmt->execute([$subforum_slug]);
$subforum = $stmt->fetch();

if (!$subforum) {
    redirect('index.php');
}

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Sorting options
$sort = isset($_GET['sort']) ? sanitizeInput($_GET['sort']) : 'last_post';
$order = isset($_GET['order']) ? sanitizeInput($_GET['order']) : 'desc';

$valid_sorts = ['last_post', 'created', 'title', 'author', 'replies', 'views'];
$valid_orders = ['asc', 'desc'];

if (!in_array($sort, $valid_sorts)) $sort = 'last_post';
if (!in_array($order, $valid_orders)) $order = 'desc';

// Build sort query
$sort_query = '';
switch ($sort) {
    case 'last_post':
        $sort_query = 't.last_post_date ' . strtoupper($order);
        break;
    case 'created':
        $sort_query = 't.created_at ' . strtoupper($order);
        break;
    case 'title':
        $sort_query = 't.title ' . strtoupper($order);
        break;
    case 'author':
        $sort_query = 'u.username ' . strtoupper($order);
        break;
    case 'replies':
        $sort_query = 't.reply_count ' . strtoupper($order);
        break;
    case 'views':
        $sort_query = 't.view_count ' . strtoupper($order);
        break;
    default:
        $sort_query = 't.last_post_date DESC';
}

// Get threads
$stmt = $pdo->prepare("
    SELECT t.*, u.username, u.avatar, u.rank, u.is_online,
           (SELECT COUNT(*) FROM posts WHERE thread_id = t.id AND is_active = 1) as actual_replies,
           lp.username as last_poster, lp.avatar as last_poster_avatar
    FROM threads t
    JOIN users u ON t.user_id = u.id
    LEFT JOIN users lp ON t.last_poster_id = lp.id
    WHERE t.subforum_id = ? AND t.is_active = 1
    ORDER BY t.is_pinned DESC, t.is_locked DESC, {$sort_query}
    LIMIT ? OFFSET ?
");
$stmt->execute([$subforum['id'], $per_page, $offset]);
$threads = $stmt->fetchAll();

// Get total thread count for pagination
$stmt = $pdo->prepare("SELECT COUNT(*) FROM threads WHERE subforum_id = ? AND is_active = 1");
$stmt->execute([$subforum['id']]);
$total_threads = $stmt->fetchColumn();
$total_pages = ceil($total_threads / $per_page);

// Update view count for subforum
$stmt = $pdo->prepare("UPDATE subforums SET view_count = view_count + 1 WHERE id = ?");
$stmt->execute([$subforum['id']]);

$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($subforum['name']); ?> - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($subforum['description']); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/themes.css" rel="stylesheet">
</head>
<body data-theme="light">
    <?php include 'includes/header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-9">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="mt-3">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item"><a href="index.php#category-<?php echo $subforum['category_id']; ?>"><?php echo htmlspecialchars($subforum['category_name']); ?></a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($subforum['name']); ?></li>
                    </ol>
                </nav>

                <!-- Subforum Header -->
                <div class="subforum-header mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="mb-2">
                                <i class="<?php echo $subforum['icon']; ?> me-2"></i>
                                <?php echo htmlspecialchars($subforum['name']); ?>
                            </h1>
                            <p class="text-muted mb-0"><?php echo htmlspecialchars($subforum['description']); ?></p>
                        </div>
                        <div>
                            <?php if (isLoggedIn()): ?>
                            <a href="new-thread.php?subforum=<?php echo $subforum['id']; ?>" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>New Thread
                            </a>
                            <?php else: ?>
                            <a href="login.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" class="btn btn-premium me-2">
                                <i class="fas fa-sign-in-alt me-2"></i>Login to Post
                            </a>
                            <a href="register.php" class="btn btn-outline-light">
                                <i class="fas fa-user-plus me-2"></i>Create Account
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Subforum Stats -->
                    <div class="subforum-stats mt-3">
                        <div class="row text-center">
                            <div class="col-md-3">
                                <div class="stat-item">
                                    <i class="fas fa-comments text-primary"></i>
                                    <div class="stat-number"><?php echo number_format($subforum['thread_count']); ?></div>
                                    <div class="stat-label">Threads</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-item">
                                    <i class="fas fa-reply text-success"></i>
                                    <div class="stat-number"><?php echo number_format($subforum['post_count']); ?></div>
                                    <div class="stat-label">Posts</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-item">
                                    <i class="fas fa-eye text-info"></i>
                                    <div class="stat-number"><?php echo number_format($subforum['view_count']); ?></div>
                                    <div class="stat-label">Views</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-item">
                                    <i class="fas fa-users text-warning"></i>
                                    <div class="stat-number"><?php echo number_format($subforum['thread_count'] + $subforum['post_count']); ?></div>
                                    <div class="stat-label">Total Activity</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thread Controls -->
                <div class="thread-controls mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <span class="me-3">Sort by:</span>
                                <div class="btn-group" role="group">
                                    <a href="?slug=<?php echo $subforum_slug; ?>&sort=last_post&order=<?php echo $sort === 'last_post' && $order === 'desc' ? 'asc' : 'desc'; ?>" 
                                       class="btn btn-sm <?php echo $sort === 'last_post' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                        Latest
                                    </a>
                                    <a href="?slug=<?php echo $subforum_slug; ?>&sort=created&order=<?php echo $sort === 'created' && $order === 'desc' ? 'asc' : 'desc'; ?>" 
                                       class="btn btn-sm <?php echo $sort === 'created' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                        Created
                                    </a>
                                    <a href="?slug=<?php echo $subforum_slug; ?>&sort=replies&order=<?php echo $sort === 'replies' && $order === 'desc' ? 'asc' : 'desc'; ?>" 
                                       class="btn btn-sm <?php echo $sort === 'replies' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                        Replies
                                    </a>
                                    <a href="?slug=<?php echo $subforum_slug; ?>&sort=views&order=<?php echo $sort === 'views' && $order === 'desc' ? 'asc' : 'desc'; ?>" 
                                       class="btn btn-sm <?php echo $sort === 'views' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                        Views
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <span class="text-muted">
                                Showing <?php echo $offset + 1; ?>-<?php echo min($offset + $per_page, $total_threads); ?> 
                                of <?php echo number_format($total_threads); ?> threads
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Threads List -->
                <div class="threads-list">
                    <?php if (empty($threads)): ?>
                    <div class="no-threads text-center py-5">
                        <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                        <h4>No threads yet</h4>
                        <p class="text-muted">Be the first to start a discussion in this forum!</p>
                        <?php if (isLoggedIn()): ?>
                        <a href="new-thread.php?subforum=<?php echo $subforum['id']; ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create First Thread
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php else: ?>
                        <?php foreach ($threads as $thread): ?>
                        <div class="thread-item <?php echo $thread['is_pinned'] ? 'thread-pinned' : ''; ?> <?php echo $thread['is_locked'] ? 'thread-locked' : ''; ?>">
                            <div class="thread-avatar">
                                <img src="<?php echo $thread['avatar'] ?: 'images/default-avatar.svg'; ?>" 
                                     alt="<?php echo htmlspecialchars($thread['username']); ?>" 
                                     class="avatar-img <?php echo $thread['is_online'] ? 'online' : ''; ?>">
                                <?php if ($thread['is_online']): ?>
                                <span class="online-indicator"></span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="thread-content">
                                <div class="thread-header">
                                    <div class="thread-title">
                                        <h5 class="mb-1">
                                            <?php if ($thread['is_pinned']): ?>
                                            <i class="fas fa-thumbtack text-warning me-2" title="Pinned Thread"></i>
                                            <?php endif; ?>
                                            <?php if ($thread['is_locked']): ?>
                                            <i class="fas fa-lock text-danger me-2" title="Locked Thread"></i>
                                            <?php endif; ?>
                                            <a href="thread.php?id=<?php echo $thread['id']; ?>" class="thread-link">
                                                <?php echo htmlspecialchars($thread['title']); ?>
                                            </a>
                                        </h5>
                                        <div class="thread-meta">
                                            <span class="author">
                                                by <a href="profile.php?user=<?php echo urlencode($thread['username']); ?>" class="user-link">
                                                    <?php echo htmlspecialchars($thread['username']); ?>
                                                </a>
                                                <span class="rank-badge rank-<?php echo $thread['rank']; ?>"><?php echo ucfirst($thread['rank']); ?></span>
                                            </span>
                                            <span class="date">
                                                <i class="fas fa-clock me-1"></i>
                                                <?php echo formatTimeAgo($thread['created_at']); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="thread-stats">
                                    <div class="stat">
                                        <i class="fas fa-comments text-muted"></i>
                                        <span><?php echo number_format($thread['actual_replies']); ?></span>
                                    </div>
                                    <div class="stat">
                                        <i class="fas fa-eye text-muted"></i>
                                        <span><?php echo number_format($thread['view_count']); ?></span>
                                    </div>
                                    <div class="stat">
                                        <i class="fas fa-heart text-muted"></i>
                                        <span><?php echo number_format($thread['like_count']); ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="thread-last-post">
                                <div class="last-post-info">
                                    <div class="last-poster">
                                        <img src="<?php echo $thread['last_poster_avatar'] ?: 'images/default-avatar.svg'; ?>" 
                                             alt="<?php echo htmlspecialchars($thread['last_poster']); ?>" 
                                             class="avatar-img-sm">
                                        <span class="username"><?php echo htmlspecialchars($thread['last_poster']); ?></span>
                                    </div>
                                    <div class="last-post-date">
                                        <?php echo formatTimeAgo($thread['last_post_date']); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <nav aria-label="Thread pagination" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?slug=<?php echo $subforum_slug; ?>&page=<?php echo $page - 1; ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>">
                                <i class="fas fa-chevron-left"></i> Previous
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php
                        $start_page = max(1, $page - 2);
                        $end_page = min($total_pages, $page + 2);
                        
                        if ($start_page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?slug=<?php echo $subforum_slug; ?>&page=1&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>">1</a>
                        </li>
                        <?php if ($start_page > 2): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?slug=<?php echo $subforum_slug; ?>&page=<?php echo $i; ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>"><?php echo $i; ?></a>
                        </li>
                        <?php endfor; ?>
                        
                        <?php if ($end_page < $total_pages): ?>
                        <?php if ($end_page < $total_pages - 1): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                        <li class="page-item">
                            <a class="page-link" href="?slug=<?php echo $subforum_slug; ?>&page=<?php echo $total_pages; ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>"><?php echo $total_pages; ?></a>
                        </li>
                        <?php endif; ?>
                        
                        <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?slug=<?php echo $subforum_slug; ?>&page=<?php echo $page + 1; ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>">
                                Next <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
            
            <div class="col-lg-3">
                <?php include 'includes/sidebar.php'; ?>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="js/main.js"></script>
    <script>
        // Thread-specific JavaScript
        $(document).ready(function() {
            // Auto-refresh threads every 30 seconds
            setInterval(function() {
                // Only refresh if user is not actively scrolling
                if (!$(document).data('scrolling')) {
                    location.reload();
                }
            }, 30000);
            
            // Track scrolling activity
            let scrollTimer;
            $(window).on('scroll', function() {
                $(document).data('scrolling', true);
                clearTimeout(scrollTimer);
                scrollTimer = setTimeout(function() {
                    $(document).data('scrolling', false);
                }, 1000);
            });
            
            // Thread item hover effects
            $('.thread-item').hover(
                function() {
                    $(this).addClass('thread-hover');
                },
                function() {
                    $(this).removeClass('thread-hover');
                }
            );
        });
    </script>
</body>
</html>
