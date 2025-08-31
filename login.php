<?php
require_once 'config.php';
// Ensure PDO connection is initialized before any queries
$pdo = getDBConnection();

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';
$success = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);
    
    // Validate CSRF token
    if (!validateCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid request. Please try again.';
    } else {
        // Check for login attempts
        $ip = $_SERVER['REMOTE_ADDR'];
        $stmt = $pdo->prepare("SELECT * FROM login_attempts WHERE ip_address = ? AND is_blocked = 1 AND blocked_until > NOW()");
        $stmt->execute([$ip]);
        $blocked = $stmt->fetch();
        
        if ($blocked) {
            $error = 'Too many failed login attempts. Please try again later.';
        } else {
            // Find user by email
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND is_banned = 0");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                // Reset login attempts
                $stmt = $pdo->prepare("DELETE FROM login_attempts WHERE ip_address = ?");
                $stmt->execute([$ip]);
                
                // Create session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_role'] = $user['rank'];
                $_SESSION['user_avatar'] = $user['avatar'];
                
                // Update last login and online status
                $stmt = $pdo->prepare("UPDATE users SET last_login = NOW(), is_online = 1 WHERE id = ?");
                $stmt->execute([$user['id']]);
                
                // Create remember me token if requested
                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
                    
                    $stmt = $pdo->prepare("INSERT INTO user_sessions (user_id, session_token, ip_address, user_agent, expires_at) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$user['id'], $token, $ip, $_SERVER['HTTP_USER_AGENT'], $expires]);
                    
                    setcookie('remember_token', $token, strtotime('+30 days'), '/', '', true, true);
                }
                
                // Redirect to intended page or home
                $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
                redirect($redirect);
            } else {
                // Increment login attempts
                $stmt = $pdo->prepare("SELECT * FROM login_attempts WHERE ip_address = ?");
                $stmt->execute([$ip]);
                $attempt = $stmt->fetch();
                
                if ($attempt) {
                    $attempt_count = $attempt['attempt_count'] + 1;
                    $is_blocked = $attempt_count >= MAX_LOGIN_ATTEMPTS;
                    $blocked_until = $is_blocked ? date('Y-m-d H:i:s', strtotime('+15 minutes')) : null;
                    
                    $stmt = $pdo->prepare("UPDATE login_attempts SET attempt_count = ?, is_blocked = ?, blocked_until = ?, last_attempt = NOW() WHERE ip_address = ?");
                    $stmt->execute([$attempt_count, $is_blocked, $blocked_until, $ip]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO login_attempts (ip_address, attempt_count, last_attempt) VALUES (?, 1, NOW())");
                    $stmt->execute([$ip]);
                }
                
                $error = 'Invalid email or password.';
            }
        }
    }
}

// Handle password reset request
if (isset($_POST['reset_password'])) {
    $reset_email = sanitizeInput($_POST['reset_email']);
    
    if (validateCSRFToken($_POST['csrf_token'])) {
        $stmt = $pdo->prepare("SELECT id, username FROM users WHERE email = ? AND is_banned = 0");
        $stmt->execute([$reset_email]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Generate reset token
            $reset_token = bin2hex(random_bytes(32));
            $reset_expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?");
            $stmt->execute([$reset_token, $reset_expires, $user['id']]);
            
            // Send reset email (in production, use proper email service)
            $reset_link = SITE_URL . '/reset-password.php?token=' . $reset_token;
            $subject = 'Password Reset Request - ' . SITE_NAME;
            $message = "Hello {$user['username']},\n\n";
            $message .= "You have requested a password reset for your account.\n";
            $message .= "Click the following link to reset your password:\n\n";
            $message .= $reset_link . "\n\n";
            $message .= "This link will expire in 1 hour.\n\n";
            $message .= "If you didn't request this reset, please ignore this email.\n\n";
            $message .= "Best regards,\n" . SITE_NAME . " Team";
            
            // For development, just show the link
            $success = 'Password reset link sent to your email. For development, use this link: ' . $reset_link;
        } else {
            $error = 'If an account with that email exists, a reset link has been sent.';
        }
    } else {
        $error = 'Invalid request. Please try again.';
    }
}

$csrf_token = generateCSRFToken();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="Login to your Affinity account">
    
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
    
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="auth-container mt-5">
                    <!-- Login Form -->
                    <div class="auth-form" id="loginForm">
                        <div class="auth-header text-center mb-4">
                            <h2><i class="fas fa-sign-in-alt"></i> Welcome Back</h2>
                            <p class="text-muted">Sign in to your Affinity account</p>
                        </div>
                        
                        <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" class="needs-validation" novalidate>
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                           required>
                                </div>
                                <div class="invalid-feedback">
                                    Please enter a valid email address.
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback">
                                    Please enter your password.
                                </div>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">
                                    Remember me for 30 days
                                </label>
                            </div>
                            
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-sign-in-alt me-2"></i>Sign In
                                </button>
                            </div>
                            
                            <div class="text-center">
                                <a href="#" class="text-decoration-none" onclick="showPasswordReset()">
                                    <i class="fas fa-key me-1"></i>Forgot your password?
                                </a>
                            </div>
                        </form>
                        
                        <hr class="my-4">
                        
                        <div class="text-center">
                            <p class="text-muted">Don't have an account?</p>
                            <a href="register.php" class="btn btn-outline-primary">
                                <i class="fas fa-user-plus me-2"></i>Create Account
                            </a>
                        </div>
                    </div>
                    
                    <!-- Password Reset Form -->
                    <div class="auth-form d-none" id="resetForm">
                        <div class="auth-header text-center mb-4">
                            <h2><i class="fas fa-key"></i> Reset Password</h2>
                            <p class="text-muted">Enter your email to receive a reset link</p>
                        </div>
                        
                        <form method="POST" class="needs-validation" novalidate>
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            <input type="hidden" name="reset_password" value="1">
                            
                            <div class="mb-3">
                                <label for="reset_email" class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" class="form-control" id="reset_email" name="reset_email" required>
                                </div>
                                <div class="invalid-feedback">
                                    Please enter a valid email address.
                                </div>
                            </div>
                            
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>Send Reset Link
                                </button>
                            </div>
                        </form>
                        
                        <div class="text-center">
                            <a href="#" class="text-decoration-none" onclick="showLoginForm()">
                                <i class="fas fa-arrow-left me-1"></i>Back to Login
                            </a>
                        </div>
                    </div>
                </div>
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
        // Password visibility toggle
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
        
        // Form switching
        function showPasswordReset() {
            document.getElementById('loginForm').classList.add('d-none');
            document.getElementById('resetForm').classList.remove('d-none');
        }
        
        function showLoginForm() {
            document.getElementById('resetForm').classList.add('d-none');
            document.getElementById('loginForm').classList.remove('d-none');
        }
        
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
    </script>
</body>
</html>
