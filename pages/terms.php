<?php
// /pages/terms.php

require_once '../config.php';
require_once '../functions.php';

$page_title = "Terms & Conditions";
require_once '../includes/header.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <h1 class="display-5 text-center mb-4"><?php echo $page_title; ?></h1>
            <p class="text-center text-muted">Last updated: <?php echo date('F j, Y'); ?></p>

            <hr class="my-4">

            <h2 class="h4">1. Introduction</h2>
            <p>Welcome to <?php echo SITE_NAME; ?> ("us", "we", or "our"). These Terms & Conditions govern your use of our website located at <?php echo BASE_URL; ?> (the "Service") operated by <?php echo SITE_NAME; ?>.</p>
            <p>Your access to and use of the Service is conditioned on your acceptance of and compliance with these Terms. These Terms apply to all visitors, users, and others who access or use the Service.</p>

            <h2 class="h4 mt-4">2. Accounts</h2>
            <p>When you create an account with us, you must provide us with information that is accurate, complete, and current at all times. Failure to do so constitutes a breach of the Terms, which may result in immediate termination of your account on our Service.</p>
            <p>You are responsible for safeguarding the password that you use to access the Service and for any activities or actions under your password, whether your password is with our Service or a third-party service.</p>

            <h2 class="h4 mt-4">3. Intellectual Property</h2>
            <p>The Service and its original content (excluding Content provided by users), features, and functionality are and will remain the exclusive property of <?php echo SITE_NAME; ?> and its licensors. The Service is protected by copyright, trademark, and other laws of both the and foreign countries.</p>

            <h2 class="h4 mt-4">4. User Content</h2>
            <p>Our Service allows you to post, link, store, share, and otherwise make available certain information, text, graphics, videos, or other material ("Content"). You are responsible for the Content that you post to the Service, including its legality, reliability, and appropriateness.</p>
            <p>By posting Content to the Service, you grant us the right and license to use, modify, publicly perform, publicly display, reproduce, and distribute such Content on and through the Service.</p>

            <h2 class="h4 mt-4">5. Prohibited Uses</h2>
            <p>You may use the Service only for lawful purposes and in accordance with Terms. You agree not to use the Service:</p>
            <ul>
                <li>In any way that violates any applicable national or international law or regulation.</li>
                <li>For the purpose of exploiting, harming, or attempting to exploit or harm minors in any way by exposing them to inappropriate content or otherwise.</li>
                <li>To transmit, or procure the sending of, any advertising or promotional material, including any "junk mail", "chain letter", "spam", or any other similar solicitation.</li>
            </ul>

            <h2 class="h4 mt-4">6. Termination</h2>
            <p>We may terminate or suspend your account immediately, without prior notice or liability, for any reason whatsoever, including without limitation if you breach the Terms.</p>

            <h2 class="h4 mt-4">7. Governing Law</h2>
            <p>These Terms shall be governed and construed in accordance with the laws of [Your Jurisdiction], without regard to its conflict of law provisions.</p>

            <h2 class="h4 mt-4">8. Changes to Terms</h2>
            <p>We reserve the right, at our sole discretion, to modify or replace these Terms at any time. If a revision is material we will provide at least 30 days' notice prior to any new terms taking effect. What constitutes a material change will be determined at our sole discretion.</p>

            <h2 class="h4 mt-4">9. Contact Us</h2>
            <p>If you have any questions about these Terms, please <a href="contact.php">contact us</a>.</p>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';
?>