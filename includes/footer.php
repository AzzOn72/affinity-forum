            </div> <!-- End of content-area -->
        </div> <!-- End of container-fluid -->
    </main>

<!-- ===== ULTRA PREMIUM FOOTER ===== -->
<footer class="ultra-premium-footer">
    <div class="footer-background">
        <div class="footer-particles"></div>
        <div class="footer-waves">
            <div class="wave wave-1"></div>
            <div class="wave wave-2"></div>
        </div>
    </div>
    
    <div class="container">
        <!-- Main Footer Content -->
        <div class="footer-main">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="footer-brand">
                        <div class="brand-logo">
                            <div class="logo-icon">
                                <i class="fas fa-shield-alt"></i>
                                <div class="logo-glow"></div>
                            </div>
                            <div class="brand-info">
                                <h4 class="brand-name">Affinity</h4>
                                <p class="brand-tagline">Elite CS2 Community</p>
                            </div>
                        </div>
                        
                        <p class="footer-description">
                            The most advanced and undetectable Counter-Strike 2 cheat community. 
                            Join thousands of elite players and dominate every match with confidence.
                        </p>
                        
                        <div class="footer-stats">
                            <div class="footer-stat">
                                <div class="stat-number" id="footerMembers"><?php echo rand(15000, 25000); ?>+</div>
                                <div class="stat-label">Members</div>
                            </div>
                            <div class="footer-stat">
                                <div class="stat-number">732+</div>
                                <div class="stat-label">Days Undetected</div>
                            </div>
                            <div class="footer-stat">
                                <div class="stat-number">99.7%</div>
                                <div class="stat-label">Satisfaction</div>
                            </div>
                        </div>
                        
                        <div class="social-links">
                            <a href="#" class="social-link" title="Discord Server">
                                <i class="fab fa-discord"></i>
                                <div class="social-glow"></div>
                            </a>
                            <a href="#" class="social-link" title="Telegram Group">
                                <i class="fab fa-telegram"></i>
                                <div class="social-glow"></div>
                            </a>
                            <a href="#" class="social-link" title="YouTube Channel">
                                <i class="fab fa-youtube"></i>
                                <div class="social-glow"></div>
                            </a>
                            <a href="#" class="social-link" title="GitHub Repository">
                                <i class="fab fa-github"></i>
                                <div class="social-glow"></div>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <div class="footer-section">
                        <h6 class="footer-section-title">
                            <i class="fas fa-download me-2"></i>Downloads
                        </h6>
                        <ul class="footer-links">
                            <li><a href="downloads.php" class="footer-link">
                                <i class="fas fa-shield-alt me-2"></i>Affinity Cheat
                            </a></li>
                            <li><a href="#" class="footer-link">
                                <i class="fas fa-cog me-2"></i>Config Generator
                            </a></li>
                            <li><a href="#" class="footer-link">
                                <i class="fas fa-book me-2"></i>User Manual
                            </a></li>
                            <li><a href="#" class="footer-link">
                                <i class="fas fa-video me-2"></i>Video Tutorials
                            </a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <div class="footer-section">
                        <h6 class="footer-section-title">
                            <i class="fas fa-comments me-2"></i>Community
                        </h6>
                        <ul class="footer-links">
                            <li><a href="forum.php" class="footer-link">
                                <i class="fas fa-comments me-2"></i>Forums
                            </a></li>
                            <li><a href="members.php" class="footer-link">
                                <i class="fas fa-users me-2"></i>Members
                            </a></li>
                            <li><a href="#" class="footer-link">
                                <i class="fas fa-trophy me-2"></i>Leaderboard
                            </a></li>
                            <li><a href="#" class="footer-link">
                                <i class="fas fa-calendar me-2"></i>Events
                            </a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-4 mb-4">
                    <div class="footer-section">
                        <h6 class="footer-section-title">
                            <i class="fas fa-bell me-2"></i>Stay Updated
                        </h6>
                        <p class="footer-description">
                            Get notified about cheat updates, new features, and community events.
                        </p>
                        
                        <div class="newsletter-form">
                            <div class="input-group">
                                <input type="email" class="form-control ultra-premium-input" placeholder="Enter your email">
                                <button class="btn btn-newsletter" type="button">
                                    <i class="fas fa-rocket"></i>
                                    <span>Join Elite</span>
                                    <div class="btn-glow"></div>
                                </button>
                            </div>
                        </div>
                        
                        <div class="footer-features">
                            <div class="feature-item">
                                <i class="fas fa-shield-check"></i>
                                <span>100% VAC Safe</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-bolt"></i>
                                <span>Instant Updates</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-headset"></i>
                                <span>24/7 Support</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="footer-copyright">
                        <p class="mb-0">
                            &copy; <?php echo date('Y'); ?> <strong>Affinity</strong> - Elite CS2 Community. All rights reserved.
                        </p>
                        <p class="footer-disclaimer">
                            Educational purposes only. Use responsibly and at your own risk.
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="footer-controls">
                        <div class="theme-switcher">
                            <span class="switcher-label">Theme:</span>
                            <div class="theme-buttons">
                                <button class="theme-btn" onclick="switchTheme('light')" title="Light Mode">
                                    <i class="fas fa-sun"></i>
                                </button>
                                <button class="theme-btn" onclick="switchTheme('dark')" title="Dark Mode">
                                    <i class="fas fa-moon"></i>
                                </button>
                                <button class="theme-btn" onclick="switchTheme('cs2')" title="CS2 Gaming">
                                    <i class="fas fa-gamepad"></i>
                                </button>
                                <button class="theme-btn" onclick="switchTheme('premium')" title="Premium Luxury">
                                    <i class="fas fa-crown"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="footer-info">
                            <span class="version-info">v3.0 Ultra Premium</span>
                            <span class="status-info">
                                <span class="status-dot online"></span>
                                All Systems Online
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Back to Top Button -->
<button id="backToTop" class="back-to-top" title="Back to Top">
    <i class="fas fa-arrow-up"></i>
</button>

<!-- JavaScript Libraries -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- Custom JavaScript -->
<script src="js/main.js"></script>
<script src="js/themes-ultra-premium.js"></script>

<!-- Initialize Theme Manager -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Footer script loaded, initializing theme manager...');
    
    // Wait a bit for scripts to load
    setTimeout(() => {
        console.log('🔍 Checking for theme manager...');
        console.log('window.themeManager:', window.themeManager);
        console.log('window.selectDevice:', window.selectDevice);
        console.log('window.switchTheme:', window.switchTheme);
        
        // Initialize theme manager if not already done
        if (window.themeManager && !window.themeManager.isInitialized) {
            console.log('🔄 Re-initializing theme manager...');
            window.themeManager.init();
        }
        
        // Ensure global functions exist
        if (!window.selectDevice) {
            console.log('⚠️ selectDevice not found, creating fallback...');
            window.selectDevice = function(device) {
                console.log('Fallback selectDevice called with:', device);
                if (window.themeManager) {
                    window.themeManager.selectDevice(device);
                } else {
                    console.error('Theme manager not available');
                }
            };
        }
        
        if (!window.switchTheme) {
            console.log('⚠️ switchTheme not found, creating fallback...');
            window.switchTheme = function(theme) {
                console.log('Fallback switchTheme called with:', theme);
                if (window.themeManager) {
                    window.themeManager.switchTheme(theme);
                } else {
                    console.error('Theme manager not available');
                }
            };
        }
        
        console.log('✅ Global functions check complete');
    }, 100);
    
    // Back to top functionality
    const backToTopButton = document.getElementById('backToTop');
    if (backToTopButton) {
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.add('show');
            } else {
                backToTopButton.classList.remove('show');
            }
        });
        
        backToTopButton.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
    
    // Newsletter form
    const newsletterForm = document.querySelector('.newsletter-form .btn');
    if (newsletterForm) {
        newsletterForm.addEventListener('click', function() {
            const emailInput = this.parentElement.querySelector('input');
            const email = emailInput.value.trim();
            
            if (email && isValidEmail(email)) {
                // Show success message
                if (window.themeManager) {
                    window.themeManager.showNotification('Newsletter subscription successful!', 'success');
                }
                emailInput.value = '';
            } else {
                // Show error message
                if (window.themeManager) {
                    window.themeManager.showNotification('Please enter a valid email address.', 'error');
                }
            }
        });
    }
    
    // Email validation helper
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    console.log('✅ Footer initialization complete');
});
</script>

<!-- PWA Service Worker Registration -->
<script>
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then((registration) => {
                console.log('SW registered: ', registration);
            })
            .catch((registrationError) => {
                console.log('SW registration failed: ', registrationError);
            });
    });
}
</script>

</body>
</html>
