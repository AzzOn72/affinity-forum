                </div>
            </div>
        </div>
    </main>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Custom JS -->
    <script src="../js/main.js"></script>
    <script src="../js/themes.js"></script>
    
    <script>
        // Admin sidebar functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('admin-sidebar');
            const main = document.getElementById('admin-main');
            const sidebarToggle = document.getElementById('sidebar-toggle');
            
            // Mobile sidebar toggle
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });
            }
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(e) {
                if (window.innerWidth <= 768) {
                    if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                        sidebar.classList.remove('show');
                    }
                }
            });
            
            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('show');
                }
            });
            
            // Auto-hide sidebar on mobile after navigation
            const navLinks = sidebar.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        setTimeout(() => {
                            sidebar.classList.remove('show');
                        }, 300);
                    }
                });
            });
            
            // Add active class to current page
            const currentPage = window.location.pathname.split('/').pop();
            navLinks.forEach(link => {
                const href = link.getAttribute('href');
                if (href === currentPage) {
                    link.classList.add('active');
                }
            });
            
            // Initialize tooltips
            if (typeof bootstrap !== 'undefined') {
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }
            
            // Initialize popovers
            if (typeof bootstrap !== 'undefined') {
                const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
                popoverTriggerList.map(function (popoverTriggerEl) {
                    return new bootstrap.Popover(popoverTriggerEl);
                });
            }
            
            // Add loading states to buttons
            const buttons = document.querySelectorAll('button[type="submit"], .btn[type="submit"]');
            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    if (!button.disabled) {
                        const originalText = button.innerHTML;
                        button.disabled = true;
                        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                        
                        // Re-enable after 10 seconds (safety)
                        setTimeout(() => {
                            if (button.disabled) {
                                button.disabled = false;
                                button.innerHTML = originalText;
                            }
                        }, 10000);
                    }
                });
            });
            
            // Add confirmation to destructive actions
            const destructiveButtons = document.querySelectorAll('.btn-danger, .btn-warning');
            destructiveButtons.forEach(button => {
                if (!button.hasAttribute('data-confirm')) {
                    button.addEventListener('click', function(e) {
                        const action = button.textContent.trim() || 'perform this action';
                        if (!confirm(`Are you sure you want to ${action}? This action cannot be undone.`)) {
                            e.preventDefault();
                            return false;
                        }
                    });
                }
            });
            
            // Auto-refresh functionality for dashboard
            if (window.location.pathname.includes('index.php')) {
                let refreshInterval;
                
                function startAutoRefresh() {
                    refreshInterval = setInterval(() => {
                        if (!document.hidden) {
                            location.reload();
                        }
                    }, 30000); // 30 seconds
                }
                
                function stopAutoRefresh() {
                    if (refreshInterval) {
                        clearInterval(refreshInterval);
                    }
                }
                
                // Start auto-refresh
                startAutoRefresh();
                
                // Pause auto-refresh when user is not active
                document.addEventListener('visibilitychange', function() {
                    if (document.hidden) {
                        stopAutoRefresh();
                    } else {
                        startAutoRefresh();
                    }
                });
                
                // Pause auto-refresh when user is interacting
                let userActivityTimeout;
                const userActivityEvents = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'];
                
                userActivityEvents.forEach(event => {
                    document.addEventListener(event, function() {
                        stopAutoRefresh();
                        
                        clearTimeout(userActivityTimeout);
                        userActivityTimeout = setTimeout(() => {
                            startAutoRefresh();
                        }, 60000); // Resume after 1 minute of inactivity
                    });
                });
            }
            
            // Enhanced table functionality
            const tables = document.querySelectorAll('.table');
            tables.forEach(table => {
                // Add sortable functionality
                const headers = table.querySelectorAll('th[data-sortable]');
                headers.forEach(header => {
                    header.style.cursor = 'pointer';
                    header.addEventListener('click', function() {
                        const column = Array.from(header.parentNode.children).indexOf(header);
                        const tbody = table.querySelector('tbody');
                        const rows = Array.from(tbody.querySelectorAll('tr'));
                        
                        // Toggle sort direction
                        const isAscending = header.classList.contains('sort-asc');
                        
                        // Remove existing sort classes
                        headers.forEach(h => h.classList.remove('sort-asc', 'sort-desc'));
                        
                        // Add new sort class
                        header.classList.add(isAscending ? 'sort-desc' : 'sort-asc');
                        
                        // Sort rows
                        rows.sort((a, b) => {
                            const aValue = a.children[column]?.textContent || '';
                            const bValue = b.children[column]?.textContent || '';
                            
                            if (isAscending) {
                                return bValue.localeCompare(aValue);
                            } else {
                                return aValue.localeCompare(bValue);
                            }
                        });
                        
                        // Reorder rows
                        rows.forEach(row => tbody.appendChild(row));
                    });
                });
                
                // Add search functionality
                const searchInput = table.parentNode.querySelector('.table-search');
                if (searchInput) {
                    searchInput.addEventListener('input', function() {
                        const searchTerm = this.value.toLowerCase();
                        const rows = table.querySelectorAll('tbody tr');
                        
                        rows.forEach(row => {
                            const text = row.textContent.toLowerCase();
                            if (text.includes(searchTerm)) {
                                row.style.display = '';
                            } else {
                                row.style.display = 'none';
                            }
                        });
                    });
                }
            });
            
            // Enhanced form functionality
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                // Auto-save form data
                const inputs = form.querySelectorAll('input, textarea, select');
                const formId = form.id || 'form_' + Math.random().toString(36).substr(2, 9);
                
                inputs.forEach(input => {
                    input.addEventListener('change', function() {
                        const formData = new FormData(form);
                        const data = {};
                        formData.forEach((value, key) => {
                            data[key] = value;
                        });
                        localStorage.setItem('form_autosave_' + formId, JSON.stringify(data));
                    });
                    
                    input.addEventListener('input', function() {
                        // Debounced auto-save
                        clearTimeout(input.autoSaveTimeout);
                        input.autoSaveTimeout = setTimeout(() => {
                            const formData = new FormData(form);
                            const data = {};
                            formData.forEach((value, key) => {
                                data[key] = value;
                            });
                            localStorage.setItem('form_autosave_' + formId, JSON.stringify(data));
                        }, 1000);
                    });
                });
                
                // Restore form data on page load
                const savedData = localStorage.getItem('form_autosave_' + formId);
                if (savedData) {
                    try {
                        const data = JSON.parse(savedData);
                        Object.keys(data).forEach(key => {
                            const input = form.querySelector(`[name="${key}"]`);
                            if (input) {
                                input.value = data[key];
                            }
                        });
                    } catch (e) {
                        console.error('Failed to restore form data:', e);
                    }
                }
                
                // Clear auto-saved data on successful submission
                form.addEventListener('submit', function() {
                    localStorage.removeItem('form_autosave_' + formId);
                });
            });
            
            // Enhanced notification system
            if (typeof showNotification === 'function') {
                // Override default notification for admin panel
                const originalShowNotification = showNotification;
                window.showNotification = function(message, type = 'info', duration = 5000) {
                    // Create admin-styled notification
                    const notification = document.createElement('div');
                    notification.className = `alert alert-${type} alert-dismissible fade show admin-notification`;
                    notification.style.cssText = `
                        position: fixed;
                        top: 20px;
                        right: 20px;
                        z-index: 9999;
                        min-width: 300px;
                        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
                        border: none;
                        border-radius: 8px;
                    `;
                    
                    notification.innerHTML = `
                        <div class="d-flex align-items-center">
                            <i class="fas fa-${getNotificationIcon(type)} me-2"></i>
                            <span>${message}</span>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    
                    document.body.appendChild(notification);
                    
                    // Auto-remove
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.remove();
                        }
                    }, duration);
                    
                    // Call original function as fallback
                    return originalShowNotification(message, type, duration);
                };
            }
            
            // Helper function for notification icons
            function getNotificationIcon(type) {
                const icons = {
                    success: 'check-circle',
                    error: 'exclamation-circle',
                    warning: 'exclamation-triangle',
                    info: 'info-circle',
                    default: 'bell'
                };
                return icons[type] || icons.default;
            }
            
            // Enhanced error handling
            window.addEventListener('error', function(e) {
                console.error('Admin panel error:', e.error);
                if (typeof showNotification === 'function') {
                    showNotification('An unexpected error occurred. Check console for details.', 'error');
                }
            });
            
            window.addEventListener('unhandledrejection', function(e) {
                console.error('Admin panel promise rejection:', e.reason);
                if (typeof showNotification === 'function') {
                    showNotification('A request failed unexpectedly. Check console for details.', 'error');
                }
            });
            
            // Performance monitoring
            if ('performance' in window) {
                window.addEventListener('load', function() {
                    setTimeout(() => {
                        const perfData = performance.getEntriesByType('navigation')[0];
                        if (perfData) {
                            const loadTime = perfData.loadEventEnd - perfData.loadEventStart;
                            const domContentLoaded = perfData.domContentLoadedEventEnd - perfData.domContentLoadedEventStart;
                            
                            console.log(`Admin Panel Performance:
                                - DOM Content Loaded: ${domContentLoaded}ms
                                - Page Load: ${loadTime}ms
                                - Total Time: ${perfData.loadEventEnd - perfData.fetchStart}ms`);
                            
                            // Log slow loading pages
                            if (loadTime > 3000) {
                                console.warn('Admin panel loaded slowly. Consider optimization.');
                            }
                        }
                    }, 0);
                });
            }
            
            console.log('🚀 Admin Panel JavaScript initialized successfully!');
        });
        
        // Global admin functions
        window.adminUtils = {
            // Refresh current page
            refresh: function() {
                location.reload();
            },
            
            // Go back
            goBack: function() {
                history.back();
            },
            
            // Show loading overlay
            showLoading: function(message = 'Loading...') {
                const overlay = document.createElement('div');
                overlay.id = 'admin-loading-overlay';
                overlay.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0,0,0,0.7);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 9999;
                    color: white;
                    font-size: 1.2rem;
                `;
                overlay.innerHTML = `
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin fa-2x mb-3"></i>
                        <div>${message}</div>
                    </div>
                `;
                document.body.appendChild(overlay);
            },
            
            // Hide loading overlay
            hideLoading: function() {
                const overlay = document.getElementById('admin-loading-overlay');
                if (overlay) {
                    overlay.remove();
                }
            },
            
            // Confirm action
            confirm: function(message, callback) {
                if (confirm(message)) {
                    if (typeof callback === 'function') {
                        callback();
                    }
                }
            },
            
            // Show success message
            showSuccess: function(message) {
                if (typeof showNotification === 'function') {
                    showNotification(message, 'success');
                } else {
                    alert('Success: ' + message);
                }
            },
            
            // Show error message
            showError: function(message) {
                if (typeof showNotification === 'function') {
                    showNotification(message, 'error');
                } else {
                    alert('Error: ' + message);
                }
            },
            
            // Format number
            formatNumber: function(num) {
                if (num >= 1000000) {
                    return (num / 1000000).toFixed(1) + 'M';
                } else if (num >= 1000) {
                    return (num / 1000).toFixed(1) + 'K';
                }
                return num.toString();
            },
            
            // Format date
            formatDate: function(date) {
                if (typeof date === 'string') {
                    date = new Date(date);
                }
                return date.toLocaleDateString();
            },
            
            // Format time ago
            formatTimeAgo: function(date) {
                if (typeof date === 'string') {
                    date = new Date(date);
                }
                
                const now = new Date();
                const diff = now - date;
                const seconds = Math.floor(diff / 1000);
                const minutes = Math.floor(seconds / 60);
                const hours = Math.floor(minutes / 60);
                const days = Math.floor(hours / 24);
                
                if (days > 0) return days + ' day' + (days > 1 ? 's' : '') + ' ago';
                if (hours > 0) return hours + ' hour' + (hours > 1 ? 's' : '') + ' ago';
                if (minutes > 0) return minutes + ' minute' + (minutes > 1 ? 's' : '') + ' ago';
                return 'Just now';
            }
        };
    </script>
</body>
</html>
