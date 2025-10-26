<?php
// /dashboard/admin/reports/marks-report.php

// Load core files
require_once '../../../config.php'; // Ensures $pdo is available
require_once '../../../functions.php';
require_once '../../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['admin']);

$page_title = "Marks & Grades Report";
$user = get_session_user();

// --- Report Generation Logic (Handles POST request) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Get filter values
    $course_id = filter_input(INPUT_POST, 'course_id', FILTER_SANITIZE_NUMBER_INT);
    $subject_id = filter_input(INPUT_POST, 'subject_id', FILTER_SANITIZE_NUMBER_INT);

    try {
        // 2. Build SQL query
        $sql = "SELECT
                    m.student_id,
                    CONCAT(u.first_name, ' ', u.last_name) AS student_name,
                    u.email AS student_email,
                    c.course_name,
                    s.subject_name,
                    m.assignment_marks,
                    m.midterm_marks,
                    m.final_marks,
                    m.total_marks,
                    m.grade,
                    CONCAT(t.first_name, ' ', t.last_name) AS teacher_name
                FROM Marks m
                JOIN Users u ON m.student_id = u.id
                JOIN Subjects s ON m.subject_id = s.id
                JOIN Courses c ON s.course_id = c.id
                LEFT JOIN Users t ON m.teacher_id = t.id
                WHERE 1=1";

        $params = [];

        // Apply filters
        if (!empty($course_id)) {
            $sql .= " AND s.course_id = ?";
            $params[] = $course_id;
        }
        if (!empty($subject_id)) {
            $sql .= " AND m.subject_id = ?";
            $params[] = $subject_id;
        }

        $sql .= " ORDER BY c.course_name, s.subject_name, u.last_name, u.first_name";

        // 3. Prepare and Execute
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 4. Set headers
        $filename = "marks_report_" . date('Y-m-d') . ".csv";
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // 5. Output CSV
        $output = fopen('php://output', 'w');
        fputcsv($output, [
            'Student ID', 'Student Name', 'Student Email', 'Course', 'Subject',
            'Assignment Marks', 'Midterm Marks', 'Final Marks', 'Total Marks', 'Grade',
            'Last Updated By'
        ]);

        if (!empty($results)) {
            foreach ($results as $row) {
                fputcsv($output, [
                    $row['student_id'],
                    $row['student_name'],
                    $row['student_email'],
                    $row['course_name'],
                    $row['subject_name'],
                    $row['assignment_marks'],
                    $row['midterm_marks'],
                    $row['final_marks'],
                    $row['total_marks'],
                    $row['grade'],
                    $row['teacher_name'] ?? 'N/A'
                ]);
            }
        } else {
             fputcsv($output, ['No marks records found matching your criteria.']);
        }

        fclose($output);
        exit;

    } catch (PDOException $e) {
        log_error("Error generating marks report: " . $e->getMessage(), __FILE__, __LINE__);
        $_SESSION['error_message'] = "A database error occurred while generating the report: " . $e->getMessage();
        redirect('dashboard/admin/reports/marks-report.php');
     } catch (Exception $e) {
         log_error("General error generating marks report: " . $e->getMessage(), __FILE__, __LINE__);
        $_SESSION['error_message'] = "An unexpected error occurred: " . $e->getMessage();
        redirect('dashboard/admin/reports/marks-report.php');
    }
}
// --- End Report Generation Logic ---


// --- Fetch data for filters (GET Request Part) ---
$courses = [];
$subjects = [];
$fetch_error = null;
try {
    $courses_stmt = $pdo->query("SELECT id, course_name FROM Courses WHERE status = 'active' ORDER BY course_name");
    if($courses_stmt) $courses = $courses_stmt->fetchAll(PDO::FETCH_ASSOC);

    $subjects_stmt = $pdo->query("SELECT id, subject_name, course_id FROM Subjects WHERE status = 'active' ORDER BY subject_name");
    if($subjects_stmt) $subjects = $subjects_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    log_error("Error fetching filter data for marks report: " . $e->getMessage(), __FILE__, __LINE__);
    $fetch_error = "Could not load filter options from the database.";
    $_SESSION['error_message'] = $fetch_error;
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
                <h4 class="mb-0"><?php echo $page_title; ?></h4>
            </div>
            <div class="card-body">
                <p>Select criteria to generate a CSV report of student marks.</p>

                 <?php
                display_flash_message('success_message', 'alert-success');
                display_flash_message('error_message', 'alert-danger');
                display_flash_message('info_message', 'alert-info');
                ?>

                <?php if ($fetch_error && $_SERVER['REQUEST_METHOD'] !== 'POST'): ?>
                    <div class="alert alert-warning">Could not load all filter options. Please check database connection.</div>
                <?php endif; ?>

                <form action="marks-report.php" method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="course_id" class="form-label">Filter by Course (Optional)</label>
                        <select class="form-select" id="course_id" name="course_id">
                             <option value="">All Courses</option>
                            <?php foreach ($courses as $course): ?>
                                <option value="<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['course_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="subject_id" class="form-label">Filter by Subject (Optional)</label>
                        <select class="form-select" id="subject_id" name="subject_id">
                            <option value="">All Subjects</option>
                            <?php foreach ($subjects as $subject): ?>
                                <option value="<?php echo $subject['id']; ?>" data-course="<?php echo $subject['course_id']; ?>"><?php echo htmlspecialchars($subject['subject_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text">Consider adding JavaScript to filter this list based on the selected course.</small>
                    </div>

                    <button type="submit" class="btn btn-primary">
                         <i class="fas fa-file-csv me-2"></i> Generate Report
                    </button>
                </form>

            </div>
        </div>
    </div>
</div>

<?php
require_once $path_prefix . 'includes/footer.php';
?>