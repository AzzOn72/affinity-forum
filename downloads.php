<?php
require_once 'config.php';

// Get database connection
$pdo = getDBConnection();

// Get downloads with pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 12;
$offset = ($page - 1) * $per_page;

// Get category filter
$category_filter = isset($_GET['category']) ? sanitizeInput($_GET['category']) : '';

// Build query
$where_clause = "WHERE d.is_active = 1";
$params = [];

if ($category_filter) {
    $where_clause .= " AND d.category = ?";
    $params[] = $category_filter;
}

// Get total count
$count_stmt = $pdo->prepare("SELECT COUNT(*) as total FROM downloads d $where_clause");
$count_stmt->execute($params);
$total_downloads = $count_stmt->fetch()['total'];

$total_pages = ceil($total_downloads / $per_page);

// Get downloads
$downloads_query = "
    SELECT d.*, u.username as uploader_name, u.avatar as uploader_avatar
    FROM downloads d
    JOIN users u ON d.uploaded_by = u.id
    $where_clause
    ORDER BY d.created_at DESC
    LIMIT ? OFFSET ?
";

$stmt = $pdo->prepare($downloads_query);
$params[] = $per_page;
$params[] = $offset;
$stmt->execute($params);
$downloads = $stmt->fetchAll();

// Get categories for filter
$stmt = $pdo->prepare("SELECT DISTINCT category FROM downloads WHERE is_active = 1 AND category IS NOT NULL ORDER BY category");
$stmt->execute();
$categories = $stmt->fetchAll();

// Handle file upload (admin only)
$upload_message = '';
if (isAdmin() && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_file'])) {
    if (validateCSRFToken($_POST['csrf_token'])) {
        $title = sanitizeInput($_POST['title']);
        $description = sanitizeInput($_POST['description']);
        $category = sanitizeInput($_POST['category']);
        
        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['file'];
            $file_size = $file['size'];
            $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            // Validate file
            if ($file_size > MAX_FILE_SIZE) {
                $upload_message = 'File size exceeds maximum allowed size.';
            } elseif (!in_array($file_extension, ALLOWED_EXTENSIONS)) {
                $upload_message = 'File type not allowed.';
            } else {
                // Generate unique filename
                $filename = uniqid() . '_' . time() . '.' . $file_extension;
                $upload_path = UPLOAD_PATH . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    // Save to database
                    $stmt = $pdo->prepare("
                        INSERT INTO downloads (title, description, filename, file_size, category, uploaded_by, created_at)
                        VALUES (?, ?, ?, ?, ?, ?, NOW())
                    ");
                    $stmt->execute([$title, $description, $filename, $file_size, $category, $_SESSION['user_id']]);
                    
                    $upload_message = 'File uploaded successfully!';
                    
                    // Redirect to refresh the page
                    redirect('downloads.php?uploaded=1');
                } else {
                    $upload_message = 'Error uploading file.';
                }
            }
        } else {
            $upload_message = 'Please select a file to upload.';
        }
    } else {
        $upload_message = 'Invalid request. Please try again.';
    }
}

$csrf_token = generateCSRFToken();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Downloads - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="Download CS2 cheat tools and resources">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet">
    <link href="css/themes.css" rel="stylesheet">
</head>
<body data-theme="light">
    <?php include 'includes/header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-9">
                <!-- Page Header -->
                <div class="page-header mb-4">
                    <h1><i class="fas fa-download"></i> Downloads</h1>
                    <p class="text-muted">Download CS2 cheat tools, tutorials, and resources</p>
                </div>
                
                <!-- Admin Upload Section -->
                <?php if (isAdmin()): ?>
                <div class="admin-upload-section mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-upload"></i> Upload New File</h5>
                        </div>
                        <div class="card-body">
                            <?php if ($upload_message): ?>
                            <div class="alert alert-info"><?php echo htmlspecialchars($upload_message); ?></div>
                            <?php endif; ?>
                            
                            <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="title" class="form-label">File Title</label>
                                            <input type="text" class="form-control" id="title" name="title" required>
                                            <div class="invalid-feedback">
                                                Please enter a file title.
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="category" class="form-label">Category</label>
                                            <select class="form-select" id="category" name="category" required>
                                                <option value="">Select Category</option>
                                                <option value="Cheats">Cheats</option>
                                                <option value="Tutorials">Tutorials</option>
                                                <option value="Configs">Configs</option>
                                                <option value="Tools">Tools</option>
                                                <option value="Other">Other</option>
                                            </select>
                                            <div class="invalid-feedback">
                                                Please select a category.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                                    <div class="invalid-feedback">
                                        Please enter a description.
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="file" class="form-label">File</label>
                                    <input type="file" class="form-control" id="file" name="file" required>
                                    <div class="form-text">
                                        Allowed file types: <?php echo implode(', ', ALLOWED_EXTENSIONS); ?><br>
                                        Maximum file size: <?php echo formatFileSize(MAX_FILE_SIZE); ?>
                                    </div>
                                    <div class="invalid-feedback">
                                        Please select a file to upload.
                                    </div>
                                </div>
                                
                                <button type="submit" name="upload_file" class="btn btn-primary">
                                    <i class="fas fa-upload me-2"></i>Upload File
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Category Filter -->
                <div class="category-filter mb-4">
                    <div class="btn-group" role="group">
                        <a href="downloads.php" class="btn btn-outline-primary <?php echo !$category_filter ? 'active' : ''; ?>">
                            All Files
                        </a>
                        <?php foreach ($categories as $cat): ?>
                        <a href="downloads.php?category=<?php echo urlencode($cat['category']); ?>" 
                           class="btn btn-outline-primary <?php echo $category_filter === $cat['category'] ? 'active' : ''; ?>">
                            <?php echo htmlspecialchars($cat['category']); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Downloads Grid -->
                <div class="downloads-grid">
                    <?php if (empty($downloads)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-download fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No downloads available</h4>
                        <p class="text-muted">Check back later for new files.</p>
                    </div>
                    <?php else: ?>
                    <div class="row">
                        <?php foreach ($downloads as $download): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="download-card card h-100">
                                <div class="card-body">
                                    <div class="download-icon mb-3">
                                        <i class="fas fa-file-<?php echo getFileIcon($download['filename']); ?> fa-3x text-primary"></i>
                                    </div>
                                    
                                    <h5 class="card-title"><?php echo htmlspecialchars($download['title']); ?></h5>
                                    <p class="card-text text-muted">
                                        <?php echo htmlspecialchars(substr($download['description'], 0, 100)); ?>
                                        <?php if (strlen($download['description']) > 100): ?>...<?php endif; ?>
                                    </p>
                                    
                                    <div class="download-meta mb-3">
                                        <small class="text-muted">
                                            <i class="fas fa-folder me-1"></i>
                                            <?php echo htmlspecialchars($download['category']); ?>
                                        </small>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-hdd me-1"></i>
                                            <?php echo formatFileSize($download['file_size']); ?>
                                        </small>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-download me-1"></i>
                                            <?php echo number_format($download['download_count']); ?> downloads
                                        </small>
                                    </div>
                                    
                                    <div class="uploader-info mb-3">
                                        <div class="d-flex align-items-center">
                                            <?php if ($download['uploader_avatar']): ?>
                                                <img src="<?php echo htmlspecialchars($download['uploader_avatar']); ?>" 
                                                     alt="Avatar" class="avatar-xs me-2">
                                            <?php else: ?>
                                                <div class="avatar-placeholder-xs me-2">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            <?php endif; ?>
                                            <small class="text-muted">
                                                by <?php echo htmlspecialchars($download['uploader_name']); ?>
                                            </small>
                                        </div>
                                        <small class="text-muted">
                                            <?php echo formatTimeAgo($download['created_at']); ?>
                                        </small>
                                    </div>
                                </div>
                                
                                <div class="card-footer">
                                    <div class="d-grid">
                                        <a href="download-file.php?id=<?php echo $download['id']; ?>" 
                                           class="btn btn-primary btn-sm">
                                            <i class="fas fa-download me-2"></i>Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <nav aria-label="Downloads pagination" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo $category_filter ? '&category=' . urlencode($category_filter) : ''; ?>">
                                <i class="fas fa-chevron-left"></i> Previous
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?><?php echo $category_filter ? '&category=' . urlencode($category_filter) : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo $category_filter ? '&category=' . urlencode($category_filter) : ''; ?>">
                                Next <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-3">
                <?php include 'includes/sidebar.php'; ?>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Custom JS -->
    <script src="js/main.js"></script>
    <script src="js/themes.js"></script>
    
    <script>
        // Form validation
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
        
        // File upload preview
        document.getElementById('file').addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const fileSize = file.size;
                const maxSize = <?php echo MAX_FILE_SIZE; ?>;
                
                if (fileSize > maxSize) {
                    alert('File size exceeds maximum allowed size of <?php echo formatFileSize(MAX_FILE_SIZE); ?>');
                    this.value = '';
                }
            }
        });
    </script>
</body>
</html>

<?php
// Helper functions
function getFileIcon($filename) {
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    switch ($extension) {
        case 'pdf':
            return 'pdf';
        case 'zip':
        case 'rar':
        case '7z':
            return 'archive';
        case 'exe':
            return 'code';
        case 'txt':
        case 'md':
            return 'text';
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif':
            return 'image';
        default:
            return 'alt';
    }
}

function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}
?>
