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

?>
