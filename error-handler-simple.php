<?php
/**
 * Simple Error Handler for Affinity Forum
 * Minimal version to avoid output issues
 */

// Set error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Custom error handler
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    error_log("[$errno] $errstr in $errfile on line $errline");
    return true;
}

// Custom exception handler
function customExceptionHandler($exception) {
    error_log("Uncaught Exception: " . $exception->getMessage());
}

// Set custom handlers
set_error_handler("customErrorHandler");
set_exception_handler("customExceptionHandler");

// Fatal error handler
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        error_log("Fatal Error: " . $error['message'] . " in " . $error['file'] . " on line " . $error['line']);
    }
});
?>
