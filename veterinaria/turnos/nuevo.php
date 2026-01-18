<?php
// turnos/nuevo.php
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';

$database = new Database();
$db = $database->getConnection();
$mascotas = $db->query("SELECT m.id, m.nombre, c.nombre as dueno_nombre FROM mascotas m JOIN clientes c ON m.cliente_id = c.id ORDER BY m.nombre ASC")->fetchAll();
?>

<div class="page-header">
    <h2>Nuevo Turno</h2>
</div>

<div class="card" style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto;">
    
    <form action="guardar.php" method="POST">
        <div class="form-group">
            <label for="mascota_id">Mascota *</label>
            <select name="mascota_id" class="form-control" required>
                <option value="">Seleccione una mascota</option>
                <?php foreach($mascotas as $m): ?>
                    <option value="<?php echo $m['id']; ?>">
                        <?php echo htmlspecialchars($m['nombre']) . ' (Dueño: ' . htmlspecialchars($m['dueno_nombre']) . ')'; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group" style="display: flex; gap: 1rem;">
            <div style="flex: 1;">
                <label for="fecha">Fecha *</label>
                <input type="date" name="fecha" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
            </div>
            <div style="flex: 1;">
                <label for="hora">Hora *</label>
                <input type="time" name="hora" class="form-control" required>
            </div>
        </div>
        
        <div class="form-group">
            <label for="motivo">Motivo de la Consulta *</label>
            <input type="text" name="motivo" class="form-control" required placeholder="Ej: Vacunación, Control, Urgencia...">
        </div>
        
        <div style="display: flex; gap: 1rem; justify-content: flex-end;">
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Agendar Turno</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
