<?php
// facturacion/pago_simulado.php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

if (!isset($_GET['id'])) {
    die("Error: Pedido inválido");
}

$id = $_GET['id'];
$database = new Database();
$db = $database->getConnection();

$stmt = $db->prepare("SELECT f.*, c.nombre as cliente_nombre FROM facturacion f JOIN clientes c ON f.cliente_id = c.id WHERE f.id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$factura = $stmt->fetch();

if (!$factura) {
    die("Factura no encontrada");
}

// Handle Payment Simulation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pay'])) {
    $update = $db->prepare("UPDATE facturacion SET estado = 'pagado' WHERE id = :id");
    $update->bindParam(':id', $id);
    $update->execute();
    $success = true;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billetera Virtual - Pago</title>
    <style>
        body { font-family: sans-serif; background-color: #009ee3; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .phone-mock { background: white; width: 100%; max-width: 320px; padding: 2rem; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); text-align: center; }
        .amount { font-size: 2.5rem; font-weight: bold; color: #333; margin: 1rem 0; }
        .store { color: #666; margin-bottom: 2rem; }
        .btn-pay { background: #009ee3; color: white; border: none; padding: 1rem; width: 100%; border-radius: 8px; font-size: 1.2rem; cursor: pointer; font-weight: bold; }
        .btn-pay:hover { background: #0077aa; }
        .success-icon { font-size: 4rem; color: #43a047; margin-bottom: 1rem; }
    </style>
</head>
<body>

    <div class="phone-mock">
        <?php if(isset($success)): ?>
            <div class="success-icon">✓</div>
            <h2>¡Listo!</h2>
            <p>Le pagaste a Veterinaria</p>
            <div class="amount">$<?php echo number_format($factura['monto'], 2); ?></div>
            <p style="color: #888; font-size: 0.8rem;">Ya puedes cerrar esta ventana</p>
        <?php else: ?>
            <h3>Confirmar Pago</h3>
            <p class="store">Pagar a <strong>Veterinaria</strong></p>
            <div class="amount">$<?php echo number_format($factura['monto'], 2); ?></div>
            <p><?php echo htmlspecialchars($factura['concepto']); ?></p>
            
            <form method="POST">
                <input type="hidden" name="pay" value="1">
                <button type="submit" class="btn-pay">Pagar Ahora</button>
            </form>
        <?php endif; ?>
    </div>

</body>
</html>
