<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

$pdo = getDBConnection();
$user_id = $_SESSION['user_id'];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        redirect('settings.php?error=invalid_token');
    }
    
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_profile') {
        $username = sanitizeInput($_POST['username'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $bio = sanitizeInput($_POST['bio'] ?? '');
        
        // Check if username is available
        if ($username !== $_SESSION['username']) {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
            $stmt->execute([$username, $user_id]);
            if ($stmt->fetch()) {
                redirect('settings.php?error=username_taken');
            }
        }
        
        // Update profile
        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, bio = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$username, $email, $bio, $user_id]);
        
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        
        redirect('settings.php?success=profile_updated');
    }
    
    if ($action === 'update_password') {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        if ($new_password !== $confirm_password) {
            redirect('settings.php?error=passwords_dont_match');
        }
        
        if (strlen($new_password) < 8) {
            redirect('settings.php?error=password_too_short');
        }
        
        // Verify current password
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        
        if (!password_verify($current_password, $user['password'])) {
            redirect('settings.php?error=wrong_password');
        }
        
        // Update password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$hashed_password, $user_id]);
        
        redirect('settings.php?success=password_updated');
    }
    
    if ($action === 'update_avatar') {
        if (isset($_POST['remove_avatar'])) {
            // Remove avatar
            if ($user['avatar'] && file_exists($user['avatar'])) {
                unlink($user['avatar']);
            }
            $stmt = $pdo->prepare("UPDATE users SET avatar = NULL, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$user_id]);
            redirect('settings.php?success=avatar_removed');
        }
        
        // Handle file upload
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['avatar'];
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 2 * 1024 * 1024; // 2MB
            
            // Validate file type
            if (!in_array($file['type'], $allowed_types)) {
                redirect('settings.php?error=invalid_file_type');
            }
            
            // Validate file size
            if ($file['size'] > $max_size) {
                redirect('settings.php?error=file_too_large');
            }
            
            // Create uploads directory if it doesn't exist
            $upload_dir = 'uploads/avatars/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'avatar_' . $user_id . '_' . time() . '.' . $extension;
            $filepath = $upload_dir . $filename;
            
            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                // Remove old avatar if exists
                if ($user['avatar'] && file_exists($user['avatar'])) {
                    unlink($user['avatar']);
                }
                
                // Update database
                $stmt = $pdo->prepare("UPDATE users SET avatar = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$filepath, $user_id]);
                
                redirect('settings.php?success=avatar_updated');
            } else {
                redirect('settings.php?error=upload_failed');
            }
        } else {
            redirect('settings.php?error=no_file');
        }
    }
}

// Get user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - <?php echo SITE_NAME; ?></title>
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
                <nav aria-label="breadcrumb" class="mt-3">
                    <ol class="breadcrumb premium-breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Settings</li>
                    </ol>
                </nav>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="premium-heading mb-2">
                            <i class="fas fa-cog me-2"></i>Account Settings
                        </h1>
                        <p class="text-muted mb-0">Customize your experience and manage your account</p>
                    </div>
                    <div class="premium-badge">
                        <?php echo ucfirst($user['rank']); ?> Member
                    </div>
                </div>

                <?php if (isset($_GET['success'])): ?>
                <div class="premium-alert alert-success mb-4">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php
                    switch ($_GET['success']) {
                        case 'profile_updated': echo 'Profile updated successfully!'; break;
                        case 'password_updated': echo 'Password updated successfully!'; break;
                        case 'avatar_updated': echo 'Avatar updated successfully!'; break;
                        case 'avatar_removed': echo 'Avatar removed successfully!'; break;
                        default: echo 'Settings updated successfully!';
                    }
                    ?>
                </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                <div class="premium-alert alert-danger mb-4">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php
                    switch ($_GET['error']) {
                        case 'invalid_token': echo 'Invalid security token. Please try again.'; break;
                        case 'username_taken': echo 'Username is already taken. Please choose another.'; break;
                        case 'passwords_dont_match': echo 'New passwords do not match.'; break;
                        case 'password_too_short': echo 'Password must be at least 8 characters long.'; break;
                        case 'wrong_password': echo 'Current password is incorrect.'; break;
                        case 'invalid_file_type': echo 'Invalid file type. Please upload JPG, PNG, or GIF images only.'; break;
                        case 'file_too_large': echo 'File is too large. Maximum size is 2MB.'; break;
                        case 'upload_failed': echo 'Failed to upload file. Please try again.'; break;
                        case 'no_file': echo 'No file was selected for upload.'; break;
                        default: echo 'An error occurred. Please try again.';
                    }
                    ?>
                </div>
                <?php endif; ?>

                <!-- Profile Settings -->
                <div class="premium-card p-4 mb-4">
                    <h4 class="premium-heading mb-4">
                        <i class="fas fa-user me-2"></i>Profile Information
                    </h4>
                    
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <input type="hidden" name="action" value="update_profile">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control premium-input" id="username" name="username" 
                                       value="<?php echo htmlspecialchars($user['username']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control premium-input" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="bio" class="form-label">Bio</label>
                            <textarea class="form-control premium-input" id="bio" name="bio" rows="3" 
                                      placeholder="Tell us about yourself..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-premium">
                            <i class="fas fa-save me-2"></i>Update Profile
                        </button>
                    </form>
                </div>

                <!-- Avatar Settings -->
                <div class="premium-card p-4 mb-4">
                    <h4 class="premium-heading mb-4">
                        <i class="fas fa-camera me-2"></i>Profile Picture
                    </h4>
                    
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center mb-3">
                            <div class="current-avatar">
                                <img src="<?php echo $user['avatar'] ?: 'images/default-avatar.svg'; ?>" 
                                     alt="Current Avatar" class="avatar-img-large mb-3">
                                <div class="avatar-info">
                                    <small class="text-muted">Current Avatar</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-9">
                            <form method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                <input type="hidden" name="action" value="update_avatar">
                                
                                <div class="mb-3">
                                    <label for="avatar" class="form-label">Upload New Avatar</label>
                                    <input type="file" class="form-control premium-input" id="avatar" name="avatar" 
                                           accept="image/*" required>
                                    <div class="form-text">
                                        Supported formats: JPG, PNG, GIF. Maximum size: 2MB. Recommended: 200x200 pixels.
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-premium">
                                    <i class="fas fa-upload me-2"></i>Upload Avatar
                                </button>
                                
                                <?php if ($user['avatar']): ?>
                                <button type="submit" class="btn btn-outline-danger ms-2" name="remove_avatar" value="1">
                                    <i class="fas fa-trash me-2"></i>Remove Avatar
                                </button>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Password Settings -->
                <div class="premium-card p-4">
                    <h4 class="premium-heading mb-4">
                        <i class="fas fa-shield-alt me-2"></i>Security Settings
                    </h4>
                    
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <input type="hidden" name="action" value="update_password">
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control premium-input" id="current_password" name="current_password" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control premium-input" id="new_password" name="new_password" 
                                       required minlength="8">
                                <div class="form-text">Minimum 8 characters</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control premium-input" id="confirm_password" name="confirm_password" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-premium">
                            <i class="fas fa-key me-2"></i>Update Password
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="col-lg-3">
                <div class="premium-sidebar">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6><i class="fas fa-info-circle me-2"></i>Account Status</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Member Since:</span>
                                <strong><?php echo formatDate($user['created_at']); ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Posts:</span>
                                <strong><?php echo number_format($user['post_count']); ?></strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Status:</span>
                                <span class="badge bg-success">Active</span>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h6><i class="fas fa-bolt me-2"></i>Quick Actions</h6>
                        </div>
                        <div class="card-body">
                            <a href="profile.php?user=<?php echo urlencode($user['username']); ?>" class="btn btn-outline-primary btn-sm w-100 mb-2">
                                <i class="fas fa-user me-2"></i>View Profile
                            </a>
                            <a href="downloads.php" class="btn btn-outline-success btn-sm w-100">
                                <i class="fas fa-download me-2"></i>Downloads
                            </a>
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
</body>
</html>
