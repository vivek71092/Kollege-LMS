<?php
// /auth/reset-password.php

require_once '../config.php';
require_once '../functions.php';

$page_title = "Reset Password";

// --- Token Validation (GET) ---
$token = sanitize_input($_GET['token'] ?? '');
$is_token_valid = false;

if (empty($token)) {
    $_SESSION['error_message'] = "No reset token provided.";
    redirect('auth/login.php');
} else {
    // --- PLACEHOLDER LOGIC ---
    // In a real application, you would:
    // 1. Query your `PasswordResets` table (or `Users` table) for this token.
    // 2. Check that the token exists and has not expired (e.g., `expires_at > NOW()`).
    // 3. If valid, set $is_token_valid = true;
    
    // For this template, we'll assume the token "12345abcdef" is valid.
    if ($token === "12345abcdef") {
        $is_token_valid = true;
    } else {
        $_SESSION['error_message'] = "Invalid or expired reset token.";
        redirect('auth/login.php');
    }
}
// --- End Token Validation ---


// --- Password Reset Processing (POST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = sanitize_input($_POST['token']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Re-validate token from hidden form field
    // (This is just placeholder validation)
    if ($token !== "12345abcdef") {
        $_SESSION['error_message'] = "Invalid token. Please try again.";
        redirect('auth/login.php');
    }

    if (strlen($password) < 8) {
        $_SESSION['error_message'] = "Password must be at least 8 characters long.";
    } elseif ($password !== $confirm_password) {
        $_SESSION['error_message'] = "Passwords do not match.";
    } else {
        // --- PLACEHOLDER LOGIC ---
        // In a real application, you would:
        // 1. Hash the new password: $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        // 2. Get the user's email associated with the token.
        // 3. Update the user's password: 
        //    $pdo->prepare("UPDATE Users SET password = ? WHERE email = ?")->execute([$hashed_password, $user_email]);
        // 4. Delete the token from your `PasswordResets` table so it can't be reused.

        // For this template, we just show success.
        $_SESSION['success_message'] = "Your password has been reset successfully. You can now log in.";
        redirect('auth/login.php');
    }
    // Reload page to show error
    redirect('auth/reset-password.php?token=' . $token);
}
// --- End Processing ---


require_once '../includes/header.php';
?>

<div class="container" style="max-width: 500px; margin-top: 5rem; margin-bottom: 5rem;">
    <div class="card shadow-lg border-0">
        <div class="card-body p-4 p-md-5">
            <h2 class="text-center h3 mb-4"><?php echo $page_title; ?></h2>
            <p class="text-center text-muted mb-4">Enter your new password below.</p>
            
            <?php 
            display_flash_message('error_message', 'alert-danger');
            ?>

            <form action="auth/reset-password.php" method="POST" class="needs-validation" novalidate>
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                
                <div class="mb-3">
                    <label for="password" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="password" name="password" minlength="8" required>
                    <div class="invalid-feedback">Password must be at least 8 characters.</div>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    <div class="invalid-feedback">Please confirm your password.</div>
                </div>
                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary btn-lg">Reset Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';
?>