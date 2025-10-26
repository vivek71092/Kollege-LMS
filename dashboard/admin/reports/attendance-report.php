<?php
// /dashboard/admin/reports/attendance-report.php

// Load core files
require_once '../../../config.php'; // Ensures $pdo is available
require_once '../../../functions.php';
require_once '../../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['admin']);

$page_title = "Attendance Report";
$user = get_session_user();

// --- Report Generation Logic (Handles POST request) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Get filter values from $_POST
    $start_date = sanitize_input($_POST['start_date'] ?? null);
    $end_date = sanitize_input($_POST['end_date'] ?? null);
    $course_id = filter_input(INPUT_POST, 'course_id', FILTER_SANITIZE_NUMBER_INT);
    $subject_id = filter_input(INPUT_POST, 'subject_id', FILTER_SANITIZE_NUMBER_INT);
    $status_filter = sanitize_input($_POST['status'] ?? null);

    try {
        // 2. Build SQL query dynamically
        $sql = "SELECT
                    a.date,
                    u.id AS student_id,
                    CONCAT(u.first_name, ' ', u.last_name) AS student_name,
                    s.subject_name,
                    c.course_name,
                    a.status,
                    a.remarks,
                    CONCAT(t.first_name, ' ', t.last_name) AS teacher_name
                FROM Attendance a
                JOIN Users u ON a.student_id = u.id
                JOIN Subjects s ON a.subject_id = s.id
                JOIN Courses c ON s.course_id = c.id
                LEFT JOIN Users t ON a.teacher_id = t.id
                WHERE 1=1"; // Start condition

        $params = []; // Parameters for prepared statement

        if (!empty($start_date)) {
            $sql .= " AND a.date >= ?";
            $params[] = $start_date;
        }
        if (!empty($end_date)) {
            $sql .= " AND a.date <= ?";
            $params[] = $end_date;
        }
        if (!empty($course_id)) {
            $sql .= " AND s.course_id = ?";
            $params[] = $course_id;
        }
        if (!empty($subject_id)) {
            $sql .= " AND a.subject_id = ?";
            $params[] = $subject_id;
        }
        if (!empty($status_filter)) {
            $sql .= " AND a.status = ?";
            $params[] = $status_filter;
        }

        $sql .= " ORDER BY a.date DESC, c.course_name, s.subject_name, u.last_name";

        // 3. Prepare and Execute query
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 4. Set headers for CSV download
        $filename = "attendance_report_" . date('Y-m-d') . ".csv";
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache'); // Prevent caching
        header('Expires: 0');

        // 5. Open output stream and write CSV data
        $output = fopen('php://output', 'w');

        // 6. Write header row
        fputcsv($output, ['Date', 'Student ID', 'Student Name', 'Course', 'Subject', 'Status', 'Remarks', 'Marked By']);

        // 7. Loop through results and write each row
        if (!empty($results)) {
            foreach ($results as $row) {
                fputcsv($output, [
                    $row['date'],
                    $row['student_id'],
                    $row['student_name'],
                    $row['course_name'],
                    $row['subject_name'],
                    ucfirst($row['status']), // Capitalize status
                    $row['remarks'],
                    $row['teacher_name'] ?? 'N/A'
                ]);
            }
        } else {
             fputcsv($output, ['No attendance records found matching your criteria.']);
        }

        // 8. Close the output stream
        fclose($output);

        // 9. Stop script execution after sending file
        exit;

    } catch (PDOException $e) {
        log_error("Error generating attendance report: " . $e->getMessage(), __FILE__, __LINE__);
        $_SESSION['error_message'] = "A database error occurred while generating the report: " . $e->getMessage();
        redirect('dashboard/admin/reports/attendance-report.php');
    } catch (Exception $e) {
         log_error("General error generating attendance report: " . $e->getMessage(), __FILE__, __LINE__);
        $_SESSION['error_message'] = "An unexpected error occurred: " . $e->getMessage();
        redirect('dashboard/admin/reports/attendance-report.php');
    }
}
// --- End Report Generation Logic ---


// --- Fetch data for filters (GET Request Part) ---
$courses = [];
$subjects = [];
$fetch_error = null;
try {
    $courses_stmt = $pdo->query("SELECT id, course_name FROM Courses WHERE status = 'active' ORDER BY course_name");
    if ($courses_stmt) $courses = $courses_stmt->fetchAll(PDO::FETCH_ASSOC);

    $subjects_stmt = $pdo->query("SELECT id, subject_name FROM Subjects WHERE status = 'active' ORDER BY subject_name");
    if ($subjects_stmt) $subjects = $subjects_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    log_error("Error fetching filter data for attendance report: " . $e->getMessage(), __FILE__, __LINE__);
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
                <p>Select your criteria to generate a CSV report of attendance records.</p>

                <?php
                display_flash_message('success_message', 'alert-success');
                display_flash_message('error_message', 'alert-danger');
                display_flash_message('info_message', 'alert-info');
                ?>

                <?php if ($fetch_error && $_SERVER['REQUEST_METHOD'] !== 'POST'): ?>
                    <div class="alert alert-warning">Could not load all filter options. Please check database connection.</div>
                <?php endif; ?>

                <form action="attendance-report.php" method="POST" class="needs-validation" novalidate>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Start Date (Optional)</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo date('Y-m-01'); // Default to start of month ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">End Date (Optional)</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo date('Y-m-d'); // Default to today ?>">
                        </div>
                    </div>
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
                                <option value="<?php echo $subject['id']; ?>"><?php echo htmlspecialchars($subject['subject_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Filter by Status (Optional)</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All (Present & Absent)</option>
                            <option value="present">Present Only</option>
                            <option value="absent">Absent Only</option>
                        </select>
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