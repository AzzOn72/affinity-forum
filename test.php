<?php
/**
 * Simple Test File for Affinity Forum
 * Use this to verify everything is working before going live
 */

echo "<!DOCTYPE html>";
echo "<html lang='en'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>Affinity Forum - System Test</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }";
echo ".test-section { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }";
echo ".success { border-left: 4px solid #28a745; }";
echo ".error { border-left: 4px solid #dc3545; }";
echo ".warning { border-left: 4px solid #ffc107; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<h1>🚀 Affinity Forum - System Test</h1>";

// Test 1: PHP Version
echo "<div class='test-section success'>";
echo "<h3>✅ PHP Version</h3>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Required: 7.4+</p>";
echo "</div>";

// Test 2: Database Connection
echo "<div class='test-section'>";
echo "<h3>🔌 Database Connection</h3>";
try {
    require_once 'config.php';
    $pdo = getDBConnection();
    echo "<p class='success'>✅ Database connection successful!</p>";
    
    // Test basic query
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "<p>Total users: " . $result['count'] . "</p>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Database connection failed: " . $e->getMessage() . "</p>";
}
echo "</div>";

// Test 3: File Permissions
echo "<div class='test-section'>";
echo "<h3>📁 File Permissions</h3>";
$uploadDirs = ['uploads', 'uploads/avatars', 'uploads/attachments'];
foreach ($uploadDirs as $dir) {
    if (is_dir($dir)) {
        if (is_writable($dir)) {
            echo "<p class='success'>✅ $dir - Writable</p>";
        } else {
            echo "<p class='warning'>⚠️ $dir - Not writable</p>";
        }
    } else {
        echo "<p class='error'>❌ $dir - Directory not found</p>";
    }
}
echo "</div>";

// Test 4: Required Extensions
echo "<div class='test-section'>";
echo "<h3>🔧 PHP Extensions</h3>";
$requiredExtensions = ['pdo', 'pdo_mysql', 'mbstring', 'json', 'session'];
foreach ($requiredExtensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<p class='success'>✅ $ext - Loaded</p>";
    } else {
        echo "<p class='error'>❌ $ext - Not loaded</p>";
    }
}
echo "</div>";

// Test 5: Session Support
echo "<div class='test-section'>";
echo "<h3>🔐 Session Support</h3>";
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "<p class='success'>✅ Sessions are active</p>";
} else {
    echo "<p class='warning'>⚠️ Sessions not started</p>";
}
echo "</div>";

// Test 6: Theme System
echo "<div class='test-section'>";
echo "<h3>🎨 Theme System</h3>";
if (file_exists('css/themes.css')) {
    echo "<p class='success'>✅ Themes CSS file exists</p>";
} else {
    echo "<p class='error'>❌ Themes CSS file missing</p>";
}
echo "</div>";

// Test 7: Admin Panel
echo "<div class='test-section'>";
echo "<h3>⚙️ Admin Panel</h3>";
if (file_exists('admin/index.php')) {
    echo "<p class='success'>✅ Admin panel exists</p>";
} else {
    echo "<p class='error'>❌ Admin panel missing</p>";
}
echo "</div>";

echo "<div class='test-section success'>";
echo "<h3>🎯 Next Steps</h3>";
echo "<ol>";
echo "<li>If all tests pass, your forum is ready!</li>";
echo "<li>Run <code>update-database.php</code> to add missing fields</li>";
echo "<li>Test user registration and login</li>";
echo "<li>Test creating threads and posts</li>";
echo "<li>Test admin panel access</li>";
echo "</ol>";
echo "</div>";

echo "<div class='test-section'>";
echo "<h3>🔗 Quick Links</h3>";
echo "<p><a href='index.php'>🏠 Homepage</a></p>";
echo "<p><a href='register.php'>📝 Register</a></p>";
echo "<p><a href='login.php'>🔑 Login</a></p>";
echo "<p><a href='admin/index.php'>⚙️ Admin Panel</a></p>";
echo "<p><a href='update-database.php'>🔄 Update Database</a></p>";
echo "</div>";

echo "</body>";
echo "</html>";
?>
