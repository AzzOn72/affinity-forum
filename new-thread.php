<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

$pdo = getDBConnection();

// Get subforum ID from URL
$subforum_id = isset($_GET['subforum']) ? intval($_GET['subforum']) : 0;

if (!$subforum_id) {
    redirect('index.php');
}

// Get subforum information
$stmt = $pdo->prepare("
    SELECT sf.*, c.name as category_name 
    FROM subforums sf 
    JOIN categories c ON sf.category_id = c.id 
    WHERE sf.id = ? AND sf.is_active = 1
");
$stmt->execute([$subforum_id]);
$subforum = $stmt->fetch();

if (!$subforum) {
    redirect('index.php');
}

// Check if user can post in this subforum
if ($subforum['is_locked']) {
    redirect('subforum.php?slug=' . $subforum['slug'] . '&error=locked');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $title = sanitizeInput($_POST['title'] ?? '');
        $content = sanitizeInput($_POST['content'] ?? '');
        $tags = sanitizeInput($_POST['tags'] ?? '');
        $is_announcement = isset($_POST['is_announcement']) && isAdmin();
        $subscribe = isset($_POST['subscribe']);
        
        // Validation
        if (empty($title) || strlen($title) < 5 || strlen($title) > 200) {
            $error = 'Title must be between 5 and 200 characters.';
        } elseif (empty($content) || strlen($content) < 10) {
            $error = 'Content must be at least 10 characters long.';
        } elseif (strlen($content) > 700) {
            $error = 'Content must be 700 characters or less.';
        } else {
            try {
                $pdo->beginTransaction();
                
                // Insert thread
                $stmt = $pdo->prepare("
                    INSERT INTO threads (title, content, user_id, subforum_id, is_announcement, created_at, updated_at, last_post_date, last_poster_id)
                    VALUES (?, ?, ?, ?, ?, NOW(), NOW(), NOW(), ?)
                ");
                $stmt->execute([$title, $content, $_SESSION['user_id'], $subforum_id, $is_announcement, $_SESSION['user_id']]);
                $thread_id = $pdo->lastInsertId();
                
                // Insert first post
                $stmt = $pdo->prepare("
                    INSERT INTO posts (content, user_id, thread_id, created_at, updated_at)
                    VALUES (?, ?, ?, NOW(), NOW())
                ");
                $stmt->execute([$content, $_SESSION['user_id'], $thread_id]);
                $post_id = $pdo->lastInsertId();
                
                // Update thread with first post info
                $stmt = $pdo->prepare("
                    UPDATE threads SET first_post_id = ? WHERE id = ?
                ");
                $stmt->execute([$post_id, $thread_id]);
                
                // Handle tags
                if (!empty($tags)) {
                    $tag_array = array_map('trim', explode(',', $tags));
                    $tag_array = array_filter($tag_array);
                    
                    foreach ($tag_array as $tag) {
                        if (strlen($tag) <= 50) {
                            $stmt = $pdo->prepare("
                                INSERT INTO thread_tags (thread_id, tag) VALUES (?, ?)
                            ");
                            $stmt->execute([$thread_id, $tag]);
                        }
                    }
                }
                
                // Handle file attachments
                if (!empty($_FILES['attachments']['name'][0])) {
                    $upload_dir = 'uploads/attachments/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    foreach ($_FILES['attachments']['tmp_name'] as $key => $tmp_name) {
                        if ($_FILES['attachments']['error'][$key] === UPLOAD_ERR_OK) {
                            $filename = $_FILES['attachments']['name'][$key];
                            $filesize = $_FILES['attachments']['size'][$key];
                            $filetype = $_FILES['attachments']['type'][$key];
                            
                            // Validate file
                            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'text/plain', 'application/zip'];
                            $max_size = 5 * 1024 * 1024; // 5MB
                            
                            if (in_array($filetype, $allowed_types) && $filesize <= $max_size) {
                                $unique_filename = uniqid() . '_' . $filename;
                                $filepath = $upload_dir . $unique_filename;
                                
                                if (move_uploaded_file($tmp_name, $filepath)) {
                                    // Insert attachment record
                                    $stmt = $pdo->prepare("
                                        INSERT INTO post_attachments (post_id, filename, original_name, file_size, file_type, file_path)
                                        VALUES (?, ?, ?, ?, ?, ?)
                                    ");
                                    $stmt->execute([$post_id, $unique_filename, $filename, $filesize, $filetype, $filepath]);
                                }
                            }
                        }
                    }
                }
                
                // Update user post count
                $stmt = $pdo->prepare("
                    UPDATE users SET post_count = post_count + 1 WHERE id = ?
                ");
                $stmt->execute([$_SESSION['user_id']]);
                
                // Update subforum stats
                $stmt = $pdo->prepare("
                    UPDATE subforums SET thread_count = thread_count + 1, post_count = post_count + 1 WHERE id = ?
                ");
                $stmt->execute([$subforum_id]);
                
                // Subscribe to thread if requested
                if ($subscribe) {
                    $stmt = $pdo->prepare("
                        INSERT INTO thread_subscriptions (user_id, thread_id, created_at) VALUES (?, ?, NOW())
                    ");
                    $stmt->execute([$_SESSION['user_id'], $thread_id]);
                }
                
                $pdo->commit();
                
                // Redirect to the new thread
                redirect('thread.php?id=' . $thread_id . '&created=1');
                
            } catch (Exception $e) {
                $pdo->rollBack();
                $error = 'Failed to create thread. Please try again.';
                error_log('Thread creation error: ' . $e->getMessage());
            }
        }
    }
}

$csrf_token = generateCSRFToken();

// Get available tags for suggestions
$stmt = $pdo->prepare("
    SELECT tag, COUNT(*) as usage_count 
    FROM thread_tags 
    GROUP BY tag 
    ORDER BY usage_count DESC 
    LIMIT 20
");
$stmt->execute();
$popular_tags = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Thread - <?php echo htmlspecialchars($subforum['name']); ?> - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="Create a new thread in <?php echo htmlspecialchars($subforum['name']); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
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
                        <li class="breadcrumb-item"><a href="subforum.php?slug=<?php echo $subforum['slug']; ?>"><?php echo htmlspecialchars($subforum['name']); ?></a></li>
                        <li class="breadcrumb-item active" aria-current="page">New Thread</li>
                    </ol>
                </nav>

                <!-- Page Header -->
                <div class="page-header mb-4">
                    <h1><i class="fas fa-plus me-2"></i>Create New Thread</h1>
                    <p class="text-muted">Posting in: <strong><?php echo htmlspecialchars($subforum['name']); ?></strong></p>
                </div>

                <!-- Error/Success Messages -->
                <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <!-- Thread Creation Form -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-edit me-2"></i>Thread Details</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            
                            <!-- Title -->
                            <div class="mb-3">
                                <label for="title" class="form-label">Thread Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" 
                                       required minlength="5" maxlength="200"
                                       placeholder="Enter a descriptive title for your thread">
                                <div class="form-text">Title should be clear and descriptive (5-200 characters)</div>
                                <div class="invalid-feedback">Please enter a valid title (5-200 characters).</div>
                            </div>
                            
                            <!-- Content -->
                            <div class="mb-3">
                                <label for="content" class="form-label">Thread Content <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="content" name="content" rows="12" required minlength="10" maxlength="700"
                                          placeholder="Write your thread content here..."><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea>
                                <div class="form-text">Use the toolbar above to format your text. Minimum 10 characters, maximum 700 characters.</div>
                                <div class="invalid-feedback">Please enter your thread content (minimum 10 characters).</div>
                            </div>
                            
                            <!-- Tags -->
                            <div class="mb-3">
                                <label for="tags" class="form-label">Tags (optional)</label>
                                <input type="text" class="form-control" id="tags" name="tags" 
                                       value="<?php echo htmlspecialchars($_POST['tags'] ?? ''); ?>"
                                       placeholder="Enter tags separated by commas (e.g., cs2, cheat, guide)">
                                <div class="form-text">Tags help others find your thread. Separate multiple tags with commas.</div>
                                
                                <!-- Popular Tags -->
                                <?php if (!empty($popular_tags)): ?>
                                <div class="popular-tags mt-2">
                                    <small class="text-muted">Popular tags: </small>
                                    <?php foreach ($popular_tags as $tag): ?>
                                    <span class="badge bg-secondary me-1 tag-suggestion" 
                                          onclick="addTag('<?php echo htmlspecialchars($tag['tag']); ?>')">
                                        <?php echo htmlspecialchars($tag['tag']); ?>
                                        <small>(<?php echo $tag['usage_count']; ?>)</small>
                                    </span>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- File Attachments -->
                            <div class="mb-3">
                                <label for="attachments" class="form-label">Attachments (optional)</label>
                                <input type="file" class="form-control" id="attachments" name="attachments[]" multiple>
                                <div class="form-text">
                                    Max file size: 5MB per file. Allowed types: JPG, PNG, GIF, PDF, TXT, ZIP
                                </div>
                                
                                <!-- Attachment Preview -->
                                <div id="attachmentPreview" class="mt-2"></div>
                            </div>
                            
                            <!-- Thread Options -->
                            <div class="mb-3">
                                <label class="form-label">Thread Options</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="subscribe" name="subscribe" checked>
                                            <label class="form-check-label" for="subscribe">
                                                Subscribe to this thread
                                            </label>
                                            <div class="form-text">Receive notifications for new replies</div>
                                        </div>
                                    </div>
                                    <?php if (isAdmin()): ?>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_announcement" name="is_announcement">
                                            <label class="form-check-label" for="is_announcement">
                                                Mark as announcement
                                            </label>
                                            <div class="form-text">Pin this thread at the top of the forum</div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Character Count -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-muted">
                                        <span id="charCount">0</span> characters
                                    </div>
                                    <div class="text-muted">
                                        <span id="wordCount">0</span> words
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Form Actions -->
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="subforum.php?slug=<?php echo $subforum['slug']; ?>" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Cancel
                                    </a>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-outline-primary me-2" onclick="previewThread()">
                                        <i class="fas fa-eye me-2"></i>Preview
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane me-2"></i>Create Thread
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Thread Preview Modal -->
                <div class="modal fade" id="previewModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Thread Preview</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div id="previewContent"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3">
                <!-- Thread Guidelines -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6><i class="fas fa-info-circle me-2"></i>Thread Guidelines</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Use clear, descriptive titles
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Provide detailed, helpful content
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Use appropriate tags
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Follow forum rules
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Be respectful to others
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Subforum Info -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6><i class="fas fa-comments me-2"></i>Forum Information</h6>
                    </div>
                    <div class="card-body">
                        <h6><?php echo htmlspecialchars($subforum['name']); ?></h6>
                        <p class="text-muted small"><?php echo htmlspecialchars($subforum['description']); ?></p>
                        
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="stat-item">
                                    <div class="stat-number"><?php echo number_format($subforum['thread_count']); ?></div>
                                    <div class="stat-label">Threads</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-item">
                                    <div class="stat-number"><?php echo number_format($subforum['post_count']); ?></div>
                                    <div class="stat-label">Posts</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Threads -->
                <div class="card">
                    <div class="card-header">
                        <h6><i class="fas fa-clock me-2"></i>Recent Threads</h6>
                    </div>
                    <div class="card-body">
                        <?php
                        $stmt = $pdo->prepare("
                            SELECT t.title, t.created_at, u.username
                            FROM threads t
                            JOIN users u ON t.user_id = u.id
                            WHERE t.subforum_id = ? AND t.is_active = 1
                            ORDER BY t.created_at DESC
                            LIMIT 5
                        ");
                        $stmt->execute([$subforum_id]);
                        $recent_threads = $stmt->fetchAll();
                        ?>
                        
                        <?php if (!empty($recent_threads)): ?>
                        <div class="recent-threads">
                            <?php foreach ($recent_threads as $thread): ?>
                            <div class="recent-thread mb-2">
                                <a href="thread.php?id=<?php echo $thread['id']; ?>" class="text-decoration-none">
                                    <div class="thread-title small"><?php echo htmlspecialchars($thread['title']); ?></div>
                                    <div class="thread-meta text-muted small">
                                        by <?php echo htmlspecialchars($thread['username']); ?> • 
                                        <?php echo formatTimeAgo($thread['created_at']); ?>
                                    </div>
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <p class="text-muted small mb-0">No threads yet in this forum.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script src="js/main.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize rich text editor
            $('#content').summernote({
                height: 300,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'italic', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview']]
                ],
                callbacks: {
                    onChange: function(contents, $editable) {
                        updateCounts();
                    }
                }
            });
            
            // Initialize character and word counting
            updateCounts();
            
            // File attachment preview
            $('#attachments').on('change', function() {
                const files = this.files;
                const preview = $('#attachmentPreview');
                preview.empty();
                
                if (files.length > 0) {
                    preview.append('<h6 class="mt-2">Selected Files:</h6>');
                    
                    for (let i = 0; i < files.length; i++) {
                        const file = files[i];
                        const fileSize = (file.size / 1024 / 1024).toFixed(2);
                        
                        const fileItem = $(`
                            <div class="attachment-item d-flex align-items-center p-2 border rounded mb-2">
                                <i class="fas fa-file me-2"></i>
                                <div class="flex-grow-1">
                                    <div class="filename">${file.name}</div>
                                    <small class="text-muted">${fileSize} MB</small>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeAttachment(${i})">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        `);
                        
                        preview.append(fileItem);
                    }
                }
            });
            
            // Form validation
            $('form').on('submit', function(e) {
                if (!this.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                $(this).addClass('was-validated');
            });
        });
        
        function updateCounts() {
            const content = $('#content').val();
            const charCount = content.length;
            const maxChars = 700;
            const wordCount = content.trim() === '' ? 0 : content.trim().split(/\s+/).length;
            
            // Update character count with color coding
            const charCountElement = $('#charCount');
            charCountElement.text(charCount + '/' + maxChars);
            
            // Color coding based on character count
            if (charCount > maxChars) {
                charCountElement.removeClass('text-muted text-warning text-danger').addClass('text-danger');
            } else if (charCount > maxChars * 0.9) {
                charCountElement.removeClass('text-muted text-warning text-danger').addClass('text-warning');
            } else {
                charCountElement.removeClass('text-muted text-warning text-danger').addClass('text-muted');
            }
            
            $('#wordCount').text(wordCount);
        }
        
        function addTag(tag) {
            const tagsInput = $('#tags');
            const currentTags = tagsInput.val();
            const tagArray = currentTags ? currentTags.split(',').map(t => t.trim()) : [];
            
            if (!tagArray.includes(tag)) {
                tagArray.push(tag);
                tagsInput.val(tagArray.join(', '));
            }
        }
        
        function removeAttachment(index) {
            const input = document.getElementById('attachments');
            const dt = new DataTransfer();
            
            for (let i = 0; i < input.files.length; i++) {
                if (i !== index) {
                    dt.items.add(input.files[i]);
                }
            }
            
            input.files = dt.files;
            $('#attachments').trigger('change');
        }
        
        function previewThread() {
            const title = $('#title').val();
            const content = $('#content').val();
            
            if (!title || !content) {
                showNotification('Please fill in both title and content to preview', 'warning');
                return;
            }
            
            const previewContent = `
                <div class="thread-preview">
                    <h3>${title}</h3>
                    <div class="thread-meta mb-3">
                        <small class="text-muted">
                            <i class="fas fa-user me-1"></i>Your Username • 
                            <i class="fas fa-clock me-1"></i>Just now
                        </small>
                    </div>
                    <div class="thread-content">
                        ${content}
                    </div>
                </div>
            `;
            
            $('#previewContent').html(previewContent);
            $('#previewModal').modal('show');
        }
        
        // Auto-save draft
        let draftTimer;
        $('input, textarea').on('input', function() {
            clearTimeout(draftTimer);
            draftTimer = setTimeout(function() {
                saveDraft();
            }, 2000);
        });
        
        function saveDraft() {
            const draft = {
                title: $('#title').val(),
                content: $('#content').val(),
                tags: $('#tags').val(),
                timestamp: new Date().toISOString()
            };
            
            localStorage.setItem('thread_draft', JSON.stringify(draft));
        }
        
        function loadDraft() {
            const draft = localStorage.getItem('thread_draft');
            if (draft) {
                const data = JSON.parse(draft);
                $('#title').val(data.title);
                $('#content').val(data.content);
                $('#tags').val(data.tags);
                
                // Update editor if Summernote is initialized
                if ($('#content').data('summernote')) {
                    $('#content').summernote('code', data.content);
                }
                
                updateCounts();
            }
        }
        
        // Load draft on page load
        $(document).ready(function() {
            loadDraft();
        });
        
        // Clear draft when form is submitted successfully
        $('form').on('submit', function() {
            localStorage.removeItem('thread_draft');
        });
    </script>
</body>
</html>
