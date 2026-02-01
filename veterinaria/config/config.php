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

// Error Reporting (Enable for development, Disable for production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Application Secret (Should be set in environment)
define('APP_SECRET', getenv('APP_SECRET') ?: '7f8a9d0c1e2b3a4f5d6e7c8b9a0d1e2f3a4b5c6d7e8f9a0b1c2d3e4f5a6b7c8d');

?>
