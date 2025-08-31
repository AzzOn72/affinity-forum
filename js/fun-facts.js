/**
 * Ultra Premium Fun Facts & Tips System
 * Engaging content to keep users interested
 */

console.log('🎯 Fun Facts system loading...');

class UltraPremiumFunFacts {
    constructor() {
        this.facts = [
            {
                title: "🎯 Pro Tip",
                content: "Lower your aimbot smoothness gradually. Start at 50 and decrease by 5 each day until you find your sweet spot.",
                category: "aimbot",
                icon: "crosshairs"
            },
            {
                title: "🛡️ Safety First",
                content: "Always disable Windows Defender real-time protection before running Affinity to avoid false positives.",
                category: "safety",
                icon: "shield-check"
            },
            {
                title: "👀 ESP Mastery",
                content: "Use box ESP instead of glow ESP on bright maps like de_dust2 for better visibility.",
                category: "esp",
                icon: "eye"
            },
            {
                title: "⚡ Performance Boost",
                content: "Close unnecessary programs while using Affinity to maximize FPS and reduce detection risk.",
                category: "performance",
                icon: "bolt"
            },
            {
                title: "🎮 CS2 Fact",
                content: "The AK-47 has a base damage of 36, meaning it can one-shot headshot enemies with helmets at close range.",
                category: "cs2",
                icon: "gamepad"
            },
            {
                title: "📊 Statistics",
                content: "Affinity users have a 94% higher win rate compared to legit players, while maintaining 100% safety.",
                category: "stats",
                icon: "chart-line"
            },
            {
                title: "🏆 Achievement Tip",
                content: "Post helpful guides and tips to earn the 'Community Helper' badge and increase your reputation.",
                category: "community",
                icon: "trophy"
            },
            {
                title: "🔧 Configuration",
                content: "Save multiple config files for different game modes: one for MM, one for FACEIT, and one for casual.",
                category: "config",
                icon: "cog"
            },
            {
                title: "🎯 Aim Training",
                content: "Practice your legit aim on aim_botz for 10 minutes daily to make your cheat usage look more natural.",
                category: "training",
                icon: "target"
            },
            {
                title: "🌟 Fun Fact",
                content: "The longest recorded VAC-free streak with Affinity is 847 days and counting!",
                category: "record",
                icon: "star"
            }
        ];
        
        this.currentFactIndex = 0;
        this.isDisplaying = false;
        this.init();
    }
    
    init() {
        this.createFactsContainer();
        this.startFactsRotation();
        this.setupFactsInteraction();
        console.log('🎯 Fun Facts system initialized');
    }
    
    createFactsContainer() {
        const container = document.createElement('div');
        container.id = 'funFactsContainer';
        container.className = 'fun-facts-container';
        container.innerHTML = `
            <div class="fun-fact-card ultra-premium-card" id="funFactCard">
                <div class="fact-header">
                    <div class="fact-icon">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <div class="fact-title">Did You Know?</div>
                    <button class="fact-close" onclick="this.closest('.fun-facts-container').style.display='none'">×</button>
                </div>
                <div class="fact-content" id="factContent">
                    <div class="fact-category" id="factCategory"></div>
                    <div class="fact-text" id="factText"></div>
                </div>
                <div class="fact-footer">
                    <button class="btn btn-fact-next" onclick="window.funFacts.showNextFact()">
                        <i class="fas fa-arrow-right me-1"></i>Next Tip
                    </button>
                    <div class="fact-progress">
                        <div class="progress-bar" id="factProgress"></div>
                    </div>
                </div>
            </div>
        `;
        
        container.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            max-width: 350px;
            display: none;
        `;
        
        document.body.appendChild(container);
    }
    
    startFactsRotation() {
        // Show first fact after 10 seconds
        setTimeout(() => {
            this.showFact();
        }, 10000);
        
        // Rotate facts every 30 seconds
        setInterval(() => {
            if (!this.isDisplaying) {
                this.showFact();
            }
        }, 30000);
    }
    
    showFact() {
        const container = document.getElementById('funFactsContainer');
        const fact = this.facts[this.currentFactIndex];
        
        if (!fact || this.isDisplaying) return;
        
        this.isDisplaying = true;
        
        // Update content
        document.getElementById('factCategory').textContent = fact.title;
        document.getElementById('factText').textContent = fact.content;
        document.querySelector('.fact-icon i').className = `fas fa-${fact.icon}`;
        
        // Show container
        container.style.display = 'block';
        container.style.animation = 'slideInRight 0.5s ease-out';
        
        // Start progress bar
        this.startProgressBar();
        
        // Auto-hide after 15 seconds
        setTimeout(() => {
            this.hideFact();
        }, 15000);
    }
    
    showNextFact() {
        this.hideFact();
        setTimeout(() => {
            this.currentFactIndex = (this.currentFactIndex + 1) % this.facts.length;
            this.showFact();
        }, 500);
    }
    
    hideFact() {
        const container = document.getElementById('funFactsContainer');
        container.style.animation = 'slideOutRight 0.5s ease-out';
        
        setTimeout(() => {
            container.style.display = 'none';
            this.isDisplaying = false;
        }, 500);
    }
    
    startProgressBar() {
        const progressBar = document.getElementById('factProgress');
        progressBar.style.width = '0%';
        progressBar.style.transition = 'width 15s linear';
        
        setTimeout(() => {
            progressBar.style.width = '100%';
        }, 100);
    }
    
    setupFactsInteraction() {
        // Keyboard shortcut to show random fact
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey && e.key === 'F1') {
                e.preventDefault();
                this.showRandomFact();
            }
        });
    }
    
    showRandomFact() {
        this.currentFactIndex = Math.floor(Math.random() * this.facts.length);
        this.hideFact();
        setTimeout(() => {
            this.showFact();
        }, 100);
    }
    
    addCustomFact(fact) {
        this.facts.push(fact);
        console.log('✅ Custom fact added:', fact.title);
    }
}

// CS2 Tips System
class CS2TipsSystem {
    constructor() {
        this.tips = [
            {
                type: "aimbot",
                title: "Aimbot Smoothness",
                content: "For natural-looking aim, use smoothness values between 15-35. Higher values for longer ranges.",
                difficulty: "beginner"
            },
            {
                type: "esp",
                title: "ESP Colors",
                content: "Use different colors for enemies and teammates. Red for enemies, blue for teammates works best.",
                difficulty: "beginner"
            },
            {
                type: "triggerbot",
                title: "Trigger Delay",
                content: "Set trigger delay between 10-50ms to avoid looking suspicious. Adjust based on your reaction time.",
                difficulty: "intermediate"
            },
            {
                type: "safety",
                title: "VAC Avoidance",
                content: "Never use obvious settings like 0 smoothness or 180° FOV. Stay within human-possible ranges.",
                difficulty: "advanced"
            },
            {
                type: "faceit",
                title: "FACEIT Safety",
                content: "On FACEIT, use extra conservative settings and enable all anti-detection features.",
                difficulty: "advanced"
            }
        ];
        
        this.init();
    }
    
    init() {
        this.setupTipsSystem();
        console.log('💡 CS2 Tips System initialized');
    }
    
    setupTipsSystem() {
        // Add tips button to navigation
        this.addTipsButton();
        
        // Show context-sensitive tips
        this.setupContextTips();
    }
    
    addTipsButton() {
        const navbar = document.querySelector('.ultra-premium-nav-controls');
        if (navbar) {
            const tipsButton = document.createElement('button');
            tipsButton.className = 'btn ultra-premium-nav-btn ms-2';
            tipsButton.onclick = () => this.showTipsModal();
            tipsButton.innerHTML = `
                <div class="btn-content">
                    <i class="fas fa-lightbulb"></i>
                    <span>Tips</span>
                    <div class="btn-glow"></div>
                </div>
            `;
            navbar.appendChild(tipsButton);
        }
    }
    
    showTipsModal() {
        const modal = document.createElement('div');
        modal.className = 'tips-modal';
        modal.innerHTML = `
            <div class="modal-backdrop" onclick="this.parentElement.remove()"></div>
            <div class="modal-content ultra-premium-card">
                <div class="modal-header">
                    <h3>💡 CS2 Pro Tips</h3>
                    <button class="modal-close" onclick="this.closest('.tips-modal').remove()">×</button>
                </div>
                <div class="modal-body">
                    <div class="tips-categories">
                        <button class="btn btn-category active" data-category="all">All Tips</button>
                        <button class="btn btn-category" data-category="aimbot">Aimbot</button>
                        <button class="btn btn-category" data-category="esp">ESP</button>
                        <button class="btn btn-category" data-category="safety">Safety</button>
                    </div>
                    <div class="tips-list" id="tipsList">
                        ${this.renderTips()}
                    </div>
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
        
        // Setup category filtering
        modal.querySelectorAll('.btn-category').forEach(btn => {
            btn.addEventListener('click', () => {
                modal.querySelectorAll('.btn-category').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                this.filterTips(btn.dataset.category, modal);
            });
        });
    }
    
    renderTips(category = 'all') {
        const filteredTips = category === 'all' 
            ? this.tips 
            : this.tips.filter(tip => tip.type === category);
            
        return filteredTips.map(tip => `
            <div class="tip-item">
                <div class="tip-header">
                    <div class="tip-title">${tip.title}</div>
                    <div class="tip-difficulty ${tip.difficulty}">${tip.difficulty.toUpperCase()}</div>
                </div>
                <div class="tip-content">${tip.content}</div>
            </div>
        `).join('');
    }
    
    filterTips(category, modal) {
        const tipsList = modal.querySelector('#tipsList');
        tipsList.innerHTML = this.renderTips(category);
    }
    
    setupContextTips() {
        // Show relevant tips based on current page
        const currentPage = window.location.pathname.split('/').pop();
        
        switch (currentPage) {
            case 'downloads.php':
                this.showInstallationTip();
                break;
            case 'profile.php':
                this.showProfileTip();
                break;
            case 'forum.php':
                this.showForumTip();
                break;
        }
    }
    
    showInstallationTip() {
        setTimeout(() => {
            this.showTooltip('💡 Pro Tip: Always run Affinity as administrator for best performance!', 'info');
        }, 5000);
    }
    
    showProfileTip() {
        setTimeout(() => {
            this.showTooltip('🏆 Complete your profile to unlock exclusive achievements!', 'info');
        }, 3000);
    }
    
    showForumTip() {
        setTimeout(() => {
            this.showTooltip('💬 Share your best configs to help the community grow!', 'info');
        }, 4000);
    }
    
    showTooltip(message, type = 'info') {
        if (window.showNotification) {
            window.showNotification(message, type, 8000);
        }
    }
}

// Easter Eggs System
class EasterEggsSystem {
    constructor() {
        this.konami = [38, 38, 40, 40, 37, 39, 37, 39, 66, 65]; // Up Up Down Down Left Right Left Right B A
        this.currentSequence = [];
        this.init();
    }
    
    init() {
        this.setupKonamiCode();
        this.setupClickEasterEggs();
        this.setupTimeBasedEasterEggs();
        console.log('🥚 Easter Eggs system initialized');
    }
    
    setupKonamiCode() {
        document.addEventListener('keydown', (e) => {
            this.currentSequence.push(e.keyCode);
            
            if (this.currentSequence.length > this.konami.length) {
                this.currentSequence.shift();
            }
            
            if (this.arraysEqual(this.currentSequence, this.konami)) {
                this.activateKonamiCode();
                this.currentSequence = [];
            }
        });
    }
    
    arraysEqual(a, b) {
        return a.length === b.length && a.every((val, i) => val === b[i]);
    }
    
    activateKonamiCode() {
        console.log('🎉 Konami Code activated!');
        
        // Special effects
        document.body.style.animation = 'rainbow-background 5s infinite';
        
        // Show special message
        this.showSpecialMessage('🎉 KONAMI CODE ACTIVATED! 🎉', 'You found the secret! Enjoy the rainbow mode!');
        
        // Add rainbow CSS
        const rainbowStyle = document.createElement('style');
        rainbowStyle.textContent = `
            @keyframes rainbow-background {
                0% { filter: hue-rotate(0deg); }
                100% { filter: hue-rotate(360deg); }
            }
        `;
        document.head.appendChild(rainbowStyle);
        
        // Remove after 10 seconds
        setTimeout(() => {
            document.body.style.animation = '';
            rainbowStyle.remove();
        }, 10000);
    }
    
    setupClickEasterEggs() {
        let clickCount = 0;
        
        document.addEventListener('click', (e) => {
            if (e.target.closest('.brand-icon')) {
                clickCount++;
                
                if (clickCount === 10) {
                    this.activateLogoEasterEgg();
                    clickCount = 0;
                }
            }
        });
    }
    
    activateLogoEasterEgg() {
        const brandIcon = document.querySelector('.brand-icon');
        if (brandIcon) {
            brandIcon.style.animation = 'spin 2s linear infinite';
            
            setTimeout(() => {
                brandIcon.style.animation = '';
                this.showSpecialMessage('🔄 Logo Spinner!', 'You made the logo spin! Nice clicking skills!');
            }, 2000);
        }
    }
    
    setupTimeBasedEasterEggs() {
        const hour = new Date().getHours();
        
        if (hour === 13 && new Date().getMinutes() === 37) {
            // 1337 time (1:37 PM)
            this.show1337EasterEgg();
        }
        
        if (hour >= 2 && hour <= 5) {
            // Late night gaming
            this.showLateNightMessage();
        }
    }
    
    show1337EasterEgg() {
        this.showSpecialMessage('🔥 1337 TIME! 🔥', 'It\'s 13:37! Time for some elite gaming!');
        
        // Add 1337 effects
        document.body.classList.add('leet-mode');
        
        const leetStyle = document.createElement('style');
        leetStyle.textContent = `
            .leet-mode {
                filter: hue-rotate(137deg) contrast(1.1);
            }
        `;
        document.head.appendChild(leetStyle);
        
        setTimeout(() => {
            document.body.classList.remove('leet-mode');
            leetStyle.remove();
        }, 13370); // 13.37 seconds
    }
    
    showLateNightMessage() {
        setTimeout(() => {
            this.showSpecialMessage('🌙 Night Owl!', 'Late night gaming session? Don\'t forget to take breaks!');
        }, 5000);
    }
    
    showSpecialMessage(title, message) {
        const modal = document.createElement('div');
        modal.className = 'easter-egg-modal';
        modal.innerHTML = `
            <div class="modal-backdrop" onclick="this.parentElement.remove()"></div>
            <div class="modal-content ultra-premium-card easter-egg-content">
                <div class="easter-egg-animation">
                    <div class="celebration-particles"></div>
                </div>
                <div class="easter-egg-header">
                    <h2>${title}</h2>
                </div>
                <div class="easter-egg-body">
                    <p>${message}</p>
                </div>
                <div class="easter-egg-footer">
                    <button class="btn btn-ultra-premium" onclick="this.closest('.easter-egg-modal').remove()">
                        <div class="btn-content">
                            <i class="fas fa-check me-2"></i>
                            <span>Awesome!</span>
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
            z-index: 10001;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        `;
        
        document.body.appendChild(modal);
        
        // Auto-remove after 8 seconds
        setTimeout(() => {
            if (modal.parentNode) {
                modal.remove();
            }
        }, 8000);
    }
}

// Add fun facts and easter eggs styles
const funFactsStyles = document.createElement('style');
funFactsStyles.textContent = `
    .fun-facts-container {
        animation: slideInRight 0.5s ease-out;
    }
    
    .fun-fact-card {
        padding: 1.5rem;
        border: 1px solid var(--accent-primary);
        box-shadow: var(--glow-primary);
    }
    
    .fact-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }
    
    .fact-icon {
        font-size: 1.5rem;
        color: var(--accent-warning);
        text-shadow: var(--glow-warning);
        margin-right: 0.5rem;
    }
    
    .fact-title {
        font-weight: 700;
        color: var(--text-primary);
        flex: 1;
    }
    
    .fact-close {
        background: none;
        border: none;
        font-size: 1.2rem;
        color: var(--text-muted);
        cursor: pointer;
        transition: color 0.3s ease;
    }
    
    .fact-close:hover {
        color: var(--accent-danger);
    }
    
    .fact-category {
        background: var(--accent-primary);
        color: white;
        padding: 0.2rem 0.5rem;
        border-radius: var(--radius-sm);
        font-size: 0.7rem;
        font-weight: 700;
        display: inline-block;
        margin-bottom: 0.5rem;
    }
    
    .fact-text {
        color: var(--text-secondary);
        line-height: 1.5;
        margin-bottom: 1rem;
    }
    
    .fact-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }
    
    .btn-fact-next {
        background: var(--accent-primary);
        border: none;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: var(--radius-md);
        font-size: 0.8rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-fact-next:hover {
        background: var(--accent-secondary);
        transform: translateY(-2px);
    }
    
    .fact-progress {
        flex: 1;
        height: 4px;
        background: var(--bg-tertiary);
        border-radius: var(--radius-sm);
        overflow: hidden;
    }
    
    .fact-progress .progress-bar {
        height: 100%;
        background: var(--gradient-primary);
        width: 0%;
        border-radius: inherit;
    }
    
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .tips-modal .modal-content {
        max-width: 600px;
        width: 100%;
        max-height: 80vh;
        overflow-y: auto;
    }
    
    .tips-categories {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }
    
    .btn-category {
        background: var(--bg-tertiary);
        border: 1px solid var(--border-primary);
        color: var(--text-secondary);
        padding: 0.5rem 1rem;
        border-radius: var(--radius-md);
        font-size: 0.8rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-category.active,
    .btn-category:hover {
        background: var(--accent-primary);
        color: white;
        border-color: var(--accent-primary);
    }
    
    .tip-item {
        background: var(--bg-glass);
        border: 1px solid var(--border-primary);
        border-radius: var(--radius-md);
        padding: 1rem;
        margin-bottom: 1rem;
    }
    
    .tip-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }
    
    .tip-title {
        font-weight: 700;
        color: var(--text-primary);
    }
    
    .tip-difficulty {
        padding: 0.1rem 0.5rem;
        border-radius: var(--radius-sm);
        font-size: 0.6rem;
        font-weight: 700;
        text-transform: uppercase;
    }
    
    .tip-difficulty.beginner {
        background: var(--accent-success);
        color: white;
    }
    
    .tip-difficulty.intermediate {
        background: var(--accent-warning);
        color: white;
    }
    
    .tip-difficulty.advanced {
        background: var(--accent-danger);
        color: white;
    }
    
    .tip-content {
        color: var(--text-secondary);
        line-height: 1.5;
    }
    
    .easter-egg-content {
        text-align: center;
        max-width: 400px;
        position: relative;
        overflow: hidden;
    }
    
    .celebration-particles {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: 
            radial-gradient(circle at 20% 20%, rgba(255, 215, 0, 0.3) 2px, transparent 2px),
            radial-gradient(circle at 80% 80%, rgba(255, 107, 53, 0.3) 2px, transparent 2px),
            radial-gradient(circle at 40% 60%, rgba(0, 255, 136, 0.3) 2px, transparent 2px);
        background-size: 50px 50px;
        animation: celebrate 2s ease-out infinite;
        pointer-events: none;
    }
    
    @keyframes celebrate {
        0% { transform: translateY(0) rotate(0deg); opacity: 1; }
        100% { transform: translateY(-100px) rotate(360deg); opacity: 0; }
    }
    
    .easter-egg-header h2 {
        background: var(--gradient-primary);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 1rem;
        animation: bounce 1s ease-out infinite;
    }
    
    .easter-egg-body p {
        color: var(--text-secondary);
        font-size: 1.1rem;
        margin-bottom: 1.5rem;
    }
`;
document.head.appendChild(funFactsStyles);

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    console.log('🚀 Initializing Fun Facts & Easter Eggs...');
    
    window.funFacts = new UltraPremiumFunFacts();
    window.cs2Tips = new CS2TipsSystem();
    window.easterEggs = new EasterEggsSystem();
    
    console.log('✅ Fun Facts & Easter Eggs fully initialized!');
});

console.log('🎯 Fun Facts system loaded successfully!');