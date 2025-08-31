            </div> <!-- End of content-area -->
        </div> <!-- End of container-fluid -->
    </main>

<!-- Footer -->
<footer class="footer-premium mt-5 py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <h5 class="text-gradient mb-3">
                    <i class="fas fa-users me-2"></i>Affinity Forum
                </h5>
                <p class="text-muted">
                    Connect, share, and grow together in our vibrant community. 
                    Join thousands of members discussing everything from technology to lifestyle.
                </p>
                <div class="social-links">
                    <a href="#" class="social-link me-3" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="social-link me-3" title="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="social-link me-3" title="Discord">
                        <i class="fab fa-discord"></i>
                    </a>
                    <a href="#" class="social-link" title="GitHub">
                        <i class="fab fa-github"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-lg-2 col-md-6 mb-4">
                <h6 class="fw-bold mb-3">Quick Links</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="index.php" class="footer-link">
                            <i class="fas fa-home me-2"></i>Home
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="members.php" class="footer-link">
                            <i class="fas fa-users me-2"></i>Members
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="search.php" class="footer-link">
                            <i class="fas fa-search me-2"></i>Search
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="about.php" class="footer-link">
                            <i class="fas fa-info-circle me-2"></i>About
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="col-lg-2 col-md-6 mb-4">
                <h6 class="fw-bold mb-3">Community</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="guidelines.php" class="footer-link">
                            <i class="fas fa-book me-2"></i>Guidelines
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="support.php" class="footer-link">
                            <i class="fas fa-life-ring me-2"></i>Support
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="contact.php" class="footer-link">
                            <i class="fas fa-envelope me-2"></i>Contact
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="privacy.php" class="footer-link">
                            <i class="fas fa-shield-alt me-2"></i>Privacy
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="col-lg-4 mb-4">
                <h6 class="fw-bold mb-3">Newsletter</h6>
                <p class="text-muted mb-3">
                    Stay updated with the latest community news and announcements.
                </p>
                <div class="newsletter-form">
                    <div class="input-group">
                        <input type="email" class="form-control form-control-premium" placeholder="Enter your email">
                        <button class="btn btn-ultra-premium" type="button">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <hr class="my-4">
        
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="mb-0 text-muted">
                    &copy; <?php echo date('Y'); ?> Affinity Forum. All rights reserved.
                </p>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="footer-actions">
                    <button class="btn btn-sm btn-outline-secondary me-2" onclick="switchTheme('light')">
                        <i class="fas fa-sun"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary me-2" onclick="switchTheme('dark')">
                        <i class="fas fa-moon"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary me-2" onclick="switchTheme('cs2')">
                        <i class="fas fa-gamepad"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="switchTheme('premium')">
                        <i class="fas fa-gem"></i>
                    </button>
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
<script src="js/themes.js"></script>

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
