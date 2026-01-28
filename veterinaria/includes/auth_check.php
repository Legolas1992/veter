<?php
// veterinaria/includes/auth_check.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure BASE_URL is available
if (!defined('BASE_URL')) {
    if (file_exists(__DIR__ . '/../config/config.php')) {
        require_once __DIR__ . '/../config/config.php';
    }
}

// Redirect if not logged in
if (!isset($_SESSION['usuario_id'])) {
    $login_path = defined('BASE_URL') ? BASE_URL . 'auth/login.php' : '../auth/login.php';
    header("Location: " . $login_path);
    exit;
}

/**
 * Requires the current user to have admin role.
 */
function require_admin() {
    if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'admin') {
        http_response_code(403);
        die("<h1>403 Acceso Denegado</h1><p>Se requieren privilegios de administrador para acceder a esta página.</p>");
    }
}
?>
