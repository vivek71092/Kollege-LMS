<?php
// /dashboard/admin/courses/assign-teacher.php

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

// --- Assignment Processing (POST request) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $teacher_id = filter_input(INPUT_POST, 'teacher_id', FILTER_SANITIZE_NUMBER_INT);
    // Use NULL if "None" (value 0) is selected
    $teacher_id_to_save = ($teacher_id == 0 || $teacher_id === false) ? null : $teacher_id;

    // --- ACTUAL DATABASE UPDATE LOGIC ---
    try {
        $sql = "UPDATE Courses SET teacher_id = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([$teacher_id_to_save, $course_id]);

        if ($success) {
            $_SESSION['success_message'] = "Head Teacher assigned successfully!";
        } else {
             $_SESSION['error_message'] = "Failed to assign teacher. An error occurred.";
        }
    } catch (PDOException $e) {
        log_error("Error assigning teacher to course ID $course_id: " . $e->getMessage(), __FILE__, __LINE__);
        $_SESSION['error_message'] = "A database error occurred while assigning the teacher.";
    }
    // --- END ACTUAL DATABASE LOGIC ---

    redirect('dashboard/admin/courses/list-courses.php');
}
// --- End POST Processing ---


// --- Fetch Course Data and Teachers (GET request) ---
try {
    // Fetch Course Name
    $stmt_course = $pdo->prepare("SELECT course_name, teacher_id FROM Courses WHERE id = ?");
    $stmt_course->execute([$course_id]);
    $course = $stmt_course->fetch(PDO::FETCH_ASSOC);

    if (!$course) {
        $_SESSION['error_message'] = "Course not found.";
        redirect('dashboard/admin/courses/list-courses.php');
    }

    // Fetch all active teachers
    $stmt_teachers = $pdo->query("SELECT id, first_name, last_name FROM Users WHERE role = 'teacher' AND status = 'active' ORDER BY last_name, first_name");
    $teachers = $stmt_teachers->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    log_error("Error fetching data for assign teacher page (Course ID $course_id): " . $e->getMessage(), __FILE__, __LINE__);
    $_SESSION['error_message'] = "A database error occurred while loading data.";
    $course = ['course_name' => 'Error Loading', 'teacher_id' => null]; // Set defaults
    $teachers = []; // Set default
    // Optionally redirect if critical data is missing
    // redirect('dashboard/admin/courses/list-courses.php');
}
// --- End Fetch ---

$page_title = "Assign Teacher to " . htmlspecialchars($course['course_name']);
// Pass correct path prefix to header/footer
$path_prefix = '../../../';
require_once $path_prefix . 'includes/header.php';
?>

<div class="row">
    <div class="col-lg-6 offset-lg-3">
        <div class="card shadow-sm">
            <div class="card-header">
                <h4 class="mb-0"><?php echo $page_title; ?></h4>
            </div>
            <div class="card-body">

                 <?php
                 // Display any potential error messages from fetching data
                 display_flash_message('error_message', 'alert-danger');
                 ?>

                <form action="assign-teacher.php?id=<?php echo $course_id; ?>" method="POST">
                    <div class="mb-3">
                        <label for="teacher_id" class="form-label">Select Head Teacher</label>
                        <select class="form-select" id="teacher_id" name="teacher_id" required>
                            <option value="0" <?php if (is_null($course['teacher_id'])) echo 'selected'; ?>>-- None (Unassign) --</option>

                            <?php if (empty($teachers)): ?>
                                <option value="" disabled>No active teachers found</option>
                            <?php else: ?>
                                <?php foreach ($teachers as $teacher): ?>
                                    <option value="<?php echo $teacher['id']; ?>" <?php if ($course['teacher_id'] == $teacher['id']) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                             <?php endif; ?>
                        </select>
                        <small class="form-text">This assigns a teacher as the head coordinator for the entire course/program.</small>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Assignment</button>
                    <a href="list-courses.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require_once $path_prefix . 'includes/footer.php';
?>