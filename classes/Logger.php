<?php
// /classes/Logger.php

// Requires LOG_FILE to be defined, which happens in error_handler.php
// Ensure config.php (which includes error_handler.php) is loaded first.
if (!defined('LOG_FILE')) {
    require_once __DIR__ . '/../config.php';
}

class Logger {

    private static $log_file = LOG_FILE;

    /**
     * Writes a message to the log file.
     * @param string $level e.g., 'INFO', 'ERROR', 'DEBUG'
     * @param string $message The log message.
     */
    public static function log($level, $message) {
        try {
            $timestamp = date('Y-m-d H:i:s');
            $log_message = "[$timestamp] | " . strtoupper($level) . " | $message" . PHP_EOL;
            
            // Append to the log file
            file_put_contents(self::$log_file, $log_message, FILE_APPEND);
        
        } catch (Exception $e) {
            // Failsafe in case log file isn't writable
            error_log("Failed to write to custom log file: $message");
        }
    }

    /**
     * Logs an informational message.
     * @param string $message
     */
    public static function info($message) {
        self::log('INFO', $message);
    }

    /**
     * Logs a warning message.
     * @param string $message
     */
    public static function warning($message) {
        self::log('WARNING', $message);
    }

    /**
     * Logs an error message.
     * @param string $message The error message.
     * @param string $file (Optional) File where error occurred.
     * @param int $line (Optional) Line number.
     */
    public static function error($message, $file = '', $line = '') {
        if ($file) $message .= " | File: $file";
        if ($line) $message .= " | Line: $line";
        self::log('ERROR', $message);
    }
}
?>