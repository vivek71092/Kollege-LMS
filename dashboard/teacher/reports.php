<?php
// /dashboard/teacher/reports.php

// Load core files
require_once '../../config.php'; // Ensures $pdo is available
require_once '../../functions.php';
require_once '../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['teacher']); // Only teachers access this page
$user = get_session_user(); // Get logged-in teacher info
$teacher_id = $user['id'];
$page_title = "Generate Reports";

// --- Report Generation Logic (Handles POST request) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_report'])) {
    $report_type = sanitize_input($_POST['report_type'] ?? '');
    $subject_id = filter_input(INPUT_POST, 'subject_id', FILTER_SANITIZE_NUMBER_INT);
    $start_date = sanitize_input($_POST['start_date'] ?? null); // For attendance
    $end_date = sanitize_input($_POST['end_date'] ?? null); // For attendance

    // --- Validate Subject ID (Crucial for security/scoping) ---
    if (empty($subject_id)) {
        $_SESSION['error_message'] = "Please select a subject for the report.";
        redirect('dashboard/teacher/reports.php');
    }
    // Verify the teacher is actually assigned to this subject (important check!)
    try {
        $auth_check_sql = "SELECT EXISTS (
                            SELECT 1 FROM Subjects s JOIN Courses c ON s.course_id = c.id WHERE s.id = :subject_id AND c.teacher_id = :teacher_id
                            UNION
                            SELECT 1 FROM ClassSchedule cs WHERE cs.subject_id = :subject_id AND cs.teacher_id = :teacher_id
                           )";
        $auth_stmt = $pdo->prepare($auth_check_sql);
        $auth_stmt->execute([':subject_id' => $subject_id, ':teacher_id' => $teacher_id]);
        if (!$auth_stmt->fetchColumn()) {
            $_SESSION['error_message'] = "You are not authorized to generate reports for this subject.";
            redirect('dashboard/teacher/reports.php');
        }
    } catch (PDOException $e) {
         log_error("Error verifying teacher subject access: " . $e->getMessage(), __FILE__, __LINE__);
         $_SESSION['error_message'] = "Database error verifying subject access.";
         redirect('dashboard/teacher/reports.php');
    }
    // --- End Subject Validation ---


    // --- Generate Specific Report ---
    try {
        $results = [];
        $headers = [];
        $filename = "report_" . date('Y-m-d') . ".csv"; // Default filename

        if ($report_type === 'attendance') {
            // --- ATTENDANCE REPORT ---
            $filename = "attendance_report_subject_" . $subject_id . "_" . date('Y-m-d') . ".csv";
            $headers = ['Date', 'Student ID', 'Student Name', 'Status', 'Remarks'];

            $sql = "SELECT a.date, u.id AS student_id, CONCAT(u.first_name, ' ', u.last_name) AS student_name, a.status, a.remarks
                    FROM Attendance a
                    JOIN Users u ON a.student_id = u.id
                    WHERE a.subject_id = ? AND a.teacher_id = ?"; // Filter by subject AND teacher who marked it
            $params = [$subject_id, $teacher_id];

            if (!empty($start_date)) {
                $sql .= " AND a.date >= ?";
                $params[] = $start_date;
            }
            if (!empty($end_date)) {
                $sql .= " AND a.date <= ?";
                $params[] = $end_date;
            }
            $sql .= " ORDER BY a.date DESC, u.last_name";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } elseif ($report_type === 'marks') {
            // --- MARKS REPORT ---
            $filename = "marks_report_subject_" . $subject_id . "_" . date('Y-m-d') . ".csv";
            $headers = ['Student ID', 'Student Name', 'Assignment Marks', 'Midterm Marks', 'Final Marks', 'Total Marks', 'Grade'];

            $sql = "SELECT m.student_id, CONCAT(u.first_name, ' ', u.last_name) AS student_name,
                           m.assignment_marks, m.midterm_marks, m.final_marks, m.total_marks, m.grade
                    FROM Marks m
                    JOIN Users u ON m.student_id = u.id
                    JOIN Enrollments e ON m.student_id = e.student_id AND m.subject_id = e.subject_id
                    WHERE m.subject_id = ? AND e.status = 'enrolled'"; // Get marks for currently enrolled students in this subject
                    // Optional: Add AND m.teacher_id = ? if you only want marks entered by THIS teacher
            $sql .= " ORDER BY u.last_name, u.first_name";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([$subject_id]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } else {
            $_SESSION['error_message'] = "Invalid report type requested.";
            redirect('dashboard/teacher/reports.php');
        }

        // --- Output CSV ---
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        fputcsv($output, $headers); // Write header row

        if (!empty($results)) {
            foreach ($results as $row) {
                // We need to ensure the order matches the $headers array
                $ordered_row = [];
                foreach ($headers as $header) {
                    // Convert header to snake_case key (approximate)
                    $key = strtolower(str_replace(' ', '_', $header));
                    // Handle specific key differences
                    if ($key === 'student_name') $key = 'student_name'; // Already correct
                    if ($key === 'marked_by') $key = 'teacher_name';
                    // Add more mappings if needed
                    $ordered_row[] = $row[$key] ?? ''; // Use ?? '' for safety
                }
                 fputcsv($output, $ordered_row);
            }
        } else {
             fputcsv($output, ['No data found matching your criteria for this report.']);
        }

        fclose($output);
        exit; // Stop execution after sending file

    } catch (PDOException $e) {
        log_error("Error generating teacher report ({$report_type}): " . $e->getMessage(), __FILE__, __LINE__);
        $_SESSION['error_message'] = "Database error generating report: " . $e->getMessage();
        redirect('dashboard/teacher/reports.php');
     } catch (Exception $e) {
         log_error("General error generating teacher report ({$report_type}): " . $e->getMessage(), __FILE__, __LINE__);
        $_SESSION['error_message'] = "Unexpected error generating report: " . $e->getMessage();
        redirect('dashboard/teacher/reports.php');
    }
}
// --- End Report Generation Logic ---

// --- Fetch teacher's subjects for dropdown (GET Request Part) ---
$subjects = [];
$fetch_error_subjects = null;
try {
     $sql_subjects = "SELECT DISTINCT s.id, s.subject_name
                      FROM Subjects s
                      JOIN Courses c ON s.course_id = c.id
                      WHERE c.teacher_id = ?
                         OR s.id IN (SELECT DISTINCT cs.subject_id FROM ClassSchedule cs WHERE cs.teacher_id = ?)
                      ORDER BY s.subject_name";
    $stmt_subjects = $pdo->prepare($sql_subjects);
    $stmt_subjects->execute([$teacher_id, $teacher_id]);
    $subjects = $stmt_subjects->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
     log_error("Error fetching subjects for teacher ID {$teacher_id}: " . $e->getMessage(), __FILE__, __LINE__);
     $fetch_error_subjects = "Could not load your subjects for selection.";
     $_SESSION['error_message'] = $fetch_error_subjects;
}
// --- End Fetch Subjects ---

// Pass correct path prefix to header/footer
$path_prefix = '../../';
require_once $path_prefix . 'includes/header.php';
?>

<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h4 class="mb-0"><?php echo $page_title; ?></h4>
            </div>
            <div class="card-body">
                <p>Select a report type and subject to generate a downloadable CSV file.</p>

                <?php
                display_flash_message('success_message', 'alert-success');
                display_flash_message('error_message', 'alert-danger');
                display_flash_message('info_message', 'alert-info');
                ?>

                <?php if ($fetch_error_subjects && $_SERVER['REQUEST_METHOD'] !== 'POST'): ?>
                    <div class="alert alert-warning"><?php echo htmlspecialchars($fetch_error_subjects); ?></div>
                <?php endif; ?>

                <form action="reports.php" method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="report_type" class="form-label">Report Type</label>
                        <select class="form-select" id="report_type" name="report_type" required>
                            <option value="">Select a report...</option>
                            <option value="attendance">Attendance Report</option>
                            <option value="marks">Marks/Grades Report</option>
                        </select>
                         <div class="invalid-feedback">Please select a report type.</div>
                    </div>

                    <div class="mb-3">
                        <label for="subject_id" class="form-label">Subject</label>
                        <select class="form-select" id="subject_id" name="subject_id" required>
                            <option value="">Select a subject...</option>
                             <?php if (!empty($subjects)): ?>
                                <?php foreach ($subjects as $subject): ?>
                                    <option value="<?php echo $subject['id']; ?>">
                                        <?php echo htmlspecialchars($subject['subject_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled>No subjects assigned to you</option>
                            <?php endif; ?>
                        </select>
                         <div class="invalid-feedback">Please select a subject.</div>
                    </div>

                    <div id="attendanceFilters" style="display: none;"> <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">Start Date (Optional)</label>
                                <input type="date" class="form-control" id="start_date" name="start_date">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">End Date (Optional)</label>
                                <input type="date" class="form-control" id="end_date" name="end_date">
                            </div>
                        </div>
                    </div>

                    <button type="submit" name="generate_report" class="btn btn-primary">
                        <i class="fas fa-file-csv me-2"></i> Generate Report
                    </button>
                    <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                </form>

                 <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const reportTypeSelect = document.getElementById('report_type');
                        const attendanceFilters = document.getElementById('attendanceFilters');

                        function toggleDateFilters() {
                            if (reportTypeSelect.value === 'attendance') {
                                attendanceFilters.style.display = 'block';
                            } else {
                                attendanceFilters.style.display = 'none';
                            }
                        }
                        reportTypeSelect.addEventListener('change', toggleDateFilters);
                        toggleDateFilters(); // Run on page load
                    });
                </script>

            </div> </div> </div> </div> <?php
require_once $path_prefix . 'includes/footer.php';
?>