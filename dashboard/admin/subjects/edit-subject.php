<?php
// /dashboard/admin/subjects/edit-subject.php

// Load core files
require_once '../../../config.php'; // Ensures $pdo is available
require_once '../../../functions.php';
require_once '../../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['admin']);

// Get subject ID from URL
$subject_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!$subject_id) {
    $_SESSION['error_message'] = "Invalid subject ID.";
    redirect('dashboard/admin/subjects/list-subjects.php');
}

// --- Edit Subject Processing ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $course_id = filter_input(INPUT_POST, 'course_id', FILTER_SANITIZE_NUMBER_INT);
    $subject_name = sanitize_input($_POST['subject_name']);
    $subject_code = sanitize_input($_POST['subject_code']);
    $semester = filter_input(INPUT_POST, 'semester', FILTER_SANITIZE_NUMBER_INT);
    $credits = filter_input(INPUT_POST, 'credits', FILTER_SANITIZE_NUMBER_INT, ['options' => ['default' => null]]);
    $status = sanitize_input($_POST['status']);

    // Validation
    if (empty($course_id) || empty($subject_name) || empty($subject_code) || empty($semester)) {
         $_SESSION['error_message'] = "Course, Subject Name, Code, and Semester are required.";
         header("Location: edit-subject.php?id=" . $subject_id);
         exit;
    }

    // --- ACTUAL DATABASE UPDATE LOGIC ---
    try {
        // Check if new subject code conflicts with another existing subject
        $stmt_check = $pdo->prepare("SELECT id FROM Subjects WHERE subject_code = ? AND id != ?");
        $stmt_check->execute([$subject_code, $subject_id]);
        if ($stmt_check->fetch()) {
             $_SESSION['error_message'] = "Subject code '$subject_code' is already used by another subject.";
             header("Location: edit-subject.php?id=" . $subject_id);
             exit;
        }

        // Prepare the UPDATE statement
        $sql = "UPDATE Subjects SET
                course_id = ?,
                subject_code = ?,
                subject_name = ?,
                semester = ?,
                credits = ?,
                status = ?,
                updated_at = NOW()
                WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([
            $course_id,
            $subject_code,
            $subject_name,
            $semester,
            $credits,
            $status,
            $subject_id
        ]);

        if ($success) {
            $_SESSION['success_message'] = "Subject updated successfully!";
            redirect('dashboard/admin/subjects/list-subjects.php');
        } else {
            $_SESSION['error_message'] = "Failed to update subject. An error occurred or no changes were detected.";
            header("Location: edit-subject.php?id=" . $subject_id);
            exit;
        }
    } catch (PDOException $e) {
        log_error("Error updating subject ID $subject_id: " . $e->getMessage(), __FILE__, __LINE__);
        $_SESSION['error_message'] = "A database error occurred while updating the subject.";
        header("Location: edit-subject.php?id=" . $subject_id);
        exit;
    }
    // --- END ACTUAL DATABASE LOGIC ---
}
// --- End Processing ---


// --- Fetch Subject Data and Courses (GET request) ---
try {
    // Fetch Subject
    $stmt_subject = $pdo->prepare("SELECT * FROM Subjects WHERE id = ?");
    $stmt_subject->execute([$subject_id]);
    $subject = $stmt_subject->fetch(PDO::FETCH_ASSOC);

    if (!$subject) {
        $_SESSION['error_message'] = "Subject not found.";
        redirect('dashboard/admin/subjects/list-subjects.php');
    }

    // Fetch Courses for dropdown
    $courses = $pdo->query("SELECT id, course_name FROM Courses WHERE status = 'active' ORDER BY course_name")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
     log_error("Error fetching data for edit subject page (ID $subject_id): " . $e->getMessage(), __FILE__, __LINE__);
     $_SESSION['error_message'] = "A database error occurred while fetching data.";
     $subject = null; // Mark as null to prevent form rendering errors
     $courses = [];
     // Consider redirecting if subject fetch failed critically
     // redirect('dashboard/admin/subjects/list-subjects.php');
}
// --- End Fetch ---

$page_title = $subject ? "Edit Subject: " . htmlspecialchars($subject['subject_name']) : "Edit Subject";
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

                <?php if ($subject): // Only show form if subject data was loaded ?>
                <form action="edit-subject.php?id=<?php echo $subject_id; ?>" method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="course_id" class="form-label">Course (Program)</label>
                        <select class="form-select" id="course_id" name="course_id" required>
                            <?php if (!empty($courses)): ?>
                                <?php foreach ($courses as $course): ?>
                                    <option value="<?php echo $course['id']; ?>" <?php if ($subject['course_id'] == $course['id']) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($course['course_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                 <option value="" disabled>No active courses found</option>
                            <?php endif; ?>
                        </select>
                         <div class="invalid-feedback">Please select a course.</div>
                    </div>

                    <div class="mb-3">
                        <label for="subject_name" class="form-label">Subject Name</label>
                        <input type="text" class="form-control" id="subject_name" name="subject_name" value="<?php echo htmlspecialchars($subject['subject_name']); ?>" required>
                         <div class="invalid-feedback">Please enter the subject name.</div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="subject_code" class="form-label">Subject Code</label>
                            <input type="text" class="form-control" id="subject_code" name="subject_code" value="<?php echo htmlspecialchars($subject['subject_code']); ?>" required>
                             <div class="invalid-feedback">Please enter the subject code.</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="semester" class="form-label">Semester</label>
                            <input type="number" class="form-control" id="semester" name="semester" min="1" max="12" value="<?php echo htmlspecialchars($subject['semester']); ?>" required>
                            <div class="invalid-feedback">Please enter the semester number.</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="credits" class="form-label">Credits (Optional)</label>
                            <input type="number" class="form-control" id="credits" name="credits" min="0" max="10" value="<?php echo htmlspecialchars($subject['credits'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active" <?php if ($subject['status'] == 'active') echo 'selected'; ?>>Active</option>
                            <option value="inactive" <?php if ($subject['status'] == 'inactive') echo 'selected'; ?>>Inactive</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Subject</button>
                    <a href="list-subjects.php" class="btn btn-secondary">Cancel</a>
                </form>
                <?php else: ?>
                    <div class="alert alert-danger">Could not load subject data. Please go back to the list.</div>
                     <a href="list-subjects.php" class="btn btn-secondary">Back to List</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
require_once $path_prefix . 'includes/footer.php';
?>