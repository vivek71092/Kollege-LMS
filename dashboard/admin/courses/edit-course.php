<?php
// /dashboard/admin/courses/edit-course.php

// Load core files
require_once '../../../config.php'; // Ensures $pdo is available
require_once '../../../functions.php';
require_once '../../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['admin']);

// Get course ID from URL
$course_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!$course_id) {
    $_SESSION['error_message'] = "Invalid course ID.";
    redirect('dashboard/admin/courses/list-courses.php');
}

// --- Edit Course Processing (POST request) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $course_name = sanitize_input($_POST['course_name']);
    $course_code = sanitize_input($_POST['course_code']);
    $semester = filter_input(INPUT_POST, 'semester', FILTER_SANITIZE_NUMBER_INT);
    $description = sanitize_input($_POST['description']);
    $status = sanitize_input($_POST['status']);

    // Validation
    if (empty($course_name) || empty($course_code) || empty($semester)) {
        $_SESSION['error_message'] = "Course Name, Code, and Semesters are required.";
        header("Location: edit-course.php?id=" . $course_id); // Redirect back to edit page
        exit;
    }

    // --- ACTUAL DATABASE UPDATE LOGIC ---
    try {
        // Check if new course code conflicts with another existing course
        $stmt_check = $pdo->prepare("SELECT id FROM Courses WHERE course_code = ? AND id != ?");
        $stmt_check->execute([$course_code, $course_id]);
        if ($stmt_check->fetch()) {
             $_SESSION['error_message'] = "Course code '$course_code' is already used by another course.";
             header("Location: edit-course.php?id=" . $course_id);
             exit;
        }

        // Prepare the UPDATE statement
        $sql = "UPDATE Courses SET
                course_code = ?,
                course_name = ?,
                description = ?,
                semester = ?,
                status = ?,
                updated_at = NOW()
                WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([
            $course_code,
            $course_name,
            $description,
            $semester,
            $status,
            $course_id
        ]);

        if ($success) {
            $_SESSION['success_message'] = "Course updated successfully!";
            redirect('dashboard/admin/courses/list-courses.php');
        } else {
            $_SESSION['error_message'] = "Failed to update course. An error occurred or no changes were detected.";
            header("Location: edit-course.php?id=" . $course_id);
            exit;
        }
    } catch (PDOException $e) {
        log_error("Error updating course ID $course_id: " . $e->getMessage(), __FILE__, __LINE__);
        $_SESSION['error_message'] = "A database error occurred while updating the course.";
        header("Location: edit-course.php?id=" . $course_id);
        exit;
    }
    // --- END ACTUAL DATABASE LOGIC ---
}
// --- End POST Processing ---


// --- Fetch Course Data to Edit (GET request) ---
try {
    $stmt = $pdo->prepare("SELECT * FROM Courses WHERE id = ?");
    $stmt->execute([$course_id]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$course) {
        $_SESSION['error_message'] = "Course not found.";
        redirect('dashboard/admin/courses/list-courses.php');
    }
} catch (PDOException $e) {
     log_error("Error fetching course ID $course_id for edit: " . $e->getMessage(), __FILE__, __LINE__);
     $_SESSION['error_message'] = "A database error occurred while fetching course data.";
     redirect('dashboard/admin/courses/list-courses.php');
}
// --- End Fetch ---

$page_title = "Edit Course: " . htmlspecialchars($course['course_name']);
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
                // Display error message if redirect happened from POST block
                display_flash_message('error_message', 'alert-danger');
                ?>

                <form action="edit-course.php?id=<?php echo $course_id; ?>" method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="course_name" class="form-label">Course Name</label>
                        <input type="text" class="form-control" id="course_name" name="course_name" value="<?php echo htmlspecialchars($course['course_name']); ?>" required>
                        <div class="invalid-feedback">Course name is required.</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="course_code" class="form-label">Course Code</label>
                            <input type="text" class="form-control" id="course_code" name="course_code" value="<?php echo htmlspecialchars($course['course_code']); ?>" required>
                            <div class="invalid-feedback">Course code is required.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="semester" class="form-label">Total Semesters</label>
                            <input type="number" class="form-control" id="semester" name="semester" min="1" max="12" value="<?php echo htmlspecialchars($course['semester']); ?>" required>
                             <div class="invalid-feedback">Please enter a valid number of semesters.</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description (Optional)</label>
                        <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($course['description'] ?? ''); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active" <?php if ($course['status'] == 'active') echo 'selected'; ?>>Active</option>
                            <option value="inactive" <?php if ($course['status'] == 'inactive') echo 'selected'; ?>>Inactive</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Course</button>
                    <a href="list-courses.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require_once $path_prefix . 'includes/footer.php';
?>