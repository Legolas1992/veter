<?php
// mascotas/editar.php
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];
$database = new Database();
$db = $database->getConnection();

$stmt = $db->prepare("SELECT * FROM mascotas WHERE id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$mascota = $stmt->fetch();

if (!$mascota) {
    header("Location: index.php?error=Mascota no encontrada");
    exit;
}

$clientes = $db->query("SELECT id, nombre, dni FROM clientes ORDER BY nombre ASC")->fetchAll();
?>

<div class="page-header">
    <h2>Editar Mascota</h2>
</div>

<div class="card" style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto;">
    
    <form action="guardar.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $mascota['id']; ?>">

        <div class="form-group">
            <label for="cliente_id">Dueño *</label>
            <select name="cliente_id" class="form-control" required>
                <?php foreach($clientes as $cliente): ?>
                    <option value="<?php echo $cliente['id']; ?>" <?php echo ($cliente['id'] == $mascota['cliente_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cliente['nombre']) . ' (DNI: ' . htmlspecialchars($cliente['dni']) . ')'; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="nombre">Nombre de la Mascota *</label>
            <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($mascota['nombre']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="especie">Especie *</label>
            <select name="especie" class="form-control" required>
                <option value="Perro" <?php echo ($mascota['especie'] == 'Perro') ? 'selected' : ''; ?>>Perro</option>
                <option value="Gato" <?php echo ($mascota['especie'] == 'Gato') ? 'selected' : ''; ?>>Gato</option>
                <option value="Ave" <?php echo ($mascota['especie'] == 'Ave') ? 'selected' : ''; ?>>Ave</option>
                <option value="Otro" <?php echo ($mascota['especie'] == 'Otro') ? 'selected' : ''; ?>>Otro</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="raza">Raza</label>
            <input type="text" name="raza" class="form-control" value="<?php echo htmlspecialchars($mascota['raza']); ?>">
        </div>

        <div class="form-group">
            <label for="edad">Edad</label>
            <input type="text" name="edad" class="form-control" value="<?php echo htmlspecialchars($mascota['edad']); ?>">
        </div>

        <div class="form-group">
            <label for="peso">Peso (kg)</label>
            <input type="number" step="0.01" name="peso" class="form-control" value="<?php echo htmlspecialchars($mascota['peso']); ?>">
        </div>

        <div class="form-group">
            <label for="sexo">Sexo</label>
            <select name="sexo" class="form-control">
                <option value="M" <?php echo ($mascota['sexo'] == 'M') ? 'selected' : ''; ?>>Macho</option>
                <option value="H" <?php echo ($mascota['sexo'] == 'H') ? 'selected' : ''; ?>>Hembra</option>
            </select>
        </div>
        
        <div style="display: flex; gap: 1rem; justify-content: flex-end;">
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Actualizar Mascota</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
