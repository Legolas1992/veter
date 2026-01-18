<?php
// clientes/editar.php
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];
$database = new Database();
$db = $database->getConnection();
$stmt = $db->prepare("SELECT * FROM clientes WHERE id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$cliente = $stmt->fetch();

if (!$cliente) {
    header("Location: index.php?error=Cliente no encontrado");
    exit;
}
?>

<div class="page-header">
    <h2>Editar Cliente</h2>
</div>

<div class="card" style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto;">
    
    <form action="guardar.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $cliente['id']; ?>">
        
        <div class="form-group">
            <label for="nombre">Nombre Completo *</label>
            <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($cliente['nombre']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="dni">DNI / Documento *</label>
            <input type="text" name="dni" class="form-control" value="<?php echo htmlspecialchars($cliente['dni']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="telefono">Teléfono *</label>
            <input type="text" name="telefono" class="form-control" value="<?php echo htmlspecialchars($cliente['telefono']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($cliente['email']); ?>">
        </div>
        
        <div class="form-group">
            <label for="direccion">Dirección</label>
            <textarea name="direccion" class="form-control" rows="3"><?php echo htmlspecialchars($cliente['direccion']); ?></textarea>
        </div>
        
        <div style="display: flex; gap: 1rem; justify-content: flex-end;">
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Actualizar Cliente</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
