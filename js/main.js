/**
 * Affinity Forum - Main JavaScript
 * Core functionality and interactions
 */

class ForumManager {
    constructor() {
        this.init();
    }
    
    init() {
        console.log('🚀 ForumManager initialized');
        this.setupEventListeners();
        this.setupAnimations();
        this.setupSearch();
    }
    
    setupEventListeners() {
        // Newsletter form
        const newsletterForm = document.querySelector('.newsletter-form');
        if (newsletterForm) {
            newsletterForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleNewsletterSignup(e.target);
            });
        }
        
        // Like buttons
        document.querySelectorAll('.like-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.handleLike(e.target);
            });
        });
        
        // Follow buttons
        document.querySelectorAll('.follow-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.handleFollow(e.target);
            });
        });
        
        // Search form
        const searchForm = document.querySelector('.search-form');
        if (searchForm) {
            searchForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleSearch(e.target);
            });
        }
    }
    
    setupAnimations() {
        // Intersection Observer for fade-in animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in-up');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);
        
        // Observe all animated elements
        document.querySelectorAll('.animate-fade-in-up, .animate-fade-in-left, .animate-fade-in-right').forEach(el => {
            observer.observe(el);
        });
        
        // Add hover effects to cards
        document.querySelectorAll('.category-card, .timeline-item, .premium-feature').forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-4px)';
                card.style.boxShadow = 'var(--shadow-xl)';
            });
            
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'translateY(0)';
                card.style.boxShadow = 'var(--shadow-md)';
            });
        });
    }
    
    setupSearch() {
        // Live search functionality
        const searchInput = document.querySelector('.search-input');
        if (searchInput) {
            let searchTimeout;
            
            searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                const query = e.target.value.trim();
                
                if (query.length >= 2) {
                    searchTimeout = setTimeout(() => {
                        this.performLiveSearch(query);
                    }, 300);
                } else {
                    this.clearSearchResults();
                }
            });
        }
    }
    
    async performLiveSearch(query) {
        try {
            const response = await fetch(`ajax/search-suggestions.php?q=${encodeURIComponent(query)}`);
            const results = await response.json();
            this.displaySearchResults(results);
        } catch (error) {
            console.error('Search error:', error);
        }
    }
    
    displaySearchResults(results) {
        let searchResults = document.querySelector('.search-results');
        if (!searchResults) {
            searchResults = document.createElement('div');
            searchResults.className = 'search-results';
            const searchForm = document.querySelector('.search-form');
            if (searchForm) {
                searchForm.appendChild(searchResults);
            }
        }
        
        if (results.length === 0) {
            searchResults.innerHTML = '<div class="no-results">No results found</div>';
            return;
        }
        
        searchResults.innerHTML = results.map(result => `
            <div class="search-result-item">
                <a href="${result.url}" class="search-result-link">
                    <div class="search-result-title">${result.title}</div>
                    <div class="search-result-excerpt">${result.excerpt}</div>
                </a>
            </div>
        `).join('');
        
        searchResults.style.display = 'block';
    }
    
    clearSearchResults() {
        const searchResults = document.querySelector('.search-results');
        if (searchResults) {
            searchResults.style.display = 'none';
        }
    }
    
    async handleNewsletterSignup(form) {
        const emailInput = form.querySelector('input[type="email"]');
        const email = emailInput.value.trim();
        
        if (!this.isValidEmail(email)) {
            this.showNotification('Please enter a valid email address.', 'error');
            return;
        }
        
        try {
            // Simulate API call
            await new Promise(resolve => setTimeout(resolve, 1000));
            
            this.showNotification('Newsletter subscription successful!', 'success');
            emailInput.value = '';
        } catch (error) {
            this.showNotification('Subscription failed. Please try again.', 'error');
        }
    }
    
    async handleLike(button) {
        const threadId = button.dataset.threadId;
        const isLiked = button.classList.contains('liked');
        
        try {
            // Simulate API call
            await new Promise(resolve => setTimeout(resolve, 500));
            
            if (isLiked) {
                button.classList.remove('liked');
                button.innerHTML = '<i class="far fa-heart"></i> Like';
            } else {
                button.classList.add('liked');
                button.innerHTML = '<i class="fas fa-heart"></i> Liked';
            }
            
            this.showNotification(
                isLiked ? 'Removed from likes' : 'Added to likes', 
                'success'
            );
        } catch (error) {
            this.showNotification('Failed to update like status.', 'error');
        }
    }
    
    async handleFollow(button) {
        const userId = button.dataset.userId;
        const isFollowing = button.classList.contains('following');
        
        try {
            // Simulate API call
            await new Promise(resolve => setTimeout(resolve, 500));
            
            if (isFollowing) {
                button.classList.remove('following');
                button.innerHTML = '<i class="fas fa-user-plus"></i> Follow';
            } else {
                button.classList.add('following');
                button.innerHTML = '<i class="fas fa-user-check"></i> Following';
            }
            
            this.showNotification(
                isFollowing ? 'Unfollowed user' : 'Now following user', 
                'success'
            );
        } catch (error) {
            this.showNotification('Failed to update follow status.', 'error');
        }
    }
    
    async handleSearch(form) {
        const query = form.querySelector('input').value.trim();
        
        if (!query) {
            this.showNotification('Please enter a search term.', 'warning');
            return;
        }
        
        // Redirect to search results page
        window.location.href = `search.php?q=${encodeURIComponent(query)}`;
    }
    
    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-${this.getNotificationIcon(type)}"></i>
                <span>${message}</span>
                <button class="notification-close" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        // Add to page
        document.body.appendChild(notification);
        
        // Show notification
        setTimeout(() => notification.classList.add('show'), 100);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 300);
        }, 5000);
    }
    
    getNotificationIcon(type) {
        const icons = {
            success: 'check-circle',
            error: 'exclamation-circle',
            warning: 'exclamation-triangle',
            info: 'info-circle'
        };
        return icons[type] || 'info-circle';
    }
}

// Utility functions
function formatDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);
    
    if (diffInSeconds < 60) {
        return 'Just now';
    } else if (diffInSeconds < 3600) {
        const minutes = Math.floor(diffInSeconds / 60);
        return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
    } else if (diffInSeconds < 86400) {
        const hours = Math.floor(diffInSeconds / 3600);
        return `${hours} hour${hours > 1 ? 's' : ''} ago`;
    } else if (diffInSeconds < 2592000) {
        const days = Math.floor(diffInSeconds / 86400);
        return `${days} day${days > 1 ? 's' : ''} ago`;
    } else {
        return date.toLocaleDateString();
    }
}

function formatNumber(num) {
    if (num >= 1000000) {
        return (num / 1000000).toFixed(1) + 'M';
    } else if (num >= 1000) {
        return (num / 1000).toFixed(1) + 'K';
    }
    return num.toString();
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.forumManager = new ForumManager();
    
    // Format all dates on the page
    document.querySelectorAll('[data-date]').forEach(element => {
        const dateString = element.dataset.date;
        element.textContent = formatDate(dateString);
    });
    
    // Format all numbers on the page
    document.querySelectorAll('[data-number]').forEach(element => {
        const number = parseInt(element.dataset.number);
        element.textContent = formatNumber(number);
    });
    
    console.log('✅ Forum functionality ready!');
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ForumManager;
}
