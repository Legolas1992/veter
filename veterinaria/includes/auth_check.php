<?php
// includes/auth_check.php

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check for user session
if (!isset($_SESSION['usuario_id'])) {
    // Allow login.php and auth_action.php to bypass (just in case included there)
    $current_script = basename($_SERVER['PHP_SELF']);
    if ($current_script == 'login.php' || $current_script == 'auth_action.php') {
        return;
    }

    // Redirect to login
    if (defined('BASE_URL')) {
        header("Location: " . BASE_URL . "auth/login.php");
    } else {
        // Fallback if BASE_URL not defined (should not happen if config included)
        // Assuming we are in a subfolder like /clientes/ or /includes/ relative to root
        header("Location: ../auth/login.php");
    }
    exit;
}
?>
