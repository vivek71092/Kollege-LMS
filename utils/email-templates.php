<?php
// /utils/email-templates.php

// Assumes BASE_URL and SITE_NAME are defined (from config.php)

/**
 * Generates the HTML body for a password reset email.
 * @param string $reset_link The unique password reset link.
 * @param string $user_name The user's first name.
 * @return string HTML email body.
 */
function get_password_reset_email_body($reset_link, $user_name) {
    return <<<HTML
    <!DOCTYPE html>
    <html>
    <head><title>Password Reset Request</title></head>
    <body style="font-family: sans-serif; padding: 20px;">
        <h2>Password Reset Request for {$GLOBALS['SITE_NAME']}</h2>
        <p>Hello {$user_name},</p>
        <p>We received a request to reset your password. If you did not make this request, you can safely ignore this email.</p>
        <p>To reset your password, please click the link below:</p>
        <p><a href="{$reset_link}" style="display: inline-block; padding: 10px 20px; background-color: #0d6efd; color: white; text-decoration: none; border-radius: 5px;">Reset Password</a></p>
        <p>If the button doesn't work, copy and paste this link into your browser:</p>
        <p>{$reset_link}</p>
        <p>This link will expire in 1 hour.</p>
        <p>Thanks,<br>The {$GLOBALS['SITE_NAME']} Team</p>
    </body>
    </html>
HTML;
}

/**
 * Generates the HTML body for an email verification email.
 * @param string $verify_link The unique verification link.
 * @param string $user_name The user's first name.
 * @return string HTML email body.
 */
function get_email_verification_body($verify_link, $user_name) {
    return <<<HTML
    <!DOCTYPE html>
    <html>
    <head><title>Verify Your Email</title></head>
    <body style="font-family: sans-serif; padding: 20px;">
        <h2>Welcome to {$GLOBALS['SITE_NAME']}! Please Verify Your Email</h2>
        <p>Hello {$user_name},</p>
        <p>Thank you for registering. Please click the link below to verify your email address and activate your account:</p>
        <p><a href="{$verify_link}" style="display: inline-block; padding: 10px 20px; background-color: #0d6efd; color: white; text-decoration: none; border-radius: 5px;">Verify Email Address</a></p>
        <p>If the button doesn't work, copy and paste this link into your browser:</p>
        <p>{$verify_link}</p>
        <p>Thanks,<br>The {$GLOBALS['SITE_NAME']} Team</p>
    </body>
    </html>
HTML;
}

// Add more email template functions here (e.g., new assignment notification, grade published)

?>