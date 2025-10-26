<?php
// /auth/register.php

// Use __DIR__ for reliable path construction relative to this file
require_once __DIR__ . '/../config.php'; // Ensures $pdo and BASE_URL are available
require_once __DIR__ . '/../functions.php'; // Ensures redirect() and sanitize_input() are available

// If user is already logged in, redirect away from registration
if (is_logged_in()) {
    redirect('dashboard/index.php'); // Send to dashboard router
}

$page_title = "Register Student Account";

// --- Registration Processing ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $first_name = sanitize_input($_POST['first_name']);
    $last_name = sanitize_input($_POST['last_name']);
    $email = sanitize_input($_POST['email']);
    $phone = sanitize_input($_POST['phone'] ?? null); // Optional
    $password = $_POST['password'] ?? ''; // Get raw password
    $confirm_password = $_POST['confirm_password'] ?? '';

    $error_message = null; // Variable to hold validation errors

    // --- Validation ---
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        $error_message = "Please fill out First Name, Last Name, Email, and Password.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format provided.";
    } elseif (strlen($password) < 8) {
        $error_message = "Password must be at least 8 characters long.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    }

    // --- If Validation Passes, Proceed to Database ---
    if ($error_message === null) {
        try {
            // Check if email already exists
            $stmt_check = $pdo->prepare("SELECT id FROM Users WHERE email = ? LIMIT 1");
            $stmt_check->execute([$email]);
            if ($stmt_check->fetch()) {
                $error_message = "An account with this email address already exists.";
            } else {
                // Hash the password securely
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                // Determine initial status (active or pending)
                $status = 'active'; // Change to 'pending' if implementing email verification
                $role = 'student'; // Self-registration is always for students

                // Prepare INSERT statement
                $sql = "INSERT INTO Users (first_name, last_name, email, phone, password, role, status, created_at, updated_at)
                        VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
                $stmt_insert = $pdo->prepare($sql);

                // Execute the insert
                $success = $stmt_insert->execute([
                    $first_name,
                    $last_name,
                    $email,
                    $phone,
                    $hashed_password,
                    $role,
                    $status
                ]);

                if ($success) {
                    // --- SUCCESS ---
                    $_SESSION['success_message'] = "Registration successful! You can now log in.";
                    // **Redirect to LOGIN page on success**
                    redirect('auth/login.php');
                    // No further code executes after redirect()
                } else {
                    $error_message = "Registration failed due to a database error. Please try again.";
                }
            }
        } catch (PDOException $e) {
            log_error("Registration database error: " . $e->getMessage(), __FILE__, __LINE__);
            $error_message = "A database error occurred during registration. Please try again later.";
        }
    }

    // --- If ANY error occurred (validation or DB), set flash message and redirect back ---
    if ($error_message !== null) {
        $_SESSION['error_message'] = $error_message;
        // **Redirect back to THIS registration page**
        redirect('auth/register.php');
        // No further code executes after redirect()
    }
}
// --- End Registration Processing ---

// Set path prefix for header/footer includes relative to this file
$path_prefix = '../';
require_once $path_prefix . 'includes/header.php';
?>

<div class="container" style="max-width: 600px; margin-top: 5rem; margin-bottom: 5rem;">
    <div class="card shadow-lg border-0">
        <div class="card-body p-4 p-md-5">
            <h2 class="text-center h3 mb-4"><?php echo htmlspecialchars($page_title); ?></h2>

            <?php
            // Display flash messages on page load (will show errors after redirect)
            display_flash_message('success_message', 'alert-success'); // Should only show if redirected from elsewhere
            display_flash_message('error_message', 'alert-danger');
            ?>

            <!-- CORRECTED Form action: points back to this same file -->
            <form action="register.php" method="POST" class="needs-validation" novalidate>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                        <div class="invalid-feedback">First name is required.</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                        <div class="invalid-feedback">Last name is required.</div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                    <div class="invalid-feedback">A valid email is required.</div>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number (Optional)</label>
                    <input type="tel" class="form-control" id="phone" name="phone">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" minlength="8" required>
                        <small class="form-text text-muted">Minimum 8 characters.</small>
                        <div class="invalid-feedback">Password must be at least 8 characters.</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        <div class="invalid-feedback">Please confirm your password.</div>
                    </div>
                </div>
                <div class="d-grid mb-3 mt-2">
                    <button type="submit" class="btn btn-primary btn-lg">Register</button>
                </div>
            </form>
        </div>
        <div class="card-footer text-center p-3 bg-light">
            <p class="mb-0">Already have an account?
                <!-- Corrected relative link -->
                <a href="login.php">Login here</a>
            </p>
        </div>
    </div>
</div>

<?php
require_once $path_prefix . 'includes/footer.php';
?>