<?php
// /error_handler.php

// Define the log file path.
// IMPORTANT: You must create the 'logs' directory in your project root
// and make it writable by the web server (e.g., chmod 775).
define('LOG_FILE', __DIR__ . '/logs/app_error.log');

// Set error reporting to catch all errors
error_reporting(E_ALL);

// Set display_errors based on the ENVIRONMENT constant from config.php
if (defined('ENVIRONMENT') && ENVIRONMENT === 'production') {
    ini_set('display_errors', 0); // Don't show errors to users
    ini_set('log_errors', 1);     // Log errors
    ini_set('error_log', LOG_FILE); // Specify log file
} else {
    // In 'development' mode
    ini_set('display_errors', 1); // Show errors
    ini_set('log_errors', 1);     // Log errors
    ini_set('error_log', LOG_FILE); // Specify log file
}

/**
 * Custom error logging function.
 * @param string $message The error message.
 * @param string $file The file where the error occurred.
 * @param int $line The line number of the error.
 */
function log_error($message, $file, $line) {
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] | $message | File: $file | Line: $line" . PHP_EOL;
    
    // Ensure log directory exists
    $log_dir = dirname(LOG_FILE);
    if (!is_dir($log_dir)) {
        // Attempt to create it
        if (!mkdir($log_dir, 0755, true) && !is_dir($log_dir)) {
             // Fallback if creation fails: log to default PHP error log
             error_log("Failed to create log directory: $log_dir. Logging to default.");
             error_log($log_message);
             return;
        }
    }

    // Append to the log file
    file_put_contents(LOG_FILE, $log_message, FILE_APPEND);
}

/**
 * Custom error handler.
 * Converts PHP errors (like warnings, notices) into ErrorExceptions.
 */
function custom_error_handler($errno, $errstr, $errfile, $errline) {
    // This error code is not included in error_reporting
    if (!(error_reporting() & $errno)) {
        return false;
    }

    // Don't throw exceptions for notices or deprecation warnings, but log them.
    if ($errno == E_NOTICE || $errno == E_USER_NOTICE || $errno == E_DEPRECATED || $errno == E_USER_DEPRECATED) {
        if (ini_get('log_errors')) {
            log_error("Notice: $errstr", $errfile, $errline);
        }
        return true; // Continue script execution
    }

    // For all other errors, throw an exception
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}

/**
 * Custom exception handler.
 * Catches all uncaught exceptions.
 */
function custom_exception_handler($exception) {
    // Log the exception
    log_error(
        "Uncaught Exception: " . $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine()
    );

    // Send a 500 Internal Server Error header
    http_response_code(500);

    // Display a generic error page in production
    if (defined('ENVIRONMENT') && ENVIRONMENT === 'production') {
        echo "<h1>500 - Internal Server Error</h1>";
        echo "<p>We're sorry, but something went wrong. Our team has been notified.</p>";
        echo "<p>Please try again later.</p>";
        // In a real app, you would load a styled error page:
        // if (file_exists('pages/500.php')) {
        //     require 'pages/500.php';
        // }
    } else {
        // Display detailed error in development
        echo "<div style='font-family: monospace; border: 2px solid red; padding: 15px; background: #fff8f8;'>";
        echo "<h1>Fatal Error</h1>";
        echo "<pre>";
        echo "<strong>Message:</strong> " . htmlspecialchars($exception->getMessage()) . "\n\n";
        echo "<strong>File:</strong> " . $exception->getFile() . "\n";
        echo "<strong>Line:</strong> " . $exception->getLine() . "\n\n";
        echo "<strong>Stack Trace:</strong>\n" . htmlspecialchars($exception->getTraceAsString());
        echo "</pre>";
        echo "</div>";
    }
    exit;
}

/**
 * Shutdown function.
 * Catches fatal errors that aren't caught by the error handler (e.g., parse errors).
 */
function custom_shutdown_handler() {
    $error = error_get_last();
    // Check for fatal errors
    if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
        // Create a pseudo-exception to pass to our exception handler
        $exception = new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']);
        custom_exception_handler($exception);
    }
}

// Register the handlers
set_error_handler('custom_error_handler');
set_exception_handler('custom_exception_handler');
register_shutdown_function('custom_shutdown_handler');

?>