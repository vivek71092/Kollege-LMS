<?php
// /dashboard/student/marks.php

// Load core files
require_once '../../config.php'; // Ensures $pdo is available
require_once '../../functions.php';
require_once '../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['student']);

$page_title = "My Marks & Grades";
$user = get_session_user();
$student_id = $user['id'];

// --- Fetch Marks Data ---
$marks_data = []; // Initialize
$fetch_error = null;
try {
    // Query the Marks table, joining Subjects for the name
    $sql = "SELECT
                m.assignment_marks, m.midterm_marks, m.final_marks, m.total_marks, m.grade,
                s.subject_name
            FROM Marks m
            JOIN Subjects s ON m.subject_id = s.id
            WHERE m.student_id = ?
            ORDER BY s.subject_name"; // Order alphabetically by subject

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$student_id]);
    $marks_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    log_error("Error fetching marks for student ID {$student_id}: " . $e->getMessage(), __FILE__, __LINE__);
    $fetch_error = "Could not fetch your marks list from the database.";
    $_SESSION['error_message'] = $fetch_error;
}
// --- End Data Fetching ---

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
        display_flash_message('error_message', 'alert-danger');
        ?>

        <?php if ($fetch_error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($fetch_error); ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-hover align-middle data-table" id="marksTable">
                <thead class="table-light">
                    <tr>
                        <th>Subject</th>
                        <th>Assignment Marks</th>
                        <th>Midterm Marks</th>
                        <th>Final Marks</th>
                        <th>Total Marks</th>
                        <th>Grade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($marks_data)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">Your marks have not been published yet for any subject.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($marks_data as $mark): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($mark['subject_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($mark['assignment_marks'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($mark['midterm_marks'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($mark['final_marks'] ?? 'N/A'); ?></td>
                                <td><strong><?php echo htmlspecialchars($mark['total_marks'] ?? 'N/A'); ?></strong></td>
                                <td>
                                    <?php if (!empty($mark['grade'])): ?>
                                        <span class="badge bg-primary fs-6"><?php echo htmlspecialchars($mark['grade']); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div> </div> </div> <?php
require_once $path_prefix . 'includes/footer.php';
?>