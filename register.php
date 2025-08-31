<?php
require_once 'config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';
$success = '';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $agree_terms = isset($_POST['agree_terms']);
    
    // Validate CSRF token
    if (!validateCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid request. Please try again.';
    } else {
        // Validation
        $errors = [];
        
        // Username validation
        if (strlen($username) < 3 || strlen($username) > 25) {
            $errors[] = 'Username must be between 3 and 25 characters.';
        }
        if (!preg_match('/^[a-zA-Z]+$/', $username)) {
            $errors[] = 'Username can only contain letters (a-z, A-Z).';
        }
        
        // Email validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }
        
        // Password validation
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long.';
        }
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter, one uppercase letter, and one number.';
        }
        
        // Confirm password
        if ($password !== $confirm_password) {
            $errors[] = 'Passwords do not match.';
        }
        
        // Terms agreement
        if (!$agree_terms) {
            $errors[] = 'You must agree to the Terms of Service and Privacy Policy.';
        }
        
        if (empty($errors)) {
            // Get database connection
            $pdo = getDBConnection();
            
            // Check if username already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $errors[] = 'Username is already taken.';
            }
            
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = 'Email address is already registered.';
            }
            
            if (empty($errors)) {
                try {
                    // Hash password
                    $password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => HASH_COST]);
                    
                    // Insert user (align with schema: password column, no verification columns)
                    $stmt = $pdo->prepare("
                        INSERT INTO users (username, email, password, created_at) 
                        VALUES (?, ?, ?, NOW())
                    ");
                    $stmt->execute([$username, $email, $password_hash]);
                    
                    $user_id = $pdo->lastInsertId();
                    
                    // Success message (no email verification required)
                    $success = 'Account created successfully! You can sign in now.';
                    
                } catch (PDOException $e) {
                    $error = 'An error occurred while creating your account. Please try again.';
                    error_log('Registration error: ' . $e->getMessage());
                }
            }
        }
        
        if (!empty($errors)) {
            $error = implode('<br>', $errors);
        }
    }
}

$csrf_token = generateCSRFToken();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="Create your Affinity account">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet">
    <link href="css/themes.css" rel="stylesheet">
</head>
<body class="theme-light">
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="auth-container mt-5">
                    <div class="auth-form">
                        <div class="auth-header text-center mb-4">
                            <h2><i class="fas fa-user-plus"></i> Join Affinity</h2>
                            <p class="text-muted">Create your account and join the CS2 cheat community</p>
                        </div>
                        
                        <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" class="needs-validation" novalidate>
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-user"></i>
                                            </span>
                                            <input type="text" class="form-control" id="username" name="username" 
                                                   value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                                                   required minlength="3" maxlength="25">
                                        </div>
                                        <div class="invalid-feedback">
                                            Username must be between 3 and 25 characters.
                                        </div>
                                        <div class="form-text" id="usernameFeedback">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Letters only (a-z, A-Z), 3-25 characters.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
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
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                            <input type="password" class="form-control" id="password" name="password" 
                                                   required minlength="8">
                                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        <div class="invalid-feedback">
                                            Password must be at least 8 characters long.
                                        </div>
                                        <div class="password-strength mt-2">
                                            <div class="progress" style="height: 5px;">
                                                <div class="progress-bar" id="passwordStrength" role="progressbar" style="width: 0%"></div>
                                            </div>
                                            <small class="text-muted" id="passwordFeedback">Enter a password</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirm Password</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                                   required>
                                        </div>
                                        <div class="invalid-feedback" id="confirmPasswordFeedback">
                                            Please confirm your password.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="agree_terms" name="agree_terms" required>
                                <label class="form-check-label" for="agree_terms">
                                    I agree to the <a href="terms.php" target="_blank">Terms of Service</a> and 
                                    <a href="privacy.php" target="_blank">Privacy Policy</a>
                                </label>
                                <div class="invalid-feedback">
                                    You must agree to the terms and conditions.
                                </div>
                            </div>
                            
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                    <i class="fas fa-user-plus me-2"></i>Create Account
                                </button>
                            </div>
                        </form>
                        
                        <hr class="my-4">
                        
                        <div class="text-center">
                            <p class="text-muted">Already have an account?</p>
                            <a href="login.php" class="btn btn-outline-primary">
                                <i class="fas fa-sign-in-alt me-2"></i>Sign In
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
        
        function updateSubmitState() {
            const strengthWidth = parseInt(document.getElementById('passwordStrength').style.width || '0');
            const termsChecked = document.getElementById('agree_terms').checked;
            const btn = document.getElementById('submitBtn');
            btn.disabled = !(strengthWidth >= 75 && termsChecked);
        }
        
        // Password strength checker
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('passwordStrength');
            const feedback = document.getElementById('passwordFeedback');
            
            let strength = 0;
            let feedbackText = '';
            
            if (password.length >= 8) strength += 25;
            if (/[a-z]/.test(password)) strength += 25;
            if (/[A-Z]/.test(password)) strength += 25;
            if (/\d/.test(password)) strength += 25;
            
            strengthBar.style.width = strength + '%';
            
            if (strength === 0) { feedbackText = 'Enter a password'; strengthBar.className = 'progress-bar'; }
            else if (strength <= 25) { feedbackText = 'Very weak'; strengthBar.className = 'progress-bar bg-danger'; }
            else if (strength <= 50) { feedbackText = 'Weak'; strengthBar.className = 'progress-bar bg-warning'; }
            else if (strength <= 75) { feedbackText = 'Good'; strengthBar.className = 'progress-bar bg-info'; }
            else { feedbackText = 'Strong'; strengthBar.className = 'progress-bar bg-success'; }
            
            feedback.textContent = feedbackText;
            updateSubmitState();
        });
        
        // Confirm password validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            const feedback = document.getElementById('confirmPasswordFeedback');
            
            if (confirmPassword === '') {
                feedback.textContent = 'Please confirm your password.';
                this.setCustomValidity('');
            } else if (password !== confirmPassword) {
                feedback.textContent = 'Passwords do not match.';
                this.setCustomValidity('Passwords do not match.');
            } else {
                feedback.textContent = 'Passwords match!';
                this.setCustomValidity('');
            }
        });
        
        // Real-time username availability check
        let usernameTimeout;
        document.getElementById('username').addEventListener('input', function() {
            const username = this.value;
            const feedback = document.getElementById('usernameFeedback');
            const csrfToken = document.querySelector('input[name="csrf_token"]').value;
            
            clearTimeout(usernameTimeout);
            
            // Check username format first
            if (username.length < 3) {
                feedback.innerHTML = '<i class="fas fa-info-circle me-1"></i>Username must be at least 3 characters.';
                feedback.className = 'form-text';
            } else if (username.length > 25) {
                feedback.innerHTML = '<i class="fas fa-times-circle text-danger me-1"></i>Username must be 25 characters or less.';
                feedback.className = 'form-text text-danger';
            } else if (!/^[a-zA-Z]+$/.test(username)) {
                feedback.innerHTML = '<i class="fas fa-times-circle text-danger me-1"></i>Username can only contain letters (a-z, A-Z).';
                feedback.className = 'form-text text-danger';
            } else {
                usernameTimeout = setTimeout(function() {
                    $.ajax({
                        url: 'ajax/check-username.php',
                        method: 'POST',
                        data: { username: username, csrf_token: csrfToken },
                        dataType: 'json',
                        success: function(response) {
                            if (response.available) { feedback.innerHTML = '<i class="fas fa-check-circle text-success me-1"></i>Username is available!'; feedback.className = 'form-text text-success'; }
                            else if (response.error) { feedback.innerHTML = '<i class="fas fa-exclamation-triangle text-warning me-1"></i>' + response.error; feedback.className = 'form-text text-warning'; }
                            else { feedback.innerHTML = '<i class="fas fa-times-circle text-danger me-1"></i>Username is already taken.'; feedback.className = 'form-text text-danger'; }
                        },
                        error: function() { feedback.innerHTML = '<i class="fas fa-exclamation-triangle text-warning me-1"></i>Unable to check username availability.'; feedback.className = 'form-text text-warning'; }
                    });
                }, 400);
            }
        });
        
        // Terms agreement toggle
        document.getElementById('agree_terms').addEventListener('change', updateSubmitState);
        // Initialize state on load
        updateSubmitState();
    </script>
</body>
</html>
