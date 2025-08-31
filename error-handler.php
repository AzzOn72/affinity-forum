<?php
/**
 * Simple Error Handler for Affinity Forum
 * Catches critical errors and displays them in a user-friendly way
 */

// Set error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Custom error handler
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    $errorType = '';
    switch ($errno) {
        case E_ERROR:
            $errorType = 'Fatal Error';
            break;
        case E_WARNING:
            $errorType = 'Warning';
            break;
        case E_PARSE:
            $errorType = 'Parse Error';
            break;
        case E_NOTICE:
            $errorType = 'Notice';
            break;
        default:
            $errorType = 'Unknown Error';
    }
    
    // Log error
    error_log("[$errorType] $errstr in $errfile on line $errline");
    
    // Display error in development
    if (ini_get('display_errors')) {
        echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; margin: 10px; border-radius: 5px;'>";
        echo "<strong>$errorType:</strong> $errstr<br>";
        echo "<small>File: $errfile (Line: $errline)</small>";
        echo "</div>";
    }
    
    return true;
}

// Custom exception handler
function customExceptionHandler($exception) {
    error_log("Uncaught Exception: " . $exception->getMessage());
    
    if (ini_get('display_errors')) {
        echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; margin: 10px; border-radius: 5px;'>";
        echo "<strong>Uncaught Exception:</strong> " . $exception->getMessage() . "<br>";
        echo "<small>File: " . $exception->getFile() . " (Line: " . $exception->getLine() . ")</small>";
        echo "</div>";
    }
}

// Set custom handlers
set_error_handler("customErrorHandler");
set_exception_handler("customExceptionHandler");

// Fatal error handler
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        error_log("Fatal Error: " . $error['message'] . " in " . $error['file'] . " on line " . $error['line']);
        
        if (ini_get('display_errors')) {
            echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; margin: 10px; border-radius: 5px;'>";
            echo "<strong>Fatal Error:</strong> " . $error['message'] . "<br>";
            echo "<small>File: " . $error['file'] . " (Line: " . $error['line'] . ")</small>";
            echo "</div>";
        }
    }
});

echo "<!-- Error handler loaded successfully -->";
?>
