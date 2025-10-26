<?php
// /dashboard/student/schedule.php

// Load core files
require_once '../../config.php'; // Ensures $pdo is available
require_once '../../functions.php';
require_once '../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['student']);

$page_title = "My Class Schedule";
$user = get_session_user();
$student_id = $user['id'];

// --- Fetch Student's Schedule ---
$schedule_data = []; // Initialize
$fetch_error = null;
try {
    // Query ClassSchedule joined with Subjects, filtered by subjects the student is enrolled in
    $sql = "SELECT
                cs.day_of_week, cs.start_time, cs.end_time, cs.classroom,
                s.subject_name,
                CONCAT(u.first_name, ' ', u.last_name) AS teacher_name
            FROM ClassSchedule cs
            JOIN Subjects s ON cs.subject_id = s.id
            JOIN Enrollments e ON s.id = e.subject_id
            LEFT JOIN Users u ON cs.teacher_id = u.id -- Join Users table for teacher name
            WHERE e.student_id = ? AND e.status = 'enrolled'
            ORDER BY FIELD(cs.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), cs.start_time ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$student_id]);
    $schedule_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Group schedule by day
    $schedule_by_day = [];
    foreach ($schedule_data as $class) {
        $schedule_by_day[$class['day_of_week']][] = $class;
    }

} catch (PDOException $e) {
    log_error("Error fetching schedule for student ID {$student_id}: " . $e->getMessage(), __FILE__, __LINE__);
    $fetch_error = "Could not fetch your schedule from the database.";
    $_SESSION['error_message'] = $fetch_error;
    $schedule_by_day = []; // Ensure empty on error
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
            <table class="table table-bordered table-striped text-center">
                <thead class="table-light">
                    <tr>
                        <th style="width: 15%;">Day</th>
                        <th style="width: 25%;">Time</th>
                        <th>Subject</th>
                        <th>Teacher</th>
                        <th>Classroom</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $days_ordered = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                    $has_schedule = false; // Flag

                    foreach ($days_ordered as $day):
                        if (isset($schedule_by_day[$day]) && !empty($schedule_by_day[$day])):
                            $has_schedule = true;
                            $day_classes = $schedule_by_day[$day];
                            $rowspan = count($day_classes);
                            $first_class = true;

                            foreach ($day_classes as $class):
                                $start = format_date('1970-01-01 ' . $class['start_time'], 'h:i A');
                                $end = format_date('1970-01-01 ' . $class['end_time'], 'h:i A');
                    ?>
                                <tr>
                                    <?php if ($first_class): ?>
                                        <td class="fw-bold align-middle" rowspan="<?php echo $rowspan; ?>">
                                            <?php echo htmlspecialchars($day); ?>
                                        </td>
                                        <?php $first_class = false; ?>
                                    <?php endif; ?>
                                    <td><?php echo $start; ?> - <?php echo $end; ?></td>
                                    <td><?php echo htmlspecialchars($class['subject_name']); ?></td>
                                    <td><?php echo htmlspecialchars($class['teacher_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($class['classroom'] ?? 'N/A'); ?></td>
                                </tr>
                            <?php
                            endforeach; // end foreach class
                        endif; // end if day has classes
                    endforeach; // end foreach day

                    // Display message if no schedule data was found
                    if (!$has_schedule && empty($fetch_error)):
                    ?>
                        <tr>
                            <td colspan="5" class="text-muted text-center py-4">Your schedule is not available or no classes are scheduled for your enrolled subjects.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div> </div> </div> <?php
require_once $path_prefix . 'includes/footer.php';
?>