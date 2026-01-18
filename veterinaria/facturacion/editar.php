<?php
// facturacion/editar.php
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];
$database = new Database();
$db = $database->getConnection();

$stmt = $db->prepare("SELECT * FROM facturacion WHERE id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$factura = $stmt->fetch();

if (!$factura) {
    header("Location: index.php?error=Factura no encontrada");
    exit;
}

$clientes = $db->query("SELECT id, nombre, dni FROM clientes ORDER BY nombre ASC")->fetchAll();
?>

<div class="page-header">
    <h2>Gestionar Factura #<?php echo $factura['id']; ?></h2>
</div>

<div class="card" style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto;">
    
    <form action="guardar.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $factura['id']; ?>">

        <div class="form-group">
            <label for="cliente_id">Cliente *</label>
            <select name="cliente_id" class="form-control" required>
                <?php foreach($clientes as $c): ?>
                    <option value="<?php echo $c['id']; ?>" <?php echo ($c['id'] == $factura['cliente_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($c['nombre']) . ' (DNI: ' . htmlspecialchars($c['dni']) . ')'; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="concepto">Concepto *</label>
            <input type="text" name="concepto" class="form-control" value="<?php echo htmlspecialchars($factura['concepto']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="monto">Monto ($) *</label>
            <input type="number" step="0.01" name="monto" class="form-control" value="<?php echo htmlspecialchars($factura['monto']); ?>" required>
        </div>

        <div class="form-group">
            <label for="estado">Estado</label>
            <select name="estado" class="form-control">
                <option value="pendiente" <?php echo ($factura['estado'] == 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                <option value="pagado" <?php echo ($factura['estado'] == 'pagado') ? 'selected' : ''; ?>>Pagado</option>
            </select>
        </div>
        
        <div style="display: flex; gap: 1rem; justify-content: flex-end;">
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Actualizar Factura</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
