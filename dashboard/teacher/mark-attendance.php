<?php
// /dashboard/teacher/mark-attendance.php

// Load core files
require_once '../../config.php'; // Ensures $pdo is available
require_once '../../functions.php';
require_once '../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['teacher']); // Only teachers access this page
$user = get_session_user(); // Get logged-in teacher info
$teacher_id = $user['id'];
$page_title = "Mark Attendance";

// --- Attendance Processing (POST request) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_attendance'])) {
    // Sanitize inputs
    $subject_id = filter_input(INPUT_POST, 'subject_id', FILTER_SANITIZE_NUMBER_INT);
    $attendance_date = sanitize_input($_POST['attendance_date']);
    // Input arrays from the form: status[student_id] => 'present'/'absent', remarks[student_id] => 'text'
    $student_statuses = $_POST['status'] ?? [];
    $remarks = $_POST['remarks'] ?? [];

    // Basic Validation
    if (empty($subject_id) || empty($attendance_date) || empty($student_statuses)) {
        $_SESSION['error_message'] = "Invalid data. Subject, Date, and Student Statuses are required.";
        // Redirect back, trying to preserve subject/date if possible
        $redirect_url = 'dashboard/teacher/mark-attendance.php';
        if ($subject_id) $redirect_url .= '?subject_id=' . $subject_id;
        if ($attendance_date && $subject_id) $redirect_url .= '&date=' . $attendance_date;
        elseif ($attendance_date) $redirect_url .= '?date=' . $attendance_date;
        redirect($redirect_url);
    }

    // --- ACTUAL DATABASE UPDATE LOGIC ---
    $pdo->beginTransaction(); // Use transaction for multiple updates/inserts
    $all_success = true;
    try {
        // Prepare the reusable UPSERT statement
        // Assumes student_id + subject_id + date is UNIQUE in Attendance table
        $sql = "INSERT INTO Attendance (student_id, subject_id, date, status, teacher_id, remarks, created_at)
                VALUES (:student_id, :subject_id, :date, :status, :teacher_id, :remarks, NOW())
                ON DUPLICATE KEY UPDATE
                status = VALUES(status),
                remarks = VALUES(remarks),
                teacher_id = VALUES(teacher_id)";
        $stmt = $pdo->prepare($sql);

        // Loop through submitted student statuses
        foreach ($student_statuses as $student_id => $status) {
            $student_id_sanitized = filter_var($student_id, FILTER_SANITIZE_NUMBER_INT);
            // Validate status value
            $status_sanitized = ($status === 'present' || $status === 'absent') ? $status : 'absent'; // Default to absent if invalid
            $remark_sanitized = isset($remarks[$student_id]) ? sanitize_input($remarks[$student_id]) : '';

            if (!$student_id_sanitized) continue; // Skip if student ID is invalid

            // Execute the statement for each student
            $success = $stmt->execute([
                ':student_id' => $student_id_sanitized,
                ':subject_id' => $subject_id,
                ':date'       => $attendance_date,
                ':status'     => $status_sanitized,
                ':teacher_id' => $teacher_id,
                ':remarks'    => $remark_sanitized
            ]);

            if (!$success) {
                $all_success = false;
                log_error("Failed to update attendance for student ID {$student_id_sanitized}, subject ID {$subject_id}, date {$attendance_date}", __FILE__, __LINE__);
                break; // Stop on first error
            }
        } // End foreach student

        if ($all_success) {
            $pdo->commit();
            $_SESSION['success_message'] = "Attendance for " . format_date($attendance_date, 'M d, Y') . " saved successfully!";
        } else {
            $pdo->rollBack();
            $_SESSION['error_message'] = "Failed to save attendance for one or more students.";
        }

    } catch (PDOException $e) {
        $pdo->rollBack();
        log_error("Error saving attendance: " . $e->getMessage(), __FILE__, __LINE__);
        $_SESSION['error_message'] = "A database error occurred while saving attendance.";
    }
    // --- END ACTUAL DATABASE LOGIC ---

    // Redirect back to the same subject/date view
    redirect('dashboard/teacher/mark-attendance.php?subject_id=' . $subject_id . '&date=' . $attendance_date);
}
// --- End POST Processing ---


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
     $fetch_error_subjects = "Could not load subjects for selection.";
     $_SESSION['error_message'] = $fetch_error_subjects;
}
// --- End Fetch Subjects ---

// Get selected subject/date from URL (or defaults)
$selected_subject_id = filter_input(INPUT_GET, 'subject_id', FILTER_SANITIZE_NUMBER_INT);
if (!$selected_subject_id && !empty($subjects)) {
    $selected_subject_id = $subjects[0]['id']; // Default to first subject if none selected
}
$selected_date = sanitize_input($_GET['date'] ?? date('Y-m-d')); // Default to today

// --- Fetch students enrolled in the selected subject AND their attendance status for the selected date ---
$students = [];
$fetch_error_students = null;
if ($selected_subject_id) { // Only fetch if a subject is selected
    try {
        // Use LEFT JOIN to get all enrolled students, plus any existing attendance record for the specific date
        $sql_students = "SELECT
                            u.id, u.first_name, u.last_name,
                            a.status, a.remarks
                         FROM Users u
                         JOIN Enrollments e ON u.id = e.student_id
                         LEFT JOIN Attendance a ON u.id = a.student_id
                                              AND a.subject_id = :subject_id
                                              AND a.date = :attendance_date
                         WHERE e.subject_id = :subject_id AND e.status = 'enrolled' AND u.role = 'student'
                         ORDER BY u.last_name, u.first_name";
        $stmt_students = $pdo->prepare($sql_students);
        $stmt_students->execute([
            ':subject_id' => $selected_subject_id,
            ':attendance_date' => $selected_date
        ]);
        $students = $stmt_students->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        log_error("Error fetching students/attendance for subject ID {$selected_subject_id}, date {$selected_date}: " . $e->getMessage(), __FILE__, __LINE__);
        $fetch_error_students = "Could not load student list for the selected subject and date.";
        $_SESSION['error_message'] = $fetch_error_students;
    }
}
// --- End Fetch Students ---

// Pass correct path prefix to header/footer
$path_prefix = '../../';
require_once $path_prefix . 'includes/header.php';
?>

<div class="card shadow-sm">
    <div class="card-header">
        <h4 class="mb-0"><?php echo $page_title; ?></h4>
    </div>
    <div class="card-body">

        <?php
        display_flash_message('success_message', 'alert-success');
        display_flash_message('error_message', 'alert-danger'); // Shows fetch or save errors
        ?>

        <?php if ($fetch_error_subjects): ?>
             <div class="alert alert-warning"><?php echo htmlspecialchars($fetch_error_subjects); ?></div>
        <?php endif; ?>

        <form action="mark-attendance.php" method="GET" class="row g-3 mb-4 align-items-end">
            <div class="col-md-5">
                <label for="subject_id_select" class="form-label">Subject</label>
                <select class="form-select" id="subject_id_select" name="subject_id" onchange="this.form.submit()">
                     <option value="">Select a subject...</option>
                    <?php if (!empty($subjects)): ?>
                        <?php foreach ($subjects as $subject): ?>
                            <option value="<?php echo $subject['id']; ?>" <?php if ($subject['id'] == $selected_subject_id) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($subject['subject_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="" disabled>No subjects assigned</option>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-5">
                <label for="attendance_date_select" class="form-label">Date</label>
                <input type="date" class="form-control" id="attendance_date_select" name="date" value="<?php echo htmlspecialchars($selected_date); ?>" onchange="this.form.submit()">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Load List</button>
            </div>
        </form>

        <hr>

        <?php if ($fetch_error_students): ?>
             <div class="alert alert-danger"><?php echo htmlspecialchars($fetch_error_students); ?></div>
        <?php endif; ?>

        <?php if ($selected_subject_id && empty($fetch_error_students)): ?>
            <?php if (!empty($students)): ?>
                <form action="mark-attendance.php" method="POST">
                    <input type="hidden" name="subject_id" value="<?php echo $selected_subject_id; ?>">
                    <input type="hidden" name="attendance_date" value="<?php echo $selected_date; ?>">

                    <p>Marking attendance for: <strong><?php echo htmlspecialchars( ($subjects[array_search($selected_subject_id, array_column($subjects, 'id'))]['subject_name'] ?? 'Selected Subject') ); ?></strong> on <strong><?php echo format_date($selected_date, 'M d, Y'); ?></strong></p>

                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Student Name</th>
                                    <th width="200px">Status</th>
                                    <th>Remarks (Optional)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $student):
                                    // Default to 'present' if no record exists for this date, otherwise use fetched status
                                    $current_status = $student['status'] ?? 'present';
                                ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                                        <td>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="status[<?php echo $student['id']; ?>]" id="present-<?php echo $student['id']; ?>" value="present" <?php if ($current_status == 'present') echo 'checked'; ?>>
                                                <label class="form-check-label" for="present-<?php echo $student['id']; ?>">Present</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="status[<?php echo $student['id']; ?>]" id="absent-<?php echo $student['id']; ?>" value="absent" <?php if ($current_status == 'absent') echo 'checked'; ?>>
                                                <label class="form-check-label" for="absent-<?php echo $student['id']; ?>">Absent</label>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" name="remarks[<?php echo $student['id']; ?>]" value="<?php echo htmlspecialchars($student['remarks'] ?? ''); ?>" placeholder="e.g., Late, Excused...">
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <button type="submit" name="submit_attendance" class="btn btn-primary mt-3">Save Attendance</button>
                </form>
            <?php else: ?>
                <div class="alert alert-info">No students are currently enrolled in this subject to mark attendance.</div>
            <?php endif; ?>
        <?php elseif (!$selected_subject_id): ?>
             <div class="alert alert-info">Please select a subject to mark attendance.</div>
        <?php endif; ?>
    </div> </div> <?php
require_once $path_prefix . 'includes/footer.php';
?>