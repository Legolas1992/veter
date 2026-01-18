<?php
// inventario/movimientos.php
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';

$database = new Database();
$db = $database->getConnection();

$producto_id = isset($_GET['producto_id']) ? $_GET['producto_id'] : '';
$tipo_filtro = isset($_GET['tipo']) ? $_GET['tipo'] : '';

// Fetch all products for dropdown
$productos = $db->query("SELECT * FROM productos ORDER BY nombre ASC")->fetchAll();

// Build query
$where = "WHERE 1=1";
$params = [];

if ($producto_id) {
    $where .= " AND m.producto_id = :pid";
    $params[':pid'] = $producto_id;
}
if ($tipo_filtro) {
    $where .= " AND m.tipo = :tipo";
    $params[':tipo'] = $tipo_filtro;
}

$query = "SELECT m.*, p.nombre as producto_nombre 
          FROM movimientos_stock m 
          JOIN productos p ON m.producto_id = p.id 
          $where 
          ORDER BY m.fecha DESC LIMIT 50";

$stmt = $db->prepare($query);
foreach($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->execute();
$movimientos = $stmt->fetchAll();
?>

<div class="page-header">
    <h2>Movimientos de Stock</h2>
</div>

<!-- New Movement Form -->
<div class="card" style="background: white; padding: 1.5rem; margin-bottom: 2rem; border-radius: 8px;">
    <h3>Registrar Movimiento Manual</h3>
    <form action="guardar_movimiento.php" method="POST" style="display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap;">
        
        <div class="form-group" style="flex: 2; min-width: 200px; margin-bottom: 0;">
            <label>Producto *</label>
            <select name="producto_id" class="form-control" required>
                <option value="">Seleccione...</option>
                <?php foreach($productos as $p): ?>
                    <option value="<?php echo $p['id']; ?>" <?php echo ($producto_id == $p['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($p['nombre']) . " (Stock: " . $p['stock_actual'] . ")"; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group" style="flex: 1; min-width: 150px; margin-bottom: 0;">
            <label>Tipo *</label>
            <select name="tipo" class="form-control" required>
                <option value="entrada">Entrada (+)</option>
                <option value="salida">Salida (-)</option>
                <option value="ajuste">Ajuste (Corrección)</option>
                <option value="uso_interno">Uso Interno (-)</option>
            </select>
        </div>

        <div class="form-group" style="flex: 1; min-width: 100px; margin-bottom: 0;">
            <label>Cantidad *</label>
            <input type="number" name="cantidad" class="form-control" min="1" required>
        </div>

        <div class="form-group" style="flex: 2; min-width: 200px; margin-bottom: 0;">
            <label>Motivo</label>
            <input type="text" name="motivo" class="form-control" placeholder="Ej: Compra a proveedor, Rotura...">
        </div>

        <button type="submit" class="btn btn-primary" style="margin-bottom: 0;">Registrar</button>
    </form>
</div>

<!-- History Table -->
<h3>Historial Reciente</h3>
<div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Producto</th>
                <th>Tipo</th>
                <th>Cantidad</th>
                <th>Motivo</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($movimientos as $m): ?>
            <tr>
                <td><?php echo date('d/m/Y H:i', strtotime($m['fecha'])); ?></td>
                <td><?php echo htmlspecialchars($m['producto_nombre']); ?></td>
                <td>
                    <span style="<?php echo ($m['tipo'] == 'entrada') ? 'color: green;' : 'color: red;'; ?> font-weight: bold;">
                        <?php echo ucfirst($m['tipo']); ?>
                    </span>
                </td>
                <td><?php echo $m['cantidad']; ?></td>
                <td><?php echo htmlspecialchars($m['motivo']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
