<?php
// /pages/about.php

// Use __DIR__ for reliable includes relative to the current file
require_once __DIR__ . '/../config.php'; // Ensures BASE_URL, SITE_NAME are defined
require_once __DIR__ . '/../functions.php';

$page_title = "About This Project"; // Changed title

// Determine path prefix for header/footer includes relative to this file
$path_prefix = '../';
require_once $path_prefix . 'includes/header.php';
?>

<main> <div class="container my-5">
    <div class="row">
        <div class="col-lg-10 offset-lg-1"> <h1 class="display-5 text-center mb-5"><?php echo htmlspecialchars($page_title); ?></h1>

            <div class="card shadow-sm mb-5">
                <div class="card-body p-4">
                     <h2 class="card-title h4 mb-4 text-primary">About the Developer</h2>
                     <div class="row align-items-center">
                        <div class="col-md-3 text-center mb-3 mb-md-0">
                            <img src="<?php echo BASE_URL; ?>public/images/placeholders/vivek.jpg"
                                 alt="Vivek Kumar" class="img-fluid rounded-circle mb-2 border shadow-sm"
                                 style="width: 150px; height: 150px; object-fit: cover;"
                                 onerror="this.onerror=null; this.src='<?php echo BASE_URL; ?>public/images/placeholders/profile.png';"> </div>
                        <div class="col-md-9">
                            <h4>Vivek Kumar</h4>
                            <p class="text-muted mb-1">
                                BCA 3rd Year Student<br>
                                UID: O23BCA160042<br> Chandigarh University
                            </p>
                            <p class="mt-2">This Kollege LMS platform has been developed as my Major Project submission for the 6th semester. It demonstrates the practical application of web technologies learned during my studies.</p>
                            <p>My goal was to build a comprehensive yet user-friendly tool to streamline various academic processes.</p>
                            <p class="mt-3">
                                <a href="https://github.com/vivek71092/Kollege-LMS" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary btn-sm">
                                    <i class="fab fa-github me-1"></i> View Project on GitHub
                                </a>
                                </p>
                            </div>
                    </div>
                </div>
            </div>
            <h2 class="mt-5 mb-3">About Kollege LMS Project</h2>
            <p>Kollege LMS is a web-based Learning Management System created to provide a functional and interactive platform for educational institutions. The goal was to build a system simulating real-world LMS features using core web technologies suitable for standard hosting environments.</p>
            <p>This project serves as a practical demonstration of designing, developing, and deploying a dynamic web application involving database management, user authentication, role-based access control, and content management.</p>

            <h2 class="mt-5 mb-3">Key Features Implemented</h2>
            <p>The system includes distinct functionalities for different user roles:</p>
            <ul>
                <li><strong>Public Interface:</strong> Basic informational pages (Homepage, About, Contact) accessible to everyone.</li>
                <li><strong>Authentication:</strong> Secure Login, Student Self-Registration, Password Management.</li>
                <li><strong>Role-Based Dashboards:</strong> Separate interfaces and capabilities for Students, Teachers, and Administrators.</li>
                <li><strong>Student Features:</strong> Course viewing, accessing notes, assignment submission, viewing grades and attendance.</li>
                <li><strong>Teacher Features:</strong> Subject management, note uploading, assignment creation and grading, attendance marking, student list viewing.</li>
                <li><strong>Admin Features:</strong> Full user management (CRUD), course and subject management, announcement control, system settings, report generation overview, and audit logs.</li>
            </ul>
             <p><em>(For a complete list, please refer to the project documentation.)</em></p>


            <h2 class="mt-5 mb-3">Technology Stack Used</h2>
            <p>This project was built using the following technologies:</p>
             <div class="row">
                 <div class="col-md-6">
                     <ul>
                         <li><strong>Backend:</strong> PHP (Procedural & OOP)</li>
                         <li><strong>Database:</strong> MySQL</li>
                         <li><strong>Frontend:</strong> HTML5, CSS3, JavaScript (ES6)</li>
                     </ul>
                 </div>
                 <div class="col-md-6">
                     <ul>
                         <li><strong>UI Framework:</strong> Bootstrap 5</li>
                         <li><strong>JS Libraries:</strong> jQuery, DataTables, Chart.js (for Admin)</li>
                         <li><strong>Server:</strong> Apache (Utilizing `.htaccess` for security)</li>
                     </ul>
                 </div>
             </div>

        </div> </div> </div> </main> <?php
require_once $path_prefix . 'includes/footer.php';
?>