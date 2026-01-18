<?php
// turnos/index.php
include __DIR__ . '/../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$fecha_filtro = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');
$estado_filtro = isset($_GET['estado']) ? $_GET['estado'] : '';

$where = "WHERE DATE(t.fecha_hora) >= :fecha";
$params = [':fecha' => $fecha_filtro];

if ($estado_filtro) {
    $where .= " AND t.estado = :estado";
    $params[':estado'] = $estado_filtro;
}

$query = "SELECT t.*, m.nombre as mascota_nombre, c.nombre as dueno_nombre 
          FROM turnos t 
          JOIN mascotas m ON t.mascota_id = m.id 
          JOIN clientes c ON m.cliente_id = c.id 
          $where 
          ORDER BY t.fecha_hora ASC";

$stmt = $db->prepare($query);
foreach($params as $key => $val) {
    if ($key == ':fecha' && empty($fecha_filtro)) continue; // Logic adjustment if needed, but for now date defaults to today
    $stmt->bindValue($key, $val);
}
$stmt->execute();
$turnos = $stmt->fetchAll();
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Agenda de Turnos</h2>
    <a href="nuevo.php" class="btn btn-primary"><i class="fas fa-calendar-plus"></i> Nuevo Turno</a>
</div>

<div class="search-box" style="margin-bottom: 1.5rem; background: white; padding: 1rem; border-radius: 8px;">
    <form action="" method="GET" style="display: flex; gap: 1rem; align-items: flex-end;">
        <div class="form-group" style="margin-bottom: 0; flex: 1;">
            <label>Desde Fecha:</label>
            <input type="date" name="fecha" class="form-control" value="<?php echo $fecha_filtro; ?>">
        </div>
        <div class="form-group" style="margin-bottom: 0; flex: 1;">
            <label>Estado:</label>
            <select name="estado" class="form-control">
                <option value="">Todos</option>
                <option value="pendiente" <?php echo $estado_filtro == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                <option value="confirmado" <?php echo $estado_filtro == 'confirmado' ? 'selected' : ''; ?>>Confirmado</option>
                <option value="completado" <?php echo $estado_filtro == 'completado' ? 'selected' : ''; ?>>Completado</option>
                <option value="cancelado" <?php echo $estado_filtro == 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
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
                <th>Fecha y Hora</th>
                <th>Mascota (Dueño)</th>
                <th>Motivo</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($turnos) > 0): ?>
                <?php foreach($turnos as $turno): ?>
                <tr>
                    <td>
                        <?php echo date('d/m/Y H:i', strtotime($turno['fecha_hora'])); ?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($turno['mascota_nombre']); ?> 
                        <small style="display:block; color: #666;"><?php echo htmlspecialchars($turno['dueno_nombre']); ?></small>
                    </td>
                    <td><?php echo htmlspecialchars($turno['motivo']); ?></td>
                    <td>
                        <span style="padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; 
                            background: <?php 
                                switch($turno['estado']) {
                                    case 'confirmado': echo '#e3f2fd; color: #1565c0;'; break;
                                    case 'completado': echo '#e8f5e9; color: #2e7d32;'; break;
                                    case 'cancelado': echo '#ffebee; color: #c62828;'; break;
                                    default: echo '#fff3e0; color: #ef6c00;'; 
                                }
                            ?>">
                            <?php echo ucfirst($turno['estado']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="editar.php?id=<?php echo $turno['id']; ?>" class="btn btn-secondary" style="padding: 0.5rem 1rem;">Gestionar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center;">No hay turnos programados para estos criterios.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
