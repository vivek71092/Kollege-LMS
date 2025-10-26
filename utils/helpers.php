<?php
// /utils/helpers.php

/**
 * Generates a random string (useful for tokens).
 * @param int $length The desired length of the string.
 * @return string The random string.
 */
function generate_random_token($length = 32) {
    try {
        return bin2hex(random_bytes($length / 2));
    } catch (Exception $e) {
        // Fallback for environments where random_bytes might fail
        return substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/62))), 1, $length);
    }
}

/**
 * Truncates text to a specified length and adds an ellipsis.
 * (This could be moved from the root functions.php if preferred)
 * @param string $text The text to truncate.
 * @param int $length The maximum length.
 * @return string The truncated text.
 */
function truncate_text($text, $length = 100) {
    if (strlen($text) > $length) {
        $text = substr($text, 0, $length) . '...';
    }
    return $text;
}

/**
 * Escapes output to prevent XSS (useful if not using a template engine).
 * @param string|null $string The string to escape.
 * @return string The escaped string.
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

// Add more general helper functions here...

?>