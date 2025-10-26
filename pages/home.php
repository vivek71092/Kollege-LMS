<?php
// /pages/home.php

// This file contains the main content for the homepage.
// It assumes config.php, functions.php, and header.php have already been included by index.php.

// Ensure SITE_NAME and BASE_URL constants are available (should be from config.php)
if (!defined('SITE_NAME')) define('SITE_NAME', 'Kollege LMS');
if (!defined('BASE_URL')) define('BASE_URL', '/'); // Basic fallback

?>

<div class="hero-section bg-primary text-white text-center p-5">
    <div class="container">
        <h1 class="display-4">Welcome to <?php echo htmlspecialchars(SITE_NAME); ?></h1>
        <p class="lead">Your complete solution for online learning and management.</p>
        <a href="<?php echo BASE_URL; ?>auth/login.php" class="btn btn-light btn-lg m-2">Login</a>
        <a href="<?php echo BASE_URL; ?>auth/register.php" class="btn btn-outline-light btn-lg m-2">Register Now</a>
    </div>
</div>

<section class="welcome-section p-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2>About Our Institution</h2>
                <p><?php echo htmlspecialchars(SITE_NAME); ?> is dedicated to providing high-quality education and resources to students everywhere. Our platform connects students, teachers, and administrators in a seamless, collaborative environment.</p>
                <p>Explore our courses, meet our faculty, and discover why <?php echo htmlspecialchars(SITE_NAME); ?> is the right choice for your educational journey.</p>
                <a href="<?php echo BASE_URL; ?>pages/about.php" class="btn btn-primary">Learn More About Us</a>
            </div>
            <div class="col-md-4 text-center">
                <img src="<?php echo BASE_URL; ?>public/images/placeholders/institution-image.jpg" alt="Our Institution" class="img-fluid rounded-circle shadow" style="width: 250px; height: 250px; object-fit: cover;">
            </div>
        </div>
    </div>
</section>

<section class="featured-courses-section bg-light p-5">
    <div class="container">
        <h2 class="text-center mb-4">Featured Courses</h2>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <img src="<?php echo BASE_URL; ?>public/images/placeholders/course-web-dev.jpg" class="card-img-top" alt="Web Development" style="height: 200px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Introduction to Web Development</h5>
                        <p class="card-text">Learn the fundamentals of HTML, CSS, and JavaScript to build modern websites.</p>
                        <a href="<?php echo BASE_URL; ?>auth/register.php" class="btn btn-primary mt-auto">View Details</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                     <img src="<?php echo BASE_URL; ?>public/images/placeholders/course-data-sci.jpg" class="card-img-top" alt="Data Science" style="height: 200px; object-fit: cover;">
                     <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Data Science with Python</h5>
                        <p class="card-text">Explore data analysis, visualization, and machine learning with Python.</p>
                        <a href="<?php echo BASE_URL; ?>auth/register.php" class="btn btn-primary mt-auto">View Details</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                     <img src="<?php echo BASE_URL; ?>public/images/placeholders/course-business.jpg" class="card-img-top" alt="Business Analytics" style="height: 200px; object-fit: cover;">
                     <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Business Analytics</h5>
                        <p class="card-text">Understand how to use data to make informed business decisions.</p>
                        <a href="<?php echo BASE_URL; ?>auth/register.php" class="btn btn-primary mt-auto">View Details</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>