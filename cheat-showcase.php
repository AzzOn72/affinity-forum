<?php
require_once 'config.php';
$pdo = getDBConnection();

// Get cheat statistics
$stmt = $pdo->prepare("SELECT COUNT(*) as total_users FROM users WHERE is_active = 1");
$stmt->execute();
$total_users = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) as total_threads FROM threads WHERE is_active = 1");
$stmt->execute();
$total_threads = $stmt->fetchColumn();

$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium CS2 Cheat - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="Experience the ultimate Counter-Strike 2 cheat with premium features, undetected gameplay, and 24/7 support. Join thousands of satisfied users!">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/themes.css" rel="stylesheet">
</head>
<body data-theme="light">
    <?php include 'includes/header.php'; ?>
    
    <!-- Hero Section -->
    <section class="hero-section premium-bg-gradient text-white text-center py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 text-start">
                    <h1 class="display-3 fw-bold mb-4 premium-text">
                        <span class="text-gradient">DOMINATE</span> CS2
                    </h1>
                    <p class="lead mb-4 premium-text">
                        Experience the most advanced, undetected Counter-Strike 2 cheat. 
                        Join <span class="text-gradient"><?php echo number_format($total_users); ?>+</span> elite players worldwide.
                    </p>
                    <div class="hero-features mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-shield-alt text-gradient me-3 fa-2x"></i>
                            <span class="premium-text">100% Undetected</span>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-bolt text-gradient me-3 fa-2x"></i>
                            <span class="premium-text">Instant Updates</span>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-headset text-gradient me-3 fa-2x"></i>
                            <span class="premium-text">24/7 Premium Support</span>
                        </div>
                    </div>
                    <div class="hero-cta">
                        <a href="#pricing" class="btn btn-premium btn-lg me-3 mb-3">
                            <i class="fas fa-rocket me-2"></i>Get Started Now
                        </a>
                        <a href="#features" class="btn btn-outline-light btn-lg mb-3">
                            <i class="fas fa-play me-2"></i>Watch Demo
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image float-animation">
                        <div class="premium-card p-5 text-center">
                            <i class="fas fa-gamepad fa-5x text-gradient mb-3"></i>
                            <h4>Premium CS2 Cheat</h4>
                            <p class="mb-0">Join the elite today!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="premium-stats text-center">
                        <div class="stat-number text-gradient"><?php echo number_format($total_users); ?>+</div>
                        <div class="stat-label">Active Users</div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="premium-stats text-center">
                        <div class="stat-number text-gradient">99.9%</div>
                        <div class="stat-label">Uptime</div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="premium-stats text-center">
                        <div class="stat-number text-gradient">24/7</div>
                        <div class="stat-label">Support</div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="premium-stats text-center">
                        <div class="stat-number text-gradient">0</div>
                        <div class="stat-label">Bans</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features-section py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="premium-heading display-4 mb-3">Premium Features</h2>
                <p class="lead text-muted">Everything you need to dominate the competition</p>
            </div>
            
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="premium-card h-100 text-center p-4">
                        <div class="premium-icon mb-3">
                            <i class="fas fa-crosshairs fa-3x"></i>
                        </div>
                        <h4 class="premium-heading mb-3">Aimbot</h4>
                        <p class="text-muted">Advanced aimbot with customizable settings, smooth tracking, and intelligent target selection.</p>
                        <ul class="premium-list text-start">
                            <li>Silent Aim</li>
                            <li>Bone Selection</li>
                            <li>FOV Control</li>
                            <li>Smooth Movement</li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-4 mb-4">
                    <div class="premium-card h-100 text-center p-4">
                        <div class="premium-icon mb-3">
                            <i class="fas fa-eye fa-3x"></i>
                        </div>
                        <h4 class="premium-heading mb-3">ESP/Wallhack</h4>
                        <p class="text-muted">See through walls with customizable ESP features for maximum tactical advantage.</p>
                        <ul class="premium-list text-start">
                            <li>Player ESP</li>
                            <li>Item ESP</li>
                            <li>Distance Display</li>
                            <li>Health Bars</li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-4 mb-4">
                    <div class="premium-card h-100 text-center p-4">
                        <div class="premium-icon mb-3">
                            <i class="fas fa-shield-alt fa-3x"></i>
                        </div>
                        <h4 class="premium-heading mb-3">Anti-Detection</h4>
                        <p class="text-muted">State-of-the-art anti-detection technology keeps you safe and undetected.</p>
                        <ul class="premium-list text-start">
                            <li>Memory Protection</li>
                            <li>Signature Evasion</li>
                            <li>Process Hiding</li>
                            <li>Real-time Updates</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="pricing-section py-5 premium-bg-glass">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="premium-heading display-4 mb-3">Choose Your Plan</h2>
                <p class="lead text-muted">Flexible pricing for every player</p>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-lg-4 mb-4">
                    <div class="premium-card h-100 p-4 text-center">
                        <div class="pricing-header mb-4">
                            <h4 class="premium-heading mb-2">Basic</h4>
                            <div class="price">
                                <span class="currency">$</span>
                                <span class="amount">19</span>
                                <span class="period">/month</span>
                            </div>
                        </div>
                        <ul class="premium-list text-start mb-4">
                            <li>Core Features</li>
                            <li>Basic Support</li>
                            <li>Standard Updates</li>
                            <li>1 PC License</li>
                        </ul>
                        <a href="register.php?plan=basic" class="btn btn-premium w-100">
                            Get Started
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-4 mb-4">
                    <div class="premium-card h-100 p-4 text-center position-relative">
                        <div class="popular-badge premium-badge position-absolute top-0 start-50 translate-middle">
                            Most Popular
                        </div>
                        <div class="pricing-header mb-4">
                            <h4 class="premium-heading mb-2">Premium</h4>
                            <div class="price">
                                <span class="currency">$</span>
                                <span class="amount">39</span>
                                <span class="period">/month</span>
                            </div>
                        </div>
                        <ul class="premium-list text-start mb-4">
                            <li>All Basic Features</li>
                            <li>Premium Support</li>
                            <li>Priority Updates</li>
                            <li>2 PC License</li>
                            <li>Custom Configs</li>
                        </ul>
                        <a href="register.php?plan=premium" class="btn btn-premium w-100">
                            Get Premium
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-4 mb-4">
                    <div class="premium-card h-100 p-4 text-center">
                        <div class="pricing-header mb-4">
                            <h4 class="premium-heading mb-2">Ultimate</h4>
                            <div class="price">
                                <span class="currency">$</span>
                                <span class="amount">79</span>
                                <span class="period">/month</span>
                            </div>
                        </div>
                        <ul class="premium-list text-start mb-4">
                            <li>All Premium Features</li>
                            <li>24/7 VIP Support</li>
                            <li>Instant Updates</li>
                            <li>5 PC License</li>
                            <li>Custom Features</li>
                            <li>Priority Queue</li>
                        </ul>
                        <a href="register.php?plan=ultimate" class="btn btn-premium w-100">
                            Go Ultimate
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="premium-heading display-4 mb-3">Frequently Asked Questions</h2>
                <p class="lead text-muted">Everything you need to know</p>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion" id="faqAccordion">
                        <div class="premium-card mb-3">
                            <div class="accordion-header" id="faq1">
                                <button class="accordion-button premium-bg-glass" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1">
                                    Is the cheat undetected?
                                </button>
                            </div>
                            <div id="collapse1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Yes! Our cheat uses advanced anti-detection technology and is constantly updated to stay undetected. We have a 0% ban rate.
                                </div>
                            </div>
                        </div>
                        
                        <div class="premium-card mb-3">
                            <div class="accordion-header" id="faq2">
                                <button class="accordion-button collapsed premium-bg-glass" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2">
                                    How often do you update the cheat?
                                </button>
                            </div>
                            <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    We update our cheat within hours of any CS2 update to ensure compatibility and undetection.
                                </div>
                            </div>
                        </div>
                        
                        <div class="premium-card mb-3">
                            <div class="accordion-header" id="faq3">
                                <button class="accordion-button collapsed premium-bg-glass" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3">
                                    What kind of support do you provide?
                                </button>
                            </div>
                            <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    We provide 24/7 support through our forum, Discord, and ticket system. Premium users get priority support.
                                </div>
                            </div>
                        </div>
                        
                        <div class="premium-card mb-3">
                            <div class="accordion-header" id="faq4">
                                <button class="accordion-button collapsed premium-bg-glass" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4">
                                    Can I use the cheat on multiple PCs?
                                </button>
                            </div>
                            <div id="collapse4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Yes! Our plans include multiple PC licenses. Basic: 1 PC, Premium: 2 PCs, Ultimate: 5 PCs.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section py-5 premium-bg-gradient text-white text-center">
        <div class="container">
            <h2 class="premium-heading display-4 mb-4">Ready to Dominate?</h2>
            <p class="lead mb-4">Join thousands of elite players and experience the ultimate CS2 cheat today!</p>
            <div class="cta-buttons">
                <a href="register.php" class="btn btn-light btn-lg me-3 mb-3">
                    <i class="fas fa-user-plus me-2"></i>Create Account
                </a>
                <a href="login.php" class="btn btn-outline-light btn-lg mb-3">
                    <i class="fas fa-sign-in-alt me-2"></i>Login
                </a>
            </div>
            <p class="small mt-3 opacity-75">No credit card required for trial • Instant access • 24/7 support</p>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="js/main.js"></script>
    <script>
        $(document).ready(function() {
            // Smooth scrolling for anchor links
            $('a[href^="#"]').on('click', function(e) {
                e.preventDefault();
                const target = $(this.getAttribute('href'));
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - 80
                    }, 1000);
                }
            });
            
            // Premium button hover effects
            $('.btn-premium').on('mouseenter', function() {
                $(this).addClass('pulse-glow');
            }).on('mouseleave', function() {
                $(this).removeClass('pulse-glow');
            });
        });
    </script>
</body>
</html>
