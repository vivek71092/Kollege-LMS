<?php
// /dashboard/admin/subjects/add-subject.php

// Load core files
require_once '../../../config.php'; // Ensures $pdo is available
require_once '../../../functions.php';
require_once '../../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['admin']);

$page_title = "Add New Subject";

// --- Add Subject Processing ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $course_id = filter_input(INPUT_POST, 'course_id', FILTER_SANITIZE_NUMBER_INT);
    $subject_name = sanitize_input($_POST['subject_name']);
    $subject_code = sanitize_input($_POST['subject_code']);
    $semester = filter_input(INPUT_POST, 'semester', FILTER_SANITIZE_NUMBER_INT);
    $credits = filter_input(INPUT_POST, 'credits', FILTER_SANITIZE_NUMBER_INT, ['options' => ['default' => null]]); // Allow null credits
    $status = sanitize_input($_POST['status']);

    // Validation
    if (empty($course_id) || empty($subject_name) || empty($subject_code) || empty($semester)) {
        $_SESSION['error_message'] = "Please fill out Course, Subject Name, Code, and Semester.";
        redirect('dashboard/admin/subjects/add-subject.php');
    } else {
        // --- ACTUAL DATABASE INSERT LOGIC ---
        try {
            // Check if subject code already exists (optional but good practice)
            $stmt_check = $pdo->prepare("SELECT id FROM Subjects WHERE subject_code = ?");
            $stmt_check->execute([$subject_code]);
            if ($stmt_check->fetch()) {
                 $_SESSION['error_message'] = "Subject code '$subject_code' already exists.";
                 redirect('dashboard/admin/subjects/add-subject.php');
            }

            // Prepare the INSERT statement
            $sql = "INSERT INTO Subjects (course_id, subject_code, subject_name, semester, credits, status, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $pdo->prepare($sql);

            // Execute the statement
            $success = $stmt->execute([
                $course_id,
                $subject_code,
                $subject_name,
                $semester,
                $credits, // PDO handles null correctly if $credits is null
                $status
            ]);

            if ($success) {
                $_SESSION['success_message'] = "Subject ($subject_name) created successfully!";
                redirect('dashboard/admin/subjects/list-subjects.php');
            } else {
                $_SESSION['error_message'] = "Failed to create subject. Please try again.";
                redirect('dashboard/admin/subjects/add-subject.php');
            }
        } catch (PDOException $e) {
            log_error("Error adding subject: " . $e->getMessage(), __FILE__, __LINE__);
            $_SESSION['error_message'] = "A database error occurred while creating the subject.";
            redirect('dashboard/admin/subjects/add-subject.php');
        }
        // --- END ACTUAL DATABASE LOGIC ---
    }
}
// --- End Processing ---

// --- Fetch Courses for dropdown ---
try {
    $courses = $pdo->query("SELECT id, course_name FROM Courses WHERE status = 'active' ORDER BY course_name")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    log_error("Error fetching courses for subject form: " . $e->getMessage(), __FILE__, __LINE__);
    $courses = [];
    $_SESSION['error_message'] = "Could not load courses for selection.";
}
// --- End Fetch ---

// Pass correct path prefix to header/footer
$path_prefix = '../../../';
require_once $path_prefix . 'includes/header.php';
?>

<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <div class="card shadow-sm">
            <div class="card-header">
                <h4 class="mb-0">Add New Subject</h4>
            </div>
            <div class="card-body">

                <?php
                // Display error messages (including potential course fetch error)
                display_flash_message('error_message', 'alert-danger');
                ?>

                <form action="add-subject.php" method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="course_id" class="form-label">Course (Program)</label>
                        <select class="form-select" id="course_id" name="course_id" required>
                            <option value="">Select a parent course...</option>
                            <?php if (!empty($courses)): ?>
                                <?php foreach ($courses as $course): ?>
                                    <option value="<?php echo $course['id']; ?>">
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
                        <input type="text" class="form-control" id="subject_name" name="subject_name" placeholder="e.g., Web Development" required>
                        <div class="invalid-feedback">Please enter the subject name.</div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="subject_code" class="form-label">Subject Code</label>
                            <input type="text" class="form-control" id="subject_code" name="subject_code" placeholder="e.g., CS305" required>
                            <div class="invalid-feedback">Please enter the subject code.</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="semester" class="form-label">Semester</label>
                            <input type="number" class="form-control" id="semester" name="semester" min="1" max="12" placeholder="e.g., 3" required>
                            <div class="invalid-feedback">Please enter the semester number.</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="credits" class="form-label">Credits (Optional)</label>
                            <input type="number" class="form-control" id="credits" name="credits" min="0" max="10" placeholder="e.g., 3">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active" selected>Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Create Subject</button>
                    <a href="list-subjects.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require_once $path_prefix . 'includes/footer.php';
?>