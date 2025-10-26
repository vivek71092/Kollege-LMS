<?php
// /dashboard/admin/announcements/edit-announcement.php

// Load core files
require_once '../../../config.php'; // Ensures $pdo is available
require_once '../../../functions.php';
require_once '../../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['admin']);
$user = get_session_user();

// Get announcement ID from URL
$announcement_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!$announcement_id) {
    $_SESSION['error_message'] = "Invalid announcement ID.";
    redirect('dashboard/admin/announcements/list-announcements.php');
}

// --- Edit Processing ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $title = sanitize_input($_POST['title']);
    $description = sanitize_input($_POST['description']);
    $status = sanitize_input($_POST['status']);
    $priority = filter_input(INPUT_POST, 'priority', FILTER_SANITIZE_NUMBER_INT);
    $priority = ($priority === 1) ? 1 : 0;

    // TODO: Handle image update/removal if implementing file uploads

    // Validation
    if (empty($title) || empty($description)) {
        $_SESSION['error_message'] = "Title and Description are required.";
        header("Location: edit-announcement.php?id=" . $announcement_id);
        exit;
    }

    // --- ACTUAL DATABASE UPDATE LOGIC ---
    try {
        $sql = "UPDATE Announcements SET
                title = ?,
                description = ?,
                status = ?,
                priority = ?
                -- Add image = ? if handling file uploads
                WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([
            $title,
            $description,
            $status,
            $priority,
            // $new_image_path, // If handling image update
            $announcement_id
        ]);

        if ($success) {
            $_SESSION['success_message'] = "Announcement updated successfully!";
            redirect('dashboard/admin/announcements/list-announcements.php');
        } else {
             $_SESSION['error_message'] = "Failed to update announcement. An error occurred or no changes were detected.";
             header("Location: edit-announcement.php?id=" . $announcement_id);
             exit;
        }
    } catch (PDOException $e) {
        log_error("Error updating announcement ID $announcement_id: " . $e->getMessage(), __FILE__, __LINE__);
        $_SESSION['error_message'] = "A database error occurred while updating the announcement.";
        header("Location: edit-announcement.php?id=" . $announcement_id);
        exit;
    }
    // --- END ACTUAL DATABASE LOGIC ---
}
// --- End Processing ---


// --- Fetch Announcement Data (GET request) ---
try {
    $stmt = $pdo->prepare("SELECT * FROM Announcements WHERE id = ?");
    $stmt->execute([$announcement_id]);
    $announcement = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$announcement) {
        $_SESSION['error_message'] = "Announcement not found.";
        redirect('dashboard/admin/announcements/list-announcements.php');
    }
} catch (PDOException $e) {
     log_error("Error fetching announcement ID $announcement_id for edit: " . $e->getMessage(), __FILE__, __LINE__);
     $_SESSION['error_message'] = "A database error occurred while fetching announcement data.";
     $announcement = null; // Mark as null
}
// --- End Fetch ---

$page_title = $announcement ? "Edit Announcement: " . htmlspecialchars($announcement['title']) : "Edit Announcement";
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
                // Display error messages
                display_flash_message('error_message', 'alert-danger');
                ?>

                <?php if ($announcement): // Only show form if data was loaded ?>
                <form action="edit-announcement.php?id=<?php echo $announcement_id; ?>" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($announcement['title']); ?>" required>
                        <div class="invalid-feedback">Please provide a title.</div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description / Content</label>
                        <textarea class="form-control" id="description" name="description" rows="8" required><?php echo htmlspecialchars($announcement['description']); ?></textarea>
                         <div class="invalid-feedback">Please provide content.</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="published" <?php if($announcement['status'] == 'published') echo 'selected'; ?>>Published</option>
                                <option value="draft" <?php if($announcement['status'] == 'draft') echo 'selected'; ?>>Draft</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="priority" class="form-label">Priority</label>
                            <select class="form-select" id="priority" name="priority">
                                <option value="0" <?php if($announcement['priority'] == 0) echo 'selected'; ?>>Normal</option>
                                <option value="1" <?php if($announcement['priority'] == 1) echo 'selected'; ?>>High (Pin to top)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Featured Image (Optional)</label>
                        <?php if (!empty($announcement['image'])): ?>
                            <p><img src="<?php echo BASE_URL . htmlspecialchars($announcement['image']); ?>" alt="Current Image" style="max-width: 200px; height: auto;"></p>
                            <label><input type="checkbox" name="remove_image" value="1"> Remove current image</label>
                        <?php endif; ?>
                        <input type="file" class="form-control mt-2" id="image" name="image" accept="image/jpeg, image/png, image/gif" disabled>
                        <small class="form-text">(Image update/removal not fully implemented in this demo)</small>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Announcement</button>
                    <a href="list-announcements.php" class="btn btn-secondary">Cancel</a>
                </form>
                 <?php else: ?>
                    <div class="alert alert-danger">Could not load announcement data. Please go back to the list.</div>
                     <a href="list-announcements.php" class="btn btn-secondary">Back to List</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
require_once $path_prefix . 'includes/footer.php';
?>