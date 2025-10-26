<?php
// /pages/404.php

require_once '../config.php';
require_once '../functions.php';

// Send a 404 HTTP status code
http_response_code(404);

$page_title = "404 - Page Not Found";
require_once '../includes/header.php';
?>

<div class="container my-5 py-5">
    <div class="row">
        <div class="col-md-8 offset-md-2 text-center">
            <h1 class="display-1 fw-bold text-primary">404</h1>
            <h2 class="display-4">Page Not Found</h2>
            <p class="lead mt-4">
                We're sorry, but the page you are looking for does not exist or has been moved.
            </p>
            <p>You can return to the homepage or contact us if you need further assistance.</p>
            <div class="mt-5">
                <a href="<?php echo BASE_URL; ?>" class="btn btn-primary btn-lg me-2">
                    <i class="fas fa-home me-2"></i> Go to Homepage
                </a>
                <a href="<?php echo BASE_URL; ?>pages/contact.php" class="btn btn-outline-secondary btn-lg">
                    <i class="fas fa-envelope me-2"></i> Contact Us
                </a>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';
?>