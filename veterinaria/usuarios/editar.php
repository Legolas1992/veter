<?php
// usuarios/editar.php
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';

// Only Admin access
if ($_SESSION['usuario_rol'] != 'admin') {
    header("Location: " . BASE_URL . "dashboard/");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];
$database = new Database();
$db = $database->getConnection();
$stmt = $db->prepare("SELECT * FROM usuarios WHERE id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$usuario = $stmt->fetch();

if (!$usuario) {
    header("Location: index.php?error=Usuario no encontrado");
    exit;
}
?>

<div class="page-header">
    <h2>Editar Usuario</h2>
</div>

<div class="card" style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto;">
    
    <form action="guardar.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">

        <div class="form-group">
            <label for="nombre">Nombre Completo *</label>
            <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="password">Contraseña (Dejar en blanco para mantener la actual)</label>
            <input type="password" name="password" class="form-control" placeholder="Nueva contraseña (opcional)">
        </div>
        
        <div class="form-group">
            <label for="rol">Rol *</label>
            <select name="rol" class="form-control" required>
                <option value="recepcionista" <?php echo ($usuario['rol'] == 'recepcionista') ? 'selected' : ''; ?>>Recepcionista</option>
                <option value="veterinario" <?php echo ($usuario['rol'] == 'veterinario') ? 'selected' : ''; ?>>Veterinario</option>
                <option value="admin" <?php echo ($usuario['rol'] == 'admin') ? 'selected' : ''; ?>>Administrador</option>
            </select>
        </div>
        
        <div style="display: flex; gap: 1rem; justify-content: flex-end;">
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
