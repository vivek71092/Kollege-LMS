<?php
// /dashboard/teacher/profile.php

// Load core files
require_once '../../config.php'; // Ensures $pdo is available
require_once '../../functions.php';
require_once '../../auth/check_auth.php'; // Ensure user is logged in
// Include FileHandler class (adjust path if needed, or use autoloader)
require_once '../../classes/FileHandler.php';

// Role-specific check
require_role(['teacher']); // Or adjust roles as needed

$page_title = "My Profile";
$user = get_session_user(); // Get logged-in user info
$user_id = $user['id'];

// --- Define paths ---
$default_image_path_relative = 'public/images/placeholders/profile.png'; // Relative path from project root
$upload_subfolder = 'images/placeholders/profile'; // Subfolder within /public/ for uploads

// --- Profile Update Processing (POST request) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize text inputs
    $first_name = sanitize_input($_POST['first_name']);
    $last_name = sanitize_input($_POST['last_name']);
    $phone = sanitize_input($_POST['phone'] ?? null);
    $bio = sanitize_input($_POST['bio'] ?? null);
    $current_image_path = sanitize_input($_POST['current_profile_image'] ?? null);

    // Validation (as before)
    if (empty($first_name) || empty($last_name)) {
        $_SESSION['error_message'] = "First Name and Last Name are required.";
        redirect('dashboard/' . $user['role'] . '/profile.php');
    }

    $new_image_path_db = $current_image_path; // Default to current image
    $old_image_to_delete = null;
    $fileHandler = null; // Initialize FileHandler variable

    // --- Handle File Upload ---
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == UPLOAD_ERR_OK) {
        try {
            // Instantiate FileHandler pointing one level above public (e.g., project root/public/)
            // Adjust base path if your FileHandler expects something different
             $fileHandler = new FileHandler('../../public/'); // IMPORTANT: Adjust base path if needed
             if (!$fileHandler) { throw new Exception("FileHandler class not found or failed to instantiate.");}

            $allowed_image_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_image_size = 2 * 1024 * 1024; // 2MB

            $uploadResult = $fileHandler->upload(
                $_FILES['profile_image'],
                $upload_subfolder, // Pass the new subfolder path (relative to FileHandler base path)
                $allowed_image_types,
                $max_image_size
            );

            if ($uploadResult['success']) {
                // $uploadResult['path'] SHOULD be relative to project root, e.g., 'public/images/placeholders/profile/...'
                $new_image_path_db = $uploadResult['path'];
                log_error("DEBUG: New image uploaded. DB path: " . $new_image_path_db, __FILE__, __LINE__); // Debug log

                // Mark old image for deletion if it wasn't the default one
                if ($current_image_path && $current_image_path !== $default_image_path_relative) {
                     $old_image_to_delete = $current_image_path;
                     log_error("DEBUG: Marked old image for deletion: " . $old_image_to_delete, __FILE__, __LINE__); // Debug log
                }
            } else {
                // Upload failed, stop processing and show error
                throw new Exception("Profile image upload failed: " . $uploadResult['error']);
            }
        } catch (Exception $e) {
             log_error("File Upload Exception: " . $e->getMessage(), __FILE__, __LINE__);
             $_SESSION['error_message'] = $e->getMessage();
             redirect('dashboard/' . $user['role'] . '/profile.php');
        }
    }
    // --- End File Upload Handling ---

    // --- ACTUAL DATABASE UPDATE LOGIC ---
    try {
        $sql = "UPDATE Users SET
                first_name = ?, last_name = ?, phone = ?, bio = ?, profile_image = ?, updated_at = NOW()
                WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        // Ensure $new_image_path_db is used here
        $success = $stmt->execute([
            $first_name, $last_name, $phone, $bio,
            $new_image_path_db, // Path to save in DB
            $user_id
        ]);

        if ($success) {
            $_SESSION['success_message'] = "Profile updated successfully!";
            if ($first_name !== $user['first_name']) { $_SESSION['first_name'] = $first_name; }

            // Delete the old image file AFTER successful DB update
            if ($old_image_to_delete && $fileHandler) {
                 log_error("DEBUG: Attempting to delete old image: " . $old_image_to_delete, __FILE__, __LINE__); // Debug log
                 $deleted = $fileHandler->delete($old_image_to_delete); // Assumes delete takes path relative to project root
                 if (!$deleted) {
                     log_error("Warning: Failed to delete old profile image file: " . $old_image_to_delete, __FILE__, __LINE__);
                 }
            }
        } else {
             $_SESSION['error_message'] = "Failed to update profile database record.";
             // Delete newly uploaded file if DB failed
             if ($new_image_path_db !== $current_image_path && $fileHandler) {
                 log_error("DEBUG: DB update failed, deleting newly uploaded image: " . $new_image_path_db, __FILE__, __LINE__); // Debug log
                 $fileHandler->delete($new_image_path_db);
             }
        }
    } catch (PDOException $e) {
        log_error("Error updating profile user ID $user_id: " . $e->getMessage(), __FILE__, __LINE__);
        $_SESSION['error_message'] = "Database error updating profile.";
        // Delete newly uploaded file if DB failed
         if ($new_image_path_db !== $current_image_path && $fileHandler) {
             log_error("DEBUG: DB exception, deleting newly uploaded image: " . $new_image_path_db, __FILE__, __LINE__); // Debug log
             $fileHandler->delete($new_image_path_db);
         }
    }
    // --- END ACTUAL DATABASE LOGIC ---

    redirect('dashboard/' . $user['role'] . '/profile.php');
}
// --- End POST Processing ---


// --- Fetch Current User Data (GET request) ---
$user_data = null;
$fetch_error = null;
try {
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, phone, bio, profile_image FROM Users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user_data) {
        $fetch_error = "Could not find your user data.";
        $_SESSION['error_message'] = $fetch_error;
    } else {
        // Set default profile image path if DB field is empty or NULL
        if (empty($user_data['profile_image'])) {
            $user_data['profile_image'] = $default_image_path_relative; // Use variable defined above
        }
        // DEBUG: Log the path being used
        // log_error("DEBUG: Image path fetched/set: " . $user_data['profile_image'], __FILE__, __LINE__);
    }
} catch (PDOException $e) {
     log_error("Error fetching profile user ID $user_id: " . $e->getMessage(), __FILE__, __LINE__);
     $fetch_error = "Database error fetching profile.";
     $_SESSION['error_message'] = $fetch_error;
}
// --- End Fetch ---

// Pass correct path prefix to header/footer
$path_prefix = '../../';
require_once $path_prefix . 'includes/header.php';
?>

<div class="card shadow-sm">
    <div class="card-header">
        <h4 class="mb-0"><?php echo $page_title; ?></h4>
    </div>
    <div class="card-body">

        <?php
        display_flash_message('success_message', 'alert-success');
        display_flash_message('error_message', 'alert-danger');
        ?>
        <?php if ($fetch_error && !$user_data): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($fetch_error); ?></div>
        <?php endif; ?>

        <?php if ($user_data): // Only show form if user data was loaded ?>
        <form action="profile.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
             <input type="hidden" name="current_profile_image" value="<?php echo htmlspecialchars($user_data['profile_image']); ?>">

            <div class="row">
                <div class="col-md-4 text-center mb-3">
                    <?php
                        // Construct the FULL, ABSOLUTE URL for the image src
                        // Ensure BASE_URL ends with '/' and $user_data['profile_image'] starts with 'public/'
                        $image_display_url = BASE_URL . ltrim($user_data['profile_image'], '/');
                        $fallback_display_url = BASE_URL . ltrim($default_image_path_relative, '/');
                        // DEBUG: Echo the URL being generated
                        // echo "";
                    ?>
                    <img src="<?php echo htmlspecialchars($image_display_url); ?>"
                         alt="Current Profile Image"
                         class="img-fluid rounded-circle mb-3 border"
                         style="width: 150px; height: 150px; object-fit: cover;"
                         onerror="this.onerror=null; this.src='<?php echo htmlspecialchars($fallback_display_url); ?>'; console.error('Failed to load profile image:', this.src);"> <label for="profile_image" class="form-label">Change Profile Picture</label>
                    <input type="file" class="form-control form-control-sm" id="profile_image" name="profile_image" accept="image/jpeg, image/png, image/gif">
                    <small class="form-text text-muted">Max 2MB. JPG, PNG, GIF.</small>
                </div>

                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" readonly disabled>
                        <small class="form-text text-muted">Email address cannot be changed.</small>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user_data['first_name']); ?>" required>
                             <div class="invalid-feedback">First name is required.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user_data['last_name']); ?>" required>
                            <div class="invalid-feedback">Last name is required.</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number (Optional)</label>
                        <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user_data['phone'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="bio" class="form-label">Bio / Professional Summary (Optional)</label>
                        <textarea class="form-control" id="bio" name="bio" rows="4"><?php echo htmlspecialchars($user_data['bio'] ?? ''); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                     <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                </div> </div> </form>
        <?php endif; // End check for $user_data ?>
    </div> </div> <?php
require_once $path_prefix . 'includes/footer.php';
?>