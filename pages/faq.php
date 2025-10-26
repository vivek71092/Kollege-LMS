<?php
// /pages/faq.php

require_once '../config.php';
require_once '../functions.php';

$page_title = "Frequently Asked Questions (FAQ)";
require_once '../includes/header.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <h1 class="display-5 text-center mb-5"><?php echo $page_title; ?></h1>

            <div class="accordion" id="faqAccordion">

                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            How do I register for an account?
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            You can register for a student account by clicking the "Register" button on the homepage or login page. Fill out the required information, including your name, email, and password. You may need to verify your email address to activate your account. Teacher and Admin accounts are typically created by the system administrator.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            I forgot my password. How can I reset it?
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            On the login page, click the "Forgot Password?" link. You will be asked to enter the email address associated with your account. A password reset link will be sent to that email, allowing you to create a new password.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            How do I submit an assignment?
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            As a student, navigate to your dashboard and select the course. Go to the "Assignments" section, find the assignment you wish to submit, and click on it. You will see an option to upload your file (e.g., PDF, DOCX, ZIP) and add any comments. Click "Submit" to complete the process.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFour">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                            Where can I see my grades and attendance?
                        </button>
                    </h2>
                    <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            From your student dashboard, you can access dedicated sections for "My Marks" and "My Attendance". These pages will show a detailed breakdown of your performance and attendance records for each subject you are enrolled in.
                        </div>
                    </div>
                </div>
                
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFive">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                            How do I contact my teacher?
                        </button>
                    </h2>
                    <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            You can use the built-in messaging system. From your dashboard, go to the "Messages" section to send a new message. You can select your teacher from the list of recipients.
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