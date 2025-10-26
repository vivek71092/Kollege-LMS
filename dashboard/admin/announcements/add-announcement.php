<?php
// /dashboard/admin/announcements/add-announcement.php

// Load core files
require_once '../../../config.php'; // Ensures $pdo is available
require_once '../../../functions.php';
require_once '../../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['admin']);

$page_title = "Create Announcement";
$user = get_session_user(); // Get logged-in admin user info

// --- Add Announcement Processing ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $title = sanitize_input($_POST['title']);
    $description = sanitize_input($_POST['description']); // Note: Consider allowing safe HTML if using a rich text editor
    $status = sanitize_input($_POST['status']);
    $priority = filter_input(INPUT_POST, 'priority', FILTER_SANITIZE_NUMBER_INT);
    // Ensure priority is 0 or 1
    $priority = ($priority === 1) ? 1 : 0;
    $created_by = $user['id']; // ID of the logged-in admin

    // File Upload Handling (Placeholder - integrate FileHandler class if needed)
    $image_path = null; // Set to null initially
    // if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    //     require_once '../../../classes/FileHandler.php'; // Make sure path is correct
    //     $fileHandler = new FileHandler('../../../public/uploads/'); // Base upload dir
    //     $uploadResult = $fileHandler->upload($_FILES['image'], 'announcements', ['image/jpeg', 'image/png', 'image/gif'], 2 * 1024 * 1024); // 2MB max
    //     if ($uploadResult['success']) {
    //         $image_path = $uploadResult['path']; // Relative path for DB
    //     } else {
    //         $_SESSION['error_message'] = "Image upload failed: " . $uploadResult['error'];
    //         redirect('dashboard/admin/announcements/add-announcement.php');
    //     }
    // }

    // Validation
    if (empty($title) || empty($description)) {
        $_SESSION['error_message'] = "Title and Description are required.";
        redirect('dashboard/admin/announcements/add-announcement.php');
    } else {
        // --- ACTUAL DATABASE INSERT LOGIC ---
        try {
            $sql = "INSERT INTO Announcements (title, description, created_by, created_date, status, priority, image)
                    VALUES (?, ?, ?, NOW(), ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $success = $stmt->execute([
                $title,
                $description,
                $created_by,
                $status,
                $priority,
                $image_path // Use the path from upload, or null
            ]);

            if ($success) {
                $_SESSION['success_message'] = "Announcement created successfully!";
                redirect('dashboard/admin/announcements/list-announcements.php');
            } else {
                $_SESSION['error_message'] = "Failed to create announcement. Please try again.";
                // Optional: Clean up uploaded file if DB insert failed
                // if ($image_path && isset($fileHandler)) $fileHandler->delete($image_path);
                redirect('dashboard/admin/announcements/add-announcement.php');
            }
        } catch (PDOException $e) {
            log_error("Error adding announcement: " . $e->getMessage(), __FILE__, __LINE__);
            $_SESSION['error_message'] = "A database error occurred while creating the announcement.";
            // Optional: Clean up uploaded file if DB insert failed
            // if ($image_path && isset($fileHandler)) $fileHandler->delete($image_path);
            redirect('dashboard/admin/announcements/add-announcement.php');
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
                <h4 class="mb-0">Create New Announcement</h4>
            </div>
            <div class="card-body">

                <?php
                display_flash_message('error_message', 'alert-danger');
                ?>

                <form action="add-announcement.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                        <div class="invalid-feedback">Please provide a title.</div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description / Content</label>
                        <textarea class="form-control" id="description" name="description" rows="8" required></textarea>
                         <div class="invalid-feedback">Please provide content for the announcement.</div>
                        </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="published" selected>Published (Visible to all)</option>
                                <option value="draft">Draft (Hidden)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="priority" class="form-label">Priority</label>
                            <select class="form-select" id="priority" name="priority">
                                <option value="0" selected>Normal</option>
                                <option value="1">High (Pin to top)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Featured Image (Optional)</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/jpeg, image/png, image/gif">
                        <small class="form-text">(Max 2MB. Types: JPG, PNG, GIF. Placeholder - upload logic commented out)</small>
                    </div>

                    <button type="submit" class="btn btn-primary">Create Announcement</button>
                    <a href="list-announcements.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require_once $path_prefix . 'includes/footer.php';
?>