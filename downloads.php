<?php
require_once 'config.php';

// Set page title
$page_title = 'Download Affinity';

// Simulate cheat data (in a real application, this would come from a database)
$cheat_data = [
    'version' => 'v2.4.1',
    'release_date' => date('Y-m-d H:i:s', strtotime('-2 hours')),
    'file_size' => '12.8 MB',
    'downloads_today' => rand(1200, 2500),
    'total_downloads' => rand(150000, 300000),
    'detection_status' => 'UNDETECTED',
    'last_detection_check' => date('Y-m-d H:i:s', strtotime('-5 minutes')),
    'features' => [
        'aimbot' => ['status' => 'active', 'accuracy' => '97%'],
        'esp' => ['status' => 'active', 'range' => '500m'],
        'triggerbot' => ['status' => 'active', 'delay' => '15ms'],
        'anti_detection' => ['status' => 'active', 'level' => 'maximum']
    ],
    'requirements' => [
        'os' => 'Windows 10/11 (64-bit)',
        'ram' => '8GB RAM minimum',
        'storage' => '50MB free space',
        'game' => 'Counter-Strike 2 (Steam)',
        'antivirus' => 'Disable real-time protection'
    ],
    'changelog' => [
        [
            'version' => 'v2.4.1',
            'date' => '2 hours ago',
            'changes' => [
                'Improved aimbot smoothness algorithm',
                'Enhanced ESP rendering performance',
                'Fixed rare crash on map change',
                'Updated anti-detection signatures',
                'Added new customization options'
            ]
        ],
        [
            'version' => 'v2.4.0',
            'date' => '1 day ago',
            'changes' => [
                'Major performance improvements',
                'New triggerbot reaction time settings',
                'Enhanced UI with better animations',
                'Improved memory usage',
                'Security enhancements'
            ]
        ]
    ]
];

// Include header
include 'includes/header.php';
?>

<!-- ===== ULTRA PREMIUM DOWNLOAD HERO ===== -->
<section class="download-hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="download-hero-content animate-fade-in-left">
                    <div class="hero-badge">
                        <span class="badge-icon">🚀</span>
                        <span class="badge-text">Latest Release</span>
                        <span class="badge-version"><?php echo $cheat_data['version']; ?></span>
                    </div>
                    
                    <h1 class="download-hero-title">
                        Download <span class="highlight-text">Affinity</span>
                        <span class="version-tag"><?php echo $cheat_data['version']; ?></span>
                    </h1>
                    
                    <p class="download-hero-subtitle">
                        The most advanced and <span class="highlight-text">undetectable</span> CS2 cheat. 
                        Join <span class="highlight-number"><?php echo number_format($cheat_data['total_downloads']); ?>+</span> 
                        satisfied users who dominate every match safely.
                    </p>
                    
                    <!-- Safety Guarantee -->
                    <div class="safety-guarantee">
                        <div class="guarantee-icon">
                            <i class="fas fa-shield-check"></i>
                        </div>
                        <div class="guarantee-content">
                            <div class="guarantee-title">100% VAC UNDETECTED</div>
                            <div class="guarantee-subtitle">732+ days without a single ban • Lifetime updates included</div>
                        </div>
                        <div class="guarantee-badge">
                            <span class="badge-text">GUARANTEED</span>
                        </div>
                    </div>
                    
                    <!-- Download Stats -->
                    <div class="download-stats">
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-download"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number"><?php echo number_format($cheat_data['downloads_today']); ?></div>
                                <div class="stat-label">Downloads Today</div>
                            </div>
                        </div>
                        
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number"><?php echo rand(800, 1500); ?></div>
                                <div class="stat-label">Active Users</div>
                            </div>
                        </div>
                        
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number">4.9</div>
                                <div class="stat-label">User Rating</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Download Card -->
                <div class="download-card-main animate-fade-in-right">
                    <div class="download-card-header">
                        <div class="card-icon">
                            <i class="fas fa-shield-alt"></i>
                            <div class="icon-glow"></div>
                        </div>
                        <div class="card-title">Affinity CS2 Cheat</div>
                        <div class="card-version"><?php echo $cheat_data['version']; ?></div>
                    </div>
                    
                    <div class="download-card-content">
                        <div class="status-indicators">
                            <div class="status-indicator safe">
                                <span class="indicator-dot"></span>
                                <span class="indicator-text"><?php echo $cheat_data['detection_status']; ?></span>
                            </div>
                            <div class="status-indicator online">
                                <span class="indicator-dot"></span>
                                <span class="indicator-text">SERVERS ONLINE</span>
                            </div>
                        </div>
                        
                        <div class="download-info">
                            <div class="info-item">
                                <span class="info-label">File Size:</span>
                                <span class="info-value"><?php echo $cheat_data['file_size']; ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Released:</span>
                                <span class="info-value"><?php echo date('M j, Y', strtotime($cheat_data['release_date'])); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Downloads:</span>
                                <span class="info-value"><?php echo number_format($cheat_data['total_downloads']); ?>+</span>
                            </div>
                        </div>
                        
                        <div class="download-actions">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <button class="btn btn-download-main" onclick="startDownload()">
                                    <div class="btn-content">
                                        <i class="fas fa-download"></i>
                                        <span>Download Now</span>
                                        <div class="btn-glow"></div>
                                    </div>
                                </button>
                                <button class="btn btn-outline-premium" onclick="showSystemRequirements()">
                                    <div class="btn-content">
                                        <i class="fas fa-cog"></i>
                                        <span>System Requirements</span>
                                    </div>
                                </button>
                            <?php else: ?>
                                <a href="register.php" class="btn btn-download-main">
                                    <div class="btn-content">
                                        <i class="fas fa-user-plus"></i>
                                        <span>Join to Download</span>
                                        <div class="btn-glow"></div>
                                    </div>
                                </a>
                                <a href="login.php" class="btn btn-outline-premium">
                                    <div class="btn-content">
                                        <i class="fas fa-sign-in-alt"></i>
                                        <span>Login</span>
                                    </div>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
                
<!-- ===== FEATURES OVERVIEW SECTION ===== -->
<section class="features-overview-section">
    <div class="container">
        <div class="section-header text-center">
            <h2 class="section-title ultra-premium-title">
                <span class="title-icon">⚡</span>
                <span>What's Inside Affinity</span>
            </h2>
            <p class="section-subtitle">Cutting-edge features designed for elite CS2 players</p>
        </div>
        
        <div class="features-showcase-grid">
            <div class="feature-showcase-card animate-fade-in-up" style="animation-delay: 0.1s;">
                <div class="feature-showcase-header">
                    <div class="feature-showcase-icon">
                        <i class="fas fa-crosshairs"></i>
                        <div class="icon-glow"></div>
                    </div>
                    <div class="feature-showcase-title">Advanced Aimbot</div>
                    <div class="feature-status active">ACTIVE</div>
                </div>
                <div class="feature-showcase-content">
                    <p>Human-like aiming with customizable smoothness, FOV, and bone selection. Our advanced algorithm ensures natural-looking movements.</p>
                    <div class="feature-specs">
                        <div class="spec-item">
                            <span class="spec-label">Accuracy:</span>
                            <span class="spec-value"><?php echo $cheat_data['features']['aimbot']['accuracy']; ?></span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Smoothness:</span>
                            <span class="spec-value">1-100 (Customizable)</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">FOV:</span>
                            <span class="spec-value">1-180° (Adjustable)</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="feature-showcase-card animate-fade-in-up" style="animation-delay: 0.2s;">
                <div class="feature-showcase-header">
                    <div class="feature-showcase-icon">
                        <i class="fas fa-eye"></i>
                        <div class="icon-glow"></div>
                    </div>
                    <div class="feature-showcase-title">ESP Wallhack</div>
                    <div class="feature-status active">ACTIVE</div>
                </div>
                <div class="feature-showcase-content">
                    <p>See enemies, weapons, and items through walls with customizable ESP boxes, names, health bars, and distance indicators.</p>
                    <div class="feature-specs">
                        <div class="spec-item">
                            <span class="spec-label">Range:</span>
                            <span class="spec-value"><?php echo $cheat_data['features']['esp']['range']; ?></span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Players:</span>
                            <span class="spec-value">Box, Name, Health</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Items:</span>
                            <span class="spec-value">Weapons, Grenades, C4</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="feature-showcase-card animate-fade-in-up" style="animation-delay: 0.3s;">
                <div class="feature-showcase-header">
                    <div class="feature-showcase-icon">
                        <i class="fas fa-bolt"></i>
                        <div class="icon-glow"></div>
                    </div>
                    <div class="feature-showcase-title">Triggerbot</div>
                    <div class="feature-status active">ACTIVE</div>
                </div>
                <div class="feature-showcase-content">
                    <p>Instant reactions with customizable delay and hitchance. Perfect for holding angles and spray control.</p>
                    <div class="feature-specs">
                        <div class="spec-item">
                            <span class="spec-label">Delay:</span>
                            <span class="spec-value"><?php echo $cheat_data['features']['triggerbot']['delay']; ?></span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Hitchance:</span>
                            <span class="spec-value">1-100% (Configurable)</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">RCS:</span>
                            <span class="spec-value">Recoil Control System</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="feature-showcase-card animate-fade-in-up" style="animation-delay: 0.4s;">
                <div class="feature-showcase-header">
                    <div class="feature-showcase-icon">
                        <i class="fas fa-shield-virus"></i>
                        <div class="icon-glow"></div>
                    </div>
                    <div class="feature-showcase-title">Anti-Detection</div>
                    <div class="feature-status safe">SAFE</div>
                </div>
                <div class="feature-showcase-content">
                    <p>Military-grade protection against VAC, FACEIT, ESEA, and other anti-cheat systems. Your safety is our priority.</p>
                    <div class="feature-specs">
                        <div class="spec-item">
                            <span class="spec-label">Protection:</span>
                            <span class="spec-value">Maximum Level</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">VAC Status:</span>
                            <span class="spec-value">Undetected</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Last Scan:</span>
                            <span class="spec-value">5 minutes ago</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
                
<!-- ===== CHANGELOG SECTION ===== -->
<section class="changelog-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="section-header">
                    <h2 class="section-title ultra-premium-title">
                        <span class="title-icon">📋</span>
                        <span>Changelog</span>
                    </h2>
                    <p class="section-subtitle">Latest updates and improvements</p>
                </div>
                
                <div class="changelog-timeline">
                    <?php foreach ($cheat_data['changelog'] as $index => $update): ?>
                    <div class="changelog-item animate-fade-in-up" style="animation-delay: <?php echo $index * 0.1; ?>s;">
                        <div class="changelog-marker">
                            <div class="marker-dot"></div>
                            <div class="marker-line"></div>
                        </div>
                        <div class="changelog-content ultra-premium-card">
                            <div class="changelog-header">
                                <div class="changelog-version"><?php echo $update['version']; ?></div>
                                <div class="changelog-date"><?php echo $update['date']; ?></div>
                                <?php if ($index === 0): ?>
                                    <div class="changelog-badge latest">LATEST</div>
                                <?php endif; ?>
                            </div>
                            <div class="changelog-changes">
                                <?php foreach ($update['changes'] as $change): ?>
                                    <div class="changelog-change">
                                        <i class="fas fa-check-circle"></i>
                                        <span><?php echo $change; ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- System Requirements -->
                <div class="requirements-card ultra-premium-card animate-fade-in-right">
                    <div class="requirements-header">
                        <div class="header-icon">
                            <i class="fas fa-desktop"></i>
                            <div class="icon-glow"></div>
                        </div>
                        <div class="header-title">System Requirements</div>
                    </div>
                    
                    <div class="requirements-content">
                        <?php foreach ($cheat_data['requirements'] as $key => $requirement): ?>
                        <div class="requirement-item">
                            <div class="requirement-icon">
                                <i class="fas fa-<?php echo getRequirementIcon($key); ?>"></i>
                            </div>
                            <div class="requirement-content">
                                <div class="requirement-label"><?php echo ucfirst(str_replace('_', ' ', $key)); ?></div>
                                <div class="requirement-value"><?php echo $requirement; ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="requirements-footer">
                        <div class="compatibility-check">
                            <button class="btn btn-outline-premium btn-sm w-100" onclick="checkCompatibility()">
                                <i class="fas fa-check-circle me-2"></i>Check Compatibility
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Installation Guide -->
                <div class="installation-guide ultra-premium-card animate-fade-in-right" style="animation-delay: 0.2s;">
                    <div class="guide-header">
                        <div class="header-icon">
                            <i class="fas fa-book"></i>
                            <div class="icon-glow"></div>
                        </div>
                        <div class="header-title">Installation Guide</div>
                    </div>
                    
                    <div class="guide-content">
                        <div class="guide-step">
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <div class="step-title">Download Affinity</div>
                                <div class="step-description">Click the download button to get the latest version</div>
                            </div>
                        </div>
                        
                        <div class="guide-step">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <div class="step-title">Disable Antivirus</div>
                                <div class="step-description">Temporarily disable real-time protection</div>
                            </div>
                        </div>
                        
                        <div class="guide-step">
                            <div class="step-number">3</div>
                            <div class="step-content">
                                <div class="step-title">Extract & Run</div>
                                <div class="step-description">Extract files and run as administrator</div>
                            </div>
                        </div>
                        
                        <div class="guide-step">
                            <div class="step-number">4</div>
                            <div class="step-content">
                                <div class="step-title">Launch CS2</div>
                                <div class="step-description">Start Counter-Strike 2 and enjoy!</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="guide-footer">
                        <button class="btn btn-outline-premium btn-sm w-100" onclick="showDetailedGuide()">
                            <i class="fas fa-book-open me-2"></i>Detailed Guide
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
                
<!-- ===== DOWNLOAD SECURITY SECTION ===== -->
<section class="security-section">
    <div class="container">
        <div class="section-header text-center">
            <h2 class="section-title ultra-premium-title">
                <span class="title-icon">🛡️</span>
                <span>Your Safety is Our Priority</span>
            </h2>
            <p class="section-subtitle">Advanced protection against all anti-cheat systems</p>
        </div>
        
        <div class="security-features">
            <div class="security-feature animate-fade-in-up" style="animation-delay: 0.1s;">
                <div class="security-icon">
                    <i class="fas fa-shield-check"></i>
                    <div class="icon-glow"></div>
                </div>
                <div class="security-content">
                    <h3>VAC Protection</h3>
                    <p>Advanced bypass technology keeps you safe from Valve Anti-Cheat</p>
                    <div class="security-status safe">PROTECTED</div>
                </div>
            </div>
            
            <div class="security-feature animate-fade-in-up" style="animation-delay: 0.2s;">
                <div class="security-icon">
                    <i class="fas fa-user-shield"></i>
                    <div class="icon-glow"></div>
                </div>
                <div class="security-content">
                    <h3>FACEIT Safe</h3>
                    <p>Undetected on FACEIT with specialized anti-detection measures</p>
                    <div class="security-status safe">PROTECTED</div>
                </div>
            </div>
            
            <div class="security-feature animate-fade-in-up" style="animation-delay: 0.3s;">
                <div class="security-icon">
                    <i class="fas fa-lock"></i>
                    <div class="icon-glow"></div>
                </div>
                <div class="security-content">
                    <h3>ESEA Compatible</h3>
                    <p>Works seamlessly with ESEA's anti-cheat system</p>
                    <div class="security-status safe">PROTECTED</div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<script>
// Ultra Premium Download Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Download page initialized');
    
    // Animate download stats
    animateDownloadStats();
    
    // Setup real-time updates
    setupRealTimeUpdates();
});

function startDownload() {
    // Create download modal
    const modal = document.createElement('div');
    modal.className = 'download-modal';
    modal.innerHTML = `
        <div class="modal-backdrop" onclick="this.parentElement.remove()"></div>
        <div class="modal-content ultra-premium-card">
            <div class="modal-header">
                <h3>🚀 Download Affinity v<?php echo $cheat_data['version']; ?></h3>
                <button class="modal-close" onclick="this.closest('.download-modal').remove()">×</button>
            </div>
            <div class="modal-body">
                <div class="download-progress-container">
                    <div class="download-icon">
                        <i class="fas fa-download"></i>
                    </div>
                    <div class="download-info">
                        <div class="download-filename">Affinity_v<?php echo str_replace('.', '_', $cheat_data['version']); ?>.zip</div>
                        <div class="download-size"><?php echo $cheat_data['file_size']; ?></div>
                    </div>
                    <div class="download-progress">
                        <div class="progress-bar" id="downloadProgress"></div>
                        <div class="progress-text">
                            <span id="progressPercent">0%</span>
                            <span id="downloadSpeed">0 KB/s</span>
                        </div>
                    </div>
                </div>
                
                <div class="download-instructions">
                    <h4>📋 Important Instructions:</h4>
                    <ol>
                        <li>Disable your antivirus temporarily</li>
                        <li>Extract the files to a secure location</li>
                        <li>Run as administrator</li>
                        <li>Follow the setup wizard</li>
                    </ol>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-ultra-premium" onclick="simulateDownload()">
                    <div class="btn-content">
                        <i class="fas fa-download me-2"></i>
                        <span>Start Download</span>
                        <div class="btn-glow"></div>
                    </div>
                </button>
            </div>
        </div>
    `;
    
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
    `;
    
    document.body.appendChild(modal);
}

function simulateDownload() {
    const progressBar = document.getElementById('downloadProgress');
    const progressPercent = document.getElementById('progressPercent');
    const downloadSpeed = document.getElementById('downloadSpeed');
    
    let progress = 0;
    const interval = setInterval(() => {
        progress += Math.random() * 15 + 5;
        if (progress >= 100) {
            progress = 100;
            clearInterval(interval);
            
            // Show completion
            progressPercent.textContent = '100%';
            downloadSpeed.textContent = 'Complete!';
            
            setTimeout(() => {
                showNotification('Download completed successfully! Check your downloads folder.', 'success');
                document.querySelector('.download-modal').remove();
            }, 1000);
        } else {
            progressBar.style.width = progress + '%';
            progressPercent.textContent = Math.floor(progress) + '%';
            downloadSpeed.textContent = (Math.random() * 500 + 100).toFixed(0) + ' KB/s';
        }
    }, 200);
}

function showSystemRequirements() {
    showNotification('System requirements displayed in the sidebar →', 'info');
    
    // Highlight requirements card
    const requirementsCard = document.querySelector('.requirements-card');
    if (requirementsCard) {
        requirementsCard.style.animation = 'pulse-glow 1s ease-out';
        setTimeout(() => {
            requirementsCard.style.animation = '';
        }, 1000);
    }
}

function checkCompatibility() {
    showNotification('Checking system compatibility...', 'info');
    
    setTimeout(() => {
        const compatible = Math.random() > 0.1; // 90% chance of compatibility
        if (compatible) {
            showNotification('✅ Your system is compatible with Affinity!', 'success');
        } else {
            showNotification('⚠️ Some requirements may not be met. Check the guide for help.', 'warning');
        }
    }, 2000);
}

function showDetailedGuide() {
    showNotification('Opening detailed installation guide...', 'info');
    // In a real application, this would open a detailed guide modal or page
}

function animateDownloadStats() {
    const statNumbers = document.querySelectorAll('.download-stats .stat-number');
    statNumbers.forEach((stat, index) => {
        setTimeout(() => {
            stat.style.animation = 'count-up 1s ease-out';
        }, index * 200);
    });
}

function setupRealTimeUpdates() {
    // Update download count every 10 seconds
    setInterval(() => {
        const downloadElements = document.querySelectorAll('[data-stat="downloads"]');
        downloadElements.forEach(element => {
            const current = parseInt(element.textContent.replace(/[^\d]/g, ''));
            const newValue = current + Math.floor(Math.random() * 10);
            element.textContent = newValue.toLocaleString();
        });
    }, 10000);
}

// Add download modal styles
const downloadStyles = document.createElement('style');
downloadStyles.textContent = `
    .download-modal {
        animation: fadeIn 0.3s ease-out;
    }
    
    .download-progress-container {
        text-align: center;
        margin: 2rem 0;
    }
    
    .download-icon {
        font-size: 3rem;
        color: var(--accent-primary);
        margin-bottom: 1rem;
        text-shadow: var(--glow-primary);
    }
    
    .download-filename {
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }
    
    .download-size {
        color: var(--text-secondary);
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }
    
    .download-progress {
        background: var(--bg-tertiary);
        border-radius: var(--radius-md);
        height: 20px;
        position: relative;
        overflow: hidden;
        margin-bottom: 1rem;
    }
    
    .progress-bar {
        background: var(--gradient-primary);
        height: 100%;
        width: 0%;
        transition: width 0.3s ease;
        border-radius: inherit;
        box-shadow: var(--glow-primary);
    }
    
    .progress-text {
        display: flex;
        justify-content: space-between;
        font-size: 0.8rem;
        color: var(--text-secondary);
    }
    
    .download-instructions {
        background: var(--bg-glass);
        border: 1px solid var(--border-primary);
        border-radius: var(--radius-md);
        padding: 1rem;
        margin-top: 1rem;
    }
    
    .download-instructions h4 {
        color: var(--accent-warning);
        margin-bottom: 1rem;
    }
    
    .download-instructions ol {
        margin: 0;
        padding-left: 1.5rem;
    }
    
    .download-instructions li {
        color: var(--text-secondary);
        margin-bottom: 0.5rem;
    }
`;
document.head.appendChild(downloadStyles);
</script>

<?php
// Helper functions
function getRequirementIcon($key) {
    $icons = [
        'os' => 'desktop',
        'ram' => 'memory',
        'storage' => 'hdd',
        'game' => 'gamepad',
        'antivirus' => 'shield-alt'
    ];
    return $icons[$key] ?? 'cog';
}

function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}
?>
