<?php
// /functions.php (in the project root)

// Ensure config.php (which defines BASE_URL and starts session) is loaded before using these functions.

/**
 * Redirects to a specified URL relative to the BASE_URL.
 * Stops script execution after sending the header.
 * @param string $url The *relative* path from the base URL (e.g., 'auth/login.php', 'dashboard/index.php').
 */
function redirect($url) {
    if (!defined('BASE_URL') || empty(BASE_URL) || substr(BASE_URL, -1) !== '/') {
        log_error("CRITICAL: BASE_URL is not defined or is invalid in config.php. Attempted redirect to '$url'.", __FILE__, __LINE__);
        die("Site configuration error prevents redirection. Please contact the administrator.");
    }
    $relative_url = ltrim($url, '/');
    $location = BASE_URL . $relative_url;
    // Prevent caching of the redirect instruction itself
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    // Perform redirect
    header("Location: " . $location, true, 302);
    exit; // Stop script execution
}

/**
 * Sanitizes user input for safe HTML output (prevents XSS).
 * @param string|null $data The input data.
 * @return string The sanitized data.
 */
function sanitize_input($data) {
    if ($data === null) return '';
    $data = trim($data);
    $data = htmlspecialchars($data, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    return $data;
}

/**
 * Checks if a user is logged in based on session variable.
 * **VERIFY THIS FUNCTION**
 * @return bool True if logged in, false otherwise.
 */
function is_logged_in() {
    // Check BOTH if the session variable is set AND if it's not empty/zero.
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// --- Keep your other helper functions below ---
// (check_role, require_role, get_session_user, format_date, display_flash_message, etc.)

/**
 * Checks if the logged-in user has a specific role.
 * @param string $role The role to check.
 * @return bool True if the user has the role, false otherwise.
 */
function check_role($role) {
    return is_logged_in() && isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

/**
 * Restricts access based on role(s). Redirects if unauthorized.
 * Assumes check_auth.php (which calls is_logged_in) has run first.
 * @param array $roles Array of allowed roles.
 */
function require_role($roles = []) {
    if (!is_logged_in()) {
         $_SESSION['error_message'] = "Authentication required.";
         redirect('auth/login.php'); // Redirect to login if somehow not logged in
    }
    if (!empty($roles) && (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $roles))) {
        $_SESSION['error_message'] = "Permission Denied: You do not have access to this page.";
        redirect('dashboard/index.php'); // Redirect to their default dashboard
    }
}


/**
 * Gets essential data for the currently logged-in user from the session.
 * @return array|null User data array or null if not logged in.
 */
function get_session_user() {
    if (!is_logged_in()) {
        return null;
    }
    return [
        'id' => $_SESSION['user_id'],
        'role' => $_SESSION['role'] ?? null,
        'email' => $_SESSION['email'] ?? null,
        'first_name' => $_SESSION['first_name'] ?? 'User'
    ];
}

/**
 * Formats a date/timestamp using DateTime object.
 * @param string|null $date The date string.
 * @param string $format The desired output format.
 * @return string The formatted date or 'N/A'.
 */
function format_date($date, $format = 'M d, Y, g:i A') {
     if (empty($date) || $date === '0000-00-00 00:00:00' || $date === '0000-00-00') {
        return 'N/A';
    }
    try {
        $dateTime = new DateTime($date);
        return $dateTime->format($format);
    } catch (Exception $e) {
        log_error("Invalid date format encountered: $date - Error: " . $e->getMessage(), __FILE__, __LINE__);
        return 'Invalid Date';
    }
}

/**
 * Displays and clears a session-based flash message.
 * @param string $key The session key.
 * @param string $class The Bootstrap alert CSS class.
 */
function display_flash_message($key, $class = 'alert-info') {
    if (isset($_SESSION[$key])) {
        echo '<div class="alert ' . htmlspecialchars($class) . ' alert-dismissible fade show mt-3" role="alert">';
        echo htmlspecialchars($_SESSION[$key]);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
        unset($_SESSION[$key]);
    }
}
?>