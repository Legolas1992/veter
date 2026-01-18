<?php
// historial/editar.php
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];
$database = new Database();
$db = $database->getConnection();

$stmt = $db->prepare("SELECT * FROM historial_medico WHERE id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$entry = $stmt->fetch();

if (!$entry) {
    header("Location: index.php?error=Registro no encontrado");
    exit;
}

// Fetch pet name for display only (usually you don't change the pet of a past record)
$stmtM = $db->prepare("SELECT nombre FROM mascotas WHERE id = :id");
$stmtM->bindParam(':id', $entry['mascota_id']);
$stmtM->execute();
$mascota = $stmtM->fetch();
?>

<div class="page-header">
    <h2>Editar Consulta - <?php echo htmlspecialchars($mascota['nombre'] ?? 'Desconocido'); ?> (<?php echo date('d/m/Y', strtotime($entry['fecha'])); ?>)</h2>
</div>

<div class="card" style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 800px; margin: 0 auto;">
    
    <form action="guardar.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $entry['id']; ?>">
        <input type="hidden" name="mascota_id" value="<?php echo $entry['mascota_id']; ?>">

        <div class="form-group">
            <label for="diagnostico">Diagnóstico *</label>
            <textarea name="diagnostico" class="form-control" rows="3" required><?php echo htmlspecialchars($entry['diagnostico']); ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="tratamiento">Tratamiento / Medicación</label>
            <textarea name="tratamiento" class="form-control" rows="3"><?php echo htmlspecialchars($entry['tratamiento']); ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="observaciones">Observaciones Adicionales</label>
            <textarea name="observaciones" class="form-control" rows="3"><?php echo htmlspecialchars($entry['observaciones']); ?></textarea>
        </div>
        
        <div style="display: flex; gap: 1rem; justify-content: flex-end;">
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Actualizar Consulta</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
