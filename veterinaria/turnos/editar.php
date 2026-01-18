<?php
// turnos/editar.php
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];
$database = new Database();
$db = $database->getConnection();

$stmt = $db->prepare("SELECT * FROM turnos WHERE id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$turno = $stmt->fetch();

if (!$turno) {
    header("Location: index.php?error=Turno no encontrado");
    exit;
}

// Split datetime
$fecha = date('Y-m-d', strtotime($turno['fecha_hora']));
$hora = date('H:i', strtotime($turno['fecha_hora']));

$mascotas = $db->query("SELECT m.id, m.nombre, c.nombre as dueno_nombre FROM mascotas m JOIN clientes c ON m.cliente_id = c.id ORDER BY m.nombre ASC")->fetchAll();
?>

<div class="page-header">
    <h2>Gestionar Turno</h2>
</div>

<div class="card" style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto;">
    
    <form action="guardar.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $turno['id']; ?>">

        <div class="form-group">
            <label for="mascota_id">Mascota *</label>
            <select name="mascota_id" class="form-control" required>
                <?php foreach($mascotas as $m): ?>
                    <option value="<?php echo $m['id']; ?>" <?php echo ($m['id'] == $turno['mascota_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($m['nombre']) . ' (Dueño: ' . htmlspecialchars($m['dueno_nombre']) . ')'; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group" style="display: flex; gap: 1rem;">
            <div style="flex: 1;">
                <label for="fecha">Fecha *</label>
                <input type="date" name="fecha" class="form-control" value="<?php echo $fecha; ?>" required>
            </div>
            <div style="flex: 1;">
                <label for="hora">Hora *</label>
                <input type="time" name="hora" class="form-control" value="<?php echo $hora; ?>" required>
            </div>
        </div>
        
        <div class="form-group">
            <label for="motivo">Motivo *</label>
            <input type="text" name="motivo" class="form-control" value="<?php echo htmlspecialchars($turno['motivo']); ?>" required>
        </div>

         <div class="form-group">
            <label for="estado">Estado *</label>
            <select name="estado" class="form-control">
                <option value="pendiente" <?php echo ($turno['estado'] == 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                <option value="confirmado" <?php echo ($turno['estado'] == 'confirmado') ? 'selected' : ''; ?>>Confirmado</option>
                <option value="completado" <?php echo ($turno['estado'] == 'completado') ? 'selected' : ''; ?>>Completado</option>
                <option value="cancelado" <?php echo ($turno['estado'] == 'cancelado') ? 'selected' : ''; ?>>Cancelado</option>
            </select>
        </div>
        
        <div style="display: flex; gap: 1rem; justify-content: flex-end;">
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Actualizar Turno</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
