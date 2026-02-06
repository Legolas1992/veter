<?php
// extender_licencia.php
require_once 'config/config.php';
require_once 'includes/auth_check.php';
require_admin();

require_once 'config/db.php';

// Auth Check
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'admin') {
    header("Location: " . BASE_URL . "auth/login.php");
    exit;
}

$msg = "";
$database = new Database();
$db = $database->getConnection();

// Get current
$stmt = $db->query("SELECT fecha_limite FROM sistema_licencia LIMIT 1");
$actual = $stmt->fetch()['fecha_limite'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['meses'])) {
    $meses = (int)$_POST['meses'];
    $nueva_fecha = date('Y-m-d', strtotime($actual . " + $meses months"));
    
    $upd = $db->prepare("UPDATE sistema_licencia SET fecha_limite = :fecha");
    $upd->bindParam(':fecha', $nueva_fecha);
    $upd->execute();
    
    $msg = "Licencia extendida $meses meses. Nueva fecha: $nueva_fecha";
    $actual = $nueva_fecha;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Licencia</title>
    <style>
        body { font-family: sans-serif; padding: 2rem; text-align: center; background: #e0f7fa; }
        .card { background: white; max-width: 400px; margin: 0 auto; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h1 { color: #006064; }
        .date { font-size: 2rem; font-weight: bold; margin: 1rem 0; color: #333; }
        button { background: #00897b; color: white; border: none; padding: 1rem 2rem; font-size: 1.2rem; cursor: pointer; border-radius: 4px; }
        button:hover { background: #00695c; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Estado del Servicio</h1>
        <p>Vencimiento Actual:</p>
        <div class="date"><?php echo date('d/m/Y', strtotime($actual)); ?></div>
        
        <?php if($msg): ?>
            <p style="color: green; font-weight: bold;"><?php echo $msg; ?></p>
        <?php endif; ?>

        <form method="POST">
            <p>Simular Pago Mensual:</p>
            <button type="submit" name="meses" value="1">✅ Renovar 1 Mes</button>
        </form>
        <br>
        <a href="index.php">Volver al Sistema</a>
    </div>
</body>
</html>
