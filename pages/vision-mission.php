<?php
// /pages/vision-mission.php

require_once '../config.php';
require_once '../functions.php';

$page_title = "Vision & Mission";
require_once '../includes/header.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <h1 class="display-5 text-center mb-5"><?php echo $page_title; ?></h1>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card h-100 shadow-sm border-primary border-3">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-eye fa-3x text-primary mb-3"></i>
                            <h2 class="card-title h3">Our Vision</h2>
                            <p class="card-text">To be a globally recognized leader in educational technology, empowering institutions to deliver exceptional learning experiences that are accessible, equitable, and transformative for all learners.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card h-100 shadow-sm border-success border-3">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-rocket fa-3x text-success mb-3"></i>
                            <h2 class="card-title h3">Our Mission</h2>
                            <p class="card-text">Our mission is to create an intuitive, reliable, and comprehensive Learning Management System that simplifies academic administration, fosters collaboration, and provides powerful tools for teaching and learning in the digital age.</p>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-5">

            <h2 class="text-center mb-4">Our Core Values</h2>
            <div class="row">
                <div class="col-md-4 text-center mb-3">
                    <div class="p-3">
                        <i class="fas fa-lightbulb fa-2x text-warning mb-2"></i>
                        <h5>Innovation</h5>
                        <p>We relentlessly pursue creative solutions and continuous improvement.</p>
                    </div>
                </div>
                <div class="col-md-4 text-center mb-3">
                    <div class="p-3">
                        <i class="fas fa-users fa-2x text-info mb-2"></i>
                        <h5>Collaboration</h5>
                        <p>We believe the best results come from working together with our partners and users.</p>
                    </div>
                </div>
                <div class="col-md-4 text-center mb-3">
                    <div class="p-3">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        <h5>Integrity</h5>
                        <p>We operate with transparency, honesty, and a strong sense of ethics.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';
?>