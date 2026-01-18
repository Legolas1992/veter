<!-- includes/header.php -->
<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

// === GET CONFIG & CHECK LICENSE ===
try {
    $db_conn = (new Database())->getConnection();
    
    // License
    $stmt_lic = $db_conn->query("SELECT fecha_limite FROM sistema_licencia LIMIT 1");
    $licencia = $stmt_lic->fetch();
    if ($licencia && date('Y-m-d') > $licencia['fecha_limite']) {
        die("<div style='text-align:center; padding:5rem; font-family:sans-serif;'>
                <h1 style='color:red;'>⛔ Acceso Bloqueado</h1>
                <p>Su suscripción venció el " . date('d/m/Y', strtotime($licencia['fecha_limite'])) . "</p>
             </div>");
    }

    // Config
    $stmt_conf = $db_conn->query("SELECT * FROM configuracion LIMIT 1");
    $config_empresa = $stmt_conf->fetch();
    $nombre_negocio = $config_empresa['nombre_negocio'] ?? 'Veterinaria';
    $logo_path = $config_empresa['logo_path'] ?? 'assets/resources/logo.jpg';

} catch (Exception $e) { /* Silent */ }
// =================================

// Auth Check
if (!isset($_SESSION['usuario_id']) && basename($_SERVER['PHP_SELF']) != 'login.php') {
    header("Location: " . BASE_URL . "auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($nombre_negocio); ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    
<?php if(isset($_SESSION['usuario_id'])): ?>
    <div class="wrapper">
        <?php include __DIR__ . '/navbar.php'; ?>
        <main class="main-content">
            <header class="top-bar" style="display: flex; align-items: center; justify-content: space-between; padding: 1rem 2rem; background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <?php if(file_exists(__DIR__ . '/../' . $logo_path)): ?>
                        <img src="<?php echo BASE_URL . $logo_path; ?>" alt="Logo" style="height: 50px; width: auto; object-fit: contain;">
                    <?php else: ?>
                        <i class="fas fa-clinic-medical" style="font-size: 2rem; color: var(--primary-color);"></i>
                    <?php endif; ?>
                    <h1 style="font-size: 1.5rem; margin: 0; color: #333;"><?php echo htmlspecialchars($nombre_negocio); ?></h1>
                </div>
                
                <div class="user-info">
                   <small>Hola,</small> <strong><?php echo $_SESSION['usuario_nombre'] ?? 'Usuario'; ?></strong> 
                   <span class="badge" style="background:#e0f2f1; color:#00695c; padding:2px 8px; border-radius:10px; font-size:0.8rem; margin-left:5px;">
                        <?php echo ucfirst($_SESSION['usuario_rol'] ?? ''); ?>
                   </span>
                </div>
            </header>
            <div class="content">
<?php endif; ?>

