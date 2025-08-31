            </div> <!-- End of content-area -->
        </div> <!-- End of container-fluid -->
    </main>

    <!-- Footer -->
    <footer class="footer mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="text-white mb-3">
                        <i class="fas fa-users me-2"></i>Affinity Forum
                    </h5>
                    <p class="text-muted">
                        Connect, share, and grow together in our vibrant community. 
                        Join thousands of members discussing everything from gaming to technology.
                    </p>
                    <div class="social-links">
                        <a href="#" class="social-link me-3" title="Discord">
                            <i class="fab fa-discord"></i>
                        </a>
                        <a href="#" class="social-link me-3" title="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="social-link me-3" title="YouTube">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <a href="#" class="social-link" title="GitHub">
                            <i class="fab fa-github"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="text-white mb-3">Community</h6>
                    <ul class="list-unstyled">
                        <li><a href="forum.php" class="footer-link">Forums</a></li>
                        <li><a href="members.php" class="footer-link">Members</a></li>
                        <li><a href="achievements.php" class="footer-link">Achievements</a></li>
                        <li><a href="events.php" class="footer-link">Events</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="text-white mb-3">Support</h6>
                    <ul class="list-unstyled">
                        <li><a href="help.php" class="footer-link">Help Center</a></li>
                        <li><a href="rules.php" class="footer-link">Community Rules</a></li>
                        <li><a href="contact.php" class="footer-link">Contact Us</a></li>
                        <li><a href="report.php" class="footer-link">Report Issue</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-4 mb-4">
                    <h6 class="text-white mb-3">Newsletter</h6>
                    <p class="text-muted mb-3">
                        Stay updated with the latest community news and events.
                    </p>
                    <form class="newsletter-form">
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="Enter your email" required>
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <hr class="border-secondary my-4">
            
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="text-muted mb-0">
                        &copy; <?php echo date('Y'); ?> Affinity Forum. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <ul class="list-inline mb-0">
                        <li class="list-inline-item">
                            <a href="privacy.php" class="footer-link">Privacy Policy</a>
                        </li>
                        <li class="list-inline-item">
                            <span class="text-muted">•</span>
                        </li>
                        <li class="list-inline-item">
                            <a href="terms.php" class="footer-link">Terms of Service</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="js/themes.js"></script>
    <script src="js/main.js"></script>
    
    <script>
        // Initialize theme system
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Affinity Forum loaded successfully!');
            
            // Add smooth scrolling to all links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
            
            // Add hover effects to cards
            document.querySelectorAll('.category-card, .timeline-item').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-4px)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</body>
</html>
