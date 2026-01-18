<?php
// mascotas/nuevo.php
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';

$database = new Database();
$db = $database->getConnection();
$clientes = $db->query("SELECT id, nombre, dni FROM clientes ORDER BY nombre ASC")->fetchAll();
?>

<div class="page-header">
    <h2>Nueva Mascota</h2>
</div>

<div class="card" style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto;">
    
    <form action="guardar.php" method="POST">
        <div class="form-group">
            <label for="cliente_id">Dueño *</label>
            <select name="cliente_id" class="form-control" required>
                <option value="">Seleccione un dueño</option>
                <?php foreach($clientes as $cliente): ?>
                    <option value="<?php echo $cliente['id']; ?>">
                        <?php echo htmlspecialchars($cliente['nombre']) . ' (DNI: ' . htmlspecialchars($cliente['dni']) . ')'; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="nombre">Nombre de la Mascota *</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="especie">Especie *</label>
            <select name="especie" class="form-control" required>
                <option value="Perro">Perro</option>
                <option value="Gato">Gato</option>
                <option value="Ave">Ave</option>
                <option value="Otro">Otro</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="raza">Raza</label>
            <input type="text" name="raza" class="form-control">
        </div>

        <div class="form-group">
            <label for="edad">Edad</label>
            <input type="text" name="edad" class="form-control" placeholder="Ej: 2 años">
        </div>

        <div class="form-group">
            <label for="peso">Peso (kg)</label>
            <input type="number" step="0.01" name="peso" class="form-control">
        </div>

        <div class="form-group">
            <label for="sexo">Sexo</label>
            <select name="sexo" class="form-control">
                <option value="M">Macho</option>
                <option value="H">Hembra</option>
            </select>
        </div>
        
        <div style="display: flex; gap: 1rem; justify-content: flex-end;">
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar Mascota</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
