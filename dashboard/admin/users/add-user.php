<?php
// /dashboard/admin/users/add-user.php

// Load core files
require_once '../../../config.php'; // Ensures $pdo is available
require_once '../../../functions.php';
require_once '../../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['admin']);

$page_title = "Add New User";
$user = get_session_user(); // Logged-in admin

// --- Add User Processing ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $first_name = sanitize_input($_POST['first_name']);
    $last_name = sanitize_input($_POST['last_name']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password']; // Get raw password
    $role = sanitize_input($_POST['role']);
    $status = sanitize_input($_POST['status']);
    $phone = sanitize_input($_POST['phone'] ?? null); // Optional phone
    $bio = sanitize_input($_POST['bio'] ?? null); // Optional bio

    // Basic Validation
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($role) || empty($status)) {
        $_SESSION['error_message'] = "Please fill out First Name, Last Name, Email, Password, Role, and Status.";
        redirect('dashboard/admin/users/add-user.php');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = "Invalid email format.";
        redirect('dashboard/admin/users/add-user.php');
    } elseif (strlen($password) < 8) {
        $_SESSION['error_message'] = "Password must be at least 8 characters long.";
        redirect('dashboard/admin/users/add-user.php');
    } else {
        // --- ACTUAL DATABASE INSERT LOGIC ---
        try {
            // Check if email already exists
            $stmt_check = $pdo->prepare("SELECT id FROM Users WHERE email = ?");
            $stmt_check->execute([$email]);
            if ($stmt_check->fetch()) {
                $_SESSION['error_message'] = "An account with this email ('$email') already exists.";
                redirect('dashboard/admin/users/add-user.php');
            } else {
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                // Prepare the INSERT statement
                $sql = "INSERT INTO Users (first_name, last_name, email, password, phone, role, status, bio, created_at)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                $stmt = $pdo->prepare($sql);

                // Execute the statement
                $success = $stmt->execute([
                    $first_name,
                    $last_name,
                    $email,
                    $hashed_password,
                    $phone,
                    $role,
                    $status,
                    $bio
                ]);

                if ($success) {
                    $_SESSION['success_message'] = "User ($email) created successfully!";
                    redirect('dashboard/admin/users/list-users.php');
                } else {
                    $_SESSION['error_message'] = "Failed to create user. Please try again.";
                    redirect('dashboard/admin/users/add-user.php');
                }
            }
        } catch (PDOException $e) {
            log_error("Error adding user: " . $e->getMessage(), __FILE__, __LINE__);
            $_SESSION['error_message'] = "A database error occurred while creating the user.";
            redirect('dashboard/admin/users/add-user.php');
        }
        // --- END ACTUAL DATABASE LOGIC ---
    }
}
// --- End Processing ---

// Pass correct path prefix to header/footer
$path_prefix = '../../../';
require_once $path_prefix . 'includes/header.php';
?>

<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <div class="card shadow-sm">
            <div class="card-header">
                <h4 class="mb-0">Add New User</h4>
            </div>
            <div class="card-body">

                <?php
                display_flash_message('error_message', 'alert-danger');
                ?>

                <form action="add-user.php" method="POST" class="needs-validation" novalidate>
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
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" minlength="8" required>
                        <small class="form-text">Min 8 characters.</small>
                        <div class="invalid-feedback">Password must be at least 8 characters.</div>
                    </div>
                     <div class="mb-3">
                         <label for="bio" class="form-label">Bio / About (Optional)</label>
                         <textarea class="form-control" id="bio" name="bio" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="">Select a role...</option>
                                <option value="student">Student</option>
                                <option value="teacher">Teacher</option>
                                <option value="admin">Admin</option>
                            </select>
                            <div class="invalid-feedback">Please select a role.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="active" selected>Active</option>
                                <option value="pending">Pending</option>
                                <option value="suspended">Suspended</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Create User</button>
                    <a href="list-users.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require_once $path_prefix . 'includes/footer.php';
?>