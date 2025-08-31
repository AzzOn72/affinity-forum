/**
 * Affinity Forum - Premium JavaScript v2.0
 * Professional Counter-Strike 2 Community Forum
 */

// Global variables and configuration
const AFFINITY_CONFIG = {
    version: '2.0.0',
    debug: false,
    animations: {
        duration: 300,
        easing: 'ease-in-out'
    },
    api: {
        baseUrl: '',
        timeout: 10000
    },
    features: {
        notifications: true,
        realTime: true,
        animations: true,
        lazyLoading: true
    }
};

// Utility functions (moved outside document.ready for global access)
function showNotification(message, type = 'info', duration = 5000) {
    if (!AFFINITY_CONFIG.features.notifications) return;
    
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show notification-toast`;
    notification.innerHTML = `
        <i class="fas fa-${getNotificationIcon(type)}"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Add to notification container
    const container = document.getElementById('notification-container') || document.body;
    container.appendChild(notification);
    
    // Auto-remove after duration
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, duration);
    
    // Add entrance animation
    notification.style.opacity = '0';
    notification.style.transform = 'translateY(-20px)';
    
    requestAnimationFrame(() => {
        notification.style.transition = 'all 0.3s ease-out';
        notification.style.opacity = '1';
        notification.style.transform = 'translateY(0)';
    });
    
    return notification;
}

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

function debounce(func, wait, immediate) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            timeout = null;
            if (!immediate) func(...args);
        };
        const callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func(...args);
    };
}

function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// Enhanced AJAX functionality
class AffinityAPI {
    constructor() {
        this.baseUrl = AFFINITY_CONFIG.api.baseUrl;
        this.timeout = AFFINITY_CONFIG.api.timeout;
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    }
    
    async request(endpoint, options = {}) {
        const url = this.baseUrl + endpoint;
        const defaultOptions = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            timeout: this.timeout
        };
        
        if (this.csrfToken) {
            defaultOptions.headers['X-CSRF-TOKEN'] = this.csrfToken;
        }
        
        const finalOptions = { ...defaultOptions, ...options };
        
        try {
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), this.timeout);
            
            const response = await fetch(url, {
                ...finalOptions,
                signal: controller.signal
            });
            
            clearTimeout(timeoutId);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            return { success: true, data };
            
        } catch (error) {
            if (error.name === 'AbortError') {
                throw new Error('Request timeout');
            }
            throw error;
        }
    }
    
    async get(endpoint, params = {}) {
        const queryString = new URLSearchParams(params).toString();
        const url = queryString ? `${endpoint}?${queryString}` : endpoint;
        return this.request(url);
    }
    
    async post(endpoint, data = {}) {
        return this.request(endpoint, {
            method: 'POST',
            body: JSON.stringify(data)
        });
    }
    
    async put(endpoint, data = {}) {
        return this.request(endpoint, {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    }
    
    async delete(endpoint) {
        return this.request(endpoint, {
            method: 'DELETE'
        });
    }
}

// Enhanced notification system
class NotificationManager {
    constructor() {
        this.notifications = [];
        this.maxNotifications = 5;
        this.init();
    }
    
    init() {
        this.createContainer();
        this.bindEvents();
    }
    
    createContainer() {
        if (!document.getElementById('notification-container')) {
            const container = document.createElement('div');
            container.id = 'notification-container';
            container.className = 'notification-container';
            document.body.appendChild(container);
        }
    }
    
    bindEvents() {
        // Mark all as read functionality
        document.addEventListener('click', (e) => {
            if (e.target.matches('.mark-all-read')) {
                e.preventDefault();
                this.markAllAsRead();
            }
        });
        
        // Individual notification clicks
        document.addEventListener('click', (e) => {
            if (e.target.closest('.notification-item')) {
                const notificationId = e.target.closest('.notification-item').dataset.id;
                this.markAsRead(notificationId);
            }
        });
    }
    
    async markAsRead(notificationId) {
        try {
            const api = new AffinityAPI();
            await api.post(`/ajax/mark-notification-read.php`, { id: notificationId });
            
            // Update UI
            const notification = document.querySelector(`[data-id="${notificationId}"]`);
            if (notification) {
                notification.classList.add('read');
                this.updateUnreadCount();
            }
        } catch (error) {
            console.error('Failed to mark notification as read:', error);
        }
    }
    
    async markAllAsRead() {
        try {
            const api = new AffinityAPI();
            await api.post(`/ajax/mark-all-notifications-read.php`);
            
            // Update UI
            document.querySelectorAll('.notification-item').forEach(item => {
                item.classList.add('read');
            });
            
            this.updateUnreadCount();
            showNotification('All notifications marked as read', 'success');
        } catch (error) {
            console.error('Failed to mark all notifications as read:', error);
        }
    }
    
    updateUnreadCount() {
        const unreadCount = document.querySelectorAll('.notification-item:not(.read)').length;
        const badge = document.querySelector('.notification-badge');
        
        if (badge) {
            if (unreadCount > 0) {
                badge.textContent = unreadCount;
                badge.style.display = 'inline';
            } else {
                badge.style.display = 'none';
            }
        }
    }
    
    addNotification(notification) {
        this.notifications.unshift(notification);
        
        if (this.notifications.length > this.maxNotifications) {
            this.notifications.pop();
        }
        
        this.render();
    }
    
    render() {
        const container = document.getElementById('notification-container');
        if (!container) return;
        
        container.innerHTML = this.notifications
            .map(notification => this.renderNotification(notification))
            .join('');
    }
    
    renderNotification(notification) {
        return `
            <div class="notification-item ${notification.read ? 'read' : ''}" data-id="${notification.id}">
                <div class="notification-icon">
                    <i class="fas fa-${getNotificationIcon(notification.type)}"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-title">${notification.title}</div>
                    <div class="notification-message">${notification.message}</div>
                    <div class="notification-time">${this.formatTime(notification.created_at)}</div>
                </div>
            </div>
        `;
    }
    
    formatTime(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();
        const diff = now - date;
        
        if (diff < 60000) return 'Just now';
        if (diff < 3600000) return `${Math.floor(diff / 60000)}m ago`;
        if (diff < 86400000) return `${Math.floor(diff / 3600000)}h ago`;
        if (diff < 604800000) return `${Math.floor(diff / 86400000)}d ago`;
        
        return date.toLocaleDateString();
    }
}

// Enhanced search functionality
class SearchManager {
    constructor() {
        this.searchInput = document.querySelector('.search-input');
        this.suggestionsContainer = document.querySelector('.search-suggestions');
        this.currentQuery = '';
        this.suggestions = [];
        this.init();
    }
    
    init() {
        if (!this.searchInput) return;
        
        this.bindEvents();
        this.setupDebouncing();
    }
    
    bindEvents() {
        this.searchInput.addEventListener('input', (e) => {
            this.currentQuery = e.target.value.trim();
            this.handleSearchInput();
        });
        
        this.searchInput.addEventListener('focus', () => {
            if (this.currentQuery.length >= 2) {
                this.showSuggestions();
            }
        });
        
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.search-container')) {
                this.hideSuggestions();
            }
        });
    }
    
    setupDebouncing() {
        this.handleSearchInput = debounce(() => {
            if (this.currentQuery.length >= 2) {
                this.fetchSuggestions();
            } else {
                this.hideSuggestions();
            }
        }, 300);
    }
    
    async fetchSuggestions() {
        try {
            const api = new AffinityAPI();
            const response = await api.get('/ajax/search-suggestions.php', {
                q: this.currentQuery
            });
            
            if (response.success) {
                this.suggestions = response.data;
                this.renderSuggestions();
                this.showSuggestions();
            }
        } catch (error) {
            console.error('Failed to fetch search suggestions:', error);
        }
    }
    
    renderSuggestions() {
        if (!this.suggestionsContainer) return;
        
        if (this.suggestions.length === 0) {
            this.suggestionsContainer.innerHTML = `
                <div class="suggestion-item no-results">
                    <i class="fas fa-search"></i>
                    <span>No results found</span>
                </div>
            `;
            return;
        }
        
        this.suggestionsContainer.innerHTML = this.suggestions
            .map(suggestion => this.renderSuggestion(suggestion))
            .join('');
    }
    
    renderSuggestion(suggestion) {
        const icon = this.getSuggestionIcon(suggestion.type);
        const url = this.getSuggestionUrl(suggestion);
        
        return `
            <a href="${url}" class="suggestion-item">
                <i class="fas fa-${icon}"></i>
                <div class="suggestion-content">
                    <div class="suggestion-title">${suggestion.title}</div>
                    <div class="suggestion-meta">${suggestion.type} • ${suggestion.author}</div>
                </div>
            </a>
        `;
    }
    
    getSuggestionIcon(type) {
        const icons = {
            thread: 'comment',
            post: 'reply',
            user: 'user',
            category: 'folder',
            subforum: 'folder-open'
        };
        return icons[type] || 'search';
    }
    
    getSuggestionUrl(suggestion) {
        switch (suggestion.type) {
            case 'thread':
                return `thread.php?id=${suggestion.id}`;
            case 'post':
                return `thread.php?id=${suggestion.thread_id}#post-${suggestion.id}`;
            case 'user':
                return `profile.php?user=${suggestion.username}`;
            case 'category':
                return `category.php?id=${suggestion.id}`;
            case 'subforum':
                return `subforum.php?id=${suggestion.id}`;
            default:
                return '#';
        }
    }
    
    showSuggestions() {
        if (this.suggestionsContainer) {
            this.suggestionsContainer.classList.add('show');
        }
    }
    
    hideSuggestions() {
        if (this.suggestionsContainer) {
            this.suggestionsContainer.classList.remove('show');
        }
    }
}

// Enhanced like system
class LikeManager {
    constructor() {
        this.init();
    }
    
    init() {
        this.bindEvents();
    }
    
    bindEvents() {
        document.addEventListener('click', (e) => {
            if (e.target.matches('.like-btn, .like-btn *')) {
                e.preventDefault();
                const button = e.target.closest('.like-btn');
                this.handleLike(button);
            }
        });
    }
    
    async handleLike(button) {
        const targetType = button.dataset.type;
        const targetId = button.dataset.id;
        const isLiked = button.classList.contains('liked');
        
        if (!targetType || !targetId) return;
        
        try {
            // Optimistic update
            this.updateLikeUI(button, !isLiked);
            
            const api = new AffinityAPI();
            const response = await api.post('/ajax/toggle-like.php', {
                target_type: targetType,
                target_id: targetId
            });
            
            if (response.success) {
                this.updateLikeCount(button, response.data.likes);
                showNotification(
                    response.data.action === 'liked' ? 'Post liked!' : 'Post unliked!',
                    'success',
                    2000
                );
            } else {
                // Revert on error
                this.updateLikeUI(button, isLiked);
                showNotification('Failed to update like', 'error');
            }
        } catch (error) {
            // Revert on error
            this.updateLikeUI(button, isLiked);
            showNotification('Failed to update like', 'error');
            console.error('Like error:', error);
        }
    }
    
    updateLikeUI(button, isLiked) {
        const icon = button.querySelector('i');
        const count = button.querySelector('.like-count');
        
        if (isLiked) {
            button.classList.add('liked');
            icon.className = 'fas fa-heart';
            button.style.color = '#e74c3c';
        } else {
            button.classList.remove('liked');
            icon.className = 'far fa-heart';
            button.style.color = '';
        }
    }
    
    updateLikeCount(button, newCount) {
        const countElement = button.querySelector('.like-count');
        if (countElement) {
            countElement.textContent = newCount;
        }
    }
}

// Enhanced user interactions
class UserInteractionManager {
    constructor() {
        this.init();
    }
    
    init() {
        this.bindEvents();
    }
    
    bindEvents() {
        // Follow user functionality
        document.addEventListener('click', (e) => {
            if (e.target.matches('.follow-btn, .follow-btn *')) {
                e.preventDefault();
                const button = e.target.closest('.follow-btn');
                this.handleFollow(button);
            }
        });
        
        // User mention functionality
        document.addEventListener('click', (e) => {
            if (e.target.matches('.user-mention')) {
                e.preventDefault();
                this.handleUserMention(e.target);
            }
        });
        
        // Quote functionality
        document.addEventListener('click', (e) => {
            if (e.target.matches('.quote-btn, .quote-btn *')) {
                e.preventDefault();
                const button = e.target.closest('.quote-btn');
                this.handleQuote(button);
            }
        });
    }
    
    async handleFollow(button) {
        const userId = button.dataset.userId;
        const isFollowing = button.classList.contains('following');
        
        try {
            const api = new AffinityAPI();
            const response = await api.post('/ajax/toggle-follow.php', {
                user_id: userId
            });
            
            if (response.success) {
                if (response.data.following) {
                    button.classList.add('following');
                    button.innerHTML = '<i class="fas fa-user-check"></i> Following';
                    showNotification('User followed!', 'success');
                } else {
                    button.classList.remove('following');
                    button.innerHTML = '<i class="fas fa-user-plus"></i> Follow';
                    showNotification('User unfollowed', 'info');
                }
            }
        } catch (error) {
            showNotification('Failed to update follow status', 'error');
            console.error('Follow error:', error);
        }
    }
    
    handleUserMention(element) {
        const username = element.textContent;
        const textarea = document.querySelector('textarea[name="content"]');
        
        if (textarea) {
            const mention = `@${username} `;
            const cursorPos = textarea.selectionStart;
            const textBefore = textarea.value.substring(0, cursorPos);
            const textAfter = textarea.value.substring(cursorPos);
            
            textarea.value = textBefore + mention + textAfter;
            textarea.selectionStart = textarea.selectionEnd = cursorPos + mention.length;
            textarea.focus();
        }
    }
    
    handleQuote(button) {
        const postId = button.dataset.postId;
        const postContent = button.dataset.content;
        const author = button.dataset.author;
        const textarea = document.querySelector('textarea[name="content"]');
        
        if (textarea) {
            const quote = `[quote="${author}"]${postContent}[/quote]\n\n`;
            const cursorPos = textarea.selectionStart;
            const textBefore = textarea.value.substring(0, cursorPos);
            const textAfter = textarea.value.substring(cursorPos);
            
            textarea.value = textBefore + quote + textAfter;
            textarea.selectionStart = textarea.selectionEnd = cursorPos + quote.length;
            textarea.focus();
            
            showNotification('Quote added to your post', 'success');
        }
    }
}

// Enhanced form handling
class FormManager {
    constructor() {
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.setupFormValidation();
    }
    
    bindEvents() {
        // Form submission
        document.addEventListener('submit', (e) => {
            if (e.target.matches('form')) {
                this.handleFormSubmit(e);
            }
        });
        
        // Character counting
        document.addEventListener('input', (e) => {
            if (e.target.matches('textarea[maxlength]')) {
                this.handleCharacterCount(e.target);
            }
        });
        
        // File uploads
        document.addEventListener('change', (e) => {
            if (e.target.matches('input[type="file"]')) {
                this.handleFileUpload(e.target);
            }
        });
    }
    
    handleFormSubmit(e) {
        const form = e.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        
        if (submitBtn && !submitBtn.disabled) {
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            
            // Re-enable after a delay (in case of validation errors)
            setTimeout(() => {
                if (submitBtn.disabled) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = submitBtn.dataset.originalText || 'Submit';
                }
            }, 5000);
        }
    }
    
    handleCharacterCount(textarea) {
        const maxLength = parseInt(textarea.getAttribute('maxlength'));
        const currentLength = textarea.value.length;
        const remaining = maxLength - currentLength;
        
        // Find or create character counter
        let counter = textarea.parentNode.querySelector('.char-counter');
        if (!counter) {
            counter = document.createElement('div');
            counter.className = 'char-counter';
            textarea.parentNode.appendChild(counter);
        }
        
        // Update counter
        counter.textContent = `${currentLength}/${maxLength}`;
        
        // Color coding
        if (remaining <= 0) {
            counter.style.color = '#dc3545';
        } else if (remaining <= 50) {
            counter.style.color = '#ffc107';
        } else {
            counter.style.color = '#28a745';
        }
    }
    
    handleFileUpload(input) {
        const files = Array.from(input.files);
        const maxSize = 5 * 1024 * 1024; // 5MB
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        
        const validFiles = files.filter(file => {
            if (file.size > maxSize) {
                showNotification(`File ${file.name} is too large (max 5MB)`, 'error');
                return false;
            }
            
            if (!allowedTypes.includes(file.type)) {
                showNotification(`File ${file.name} is not a supported image type`, 'error');
                return false;
            }
            
            return true;
        });
        
        // Update input with only valid files
        const dt = new DataTransfer();
        validFiles.forEach(file => dt.items.add(file));
        input.files = dt.files;
        
        if (validFiles.length !== files.length) {
            showNotification('Some files were rejected. Please check the requirements.', 'warning');
        }
    }
    
    setupFormValidation() {
        // Username validation
        const usernameInputs = document.querySelectorAll('input[name="username"]');
        usernameInputs.forEach(input => {
            input.addEventListener('input', debounce(() => {
                this.validateUsername(input);
            }, 500));
        });
        
        // Email validation
        const emailInputs = document.querySelectorAll('input[name="email"]');
        emailInputs.forEach(input => {
            input.addEventListener('blur', () => {
                this.validateEmail(input);
            });
        });
    }
    
    async validateUsername(input) {
        const username = input.value.trim();
        const feedback = input.parentNode.querySelector('.invalid-feedback');
        
        if (username.length < 3) {
            this.showFieldError(input, 'Username must be at least 3 characters long');
            return false;
        }
        
        if (username.length > 25) {
            this.showFieldError(input, 'Username cannot exceed 25 characters');
            return false;
        }
        
        if (!/^[a-zA-Z]+$/.test(username)) {
            this.showFieldError(input, 'Username can only contain letters');
            return false;
        }
        
        // Check availability
        try {
            const api = new AffinityAPI();
            const response = await api.get('/ajax/check-username.php', { username });
            
            if (response.success && !response.data.available) {
                this.showFieldError(input, 'Username is already taken');
                return false;
            }
        } catch (error) {
            console.error('Username validation error:', error);
        }
        
        this.clearFieldError(input);
        return true;
    }
    
    validateEmail(input) {
        const email = input.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (!emailRegex.test(email)) {
            this.showFieldError(input, 'Please enter a valid email address');
            return false;
        }
        
        this.clearFieldError(input);
        return true;
    }
    
    showFieldError(input, message) {
        this.clearFieldError(input);
        
        input.classList.add('is-invalid');
        
        const feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        feedback.textContent = message;
        input.parentNode.appendChild(feedback);
    }
    
    clearFieldError(input) {
        input.classList.remove('is-invalid');
        
        const feedback = input.parentNode.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.remove();
        }
    }
}

// Initialize everything when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Affinity Forum v2.0 Initializing...');
    
    // Initialize managers
    window.notificationManager = new NotificationManager();
    window.searchManager = new SearchManager();
    window.likeManager = new LikeManager();
    window.userInteractionManager = new UserInteractionManager();
    window.formManager = new FormManager();
    
    // Initialize API
    window.affinityAPI = new AffinityAPI();
    
    // Setup global error handling
    window.addEventListener('error', (e) => {
        console.error('Global error:', e.error);
        showNotification('An unexpected error occurred', 'error');
    });
    
    // Setup unhandled promise rejection handling
    window.addEventListener('unhandledrejection', (e) => {
        console.error('Unhandled promise rejection:', e.reason);
        showNotification('A request failed unexpectedly', 'error');
    });
    
    console.log('✅ Affinity Forum v2.0 Initialized Successfully!');
});

// Global functions for backward compatibility
window.showNotification = showNotification;
window.debounce = debounce;
window.throttle = throttle;

// Placeholder functions for profile page
function sendMessage() {
    showNotification('Message functionality coming soon!', 'info');
}

function followUser() {
    showNotification('Follow functionality coming soon!', 'info');
}

function moderateUser() {
    showNotification('Moderation functionality coming soon!', 'info');
}
