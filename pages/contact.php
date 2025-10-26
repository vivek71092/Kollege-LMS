<?php
// /pages/contact.php

require_once '../config.php';
require_once '../functions.php';

// Handle form submission (basic example)
$feedback_message = '';
$feedback_class = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // In a real app, you would:
    // 1. Sanitize all inputs
    // 2. Validate (e.g., check for valid email)
    // 3. Send an email (using PHPMailer class)
    // 4. Save the inquiry to the 'Messages' table (if for a logged-in user) or a 'ContactInquiries' table
    // 5. Set a session flash message
    
    // For this template, we just show a success message.
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $subject = sanitize_input($_POST['subject']);
    $message = sanitize_input($_POST['message']);

    // Simple validation
    if (!empty($name) && !empty($email) && !empty($message) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Here you would call your email function
        // e.g., send_contact_email($name, $email, $subject, $message);
        
        $feedback_message = "Thank you, $name! Your message has been sent successfully. We will get back to you shortly.";
        $feedback_class = "alert-success";
    } else {
        $feedback_message = "Please fill out all required fields with valid information.";
        $feedback_class = "alert-danger";
    }
}

$page_title = "Contact Us";
require_once '../includes/header.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <h1 class="display-5 text-center mb-4"><?php echo $page_title; ?></h1>
            <p class="lead text-center mb-5">Have a question or feedback? Fill out the form below to get in touch with our team.</p>

            <div class="row">
                <div class="col-md-8 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4 p-md-5">
                            <h3 class="h4 mb-4">Send Us a Message</h3>

                            <?php if ($feedback_message): ?>
                                <div class="alert <?php echo $feedback_class; ?>">
                                    <?php echo $feedback_message; ?>
                                </div>
                            <?php endif; ?>

                            <form id="contactForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="needs-validation" novalidate>
                                <div class="mb-3">
                                    <label for="name" class="form-label">Your Name</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                    <div class="invalid-feedback">Please enter your name.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Your Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                    <div class="invalid-feedback">Please enter a valid email address.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="subject" class="form-label">Subject</label>
                                    <input type="text" class="form-control" id="subject" name="subject">
                                </div>
                                <div class="mb-3">
                                    <label for="message" class="form-label">Message</label>
                                    <textarea class="form-control" id="message" name="message" rows="6" required></textarea>
                                    <div class="invalid-feedback">Please enter your message.</div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-lg">Send Message</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm border-0" style="background-color: #f8f9fa;">
                        <div class="card-body p-4">
                            <h3 class="h4 mb-4">Contact Information</h3>
                            <p>
                                <i class="fas fa-map-marker-alt fa-fw me-2 text-primary"></i>
                                123 Education Lane<br>
                                Learning City, ED 54321
                            </p>
                            <p>
                                <i class="fas fa-phone fa-fw me-2 text-primary"></i>
                                +1 234 567 890
                            </p>
                            <p>
                                <i class="fas fa-envelope fa-fw me-2 text-primary"></i>
                                <a href="mailto:info@kollege.ct.ws">info@kollege.ct.ws</a>
                            </p>
                            <p>
                                <i class="fas fa-globe fa-fw me-2 text-primary"></i>
                                <a href="<?php echo BASE_URL; ?>">kollege.ct.ws</a>
                            </p>
                            <hr>
                            <h5 class="h6 mb-3">Business Hours</h5>
                            <p>
                                Monday - Friday: 9:00 AM - 5:00 PM<br>
                                Saturday - Sunday: Closed
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';
?>