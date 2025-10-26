<?php
// /utils/validators.php

/**
 * Validates an email address format.
 * @param string $email
 * @return bool True if valid, false otherwise.
 */
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validates password strength (example: min 8 chars, 1 number, 1 letter).
 * @param string $password
 * @return bool True if strong enough, false otherwise.
 */
function is_strong_password($password) {
    if (strlen($password) < 8) {
        return false;
    }
    // Check for at least one letter and one number
    if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        return false;
    }
    return true;
}

/**
 * Validates a phone number format (simple example).
 * Allows digits, spaces, hyphens, parentheses, plus sign.
 * @param string $phone
 * @return bool True if potentially valid format, false otherwise.
 */
function is_valid_phone($phone) {
    // Allows + ( ) - space and digits, requires at least 7 digits
    return preg_match('/^[+\d\s\(\)-]{7,}$/', $phone);
}

// Add more specific validators as needed (e.g., is_valid_date, is_valid_url)

?>