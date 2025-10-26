<?php
// /auth/verify-email.php

require_once '../config.php';
require_once '../functions.php';

// --- Token Validation (GET) ---
$token = sanitize_input($_GET['token'] ?? '');

if (empty($token)) {
    $_SESSION['error_message'] = "No verification token provided.";
    redirect('auth/login.php');
}

// --- PLACEHOLDER LOGIC ---
// In a real application, you would:
// 1. Have a `verification_token` column in your `Users` table.
// 2. When a user registers, store a unique token there and set `status` to 'pending'.
// 3. Search for the user with this token:
//    $stmt = $pdo->prepare("SELECT id FROM Users WHERE verification_token = ? AND status = 'pending'");
//    $stmt->execute([$token]);
//    $user = $stmt->fetch();
// 4. if ($user) {
//       // Token is valid, activate the user
//       $update_stmt = $pdo->prepare("UPDATE Users SET status = 'active', verification_token = NULL WHERE id = ?");
//       $update_stmt->execute([$user['id']]);
//       $_SESSION['success_message'] = "Email verified successfully! You can now log in.";
//    } else {
//       $_SESSION['error_message'] = "Invalid or expired verification token.";
//    }

// For this template, we'll simulate a valid and invalid token.
if ($token === "valid-verification-token") {
    $_SESSION['success_message'] = "Email verified successfully! You can now log in.";
    redirect('auth/login.php');
} else {
    $_SESSION['error_message'] = "Invalid or expired verification token.";
    redirect('auth/register.php'); // Redirect to register, as they may need a new link
}

exit;
?>