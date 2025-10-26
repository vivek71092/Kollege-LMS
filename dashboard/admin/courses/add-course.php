ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
<?php
// /dashboard/admin/courses/add-course.php

// Load core files
require_once '../../../config.php'; // Ensures $pdo is available
require_once '../../../functions.php';
require_once '../../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['admin']);

$page_title = "Add New Course";

// --- Add Course Processing ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // --- Add this line for debugging ---
    // echo "<pre>POST Data: "; var_dump($_POST); echo "</pre>";

    $course_name = sanitize_input($_POST['course_name']);
    $course_code = sanitize_input($_POST['course_code']);
    $semester = filter_input(INPUT_POST, 'semester', FILTER_SANITIZE_NUMBER_INT);
    $description = sanitize_input($_POST['description']);
    $status = sanitize_input($_POST['status']);

    // Validation
    if (empty($course_name) || empty($course_code) || empty($semester)) {
        $_SESSION['error_message'] = "Please fill out Course Name, Code, and Semesters.";
        // --- Modify redirect for debugging if needed ---
        // echo "Validation Failed. Redirecting..."; die();
        redirect('dashboard/admin/courses/add-course.php');
    } else {
        // --- ACTUAL DATABASE LOGIC ---
        try {
            // Check if code already exists
            $stmt_check = $pdo->prepare("SELECT id FROM Courses WHERE course_code = ?");
            $stmt_check->execute([$course_code]);
            if ($stmt_check->fetch()) {
                 $_SESSION['error_message'] = "Course code '$course_code' already exists.";
                 redirect('dashboard/admin/courses/add-course.php');
            }

            // Prepare the INSERT statement
            $sql = "INSERT INTO Courses (course_code, course_name, description, semester, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
            $stmt = $pdo->prepare($sql);

            // Execute the statement
            $success = $stmt->execute([$course_code, $course_name, $description, $semester, $status]);

            // --- Add this line for debugging ---
            // echo "<pre>Insert Success: "; var_dump($success); echo "</pre>";
            // echo "<pre>Rows Affected: "; var_dump($stmt->rowCount()); echo "</pre>";


            if ($success && $stmt->rowCount() > 0) { // Check if rows were actually affected
                $_SESSION['success_message'] = "Course ($course_name) created successfully!";
                // --- Add delay for debugging if needed ---
                // echo "Success! Redirecting in 3 seconds...";
                // flush(); // Force output
                // sleep(3);
                redirect('dashboard/admin/courses/list-courses.php');
            } else {
                $_SESSION['error_message'] = "Failed to create course (Database reported no changes). Please try again.";
                 // --- Add this line for debugging ---
                // echo "<pre>Error Info: "; var_dump($stmt->errorInfo()); echo "</pre>"; die();
                redirect('dashboard/admin/courses/add-course.php');
            }
        } catch (PDOException $e) {
            // Log the detailed error and show a generic message
            log_error($e->getMessage(), __FILE__, __LINE__);
             // --- Add this line for debugging ---
            // echo "<pre>Database Exception: "; var_dump($e->getMessage()); echo "</pre>"; die();
            $_SESSION['error_message'] = "A database error occurred while creating the course.";
            redirect('dashboard/admin/courses/add-course.php');
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
                <h4 class="mb-0">Add New Course/Program</h4>
            </div>
            <div class="card-body">

                <?php
                display_flash_message('error_message', 'alert-danger');
                ?>

                <form action="add-course.php" method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="course_name" class="form-label">Course Name</label>
                        <input type="text" class="form-control" id="course_name" name="course_name" placeholder="e.g., Computer Science" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="course_code" class="form-label">Course Code</label>
                            <input type="text" class="form-control" id="course_code" name="course_code" placeholder="e.g., CS" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="semester" class="form-label">Total Semesters</label>
                            <input type="number" class="form-control" id="semester" name="semester" min="1" max="10" placeholder="e.g., 8" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description (Optional)</label>
                        <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active" selected>Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Create Course</button>
                    <a href="list-courses.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require_once $path_prefix . 'includes/footer.php';
?>