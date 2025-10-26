<?php
// /pages/announcements.php

require_once '../config.php';
require_once '../functions.php';

$page_title = "News & Announcements";
require_once '../includes/header.php';

// --- Fetch Announcements ---
// In a real application, you would fetch this from the database.
// $stmt = $pdo->query("SELECT * FROM Announcements WHERE status = 'published' ORDER BY created_date DESC");
// $announcements = $stmt->fetchAll();

// For this template, we'll use static placeholder data.
$announcements = [
    [
        'id' => 1,
        'title' => 'Midterm Exams Schedule Announced',
        'description' => 'The schedule for the upcoming midterm exams has been finalized. All students are advised to check their respective course pages for detailed timings. Good luck to everyone!',
        'created_date' => '2025-10-20 10:00:00',
        'image' => 'public/images/placeholders/announcement-1.jpg'
    ],
    [
        'id' => 2,
        'title' => 'Guest Lecture on "AI in Web Development"',
        'description' => 'We are excited to host a guest lecture by Dr. Alan Smith, a leading researcher in AI. The lecture will be held in the main auditorium on October 28th at 2:00 PM. All students are encouraged to attend.',
        'created_date' => '2025-10-18 15:30:00',
        'image' => 'public/images/placeholders/announcement-2.jpg'
    ],
    [
        'id' => 3,
        'title' => 'System Maintenance Scheduled',
        'description' => 'The Kollege LMS portal will be temporarily unavailable on Sunday, October 26th, from 2:00 AM to 4:00 AM for scheduled maintenance. We apologize for any inconvenience.',
        'created_date' => '2025-10-17 09:00:00',
        'image' => null
    ]
];
?>

<div class="container my-5">
    <h1 class="display-5 text-center mb-5"><?php echo $page_title; ?></h1>

    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            
            <?php if (empty($announcements)): ?>
                <div class="alert alert-info text-center">
                    There are no new announcements at this time. Please check back later.
                </div>
            <?php else: ?>
                <?php foreach ($announcements as $item): ?>
                    <div class="card shadow-sm mb-4">
                        <div class="row g-0">
                            <?php if (!empty($item['image'])): ?>
                                <div class="col-md-4">
                                    <img src="<?php echo BASE_URL . htmlspecialchars($item['image']); ?>" class="img-fluid rounded-start" alt="<?php echo htmlspecialchars($item['title']); ?>" style="height: 100%; object-fit: cover;">
                                </div>
                            <?php endif; ?>
                            
                            <div class="<?php echo !empty($item['image']) ? 'col-md-8' : 'col-md-12'; ?>">
                                <div class="card-body">
                                    <h3 class="card-title h4"><?php echo htmlspecialchars($item['title']); ?></h3>
                                    <p class="card-text text-muted">
                                        <small>Posted on: <?php echo format_date($item['created_date'], 'F j, Y'); ?></small>
                                    </p>
                                    <p class="card-text"><?php echo nl2br(htmlspecialchars($item['description'])); ?></p>
                                    </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';
?>