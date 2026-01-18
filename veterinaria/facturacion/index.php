<?php
// facturacion/index.php
include __DIR__ . '/../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$estado_filtro = isset($_GET['estado']) ? $_GET['estado'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

$where = "WHERE 1=1"; // placeholder
$params = [];

if ($estado_filtro) {
    $where .= " AND f.estado = :estado";
    $params[':estado'] = $estado_filtro;
}

if ($search) {
    $where .= " AND (c.nombre LIKE :search OR f.concepto LIKE :search)";
    $params[':search'] = "%$search%";
}

$query = "SELECT f.*, c.nombre as cliente_nombre 
          FROM facturacion f 
          JOIN clientes c ON f.cliente_id = c.id 
          $where 
          ORDER BY f.fecha DESC";

$stmt = $db->prepare($query);
foreach($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->execute();
$facturas = $stmt->fetchAll();
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Facturación y Pagos</h2>
    <a href="nuevo.php" class="btn btn-primary"><i class="fas fa-file-invoice-dollar"></i> Nueva Factura</a>
</div>

<div class="search-box" style="margin-bottom: 1.5rem; background: white; padding: 1rem; border-radius: 8px;">
    <form action="" method="GET" style="display: flex; gap: 1rem; align-items: flex-end;">
        <div class="form-group" style="margin-bottom: 0; flex: 2;">
            <label>Buscar (Cliente/Concepto):</label>
            <input type="text" name="search" class="form-control" value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="form-group" style="margin-bottom: 0; flex: 1;">
            <label>Estado:</label>
            <select name="estado" class="form-control">
                <option value="">Todos</option>
                <option value="pendiente" <?php echo $estado_filtro == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                <option value="pagado" <?php echo $estado_filtro == 'pagado' ? 'selected' : ''; ?>>Pagado</option>
            </select>
        </div>
        <button type="submit" class="btn btn-secondary">Filtrar</button>
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
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Concepto</th>
                <th>Monto</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($facturas) > 0): ?>
                <?php foreach($facturas as $f): ?>
                <tr>
                    <td><?php echo date('d/m/Y', strtotime($f['fecha'])); ?></td>
                    <td><?php echo htmlspecialchars($f['cliente_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($f['concepto']); ?></td>
                    <td style="font-weight: bold;">$<?php echo number_format($f['monto'], 2); ?></td>
                    <td>
                        <span style="padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; 
                            background: <?php echo ($f['estado'] == 'pagado') ? '#e8f5e9; color: #2e7d32;' : '#ffebee; color: #c62828;'; ?>">
                            <?php echo ucfirst($f['estado']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="ver_qr.php?id=<?php echo $f['id']; ?>" class="btn btn-primary" style="padding: 0.5rem 0.8rem; background-color: #009ee3;" title="Cobrar con QR"><i class="fas fa-qrcode"></i></a>
                        <a href="editar.php?id=<?php echo $f['id']; ?>" class="btn btn-secondary" style="padding: 0.5rem 1rem;">Gestionar</a>
                        <a href="eliminar.php?id=<?php echo $f['id']; ?>" class="btn btn-danger btn-delete" style="padding: 0.5rem 1rem;">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center;">No hay facturas registradas.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
