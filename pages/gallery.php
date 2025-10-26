<?php
// /pages/gallery.php

require_once '../config.php';
require_once '../functions.php';

$page_title = "Gallery";
require_once '../includes/header.php';

// Placeholder images for the gallery
$gallery_images = [
    ['src' => 'public/images/placeholders/gallery-1.jpg', 'alt' => 'Campus Building'],
    ['src' => 'public/images/placeholders/gallery-2.jpg', 'alt' => 'Students in a classroom'],
    ['src' => 'public/images/placeholders/gallery-3.jpg', 'alt' => 'Library interior'],
    ['src' => 'public/images/placeholders/gallery-4.jpg', 'alt' => 'Graduation ceremony'],
    ['src' => 'public/images/placeholders/gallery-5.jpg', 'alt' => 'Coding workshop'],
    ['src' => 'public/images/placeholders/gallery-6.jpg', 'alt' => 'Campus lawn'],
    ['src' => 'public/images/placeholders/gallery-7.jpg', 'alt' => 'Science lab'],
    ['src' => 'public/images/placeholders/gallery-8.jpg', 'alt' => 'Sports day event'],
];
?>

<div class="container my-5">
    <h1 class="display-5 text-center mb-5"><?php echo $page_title; ?></h1>
    <p class="lead text-center mb-5">A glimpse into life at our institution.</p>

    <div class="row g-4">
        <?php foreach ($gallery_images as $image): ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card shadow-sm h-100">
                    <a href="<?php echo BASE_URL . $image['src']; ?>" data-bs-toggle="modal" data-bs-target="#imageModal" data-src="<?php echo BASE_URL . $image['src']; ?>" data-alt="<?php echo $image['alt']; ?>">
                        <img src="<?php echo BASE_URL . $image['src']; ?>" class="card-img-top" alt="<?php echo $image['alt']; ?>" style="height: 200px; object-fit: cover;">
                    </a>
                    <div class="card-body text-center" style="padding: 0.5rem;">
                        <p class="card-text text-muted small"><?php echo $image['alt']; ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Image Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="" id="modalImage" class="img-fluid" alt="">
            </div>
        </div>
    </div>
</div>

<?php
// Add page-specific JS for the modal
$page_scripts = [
    'public/js/page-gallery.js' // We need to create this file
];

// Inline script for simplicity, but best to move to page-gallery.js
?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var imageModal = document.getElementById('imageModal');
    imageModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var imgSrc = button.getAttribute('data-src');
        var imgAlt = button.getAttribute('data-alt');
        
        var modalImage = imageModal.querySelector('#modalImage');
        var modalTitle = imageModal.querySelector('#imageModalLabel');
        
        modalImage.src = imgSrc;
        modalImage.alt = imgAlt;
        modalTitle.textContent = imgAlt;
    });
});
</script>

<?php
require_once '../includes/footer.php';
?>