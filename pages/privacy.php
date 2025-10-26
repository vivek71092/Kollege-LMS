<?php
// /pages/privacy.php

require_once '../config.php';
require_once '../functions.php';

$page_title = "Privacy Policy";
require_once '../includes/header.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <h1 class="display-5 text-center mb-4"><?php echo $page_title; ?></h1>
            <p class="text-center text-muted">Last updated: <?php echo date('F j, Y'); ?></p>

            <hr class="my-4">

            <h2 class="h4">1. Introduction</h2>
            <p><?php echo SITE_NAME; ?> ("us", "we", or "our") operates the <?php echo BASE_URL; ?> website (the "Service").</p>
            <p>This page informs you of our policies regarding the collection, use, and disclosure of personal data when you use our Service and the choices you have associated with that data. We use your data to provide and improve the Service. By using the Service, you agree to the collection and use of information in accordance with this policy.</p>

            <h2 class="h4 mt-4">2. Information Collection and Use</h2>
            <p>We collect several different types of information for various purposes to provide and improve our Service to you.</p>
            
            <h3 class="h5 mt-3">Types of Data Collected</h3>
            
            <h4>Personal Data</h4>
            <p>While using our Service, we may ask you to provide us with certain personally identifiable information that can be used to contact or identify you ("Personal Data"). Personally identifiable information may include, but is not limited to:</p>
            <ul>
                <li>Email address</li>
                <li>First name and last name</li>
                <li>Phone number</li>
                <li>Cookies and Usage Data</li>
            </ul>

            <h4>Usage Data</h4>
            <p>We may also collect information on how the Service is accessed and used ("Usage Data"). This Usage Data may include information such as your computer's Internet Protocol address (e.g. IP address), browser type, browser version, the pages of our Service that you visit, the time and date of your visit, the time spent on those pages, unique device identifiers and other diagnostic data.</p>

            <h2 class="h4 mt-4">3. Use of Data</h2>
            <p><?php echo SITE_NAME; ?> uses the collected data for various purposes:</p>
            <ul>
                <li>To provide and maintain the Service</li>
                <li>To notify you about changes to our Service</li>
                <li>To allow you to participate in interactive features of our Service when you choose to do so</li>
                <li>To provide customer care and support</li>
                <li>To provide analysis or valuable information so that we can improve the Service</li>
                <li>To monitor the usage of the Service</li>
                <li>To detect, prevent and address technical issues</li>
            </ul>

            <h2 class="h4 mt-4">4. Data Security</h2>
            <p>The security of your data is important to us, but remember that no method of transmission over the Internet, or method of electronic storage is 100% secure. While we strive to use commercially acceptable means to protect your Personal Data, we cannot guarantee its absolute security.</p>

            <h2 class="h4 mt-4">5. Service Providers</h2>
            <p>We may employ third-party companies and individuals to facilitate our Service ("Service Providers"), to provide the Service on our behalf, to perform Service-related services or to assist us in analyzing how our Service is used. These third parties have access to your Personal Data only to perform these tasks on our behalf and are obligated not to disclose or use it for any other purpose.</p>

            <h2 class="h4 mt-4">6. Children's Privacy</h2>
            <p>Our Service does not address anyone under the age of 18 ("Children"). We do not knowingly collect personally identifiable information from anyone under the age of 18. If you are a parent or guardian and you are aware that your Children has provided us with Personal Data, please contact us.</p>

            <h2 class="h4 mt-4">7. Changes to This Privacy Policy</h2>
            <p>We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page. We will let you know via email and/or a prominent notice on our Service, prior to the change becoming effective and update the "last updated" date at the top of this Privacy Policy.</p>

            <h2 class="h4 mt-4">8. Contact Us</h2>
            <p>If you have any questions about this Privacy Policy, please <a href="contact.php">contact us</a>.</p>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';
?>