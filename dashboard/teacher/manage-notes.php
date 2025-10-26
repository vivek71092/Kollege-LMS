<?php
// /dashboard/teacher/manage-notes.php

// Load core files
require_once '../../config.php'; // Ensures $pdo is available
require_once '../../functions.php';
require_once '../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['teacher']); // Only teachers access this specific page

$page_title = "Manage My Notes";
$user = get_session_user(); // Get logged-in teacher info
$teacher_id = $user['id'];

// --- Fetch Notes Uploaded by This Teacher ---
$notes = []; // Initialize as empty array
$fetch_error = null; // Variable to store fetch error message
try {
    // Query to fetch notes uploaded by the current teacher, joining with Subjects
    $sql = "SELECT n.id, n.title, n.upload_date, s.subject_name, n.file_path
            FROM Notes n
            JOIN Subjects s ON n.subject_id = s.id
            WHERE n.uploaded_by = ?
            ORDER BY n.upload_date DESC"; // Show newest first

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$teacher_id]);
    $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    log_error("Error fetching notes for teacher ID {$teacher_id}: " . $e->getMessage(), __FILE__, __LINE__);
    $fetch_error = "Could not fetch your notes list from the database.";
    $_SESSION['error_message'] = $fetch_error; // Set session error
}
// --- End Data Fetching ---

// Pass correct path prefix to header/footer
$path_prefix = '../../';
require_once $path_prefix . 'includes/header.php';
?>

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">My Uploaded Notes</h4>
        <a href="upload-notes.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Upload New Note
        </a>
    </div>
    <div class="card-body">

        <?php
        // Display any success or error messages (including fetch error)
        display_flash_message('success_message', 'alert-success');
        display_flash_message('error_message', 'alert-danger');
        ?>

        <?php if ($fetch_error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($fetch_error); ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-hover align-middle data-table" id="notesTable">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Subject</th>
                        <th>Uploaded On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($notes)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">You haven't uploaded any notes yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($notes as $note): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($note['title']); ?></td>
                                <td><?php echo htmlspecialchars($note['subject_name']); ?></td>
                                <td><?php echo format_date($note['upload_date']); ?></td>
                                <td class="actions-cell">
                                    <a href="<?php echo BASE_URL . $note['file_path']; ?>" class="btn btn-sm btn-outline-success" target="_blank" download title="Download Note">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-outline-primary disabled" title="Edit Note (Coming Soon)">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Delete Note"
                                            data-bs-toggle="modal" data-bs-target="#confirmModal"
                                            data-title="Delete Note Confirmation"
                                            data-body="Are you sure you want to delete the note: '<?php echo htmlspecialchars($note['title']); ?>'?"
                                            data-confirm-url="<?php echo BASE_URL; ?>api/notes/delete-note.php?id=<?php echo $note['id']; ?>"> <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div> </div> </div> <?php
// Necessary JS files (confirm-modal.js, DataTables init in dashboard/admin.js) are loaded via the footer
require_once $path_prefix . 'includes/footer.php';
?>