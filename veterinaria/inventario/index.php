<?php
// inventario/index.php
include __DIR__ . '/../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$search = isset($_GET['search']) ? $_GET['search'] : '';
$where = "WHERE p.estado = 'activo'";
$params = [];

if ($search) {
    $where .= " AND (p.nombre LIKE :search OR r.nombre LIKE :search)";
    $params[':search'] = "%$search%";
}

$query = "SELECT p.*, r.nombre as rubro_nombre 
          FROM productos p 
          LEFT JOIN rubros r ON p.rubro_id = r.id 
          $where 
          ORDER BY p.nombre ASC";

$stmt = $db->prepare($query);
foreach($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->execute();
$productos = $stmt->fetchAll();
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Control de Stock</h2>
    <div>
        <a href="<?php echo BASE_URL; ?>reportes/exportar_stock.php" target="_blank" class="btn btn-secondary" style="background-color: #d32f2f; color: white;"><i class="fas fa-file-pdf"></i> Exportar PDF</a>
        <a href="movimientos.php" class="btn btn-secondary"><i class="fas fa-exchange-alt"></i> Movimientos</a>
        <a href="nuevo_producto.php" class="btn btn-primary"><i class="fas fa-box-open"></i> Nuevo Producto</a>
    </div>
</div>

<div class="search-box" style="margin-bottom: 1.5rem;">
    <form action="" method="GET" style="display: flex; gap: 1rem;">
        <input type="text" name="search" class="form-control" placeholder="Buscar producto o rubro..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn btn-secondary">Buscar</button>
    </form>
</div>

<?php if(isset($_GET['msg'])): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($_GET['msg']); ?>
    </div>
<?php endif; ?>

<div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Rubro</th>
                <th>Presentación</th>
                <th>Stock</th>
                <th>Precio Venta</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($productos) > 0): ?>
                <?php foreach($productos as $p): ?>
                <tr style="<?php echo ($p['stock_actual'] <= $p['stock_minimo']) ? 'background-color: #fff3e0;' : ''; ?>">
                    <td>
                        <?php echo htmlspecialchars($p['nombre']); ?>
                        <?php if($p['stock_actual'] <= $p['stock_minimo']): ?>
                            <span style="color: red; font-size: 0.8rem; font-weight: bold;"><i class="fas fa-exclamation-triangle"></i> Stock Bajo</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($p['rubro_nombre'] ?? 'Sin Rubro'); ?></td>
                    <td><?php echo htmlspecialchars($p['presentacion']); ?></td>
                    <td style="font-weight: bold; <?php echo ($p['stock_actual'] <= 0) ? 'color: red;' : ''; ?>">
                        <?php echo $p['stock_actual']; ?>
                    </td>
                    <td>$<?php echo number_format($p['precio_venta'], 2); ?></td>
                    <td>
                        <a href="editar_producto.php?id=<?php echo $p['id']; ?>" class="btn btn-secondary" style="padding: 0.5rem 1rem;">Editar</a>
                        <!-- Adjustment Link -->
                        <a href="movimientos.php?producto_id=<?php echo $p['id']; ?>" class="btn btn-secondary" style="padding: 0.5rem 1rem; background-color: #795548;" title="Ajustar Stock"><i class="fas fa-sliders-h"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center;">No hay productos registrados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
