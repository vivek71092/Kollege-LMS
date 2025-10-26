<?php
// /auth/login.php

// Use __DIR__ for reliable path construction
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';

// --- DEBUG CHECK ---
// Temporarily echo session status BEFORE the redirect check
// echo "<pre>Session Status Before Check: "; print_r($_SESSION); echo "</pre>";
// echo "is_logged_in() returns: " . (is_logged_in() ? 'TRUE' : 'FALSE') . "<br>";
// --- END DEBUG ---

// If user is already logged in, redirect to their dashboard router
if (is_logged_in()) {
    // --- DEBUG ---
    // echo "DEBUG: User IS logged in, attempting redirect to dashboard/index.php...";
    // exit; // Stop here during debugging to see the message
    // --- END DEBUG ---
    redirect('dashboard/index.php');
}

$page_title = "Login";

// --- Login Processing ---
// (Keep the POST processing block from the previous version)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password']; // Get raw password for verification

    // Basic validation
    if (empty($email) || empty($password)) {
        $_SESSION['error_message'] = "Email and password are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
         $_SESSION['error_message'] = "Invalid email format provided.";
    } else {
        try {
            // Find the user by email
            $stmt = $pdo->prepare("SELECT id, first_name, email, password, role, status FROM Users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify user exists, password matches, and account is active
            if ($user && password_verify($password, $user['password'])) {

                if ($user['status'] !== 'active') {
                    if ($user['status'] === 'pending') {
                         $_SESSION['error_message'] = "Account pending verification. Check your email.";
                    } else { // suspended or other
                         $_SESSION['error_message'] = "Account suspended. Contact support.";
                    }
                } else {
                    // SUCCESSFUL LOGIN
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['first_name'] = $user['first_name'];
                    // Redirect to the main dashboard router
                    redirect('dashboard/index.php'); // Correct destination
                }
            } else {
                $_SESSION['error_message'] = "Invalid email or password combination.";
            }
        } catch (PDOException $e) {
            log_error("Login database error: " . $e->getMessage(), __FILE__, __LINE__);
            $_SESSION['error_message'] = "A server error occurred during login. Please try again later.";
        }
    }
    // If ANY error message was set during POST, redirect back to login page
    if (isset($_SESSION['error_message'])) {
        redirect('auth/login.php');
    }
}
// --- End Login Processing ---

// Set path prefix for header/footer includes
$path_prefix = '../';
require_once $path_prefix . 'includes/header.php';
?>

<div class="container" style="max-width: 500px; margin-top: 5rem; margin-bottom: 5rem;">
    <div class="card shadow-lg border-0">
        <div class="card-body p-4 p-md-5">
            <h2 class="text-center h3 mb-4">Login to <?php echo SITE_NAME; ?></h2>

            <?php
             display_flash_message('success_message', 'alert-success');
             display_flash_message('error_message', 'alert-danger');
            ?>

            <form action="login.php" method="POST" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                    <div class="invalid-feedback">Please enter your email.</div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <div class="invalid-feedback">Please enter your password.</div>
                </div>
                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary btn-lg">Login</button>
                </div>
                <div class="text-center">
                    <a href="forgot-password.php" class="form-text">Forgot Password?</a>
                </div>
            </form>
        </div>
        <div class="card-footer text-center p-3 bg-light">
            <p class="mb-0">Don't have an account?
                <a href="register.php">Register as a Student</a>
            </p>
        </div>
    </div>
</div>

<?php
require_once $path_prefix . 'includes/footer.php';
?>