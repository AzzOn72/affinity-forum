# Affinity Forum - Counter-Strike 2 Cheat Community

A comprehensive, modern forum system designed specifically for the Counter-Strike 2 cheat community. Built with PHP, MySQL, and modern web technologies, featuring a sleek design, advanced user management, and robust security features.

## 🌟 Features

### User Authentication & Management
- **Secure Registration & Login**: Password hashing, CSRF protection, and rate limiting
- **Password Recovery**: Email-based password reset system
- **Remember Me**: Secure token-based persistent login
- **User Profiles**: Customizable profiles with avatars, bio, and location
- **Role-Based Access**: User, Moderator, and Admin roles with granular permissions

### Forum Functionality
- **Categories & Subforums**: Organized forum structure with customizable categories
- **Threads & Posts**: Rich text support with quote and reply functionality
- **Search System**: Advanced search with filters and real-time results
- **Moderation Tools**: Edit, delete, pin, and lock threads/posts
- **Like System**: User reactions and reputation building

### Advanced Features
- **🎨 Advanced Theme System v3.0**: 4 built-in themes + custom theme creation with live preview
- **✨ Interactive Particle Effects**: 6 different particle types with physics and mouse interaction
- **🎵 Enhanced Audio System**: Multiple sound effects, volume control, and audio visualization
- **🧠 AI-Powered Smart Features**: Time-based suggestions and automatic optimization
- **♿ Comprehensive Accessibility**: Voice control, color blind support, and enhanced navigation
- **📊 Real-Time Performance Monitoring**: FPS tracking, memory usage, and automatic optimization
- **🎮 Gaming Mode**: Optimized settings for gaming with CS2 theme and enhanced effects
- **💾 Advanced Settings Management**: Export/import, auto-save, and performance reports
- **📱 Responsive Design**: Optimized for desktop, tablet, and mobile devices
- **🔊 Notifications**: Real-time notifications for replies, mentions, and likes
- **💬 Private Messaging**: Secure user-to-user communication
- **📁 File Downloads**: Admin-only file uploads with categorization

### Security Features
- **CSRF Protection**: Cross-site request forgery prevention
- **SQL Injection Prevention**: Prepared statements and input validation
- **XSS Protection**: Output sanitization and content security headers
- **Rate Limiting**: Login attempt throttling and IP blocking
- **Session Security**: Secure session management and timeout handling

## 🎨 Advanced Theme System v3.0

### ✨ What's New
- **6 Particle Effects**: Standard, Fireworks, Snow, Rain, Stars, and Bubbles with physics simulation
- **Enhanced Audio**: Multiple sound effects with volume control and real-time audio visualization
- **AI-Powered Features**: Smart theme suggestions based on time of day and usage patterns
- **Voice Control**: Control themes and settings with voice commands (Chrome/Edge)
- **Custom Theme Creator**: Live color picker with instant preview and sharing
- **Performance Dashboard**: Real-time FPS, memory usage, and optimization tools
- **Advanced Accessibility**: Color blind support, enhanced screen reader compatibility
- **Interactive Effects**: Click anywhere for particle explosions and hover sound effects

### 🎯 Quick Start
1. Visit `theme-control-panel.php` for the master control center
2. Try `test-themes.php` for comprehensive testing
3. Explore `features-showcase.php` for a guided tour
4. Use keyboard shortcuts (Ctrl+1-4 for themes, Ctrl+T for cycling)
5. Enable voice control and say "switch to dark theme"

### 🎮 Gaming Mode
Activate gaming mode for the ultimate CS2 experience:
- Switches to CS2 theme automatically
- Enables fireworks particle effects
- Increases audio volume to 80%
- Shows audio visualizer
- Optimizes performance for gaming

## 🚀 Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher (or MariaDB 10.2+)
- Apache web server with mod_rewrite enabled
- Composer (for dependency management)

### Step 1: Download & Setup
```bash
# Clone or download the project
git clone https://github.com/yourusername/affinity-forum.git
cd affinity-forum

# Set proper permissions
chmod 755 uploads/
chmod 644 config.php
```

### Step 2: Database Setup
1. Create a new MySQL database
2. Import the `database.sql` file
3. Update `config.php` with your database credentials

### Step 3: Configuration
Edit `config.php` and update:
```php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'affinity_forum');

// Site Configuration
define('SITE_URL', 'http://your-domain.com');
define('ADMIN_EMAIL', 'admin@your-domain.com');
```

### Step 4: Web Server Configuration
#### Apache (.htaccess)
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

#### Nginx
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### Step 5: First Admin User
After installation, create your first admin user:
```sql
INSERT INTO users (username, email, password_hash, role, email_verified, created_at) 
VALUES ('admin', 'admin@yourdomain.com', '$2y$12$...', 'admin', 1, NOW());
```

## 📁 File Structure

```
affinity-forum/
├── admin/                 # Admin panel files
├── ajax/                  # AJAX handlers
├── css/                   # Stylesheets
│   ├── style.css         # Main styles
│   └── themes.css        # Theme definitions
├── includes/              # PHP includes
│   ├── header.php        # Site header
│   ├── footer.php        # Site footer
│   └── sidebar.php       # Sidebar content
├── js/                    # JavaScript files
│   ├── main.js           # Main functionality
│   └── themes.js         # Theme management
├── uploads/               # File uploads
├── config.php            # Configuration file
├── database.sql          # Database schema
├── index.php             # Home page
├── login.php             # Login page
├── register.php          # Registration page
├── downloads.php         # Downloads page
└── README.md             # This file
```

## 🎨 Themes

### Available Themes
1. **Dark Theme**: Modern dark interface with blue accents
2. **Light Theme**: Clean light interface with blue accents  
3. **CS2 Theme**: Counter-Strike 2 inspired with orange and blue

### Customizing Themes
Themes can be customized by editing `css/themes.css`:
```css
body[data-theme="custom"] {
    --primary-color: #your-color;
    --bg-primary: #your-bg-color;
    --text-primary: #your-text-color;
}
```

## 🔧 Configuration Options

### Security Settings
```php
define('HASH_COST', 12);              // Password hashing cost
define('SESSION_TIMEOUT', 3600);       // Session timeout (seconds)
define('MAX_LOGIN_ATTEMPTS', 5);       // Max login attempts
define('LOGIN_TIMEOUT', 900);          // Login timeout (seconds)
```

### File Upload Settings
```php
define('MAX_FILE_SIZE', 10485760);     // Max file size (10MB)
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'zip', 'rar']);
define('UPLOAD_PATH', 'uploads/');     // Upload directory
```

## 👥 User Roles & Permissions

### User (Default)
- Create threads and posts
- Edit own posts
- Send private messages
- Upload avatars
- Like posts

### Moderator
- All user permissions
- Moderate posts and threads
- Pin/unpin threads
- Lock/unlock threads
- Manage user warnings

### Administrator
- All moderator permissions
- Manage users and roles
- Upload files to downloads
- Access admin panel
- Site configuration

## 🛡️ Security Features

### Input Validation
- All user inputs are sanitized and validated
- SQL injection prevention through prepared statements
- XSS protection with output encoding
- File upload validation and scanning

### Session Management
- Secure session handling
- Automatic timeout and cleanup
- Remember me functionality with secure tokens
- IP-based session validation

### Rate Limiting
- Login attempt throttling
- IP-based blocking for abuse prevention
- Configurable timeout periods

## 📱 Mobile & Responsiveness

- **Mobile-First Design**: Optimized for mobile devices
- **Responsive Layout**: Adapts to all screen sizes
- **Touch-Friendly**: Optimized for touch interfaces
- **Progressive Web App**: Can be installed as a mobile app

## 🔌 Extensions & Customization

### Adding New Features
The modular structure makes it easy to add new features:
1. Create new PHP files in appropriate directories
2. Add database tables if needed
3. Update navigation and permissions
4. Style with CSS and add JavaScript functionality

### API Integration
The system supports external API integration:
- RESTful endpoints for mobile apps
- Webhook support for external services
- OAuth integration capabilities

## 🚀 Performance Optimization

### Caching
- Database query optimization
- Static asset caching
- Session optimization
- Image optimization

### Database Optimization
- Indexed queries for fast performance
- Connection pooling
- Query result caching
- Optimized table structure

## 🐛 Troubleshooting

### Common Issues

#### Database Connection Error
- Verify database credentials in `config.php`
- Ensure MySQL service is running
- Check database permissions

#### File Upload Issues
- Verify `uploads/` directory permissions (755)
- Check `MAX_FILE_SIZE` in `config.php`
- Ensure `ALLOWED_EXTENSIONS` includes your file type

#### Theme Not Working
- Clear browser cache
- Check JavaScript console for errors
- Verify `js/themes.js` is loaded

#### Login Issues
- Check `login_attempts` table for IP blocks
- Verify session configuration
- Check PHP error logs

### Debug Mode
Enable debug mode in `config.php`:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## 📊 Database Schema

The system uses a normalized database structure with the following main tables:
- `users` - User accounts and profiles
- `categories` - Forum categories
- `subforums` - Forum subforums
- `threads` - Forum threads
- `posts` - Forum posts
- `downloads` - File downloads
- `private_messages` - Private messaging
- `notifications` - User notifications

## 🔄 Updates & Maintenance

### Regular Maintenance
- Monitor error logs
- Clean up old sessions
- Optimize database tables
- Update security patches

### Backup Strategy
- Regular database backups
- File system backups
- Configuration backups
- Version control for code changes

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🤝 Contributing

We welcome contributions! Please:
1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📞 Support

For support and questions:
- Create an issue on GitHub
- Check the documentation
- Review the troubleshooting section
- Contact the development team

## 🙏 Acknowledgments

- Bootstrap for the responsive framework
- Font Awesome for icons
- jQuery for JavaScript functionality
- The Counter-Strike 2 community for inspiration

---

**Affinity Forum** - Building the future of gaming communities, one post at a time.
