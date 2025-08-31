<?php
/**
 * Central Initialization File
 * This file handles all include paths and initialization
 */

// Prevent direct access
if (!defined('SECURE_ACCESS')) {
    // Intelligent path detection for InfinityFree
    $script_path = $_SERVER['SCRIPT_NAME'];
    $script_dir = dirname($script_path);
    
    // Determine the correct path to config.php
    if (strpos($script_dir, '/affinity-forum') !== false) {
        // We're in the affinity-forum directory
        $config_path = __DIR__ . '/../config.php';
    } else {
        // We're in a subdirectory, try multiple paths
        $config_paths = [
            __DIR__ . '/../config.php',           // From includes/
            __DIR__ . '/../../config.php',        // From admin/includes/
            dirname(__DIR__) . '/config.php',     // Alternative method
            'config.php',                         // From root
            '../config.php',                      // From subdirectories
            '../../config.php'                    // From deeper subdirectories
        ];
        
        $config_path = null;
        foreach ($config_paths as $path) {
            if (file_exists($path)) {
                $config_path = $path;
                break;
            }
        }
        
        if (!$config_path) {
            die('Could not locate config.php. Please check file paths.');
        }
    }
    
    // Load the config file
    if (file_exists($config_path)) {
        require_once $config_path;
    } else {
        die("Config file not found at: $config_path");
    }
}

// Initialize database connection if not already done
if (!isset($pdo) || $pdo === null) {
    try {
        $pdo = getDBConnection();
    } catch (Exception $e) {
        error_log("Database connection failed: " . $e->getMessage());
        die('Database connection failed. Please check your configuration.');
    }
}
?>
