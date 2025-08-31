<?php
define('SECURE_ACCESS', true);
require_once 'config.php';

$search_query = trim($_GET['q'] ?? '');
$search_type = $_GET['type'] ?? 'all';
$sort_by = $_GET['sort'] ?? 'relevance';
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 20;

$results = [];
$total_results = 0;
$search_time = 0;

if ($search_query) {
    $start_time = microtime(true);
    
    try {
        // Build search query based on type
        $where_conditions = [];
        $params = [];
        
        if ($search_type === 'threads') {
            $where_conditions[] = "t.is_active = 1";
        } elseif ($search_type === 'posts') {
            $where_conditions[] = "p.is_active = 1";
        } elseif ($search_type === 'users') {
            $where_conditions[] = "u.is_active = 1";
        } else {
            $where_conditions[] = "(t.is_active = 1 OR p.is_active = 1)";
        }
        
        // Add search terms
        $search_terms = explode(' ', $search_query);
        $search_conditions = [];
        
        foreach ($search_terms as $term) {
            if (strlen($term) >= 2) {
                if ($search_type === 'users') {
                    $search_conditions[] = "(u.username LIKE ? OR u.bio LIKE ?)";
                    $params[] = "%$term%";
                    $params[] = "%$term%";
                } else {
                    $search_conditions[] = "(t.title LIKE ? OR p.content LIKE ?)";
                    $params[] = "%$term%";
                    $params[] = "%$term%";
                }
            }
        }
        
        if (!empty($search_conditions)) {
            $where_conditions[] = '(' . implode(' OR ', $search_conditions) . ')';
        }
        
        $where_clause = implode(' AND ', $where_conditions);
        
        // Build main query
        if ($search_type === 'users') {
            $sql = "
                SELECT 'user' as type, u.id, u.username, u.avatar, u.rank, u.created_at, u.last_seen,
                       u.post_count, u.thread_count, u.reputation,
                       NULL as title, NULL as content, NULL as subforum_name, NULL as category_name
                FROM users u
                WHERE $where_clause
                ORDER BY u.reputation DESC, u.post_count DESC
                LIMIT ? OFFSET ?
            ";
        } else {
            $sql = "
                SELECT 
                    CASE WHEN p.is_first_post = 1 THEN 'thread' ELSE 'post' END as type,
                    COALESCE(t.id, p.thread_id) as id,
                    t.title, p.content, t.slug,
                    t.created_at, t.view_count, t.like_count,
                    u.username, u.avatar, u.rank,
                    sf.name as subforum_name, c.name as category_name
                FROM posts p
                JOIN users u ON p.user_id = u.id
                LEFT JOIN threads t ON p.thread_id = t.id
                LEFT JOIN subforums sf ON t.subforum_id = sf.id
                LEFT JOIN categories c ON sf.category_id = c.id
                WHERE $where_clause
                ORDER BY 
                    CASE WHEN ? = 'relevance' THEN 
                        CASE WHEN p.is_first_post = 1 THEN 3 ELSE 1 END
                    WHEN ? = 'date' THEN t.created_at
                    WHEN ? = 'views' THEN t.view_count
                    WHEN ? = 'likes' THEN t.like_count
                    ELSE t.created_at END DESC
                LIMIT ? OFFSET ?
            ";
            
            // Add sort parameters
            $params = array_merge($params, [$sort_by, $sort_by, $sort_by, $sort_by]);
        }
        
        // Add pagination parameters
        $params[] = $per_page;
        $params[] = ($page - 1) * $per_page;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll();
        
        // Get total count for pagination
        $count_sql = str_replace('SELECT', 'SELECT COUNT(*) as total', $sql);
        $count_sql = preg_replace('/ORDER BY.*LIMIT.*OFFSET.*/', '', $count_sql);
        
        $count_stmt = $pdo->prepare($count_sql);
        $count_params = array_slice($params, 0, -2); // Remove pagination params
        $count_stmt->execute($count_params);
        $total_results = $count_stmt->fetch()['total'];
        
        $search_time = microtime(true) - $start_time;
        
        // Log search for analytics
        logSearch($search_query, $search_type, count($results), $search_time);
        
    } catch (Exception $e) {
        error_log("Search error: " . $e->getMessage());
        $error_message = "An error occurred while searching. Please try again.";
    }
}

// Set page variables
$page_title = $search_query ? "Search: $search_query - " . SITE_NAME : "Search - " . SITE_NAME;
$page_description = "Search the Affinity forum for threads, posts, and users";
$breadcrumbs = [
    ['text' => 'Search'],
    ['text' => $search_query ?: 'All']
];

// Include header
include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section search-hero">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h1 class="hero-title">
                    <i class="fas fa-search"></i>
                    Search Affinity Forum
                </h1>
                <p class="hero-subtitle">
                    Find exactly what you're looking for in our vast collection of discussions, guides, and community content.
                </p>
                
                <!-- Enhanced Search Form -->
                <form class="search-form" action="search.php" method="GET">
                    <div class="search-container">
                        <div class="input-group input-group-lg">
                            <input type="text" class="form-control search-input" name="q" 
                                   value="<?php echo htmlspecialchars($search_query); ?>" 
                                   placeholder="Search for threads, posts, users, or topics..."
                                   autocomplete="off" id="main-search">
                            <button class="btn btn-primary search-btn" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        
                        <!-- Search Options -->
                        <div class="search-options">
                            <div class="row">
                                <div class="col-md-4">
                                    <select name="type" class="form-select">
                                        <option value="all" <?php echo $search_type === 'all' ? 'selected' : ''; ?>>All Content</option>
                                        <option value="threads" <?php echo $search_type === 'threads' ? 'selected' : ''; ?>>Threads Only</option>
                                        <option value="posts" <?php echo $search_type === 'posts' ? 'selected' : ''; ?>>Posts Only</option>
                                        <option value="users" <?php echo $search_type === 'users' ? 'selected' : ''; ?>>Users Only</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select name="sort" class="form-select">
                                        <option value="relevance" <?php echo $sort_by === 'relevance' ? 'selected' : ''; ?>>Most Relevant</option>
                                        <option value="date" <?php echo $sort_by === 'date' ? 'selected' : ''; ?>>Newest First</option>
                                        <option value="views" <?php echo $sort_by === 'views' ? 'selected' : ''; ?>>Most Viewed</option>
                                        <option value="likes" <?php echo $sort_by === 'likes' ? 'selected' : ''; ?>>Most Liked</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-search"></i> Search
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                
                <!-- Search Suggestions -->
                <?php if (empty($search_query)): ?>
                    <div class="search-suggestions">
                        <h4>Popular Searches</h4>
                        <div class="suggestion-tags">
                            <a href="search.php?q=CS2+cheats&type=threads" class="suggestion-tag">CS2 Cheats</a>
                            <a href="search.php?q=tournament&type=threads" class="suggestion-tag">Tournaments</a>
                            <a href="search.php?q=strategy+guide&type=posts" class="suggestion-tag">Strategy Guides</a>
                            <a href="search.php?q=cheat+update&type=threads" class="suggestion-tag">Cheat Updates</a>
                            <a href="search.php?q=pro+player&type=users" class="suggestion-tag">Pro Players</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Search Results Section -->
<?php if ($search_query): ?>
    <section class="search-results-section">
        <div class="container">
            <!-- Results Header -->
            <div class="results-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h2>
                            <i class="fas fa-search"></i>
                            Search Results
                        </h2>
                        <p class="results-summary">
                            Found <?php echo number_format($total_results); ?> results for 
                            <strong>"<?php echo htmlspecialchars($search_query); ?>"</strong>
                            in <?php echo number_format($search_time, 3); ?> seconds
                        </p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="results-actions">
                            <button class="btn btn-outline-primary btn-sm" onclick="exportResults()">
                                <i class="fas fa-download"></i> Export
                            </button>
                            <button class="btn btn-outline-primary btn-sm" onclick="saveSearch()">
                                <i class="fas fa-bookmark"></i> Save Search
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Results Content -->
            <div class="content-card">
                <div class="card-body">
                    <?php if (!empty($results)): ?>
                        <div class="search-results" id="search-results">
                            <?php foreach ($results as $result): ?>
                                <div class="result-item">
                                    <div class="result-icon">
                                        <i class="fas fa-<?php 
                                            echo $result['type'] === 'thread' ? 'file-alt' : 
                                                ($result['type'] === 'post' ? 'comment' : 'user'); 
                                        ?>"></i>
                                    </div>
                                    <div class="result-content">
                                        <div class="result-header">
                                            <h3 class="result-title">
                                                <?php if ($result['type'] === 'user'): ?>
                                                    <a href="profile.php?user=<?php echo urlencode($result['username']); ?>">
                                                        <?php echo htmlspecialchars($result['username']); ?>
                                                    </a>
                                                <?php else: ?>
                                                    <a href="thread.php?slug=<?php echo urlencode($result['slug']); ?>">
                                                        <?php echo htmlspecialchars($result['title']); ?>
                                                    </a>
                                                <?php endif; ?>
                                            </h3>
                                            <div class="result-meta">
                                                <span class="result-type">
                                                    <i class="fas fa-<?php 
                                                        echo $result['type'] === 'thread' ? 'file-alt' : 
                                                            ($result['type'] === 'post' ? 'comment' : 'user'); 
                                                    ?>"></i>
                                                    <?php echo ucfirst($result['type']); ?>
                                                </span>
                                                <span class="result-time">
                                                    <?php echo formatTimeAgo($result['created_at']); ?>
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="result-body">
                                            <?php if ($result['type'] === 'user'): ?>
                                                <div class="user-info">
                                                    <img src="<?php echo getUserAvatar($result['username']); ?>" 
                                                         alt="Avatar" class="avatar-md">
                                                    <div class="user-details">
                                                        <p class="user-rank"><?php echo htmlspecialchars($result['rank']); ?></p>
                                                        <p class="user-stats">
                                                            <span><i class="fas fa-comment"></i> <?php echo number_format($result['post_count']); ?> posts</span>
                                                            <span><i class="fas fa-file-alt"></i> <?php echo number_format($result['thread_count']); ?> threads</span>
                                                            <span><i class="fas fa-star"></i> <?php echo number_format($result['reputation']); ?> reputation</span>
                                                        </p>
                                                        <p class="user-activity">
                                                            Last seen: <?php echo formatTimeAgo($result['last_seen']); ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <div class="content-preview">
                                                    <?php 
                                                    $content = strip_tags($result['content']);
                                                    $preview = strlen($content) > 200 ? substr($content, 0, 200) . '...' : $content;
                                                    echo htmlspecialchars($preview);
                                                    ?>
                                                </div>
                                                
                                                <div class="content-meta">
                                                    <span class="author">
                                                        by <a href="profile.php?user=<?php echo urlencode($result['username']); ?>">
                                                            <?php echo htmlspecialchars($result['username']); ?>
                                                        </a>
                                                        <?php if ($result['rank']): ?>
                                                            <span class="user-rank"><?php echo htmlspecialchars($result['rank']); ?></span>
                                                        <?php endif; ?>
                                                    </span>
                                                    
                                                    <?php if ($result['subforum_name']): ?>
                                                        <span class="forum-location">
                                                            in <a href="subforum.php?slug=<?php echo urlencode($result['subforum_name']); ?>">
                                                                <?php echo htmlspecialchars($result['subforum_name']); ?>
                                                            </a>
                                                        </span>
                                                    <?php endif; ?>
                                                    
                                                    <?php if ($result['type'] === 'thread'): ?>
                                                        <span class="thread-stats">
                                                            <i class="fas fa-eye"></i> <?php echo number_format($result['view_count']); ?> views
                                                            <i class="fas fa-thumbs-up"></i> <?php echo number_format($result['like_count']); ?> likes
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="result-actions">
                                            <?php if ($result['type'] === 'user'): ?>
                                                <a href="profile.php?user=<?php echo urlencode($result['username']); ?>" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-user"></i> View Profile
                                                </a>
                                                <button class="btn btn-sm btn-outline-success" 
                                                        onclick="followUser('<?php echo $result['username']; ?>')">
                                                    <i class="fas fa-plus"></i> Follow
                                                </button>
                                            <?php else: ?>
                                                <a href="thread.php?slug=<?php echo urlencode($result['slug']); ?>" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> View Thread
                                                </a>
                                                <button class="btn btn-sm btn-outline-success" 
                                                        onclick="likeThread(<?php echo $result['id']; ?>)">
                                                    <i class="fas fa-thumbs-up"></i> Like
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Pagination -->
                        <?php if ($total_results > $per_page): ?>
                            <nav aria-label="Search results pagination" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <?php
                                    $total_pages = ceil($total_results / $per_page);
                                    $start_page = max(1, $page - 2);
                                    $end_page = min($total_pages, $page + 2);
                                    ?>
                                    
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?q=<?php echo urlencode($search_query); ?>&type=<?php echo $search_type; ?>&sort=<?php echo $sort_by; ?>&page=<?php echo $page - 1; ?>">
                                                <i class="fas fa-chevron-left"></i> Previous
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="?q=<?php echo urlencode($search_query); ?>&type=<?php echo $search_type; ?>&sort=<?php echo $sort_by; ?>&page=<?php echo $i; ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <?php if ($page < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?q=<?php echo urlencode($search_query); ?>&type=<?php echo $search_type; ?>&sort=<?php echo $sort_by; ?>&page=<?php echo $page + 1; ?>">
                                                Next <i class="fas fa-chevron-right"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                        
                    <?php else: ?>
                        <div class="no-results">
                            <div class="text-center py-5">
                                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                <h3>No results found</h3>
                                <p class="text-muted">
                                    We couldn't find any results for "<?php echo htmlspecialchars($search_query); ?>".
                                </p>
                                <div class="suggestions">
                                    <h5>Suggestions:</h5>
                                    <ul class="list-unstyled">
                                        <li>• Check your spelling</li>
                                        <li>• Try different keywords</li>
                                        <li>• Use more general terms</li>
                                        <li>• Try searching in a different category</li>
                                    </ul>
                                </div>
                                <a href="search.php" class="btn btn-primary mt-3">
                                    <i class="fas fa-search"></i> New Search
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Search Analytics Section -->
<?php if ($search_query && !empty($results)): ?>
    <section class="search-analytics-section">
        <div class="container">
            <div class="content-card">
                <div class="card-header">
                    <h3><i class="fas fa-chart-bar"></i> Search Analytics</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="analytics-item">
                                <div class="analytics-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="analytics-content">
                                    <div class="analytics-number"><?php echo number_format($search_time, 3); ?>s</div>
                                    <div class="analytics-label">Search Time</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="analytics-item">
                                <div class="analytics-icon">
                                    <i class="fas fa-list"></i>
                                </div>
                                <div class="analytics-content">
                                    <div class="analytics-number"><?php echo number_format($total_results); ?></div>
                                    <div class="analytics-label">Total Results</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="analytics-item">
                                <div class="analytics-icon">
                                    <i class="fas fa-eye"></i>
                                </div>
                                <div class="analytics-content">
                                    <div class="analytics-number"><?php echo number_format($per_page); ?></div>
                                    <div class="analytics-label">Per Page</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="analytics-item">
                                <div class="analytics-icon">
                                    <i class="fas fa-sort"></i>
                                </div>
                                <div class="analytics-content">
                                    <div class="analytics-number"><?php echo ucfirst($sort_by); ?></div>
                                    <div class="analytics-label">Sort By</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>

<script>
// Enhanced search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('main-search');
    const searchForm = document.querySelector('.search-form');
    
    if (searchInput) {
        let searchTimeout;
        
        // Live search suggestions
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length >= 2) {
                searchTimeout = setTimeout(() => {
                    getSearchSuggestions(query);
                }, 300);
            } else {
                hideSearchSuggestions();
            }
        });
        
        // Search on Enter
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchForm.submit();
            }
        });
        
        // Focus enhancements
        searchInput.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        searchInput.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
    }
    
    // Add smooth animations to results
    const resultItems = document.querySelectorAll('.result-item');
    resultItems.forEach((item, index) => {
        item.style.animationDelay = `${index * 0.1}s`;
        item.classList.add('fadeInUp');
    });
});

// Get search suggestions
function getSearchSuggestions(query) {
    // Simulate API call for suggestions
    const suggestions = [
        'CS2 cheats ' + query,
        'Tournament ' + query,
        'Strategy ' + query,
        'Guide ' + query,
        'Update ' + query
    ];
    
    showSearchSuggestions(suggestions);
}

// Show search suggestions
function showSearchSuggestions(suggestions) {
    hideSearchSuggestions();
    
    const suggestionsContainer = document.createElement('div');
    suggestionsContainer.className = 'search-suggestions-dropdown';
    suggestionsContainer.style.cssText = `
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #dee2e6;
        border-top: none;
        border-radius: 0 0 0.375rem 0.375rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        z-index: 1000;
        max-height: 300px;
        overflow-y: auto;
    `;
    
    suggestions.forEach(suggestion => {
        const item = document.createElement('div');
        item.className = 'suggestion-item';
        item.style.cssText = `
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f8f9fa;
            cursor: pointer;
            transition: background-color 0.2s ease;
        `;
        
        item.innerHTML = `
            <i class="fas fa-search text-muted me-2"></i>
            ${suggestion}
        `;
        
        item.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8f9fa';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.backgroundColor = 'transparent';
        });
        
        item.addEventListener('click', function() {
            document.getElementById('main-search').value = suggestion;
            hideSearchSuggestions();
            document.querySelector('.search-form').submit();
        });
        
        suggestionsContainer.appendChild(item);
    });
    
    // Add to search container
    const searchContainer = document.querySelector('.search-container');
    searchContainer.appendChild(suggestionsContainer);
    
    // Auto-hide on outside click
    document.addEventListener('click', function hideSuggestions(e) {
        if (!searchContainer.contains(e.target)) {
            hideSearchSuggestions();
            document.removeEventListener('click', hideSuggestions);
        }
    });
}

// Hide search suggestions
function hideSearchSuggestions() {
    const existing = document.querySelector('.search-suggestions-dropdown');
    if (existing) {
        existing.remove();
    }
}

// Export search results
function exportResults() {
    const searchQuery = '<?php echo addslashes($search_query); ?>';
    const results = document.getElementById('search-results');
    
    if (!results) return;
    
    // Create CSV content
    let csvContent = 'data:text/csv;charset=utf-8,';
    csvContent += 'Title,Type,Author,Date,Content\n';
    
    const resultItems = results.querySelectorAll('.result-item');
    resultItems.forEach(item => {
        const title = item.querySelector('.result-title a')?.textContent || '';
        const type = item.querySelector('.result-type')?.textContent || '';
        const author = item.querySelector('.author a')?.textContent || '';
        const date = item.querySelector('.result-time')?.textContent || '';
        const content = item.querySelector('.content-preview')?.textContent || '';
        
        csvContent += `"${title}","${type}","${author}","${date}","${content}"\n`;
    });
    
    // Download file
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', `search_results_${searchQuery}_${new Date().toISOString().split('T')[0]}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    showNotification('Search results exported successfully!', 'success');
}

// Save search
function saveSearch() {
    const searchQuery = '<?php echo addslashes($search_query); ?>';
    const searchType = '<?php echo addslashes($search_type); ?>';
    const searchSort = '<?php echo addslashes($sort_by); ?>';
    
    // Save to localStorage
    const savedSearches = JSON.parse(localStorage.getItem('savedSearches') || '[]');
    const searchId = Date.now();
    
    savedSearches.push({
        id: searchId,
        query: searchQuery,
        type: searchType,
        sort: searchSort,
        date: new Date().toISOString(),
        resultCount: <?php echo $total_results; ?>
    });
    
    // Keep only last 10 searches
    if (savedSearches.length > 10) {
        savedSearches.shift();
    }
    
    localStorage.setItem('savedSearches', JSON.stringify(savedSearches));
    
    showNotification('Search saved successfully!', 'success');
}

// Follow user
function followUser(username) {
    // Implement follow functionality
    console.log('Following user:', username);
    showNotification(`Now following ${username}!`, 'success');
}

// Like thread
function likeThread(threadId) {
    // Implement like functionality
    console.log('Liking thread:', threadId);
    showNotification('Thread liked successfully!', 'success');
}

// Performance monitoring
document.addEventListener('DOMContentLoaded', function() {
    const startTime = performance.now();
    
    window.addEventListener('load', function() {
        const loadTime = performance.now() - startTime;
        
        if (loadTime > 2000) {
            console.warn('Search page load time is slow:', loadTime.toFixed(2) + 'ms');
        }
        
        // Send performance data to analytics
        if (typeof gtag !== 'undefined') {
            gtag('event', 'timing_complete', {
                name: 'search_load',
                value: Math.round(loadTime)
            });
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
