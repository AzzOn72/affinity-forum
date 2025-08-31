<?php
require_once 'config.php';
$pdo = getDBConnection();

// Get thread ID from URL
$thread_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$thread_id) {
    redirect('index.php');
}

// Get thread information
$stmt = $pdo->prepare("
    SELECT t.*, u.username, u.avatar, u.rank, u.is_online, u.created_at as user_joined,
           sf.name as subforum_name, sf.slug as subforum_slug, sf.id as subforum_id,
           c.name as category_name, c.slug as category_slug
    FROM threads t
    JOIN users u ON t.user_id = u.id
    JOIN subforums sf ON t.subforum_id = sf.id
    JOIN categories c ON sf.category_id = c.id
    WHERE t.id = ? AND t.is_active = 1
");
$stmt->execute([$thread_id]);
$thread = $stmt->fetch();

if (!$thread) {
    redirect('index.php');
}

// Update view count
$stmt = $pdo->prepare("UPDATE threads SET view_count = view_count + 1 WHERE id = ?");
$stmt->execute([$thread_id]);

// Pagination for posts
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 15;
$offset = ($page - 1) * $per_page;

// Get posts
$stmt = $pdo->prepare("
    SELECT p.*, u.username, u.avatar, u.rank, u.is_online, u.created_at as user_joined,
           u.post_count, u.like_count as user_likes, u.is_banned,
           (SELECT COUNT(*) FROM likes WHERE post_id = p.id AND is_active = 1) as actual_likes
    FROM posts p
    JOIN users u ON p.user_id = u.id
    WHERE p.thread_id = ? AND p.is_active = 1
    ORDER BY p.created_at ASC
    LIMIT ? OFFSET ?
");
$stmt->execute([$thread_id, $per_page, $offset]);
$posts = $stmt->fetchAll();

// Get total post count for pagination
$stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE thread_id = ? AND is_active = 1");
$stmt->execute([$thread_id]);
$total_posts = $stmt->fetchColumn();
$total_pages = ceil($total_posts / $per_page);

// Get thread tags if any
$stmt = $pdo->prepare("SELECT tag FROM thread_tags WHERE thread_id = ?");
$stmt->execute([$thread_id]);
$tags = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Check if user can post (thread not locked, user logged in)
$can_post = isLoggedIn() && !$thread['is_locked'];

// Check if user can moderate
$can_moderate = isLoggedIn() && (isAdmin() || $_SESSION['user_id'] == $thread['user_id']);

$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($thread['title']); ?> - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="<?php echo htmlspecialchars(substr(strip_tags($thread['content']), 0, 160)); ?>">
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
                        <li class="breadcrumb-item"><a href="index.php#category-<?php echo $thread['category_id']; ?>"><?php echo htmlspecialchars($thread['category_name']); ?></a></li>
                        <li class="breadcrumb-item"><a href="subforum.php?slug=<?php echo $thread['subforum_slug']; ?>"><?php echo htmlspecialchars($thread['subforum_name']); ?></a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($thread['title']); ?></li>
                    </ol>
                </nav>

                <!-- Error Messages -->
                <?php if (isset($_GET['error'])): ?>
                    <?php
                    $error_message = '';
                    switch ($_GET['error']) {
                        case 'content_too_short':
                            $error_message = 'Your reply is too short. Please write at least 2 characters.';
                            break;
                        case 'content_too_long':
                            $error_message = 'Your reply is too long. Please keep it under 700 characters.';
                            break;
                        case 'thread_locked':
                            $error_message = 'This thread is locked. New replies are not allowed.';
                            break;
                        default:
                            $error_message = 'An error occurred. Please try again.';
                    }
                    ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php echo htmlspecialchars($error_message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Thread Header -->
                <div class="thread-header mb-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h1 class="mb-2">
                                <?php if ($thread['is_pinned']): ?>
                                <i class="fas fa-thumbtack text-warning me-2" title="Pinned Thread"></i>
                                <?php endif; ?>
                                <?php if ($thread['is_locked']): ?>
                                <i class="fas fa-lock text-danger me-2" title="Locked Thread"></i>
                                <?php endif; ?>
                                <?php echo htmlspecialchars($thread['title']); ?>
                            </h1>
                            
                            <!-- Thread Meta -->
                            <div class="thread-meta mb-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <span class="author">
                                            Started by <a href="profile.php?user=<?php echo urlencode($thread['username']); ?>" class="user-link">
                                                <?php echo htmlspecialchars($thread['username']); ?>
                                            </a>
                                            <span class="rank-badge rank-<?php echo $thread['rank']; ?>"><?php echo ucfirst($thread['rank']); ?></span>
                                        </span>
                                        <span class="date">
                                            <i class="fas fa-clock me-1"></i>
                                            <?php echo formatTimeAgo($thread['created_at']); ?>
                                        </span>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <span class="views">
                                            <i class="fas fa-eye me-1"></i>
                                            <?php echo number_format($thread['view_count']); ?> views
                                        </span>
                                        <span class="replies ms-3">
                                            <i class="fas fa-comments me-1"></i>
                                            <?php echo number_format($thread['reply_count']); ?> replies
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Thread Tags -->
                            <?php if (!empty($tags)): ?>
                            <div class="thread-tags mb-3">
                                <?php foreach ($tags as $tag): ?>
                                <span class="badge bg-secondary me-1"><?php echo htmlspecialchars($tag); ?></span>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Thread Actions -->
                        <div class="thread-actions">
                            <?php if ($can_moderate): ?>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="fas fa-cog"></i> Actions
                                </button>
                                <ul class="dropdown-menu">
                                    <?php if (isAdmin()): ?>
                                    <li><a class="dropdown-item" href="#" onclick="toggleThreadPin(<?php echo $thread_id; ?>)">
                                        <i class="fas fa-thumbtack me-2"></i>
                                        <?php echo $thread['is_pinned'] ? 'Unpin Thread' : 'Pin Thread'; ?>
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="toggleThreadLock(<?php echo $thread_id; ?>)">
                                        <i class="fas fa-lock me-2"></i>
                                        <?php echo $thread['is_locked'] ? 'Unlock Thread' : 'Lock Thread'; ?>
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="#" onclick="deleteThread(<?php echo $thread_id; ?>)">
                                        <i class="fas fa-trash me-2"></i>Delete Thread
                                    </a></li>
                                    <?php endif; ?>
                                    <li><a class="dropdown-item" href="edit-thread.php?id=<?php echo $thread_id; ?>">
                                        <i class="fas fa-edit me-2"></i>Edit Thread
                                    </a></li>
                                </ul>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Social Share -->
                            <div class="btn-group ms-2" role="group">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="shareThread()">
                                    <i class="fas fa-share"></i> Share
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="bookmarkThread()">
                                    <i class="fas fa-bookmark"></i> Bookmark
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Posts List -->
                <div class="posts-list">
                    <?php foreach ($posts as $index => $post): ?>
                    <div class="post-item" id="post-<?php echo $post['id']; ?>">
                        <div class="post-avatar">
                            <img src="<?php echo $post['avatar'] ?: 'images/default-avatar.svg'; ?>" 
                                 alt="<?php echo htmlspecialchars($post['username']); ?>" 
                                 class="avatar-img <?php echo $post['is_online'] ? 'online' : ''; ?>">
                            <?php if ($post['is_online']): ?>
                            <span class="online-indicator"></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="post-content">
                            <div class="post-header">
                                <div class="post-author">
                                    <a href="profile.php?user=<?php echo urlencode($post['username']); ?>" class="user-link">
                                        <?php echo htmlspecialchars($post['username']); ?>
                                    </a>
                                    <span class="rank-badge rank-<?php echo $post['rank']; ?>"><?php echo ucfirst($post['rank']); ?></span>
                                    <?php if ($post['is_banned']): ?>
                                    <span class="badge bg-danger">Banned</span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="post-meta">
                                    <span class="post-date">
                                        <i class="fas fa-clock me-1"></i>
                                        <?php echo formatTimeAgo($post['created_at']); ?>
                                    </span>
                                    <?php if ($post['edited_at']): ?>
                                    <span class="edited-badge ms-2" title="Edited <?php echo formatTimeAgo($post['edited_at']); ?>">
                                        <i class="fas fa-edit"></i> Edited
                                    </span>
                                    <?php endif; ?>
                                    <span class="post-number ms-2">
                                        #<?php echo $offset + $index + 1; ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="post-body">
                                <?php if ($post['is_deleted']): ?>
                                <div class="deleted-post">
                                    <em>This post has been deleted.</em>
                                </div>
                                <?php else: ?>
                                <div class="post-text">
                                    <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                                </div>
                                
                                <!-- Post Attachments -->
                                <?php if (!empty($post['attachments'])): ?>
                                <div class="post-attachments mt-3">
                                    <h6><i class="fas fa-paperclip me-2"></i>Attachments:</h6>
                                    <div class="attachments-grid">
                                        <?php 
                                        $attachments = json_decode($post['attachments'], true);
                                        foreach ($attachments as $attachment): 
                                        ?>
                                        <div class="attachment-item">
                                            <a href="download-attachment.php?id=<?php echo $attachment['id']; ?>" class="attachment-link">
                                                <i class="fas fa-file me-2"></i>
                                                <?php echo htmlspecialchars($attachment['name']); ?>
                                                <small class="text-muted">(<?php echo formatFileSize($attachment['size']); ?>)</small>
                                            </a>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php endif; ?>
                            </div>
                            
                            <div class="post-footer">
                                <div class="post-actions">
                                    <!-- Like Button -->
                                    <button type="button" class="btn btn-sm btn-outline-primary post-like-btn" 
                                            data-post-id="<?php echo $post['id']; ?>"
                                            onclick="toggleLike(<?php echo $post['id']; ?>)">
                                        <i class="fas fa-heart"></i>
                                        <span class="like-count"><?php echo number_format($post['actual_likes']); ?></span>
                                    </button>
                                    
                                    <!-- Quote Button -->
                                    <button type="button" class="btn btn-sm btn-outline-secondary" 
                                            onclick="quotePost(<?php echo $post['id']; ?>, '<?php echo htmlspecialchars($post['username']); ?>')">
                                        <i class="fas fa-quote-left"></i> Quote
                                    </button>
                                    
                                    <!-- Reply Button -->
                                    <?php if ($can_post): ?>
                                    <button type="button" class="btn btn-sm btn-outline-success" 
                                            onclick="replyToPost(<?php echo $post['id']; ?>, '<?php echo htmlspecialchars($post['username']); ?>')">
                                        <i class="fas fa-reply"></i> Reply
                                    </button>
                                    <?php endif; ?>
                                    
                                    <!-- Post Actions -->
                                    <?php if (isLoggedIn() && (isAdmin() || $_SESSION['user_id'] == $post['user_id'])): ?>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-warning dropdown-toggle" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#" onclick="editPost(<?php echo $post['id']; ?>)">
                                                <i class="fas fa-edit me-2"></i>Edit Post
                                            </a></li>
                                            <?php if (isAdmin()): ?>
                                            <li><a class="dropdown-item text-danger" href="#" onclick="deletePost(<?php echo $post['id']; ?>)">
                                                <i class="fas fa-trash me-2"></i>Delete Post
                                            </a></li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- User Stats -->
                                <div class="user-stats">
                                    <small class="text-muted">
                                        Posts: <?php echo number_format($post['post_count']); ?> | 
                                        Likes: <?php echo number_format($post['user_likes']); ?> | 
                                        Joined: <?php echo formatDate($post['user_joined']); ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <nav aria-label="Post pagination" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?id=<?php echo $thread_id; ?>&page=<?php echo $page - 1; ?>">
                                <i class="fas fa-chevron-left"></i> Previous
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php
                        $start_page = max(1, $page - 2);
                        $end_page = min($total_pages, $page + 2);
                        
                        if ($start_page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?id=<?php echo $thread_id; ?>&page=1">1</a>
                        </li>
                        <?php if ($start_page > 2): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?id=<?php echo $thread_id; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                        <?php endfor; ?>
                        
                        <?php if ($end_page < $total_pages): ?>
                        <?php if ($end_page < $total_pages - 1): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                        <li class="page-item">
                            <a class="page-link" href="?id=<?php echo $thread_id; ?>&page=<?php echo $total_pages; ?>"><?php echo $total_pages; ?></a>
                        </li>
                        <?php endif; ?>
                        
                        <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?id=<?php echo $thread_id; ?>&page=<?php echo $page + 1; ?>">
                                Next <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>

                <!-- Reply Form -->
                <?php if ($can_post): ?>
                <div class="reply-form mt-4" id="replyForm">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-reply me-2"></i>Post Reply</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="post-reply.php" class="needs-validation" novalidate>
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                <input type="hidden" name="thread_id" value="<?php echo $thread_id; ?>">
                                <input type="hidden" name="quote_post_id" id="quotePostId" value="">
                                
                                <div class="mb-3">
                                    <label for="content" class="form-label">Your Reply</label>
                                    <textarea class="form-control" id="content" name="content" rows="6" required maxlength="700"
                                              placeholder="Write your reply here..."></textarea>
                                    <div class="form-text">Maximum 700 characters.</div>
                                    <div class="invalid-feedback">Please enter your reply.</div>
                                    
                                    <!-- Character Count -->
                                    <div class="text-muted mt-1">
                                        <small><span id="replyCharCount">0</span>/700 characters</small>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="attachments" class="form-label">Attachments (optional)</label>
                                    <input type="file" class="form-control" id="attachments" name="attachments[]" multiple>
                                    <div class="form-text">Max file size: 5MB. Allowed types: jpg, png, gif, pdf, txt, zip</div>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="subscribe" name="subscribe" checked>
                                        <label class="form-check-label" for="subscribe">
                                            Subscribe to this thread
                                        </label>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane me-2"></i>Post Reply
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php elseif ($thread['is_locked']): ?>
                <div class="thread-locked mt-4">
                    <div class="alert alert-warning text-center">
                        <i class="fas fa-lock fa-2x mb-3"></i>
                        <h5>This thread is locked</h5>
                        <p>New replies are not allowed in this thread.</p>
                    </div>
                </div>
                <?php else: ?>
                <div class="login-required mt-4">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-sign-in-alt fa-2x mb-3"></i>
                        <h5>Login Required</h5>
                        <p>You must be logged in to reply to this thread.</p>
                        <a href="login.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </a>
                    </div>
                </div>
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
            // Initialize post actions
            initializePostActions();
            
            // Auto-refresh thread every 60 seconds
            setInterval(function() {
                if (!$(document).data('scrolling')) {
                    refreshThread();
                }
            }, 60000);
            
            // Track scrolling activity
            let scrollTimer;
            $(window).on('scroll', function() {
                $(document).data('scrolling', true);
                clearTimeout(scrollTimer);
                scrollTimer = setTimeout(function() {
                    $(document).data('scrolling', false);
                }, 1000);
            });
        });
        
        function initializePostActions() {
            // Like functionality
            $('.post-like-btn').each(function() {
                const postId = $(this).data('post-id');
                const likeCount = $(this).find('.like-count');
                
                // Check if user has liked this post
                checkLikeStatus(postId, $(this));
            });
        }
        
        function toggleLike(postId) {
            $.post('ajax/toggle-like.php', {
                post_id: postId,
                csrf_token: '<?php echo $csrf_token; ?>'
            })
            .done(function(response) {
                if (response.success) {
                    const btn = $(`.post-like-btn[data-post-id="${postId}"]`);
                    const likeCount = btn.find('.like-count');
                    
                    if (response.liked) {
                        btn.addClass('liked');
                        likeCount.text(parseInt(likeCount.text()) + 1);
                    } else {
                        btn.removeClass('liked');
                        likeCount.text(parseInt(likeCount.text()) - 1);
                    }
                    
                    showNotification(response.message, 'success');
                } else {
                    showNotification(response.message, 'error');
                }
            })
            .fail(function() {
                showNotification('Failed to update like status', 'error');
            });
        }
        
        function checkLikeStatus(postId, btn) {
            $.post('ajax/check-like.php', {
                post_id: postId,
                csrf_token: '<?php echo $csrf_token; ?>'
            })
            .done(function(response) {
                if (response.liked) {
                    btn.addClass('liked');
                }
            });
        }
        
        function quotePost(postId, username) {
            const content = document.getElementById('content');
            const quoteText = `[quote="${username}"]Post #${postId}[/quote]\n\n`;
            content.value = quoteText + content.value;
            content.focus();
            
            // Scroll to reply form
            document.getElementById('replyForm').scrollIntoView({ behavior: 'smooth' });
            
            // Update character count
            updateReplyCharCount();
        }
        
        // Character count for reply form
        function updateReplyCharCount() {
            const content = document.getElementById('content');
            const charCount = content.value.length;
            const maxChars = 700;
            const charCountElement = document.getElementById('replyCharCount');
            
            charCountElement.textContent = charCount + '/' + maxChars;
            
            // Color coding based on character count
            if (charCount > maxChars) {
                charCountElement.className = 'text-danger';
            } else if (charCount > maxChars * 0.9) {
                charCountElement.className = 'text-warning';
            } else {
                charCountElement.className = 'text-muted';
            }
        }
        
        // Add event listener for character count
        $(document).ready(function() {
            $('#content').on('input', updateReplyCharCount);
        });
        
        function replyToPost(postId, username) {
            const content = document.getElementById('content');
            content.value = `@${username} `;
            content.focus();
            
            // Scroll to reply form
            document.getElementById('replyForm').scrollIntoView({ behavior: 'smooth' });
        }
        
        function editPost(postId) {
            // Redirect to edit post page
            window.location.href = `edit-post.php?id=${postId}`;
        }
        
        function deletePost(postId) {
            if (confirm('Are you sure you want to delete this post? This action cannot be undone.')) {
                $.post('ajax/delete-post.php', {
                    post_id: postId,
                    csrf_token: '<?php echo $csrf_token; ?>'
                })
                .done(function(response) {
                    if (response.success) {
                        $(`#post-${postId}`).fadeOut();
                        showNotification('Post deleted successfully', 'success');
                    } else {
                        showNotification(response.message, 'error');
                    }
                })
                .fail(function() {
                    showNotification('Failed to delete post', 'error');
                });
            }
        }
        
        function toggleThreadPin(threadId) {
            $.post('ajax/toggle-thread-pin.php', {
                thread_id: threadId,
                csrf_token: '<?php echo $csrf_token; ?>'
            })
            .done(function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    showNotification(response.message, 'error');
                }
            });
        }
        
        function toggleThreadLock(threadId) {
            $.post('ajax/toggle-thread-lock.php', {
                thread_id: threadId,
                csrf_token: '<?php echo $csrf_token; ?>'
            })
            .done(function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    showNotification(response.message, 'error');
                }
            });
        }
        
        function deleteThread(threadId) {
            if (confirm('Are you sure you want to delete this thread? This action cannot be undone.')) {
                $.post('ajax/delete-thread.php', {
                    thread_id: threadId,
                    csrf_token: '<?php echo $csrf_token; ?>'
                })
                .done(function(response) {
                    if (response.success) {
                        window.location.href = 'index.php';
                    } else {
                        showNotification(response.message, 'error');
                    }
                });
            }
        }
        
        function shareThread() {
            const url = window.location.href;
            const title = '<?php echo addslashes($thread['title']); ?>';
            
            if (navigator.share) {
                navigator.share({
                    title: title,
                    url: url
                });
            } else {
                // Fallback: copy to clipboard
                navigator.clipboard.writeText(url).then(function() {
                    showNotification('Thread URL copied to clipboard!', 'success');
                });
            }
        }
        
        function bookmarkThread() {
            // Add to browser bookmarks
            if (window.sidebar && window.sidebar.addPanel) {
                window.sidebar.addPanel('<?php echo addslashes($thread['title']); ?>', window.location.href, '');
            } else if (window.external && ('AddFavorite' in window.external)) {
                window.external.AddFavorite(window.location.href, '<?php echo addslashes($thread['title']); ?>');
            } else {
                showNotification('Press Ctrl+D to bookmark this page', 'info');
            }
        }
        
        function refreshThread() {
            // Refresh only the posts section
            location.reload();
        }
    </script>
</body>
</html>
