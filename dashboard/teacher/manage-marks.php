<?php
// /dashboard/teacher/manage-marks.php

// Load core files
require_once '../../config.php'; // Ensures $pdo is available
require_once '../../functions.php';
require_once '../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['teacher']); // Only teachers access this page
$user = get_session_user(); // Get logged-in teacher info
$teacher_id = $user['id'];
$page_title = "Manage Marks";

// --- Marks Update Processing (POST request) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_marks'])) {
    $subject_id = filter_input(INPUT_POST, 'subject_id', FILTER_SANITIZE_NUMBER_INT);
    $student_marks_input = $_POST['marks'] ?? []; // Array [student_id => ['assignment' => val, 'midterm' => val, 'final' => val]]

    if (empty($subject_id) || empty($student_marks_input)) {
        $_SESSION['error_message'] = "Invalid data submitted. Subject ID or student marks missing.";
        redirect('dashboard/teacher/manage-marks.php' . ($subject_id ? '?subject_id=' . $subject_id : ''));
    }

    // --- ACTUAL DATABASE UPDATE LOGIC ---
    $pdo->beginTransaction(); // Use transaction for multiple updates
    $all_success = true;
    try {
        // Prepare the reusable UPSERT statement
        // Assumes student_id + subject_id is UNIQUE in Marks table
        $sql = "INSERT INTO Marks (student_id, subject_id, assignment_marks, midterm_marks, final_marks, total_marks, grade, teacher_id, created_at, updated_at)
                VALUES (:student_id, :subject_id, :assignment, :midterm, :final, :total, :grade, :teacher_id, NOW(), NOW())
                ON DUPLICATE KEY UPDATE
                assignment_marks = VALUES(assignment_marks),
                midterm_marks = VALUES(midterm_marks),
                final_marks = VALUES(final_marks),
                total_marks = VALUES(total_marks),
                grade = VALUES(grade),
                teacher_id = VALUES(teacher_id),
                updated_at = NOW()";
        $stmt = $pdo->prepare($sql);

        foreach ($student_marks_input as $student_id => $marks) {
            $student_id_sanitized = filter_var($student_id, FILTER_SANITIZE_NUMBER_INT);
            if (!$student_id_sanitized) continue; // Skip if student ID is invalid

            // Sanitize and convert empty strings to null for database
            $assignment = (!empty($marks['assignment']) || $marks['assignment']==='0') ? filter_var($marks['assignment'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : null;
            $midterm = (!empty($marks['midterm']) || $marks['midterm']==='0') ? filter_var($marks['midterm'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : null;
            $final = (!empty($marks['final']) || $marks['final']==='0') ? filter_var($marks['final'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : null;

            // Recalculate total and grade (Use a helper function if logic is complex)
            $total = ($assignment ?? 0) + ($midterm ?? 0) + ($final ?? 0);
            $grade = calculateGrade($total); // Assuming calculateGrade function exists or create one

            // Bind parameters and execute
            $success = $stmt->execute([
                ':student_id' => $student_id_sanitized,
                ':subject_id' => $subject_id,
                ':assignment' => $assignment,
                ':midterm'    => $midterm,
                ':final'      => $final,
                ':total'      => $total,
                ':grade'      => $grade,
                ':teacher_id' => $teacher_id
            ]);

            if (!$success) {
                $all_success = false;
                log_error("Failed to update marks for student ID {$student_id_sanitized}, subject ID {$subject_id}", __FILE__, __LINE__);
                break; // Stop on first error
            }
        } // End foreach student

        if ($all_success) {
            $pdo->commit();
            $_SESSION['success_message'] = "Marks saved successfully!";
        } else {
            $pdo->rollBack();
            $_SESSION['error_message'] = "Failed to save marks for one or more students.";
        }

    } catch (PDOException $e) {
        $pdo->rollBack();
        log_error("Error saving marks: " . $e->getMessage(), __FILE__, __LINE__);
        $_SESSION['error_message'] = "A database error occurred while saving marks.";
    }
    // --- END ACTUAL DATABASE LOGIC ---

    redirect('dashboard/teacher/manage-marks.php?subject_id=' . $subject_id); // Redirect back to the same subject view
}
// --- End POST Processing ---

// Helper function for grade calculation (can be moved to functions.php)
if (!function_exists('calculateGrade')) {
    function calculateGrade($total) {
        if ($total === null) return null;
        if ($total >= 90) return 'A+';
        if ($total >= 85) return 'A';
        if ($total >= 80) return 'A-';
        if ($total >= 75) return 'B+';
        if ($total >= 70) return 'B';
        if ($total >= 65) return 'B-';
        if ($total >= 60) return 'C+';
        if ($total >= 50) return 'C';
        if ($total >= 40) return 'D';
        return 'F';
    }
}


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

// Get selected subject ID from URL or default to the first one if available
$selected_subject_id = filter_input(INPUT_GET, 'subject_id', FILTER_SANITIZE_NUMBER_INT);
if (!$selected_subject_id && !empty($subjects)) {
    $selected_subject_id = $subjects[0]['id']; // Default to first subject
}

// --- Fetch students and their current marks for the selected subject ---
$students = [];
$fetch_error_students = null;
if ($selected_subject_id) { // Only fetch if a subject is selected
    try {
        $sql_students = "SELECT
                            u.id, u.first_name, u.last_name,
                            m.assignment_marks, m.midterm_marks, m.final_marks, m.total_marks, m.grade
                         FROM Users u
                         JOIN Enrollments e ON u.id = e.student_id
                         LEFT JOIN Marks m ON u.id = m.student_id AND m.subject_id = :subject_id
                         WHERE e.subject_id = :subject_id AND e.status = 'enrolled' AND u.role = 'student'
                         ORDER BY u.last_name, u.first_name";
        $stmt_students = $pdo->prepare($sql_students);
        $stmt_students->execute([':subject_id' => $selected_subject_id]);
        $students = $stmt_students->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        log_error("Error fetching students/marks for subject ID {$selected_subject_id}: " . $e->getMessage(), __FILE__, __LINE__);
        $fetch_error_students = "Could not load student list for the selected subject.";
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

        <form action="manage-marks.php" method="GET" class="row g-3 mb-4 align-items-end">
            <div class="col-md-6">
                <label for="subject_id_select" class="form-label">Select Subject</label>
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
            <div class="col-md-6">
                <button type="submit" class="btn btn-primary">Load Student List</button>
            </div>
        </form>

        <hr>

        <?php if ($fetch_error_students): ?>
             <div class="alert alert-danger"><?php echo htmlspecialchars($fetch_error_students); ?></div>
        <?php endif; ?>

        <?php if ($selected_subject_id && empty($fetch_error_students)): ?>
            <?php if (!empty($students)): ?>
                <form action="manage-marks.php?subject_id=<?php echo $selected_subject_id; ?>" method="POST">
                    <input type="hidden" name="subject_id" value="<?php echo $selected_subject_id; ?>">

                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Student Name</th>
                                    <th>Assignment Marks</th>
                                    <th>Midterm Marks</th>
                                    <th>Final Marks</th>
                                    <th>Total (Auto)</th>
                                    <th>Grade (Auto)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $student): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control form-control-sm"
                                                   name="marks[<?php echo $student['id']; ?>][assignment]"
                                                   value="<?php echo htmlspecialchars($student['assignment_marks'] ?? ''); ?>"
                                                   placeholder="N/A">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control form-control-sm"
                                                   name="marks[<?php echo $student['id']; ?>][midterm]"
                                                   value="<?php echo htmlspecialchars($student['midterm_marks'] ?? ''); ?>"
                                                   placeholder="N/A">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control form-control-sm"
                                                   name="marks[<?php echo $student['id']; ?>][final]"
                                                   value="<?php echo htmlspecialchars($student['final_marks'] ?? ''); ?>"
                                                   placeholder="N/A">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm"
                                                   value="<?php echo htmlspecialchars($student['total_marks'] ?? 'N/A'); ?>" readonly disabled>
                                        </td>
                                         <td>
                                             <input type="text" class="form-control form-control-sm"
                                                   value="<?php echo htmlspecialchars($student['grade'] ?? 'N/A'); ?>" readonly disabled>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <button type="submit" name="submit_marks" class="btn btn-primary mt-3">Save All Marks</button>
                </form>
            <?php else: ?>
                <div class="alert alert-info">No students are currently enrolled in this subject.</div>
            <?php endif; ?>
        <?php elseif (!$selected_subject_id): ?>
             <div class="alert alert-info">Please select a subject to manage marks.</div>
        <?php endif; ?>
    </div> </div> <?php
require_once $path_prefix . 'includes/footer.php';
?>