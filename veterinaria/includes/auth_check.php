<?php
// includes/auth_check.php

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['usuario_id'])) {
    // Redirect to login page
    // config.php defines BASE_URL. If not defined, fallback to relative path.
    $login_url = defined('BASE_URL') ? BASE_URL . 'auth/login.php' : '../auth/login.php';
    header("Location: " . $login_url);
    exit;
}
?>
