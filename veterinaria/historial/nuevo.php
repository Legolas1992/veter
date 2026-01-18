<?php
// historial/nuevo.php
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';

$mascota_id_preselected = isset($_GET['mascota_id']) ? $_GET['mascota_id'] : '';

$database = new Database();
$db = $database->getConnection();
$mascotas = $db->query("SELECT m.id, m.nombre, c.nombre as dueno_nombre FROM mascotas m JOIN clientes c ON m.cliente_id = c.id ORDER BY m.nombre ASC")->fetchAll();
$productos = $db->query("SELECT * FROM productos WHERE estado='activo' ORDER BY nombre ASC")->fetchAll();
?>

<div class="page-header">
    <h2>Nueva Consulta Médica</h2>
</div>

<div class="card" style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 800px; margin: 0 auto;">
    
    <form action="guardar.php" method="POST">
        <div class="form-group">
            <label for="mascota_id">Paciente (Mascota) *</label>
            <select name="mascota_id" class="form-control" required>
                <option value="">Seleccione una mascota</option>
                <?php foreach($mascotas as $m): ?>
                    <option value="<?php echo $m['id']; ?>" <?php echo ($m['id'] == $mascota_id_preselected) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($m['nombre']) . ' (Dueño: ' . htmlspecialchars($m['dueno_nombre']) . ')'; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="diagnostico">Diagnóstico *</label>
            <textarea name="diagnostico" class="form-control" rows="3" required></textarea>
        </div>
        
        <div class="form-group">
            <label for="tratamiento">Tratamiento / Medicación</label>
            <textarea name="tratamiento" class="form-control" rows="3"></textarea>
        </div>

        <!-- Insumos / Productos Utilizados -->
        <hr>
        <h4>Insumos / Medicamentos Utilizados</h4>
        <div id="insumos-container">
            <div class="insumo-row" style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                <select name="productos[]" class="form-control">
                    <option value="">-- Seleccionar Producto (Opcional) --</option>
                    <?php foreach($productos as $p): ?>
                        <option value="<?php echo $p['id']; ?>">
                            <?php echo htmlspecialchars($p['nombre']) . ($p['stock_actual'] < 5 ? " (Stock Bajo: {$p['stock_actual']})" : ""); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="number" name="cantidades[]" class="form-control" placeholder="Cant." style="width: 100px;" min="1" value="1">
            </div>
        </div>
        <button type="button" class="btn btn-secondary btn-sm" onclick="addInsumoRow()">+ Agregar otro producto</button>
        <hr>
        
        <div class="form-group">
            <label for="observaciones">Observaciones Adicionales</label>
            <textarea name="observaciones" class="form-control" rows="3"></textarea>
        </div>
        
        <div style="display: flex; gap: 1rem; justify-content: flex-end;">
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar Consulta</button>
        </div>
    </form>
</div>

<script>
function addInsumoRow() {
    const container = document.getElementById('insumos-container');
    const firstRow = container.querySelector('.insumo-row');
    const newRow = firstRow.cloneNode(true);
    newRow.querySelector('select').value = "";
    newRow.querySelector('input').value = "1";
    container.appendChild(newRow);
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
