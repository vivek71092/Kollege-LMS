<?php
// /dashboard/admin/settings.php

// Load core files
require_once '../../config.php'; // Ensures $pdo is available
require_once '../../functions.php';
require_once '../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['admin']); // Only Admins access this specific settings page

$page_title = "Account Settings";
$user = get_session_user(); // Get logged-in admin info
$user_id = $user['id']; // Get the logged-in admin's ID

// --- Password Change Processing (POST request) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    // Get raw passwords from POST
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // --- Validation ---
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $_SESSION['error_message'] = "Please fill out all password fields.";
    } elseif ($new_password !== $confirm_password) {
        $_SESSION['error_message'] = "The new password and confirmation password do not match.";
    } elseif (strlen($new_password) < 8) { // Basic length check
        $_SESSION['error_message'] = "The new password must be at least 8 characters long.";
    } else {
        // --- ACTUAL DATABASE LOGIC ---
        try {
            // 1. Fetch the admin's current hashed password from the database
            $stmt_fetch = $pdo->prepare("SELECT password FROM Users WHERE id = ?");
            $stmt_fetch->execute([$user_id]);
            $current_hashed_password = $stmt_fetch->fetchColumn();

            if (!$current_hashed_password) {
                 // Should not happen for a logged-in user
                 throw new Exception("Could not retrieve current user data.");
            }

            // 2. Verify the provided current password against the stored hash
            if (password_verify($current_password, $current_hashed_password)) {
                // Current password is correct

                // 3. Hash the new password
                $new_hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

                // 4. Update the password in the database
                $stmt_update = $pdo->prepare("UPDATE Users SET password = ?, updated_at = NOW() WHERE id = ?");
                $success = $stmt_update->execute([$new_hashed_password, $user_id]);

                if ($success) {
                    $_SESSION['success_message'] = "Password changed successfully!";
                    // Optional: Log password change action
                    // Logger::info("Admin user ID {$user_id} changed their password.");
                    // Optional: Force re-login after password change
                    // redirect('auth/logout.php');
                } else {
                     $_SESSION['error_message'] = "Failed to update password in the database.";
                }
            } else {
                // Current password provided was incorrect
                $_SESSION['error_message'] = "Incorrect current password provided.";
            }

        } catch (PDOException $e) {
            log_error("Database error changing password for admin user ID {$user_id}: " . $e->getMessage(), __FILE__, __LINE__);
            $_SESSION['error_message'] = "A database error occurred while changing the password.";
        } catch (Exception $e) { // Catch other potential errors
             log_error("Error changing password for admin user ID {$user_id}: " . $e->getMessage(), __FILE__, __LINE__);
             $_SESSION['error_message'] = "An unexpected error occurred: " . $e->getMessage();
        }
        // --- END ACTUAL DATABASE LOGIC ---
    }
    // Redirect back to the admin settings page to show messages
    redirect('dashboard/admin/settings.php');
}
// --- End POST Processing ---

// Pass correct path prefix to header/footer
$path_prefix = '../../';
require_once $path_prefix . 'includes/header.php';
?>

<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <div class="card shadow-sm">
            <div class="card-header">
                <h4 class="mb-0">Change My Password</h4>
            </div>
            <div class="card-body">

                <?php
                // Display success or error messages from POST attempt
                display_flash_message('success_message', 'alert-success');
                display_flash_message('error_message', 'alert-danger');
                ?>

                <form action="settings.php" method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                        <div class="invalid-feedback">Please enter your current password.</div>
                    </div>
                    <hr class="my-3">
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" minlength="8" required>
                        <small class="form-text text-muted">Must be at least 8 characters long.</small>
                        <div class="invalid-feedback">Please enter a new password (min. 8 characters).</div>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        <div class="invalid-feedback">Please confirm your new password.</div>
                    </div>
                    <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                    <a href="dashboard.php" class="btn btn-secondary">Cancel</a> </form>
            </div> </div> </div> </div> <?php
require_once $path_prefix . 'includes/footer.php';
?>