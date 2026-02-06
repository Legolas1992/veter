<?php
// config/config.php

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define Base URL
define('BASE_URL', 'http://localhost/veterinaria/');

// Set Timezone
date_default_timezone_set('America/Argentina/Buenos_Aires');

// Error Reporting (Secure for production)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Sentinel: Do not leak errors
ini_set('log_errors', 1);

// Application Secret Management
$secret = getenv('APP_SECRET');
$secretFile = __DIR__ . '/secret.key';

if (!$secret && file_exists($secretFile)) {
    $secret = file_get_contents($secretFile);
}

if (!$secret) {
    try {
        // Generate secure random secret if missing
        $secret = bin2hex(random_bytes(32));
        if (!@file_put_contents($secretFile, $secret)) {
            error_log("CRITICAL: Could not write to $secretFile. APP_SECRET will be ephemeral.");
        }
    } catch (Exception $e) {
        // Should not happen, but fail safe
        die("Critical Security Error: Unable to initialize application secret.");
    }
}

define('APP_SECRET', trim($secret));

?>
