# 🎨 Unified Theme System

A clean, modern theme system for the Affinity Forum with advanced features and accessibility support.

## ✨ Features

### 🎨 Themes
- **Light Theme** - Clean and modern light interface
- **Dark Theme** - Sleek and elegant dark interface  
- **CS2 Gaming** - Gaming-inspired Counter-Strike 2 style
- **Premium Luxury** - Exclusive luxury premium interface

### 📱 Device Optimization
- **Desktop** - Full desktop experience
- **Tablet** - Optimized for tablet devices
- **Mobile** - Mobile-optimized interface

### ♿ Accessibility
- High contrast mode
- Reduced motion support
- Adjustable font sizes
- Screen reader support
- Keyboard shortcuts

### 🚀 Advanced Features
- Particle effects system
- Audio feedback
- Performance monitoring
- Settings export/import
- Auto-save and recovery

## 🛠️ Usage

### Basic Theme Switching
```html
<button class="theme-btn" data-theme="dark">🌙 Dark</button>
<button class="theme-btn" data-theme="light">☀️ Light</button>
<button class="theme-btn" data-theme="cs2">🎮 CS2</button>
<button class="theme-btn" data-theme="premium">💎 Premium</button>
```

### Device Selection
```html
<button class="device-option" data-device="desktop">🖥️ Desktop</button>
<button class="device-option" data-device="tablet">📱 Tablet</button>
<button class="device-option" data-device="mobile">📱 Mobile</button>
```

### Accessibility Controls
```html
<button onclick="toggleHighContrast()">High Contrast</button>
<button onclick="toggleReducedMotion()">Reduced Motion</button>
<button onclick="toggleFontSize()">Font Size</button>
<button onclick="toggleParticles()">Particles</button>
<button onclick="toggleAudio()">Audio</button>
```

## ⌨️ Keyboard Shortcuts

| Shortcut | Action |
|----------|--------|
| `Ctrl + 1` | Switch to Light theme |
| `Ctrl + 2` | Switch to Dark theme |
| `Ctrl + 3` | Switch to CS2 theme |
| `Ctrl + 4` | Switch to Premium theme |
| `Ctrl + D` | Select Desktop device |
| `Ctrl + T` | Select Tablet device |
| `Ctrl + M` | Select Mobile device |
| `Ctrl + H` | Toggle High Contrast |
| `Ctrl + R` | Toggle Reduced Motion |
| `Ctrl + F` | Toggle Font Size |
| `Ctrl + P` | Toggle Particles |
| `Ctrl + A` | Toggle Audio |
| `Ctrl + S` | Export Settings |
| `Ctrl + I` | Import Settings |
| `Ctrl + R` | Reset to Defaults |
| `F1` | Show Shortcuts Help |

## 🎯 JavaScript API

### Global Functions
```javascript
// Theme switching
switchTheme('dark');

// Device selection
selectDevice('tablet');

// Accessibility toggles
toggleHighContrast();
toggleReducedMotion();
toggleFontSize();
toggleParticles();
toggleAudio();

// Settings management
exportSettings();
importSettings(file);
resetToDefaults();
```

### Theme Manager Instance
```javascript
// Access the theme manager
const themeManager = window.themeManager;

// Get current theme
const currentTheme = themeManager.getCurrentTheme();

// Get current device
const currentDevice = themeManager.getCurrentDevice();

// Check if theme is active
const isDark = themeManager.isThemeActive('dark');

// Get performance report
const report = themeManager.getPerformanceReport();
```

## 🎨 CSS Classes

### Theme Classes
```css
.theme-light    /* Light theme styles */
.theme-dark     /* Dark theme styles */
.theme-cs2      /* CS2 gaming theme styles */
.theme-premium  /* Premium luxury theme styles */
```

### Device Classes
```css
[data-device="desktop"]  /* Desktop-specific styles */
[data-device="tablet"]   /* Tablet-specific styles */
[data-device="mobile"]   /* Mobile-specific styles */
```

### Component Classes
```css
.card              /* Basic card component */
.premium-card      /* Premium card component */
.btn               /* Button component */
.btn-primary       /* Primary button */
.btn-secondary     /* Secondary button */
.btn-accent        /* Accent button */
```

## 🔧 Configuration

### CSS Custom Properties
The theme system uses CSS custom properties for easy customization:

```css
:root {
    --primary-color: #007bff;
    --secondary-color: #6c757d;
    --accent-color: #28a745;
    --text-color: #212529;
    --bg-color: #ffffff;
    --border-color: #dee2e6;
    --card-bg: #f8f9fa;
    --hover-bg: #e9ecef;
    --shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
```

### Device Variables
```css
[data-device="desktop"] {
    --container-width: 1200px;
    --font-size: 16px;
    --spacing: 1.5rem;
    --border-radius: 10px;
}
```

## 📱 Responsive Design

The theme system automatically adapts to different screen sizes:

- **Desktop** (≥1200px): Full layout with maximum features
- **Tablet** (≥768px): Optimized layout with reduced features
- **Mobile** (<768px): Mobile-first layout with essential features only

## ♿ Accessibility Features

### High Contrast Mode
Increases contrast for better readability in bright environments.

### Reduced Motion
Respects user preferences for reduced motion and animations.

### Font Size Control
Allows users to adjust font size from 12px to 24px.

### Screen Reader Support
Includes ARIA labels and screen reader announcements.

### Keyboard Navigation
Full keyboard support for all theme controls.

## 🚀 Performance Features

### Automatic Optimization
- Reduces particle count on low-end devices
- Disables animations on mobile devices
- Respects user motion preferences
- Optimizes for hardware capabilities

### Performance Monitoring
- Tracks theme switch performance
- Monitors device switch performance
- Counts notification usage
- Provides performance reports

## 🔄 Settings Management

### Auto-Save
Settings are automatically saved every 5 minutes.

### Export/Import
Users can export their settings to JSON files and import them later.

### Recovery
Settings are automatically recovered if the page is refreshed.

## 🧪 Testing

Use the `test-themes.php` page to test all theme system features:

1. Theme switching
2. Device selection
3. Accessibility controls
4. Settings management
5. Performance monitoring

## 📁 File Structure

```
js/
├── themes.js          # Main theme manager
└── main.js           # General JavaScript

css/
├── style.css         # Base styles
└── themes.css        # Theme system styles

includes/
├── header.php        # HTML head with theme CSS
└── footer.php        # HTML footer with theme JS
```

## 🐛 Troubleshooting

### Common Issues

1. **Themes not switching**: Check browser console for JavaScript errors
2. **Particles not showing**: Ensure canvas is supported and particles are enabled
3. **Audio not working**: Check if audio context is supported and enabled
4. **Settings not saving**: Check localStorage permissions

### Debug Mode

Enable debug mode by opening browser console and checking for:
- Theme manager initialization messages
- Error messages
- Performance metrics

## 🔮 Future Enhancements

- Custom theme creation
- Theme marketplace
- Advanced particle effects
- More accessibility options
- Performance analytics dashboard
- Theme presets system

## 📄 License

This theme system is part of the Affinity Forum project.