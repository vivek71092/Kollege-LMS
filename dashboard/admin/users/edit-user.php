<?php
// /dashboard/admin/users/edit-user.php

// Load core files
require_once '../../../config.php'; // Ensures $pdo is available
require_once '../../../functions.php';
require_once '../../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['admin']);
$user = get_session_user(); // Logged in admin user

// Get user ID to edit from URL
$user_id_to_edit = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!$user_id_to_edit) {
    $_SESSION['error_message'] = "Invalid user ID provided.";
    redirect('dashboard/admin/users/list-users.php');
}

// --- Edit User Processing (POST request) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs from the form
    $first_name = sanitize_input($_POST['first_name']);
    $last_name = sanitize_input($_POST['last_name']);
    $phone = sanitize_input($_POST['phone']);
    $role = sanitize_input($_POST['role']);
    $status = sanitize_input($_POST['status']);
    $bio = sanitize_input($_POST['bio']); // Added bio field

    // Validate required fields
    if (empty($first_name) || empty($last_name) || empty($role) || empty($status)) {
         $_SESSION['error_message'] = "First Name, Last Name, Role, and Status are required.";
         // Redirect back to the edit page with the same ID
         header("Location: edit-user.php?id=" . $user_id_to_edit);
         exit;
    }

    // --- ACTUAL DATABASE UPDATE LOGIC ---
    try {
        $sql = "UPDATE Users SET
                first_name = ?,
                last_name = ?,
                phone = ?,
                role = ?,
                status = ?,
                bio = ?,
                updated_at = NOW()
                WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([
            $first_name,
            $last_name,
            $phone,
            $role,
            $status,
            $bio, // Added bio
            $user_id_to_edit // Use the ID from the URL parameter
        ]);

        if ($success) {
            $_SESSION['success_message'] = "User profile updated successfully!";
            redirect('dashboard/admin/users/list-users.php');
        } else {
            $_SESSION['error_message'] = "Failed to update user profile. No changes were made or an error occurred.";
            header("Location: edit-user.php?id=" . $user_id_to_edit);
            exit;
        }
    } catch (PDOException $e) {
        log_error("Error updating user ID $user_id_to_edit: " . $e->getMessage(), __FILE__, __LINE__);
        $_SESSION['error_message'] = "A database error occurred while updating the user.";
        header("Location: edit-user.php?id=" . $user_id_to_edit);
        exit;
    }
    // --- END ACTUAL DATABASE LOGIC ---
}
// --- End POST Processing ---


// --- Fetch User Data to Edit (GET request) ---
try {
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE id = ?");
    $stmt->execute([$user_id_to_edit]);
    $user_to_edit = $stmt->fetch(PDO::FETCH_ASSOC); // Use FETCH_ASSOC

    if (!$user_to_edit) {
        $_SESSION['error_message'] = "User with ID $user_id_to_edit not found.";
        redirect('dashboard/admin/users/list-users.php');
    }
} catch (PDOException $e) {
     log_error("Error fetching user ID $user_id_to_edit for edit: " . $e->getMessage(), __FILE__, __LINE__);
     $_SESSION['error_message'] = "A database error occurred while fetching user data.";
     redirect('dashboard/admin/users/list-users.php');
}
// --- End Fetch ---

$page_title = "Edit User: " . htmlspecialchars($user_to_edit['email']);
// Pass correct path prefix to header/footer
$path_prefix = '../../../';
require_once $path_prefix . 'includes/header.php';
?>

<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <div class="card shadow-sm">
            <div class="card-header">
                <h4 class="mb-0"><?php echo $page_title; ?></h4>
            </div>
            <div class="card-body">

                <?php
                // Display any error messages from POST attempt before showing form again
                display_flash_message('error_message', 'alert-danger');
                ?>

                <form action="edit-user.php?id=<?php echo $user_id_to_edit; ?>" method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($user_to_edit['email']); ?>" readonly disabled>
                        <small class="form-text">Email address cannot be changed here.</small>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user_to_edit['first_name']); ?>" required>
                            <div class="invalid-feedback">First name is required.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user_to_edit['last_name']); ?>" required>
                            <div class="invalid-feedback">Last name is required.</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user_to_edit['phone'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                         <label for="bio" class="form-label">Bio / About</label>
                         <textarea class="form-control" id="bio" name="bio" rows="3"><?php echo htmlspecialchars($user_to_edit['bio'] ?? ''); ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role" name="role" required <?php if ($user_to_edit['id'] == $user['id']) echo 'disabled'; /* Prevent changing own role */ ?>>
                                <option value="student" <?php if ($user_to_edit['role'] == 'student') echo 'selected'; ?>>Student</option>
                                <option value="teacher" <?php if ($user_to_edit['role'] == 'teacher') echo 'selected'; ?>>Teacher</option>
                                <option value="admin" <?php if ($user_to_edit['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                            </select>
                             <?php if ($user_to_edit['id'] == $user['id']): ?>
                                <small class="form-text">You cannot change your own role.</small>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required <?php if ($user_to_edit['id'] == $user['id']) echo 'disabled'; /* Prevent suspending self */ ?>>
                                <option value="active" <?php if ($user_to_edit['status'] == 'active') echo 'selected'; ?>>Active</option>
                                <option value="pending" <?php if ($user_to_edit['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                                <option value="suspended" <?php if ($user_to_edit['status'] == 'suspended') echo 'selected'; ?>>Suspended</option>
                            </select>
                             <?php if ($user_to_edit['id'] == $user['id']): ?>
                                <small class="form-text">You cannot change your own status.</small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Update User</button>
                    <a href="list-users.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require_once $path_prefix . 'includes/footer.php';
?>