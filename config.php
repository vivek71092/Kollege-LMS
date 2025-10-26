<?php
// /config.php

// Attempt to load Composer's autoloader for .env support
// Adjust the path '../vendor/autoload.php' if your vendor directory is elsewhere
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// Load .env file variables into $_ENV if phpdotenv is loaded
if (class_exists('Dotenv\Dotenv')) {
    try {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();
        // You can add ->required([...]) here if needed, but we'll use fallbacks below
    } catch (\Dotenv\Exception\InvalidPathException $e) {
        // .env file not found, rely on potential defines or defaults (less secure)
        // You might want to log this or show a specific setup error
        error_log("Warning: .env file not found. Relying on constants or defaults.");
    } catch (Exception $e) {
        // Other error loading .env
        error_log("Error loading .env file: " . $e->getMessage());
    }
}

// --- Database Connection Details ---
// Use .env variable if available, otherwise fallback to constants (or direct values - less secure)
define('DB_HOST', $_ENV['DB_HOST'] ?? 'sql308.infinityfree.com');
define('DB_USERNAME', $_ENV['DB_USERNAME'] ?? 'if0_40212246');
define('DB_PASSWORD', $_ENV['DB_PASSWORD'] ?? 'Vivek2025'); // Be cautious hardcoding passwords
define('DB_NAME', $_ENV['DB_NAME'] ?? 'if0_40212246_kollege');

// --- System Configuration ---
// Use .env variable if available, otherwise set a default (adjust default if needed)
// CRITICAL: Ensure this ends with a '/'
$base_url_env = $_ENV['BASE_URL'] ?? 'https://kollege.ct.ws/'; // <<< YOUR ACTUAL BASE URL HERE
define('BASE_URL', rtrim($base_url_env, '/') . '/');

define('SITE_NAME', $_ENV['SITE_NAME'] ?? 'Kollege LMS');
define('ADMIN_EMAIL', $_ENV['ADMIN_EMAIL'] ?? 'admin@kollege.ct.ws'); // Use a real admin email

// --- Environment ---
define('ENVIRONMENT', $_ENV['ENVIRONMENT'] ?? 'production'); // Default to 'production' for safety

// --- Error Handling ---
// Ensure the path to error_handler.php is correct
require_once __DIR__ . '/error_handler.php';

// --- Session Management ---
ini_set('session.cookie_httponly', 1); // Prevent JS access to session cookie
ini_set('session.use_only_cookies', 1); // Prevent session fixation via URL
// Set secure flag only if served over HTTPS
if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === 1) || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) {
    ini_set('session.cookie_secure', 1);
}
// Set SameSite attribute for modern browsers (helps prevent CSRF)
ini_set('session.cookie_samesite', 'Lax'); // Or 'Strict' if appropriate
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- PDO Database Connection ---
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on SQL errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch associative arrays by default
    PDO::ATTR_EMULATE_PREPARES   => false,                    // Use real prepared statements
];

// Global PDO variable
// In larger apps, use Dependency Injection or a Service Locator instead of global
try {
    $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD, $options);
} catch (PDOException $e) {
    log_error("CRITICAL: Database Connection Failed: " . $e->getMessage(), __FILE__, __LINE__);
    // Display a generic, user-friendly error message in production
    if (ENVIRONMENT === 'production') {
        die("A critical error occurred while connecting to the database. Please contact the site administrator.");
    } else {
        // Show more details in development
        die("Database Connection Failed: " . $e->getMessage() . "<br/>Please check your config.php or .env file credentials and database server status.");
    }
}

?>