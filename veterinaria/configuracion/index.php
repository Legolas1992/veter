<?php
// configuracion/index.php
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';

// Only Admin access
if ($_SESSION['usuario_rol'] != 'admin') {
    header("Location: " . BASE_URL . "dashboard/");
    exit;
}

$database = new Database();
$db = $database->getConnection();

$stmt = $db->query("SELECT * FROM configuracion LIMIT 1");
$config = $stmt->fetch();

if (!$config) {
    // Should be seeded, but just in case
    $db->query("INSERT INTO configuracion (nombre_negocio) VALUES ('Mi Veterinaria')");
    $stmt = $db->query("SELECT * FROM configuracion LIMIT 1");
    $config = $stmt->fetch();
}
?>

<div class="page-header">
    <h2>Configuración de la Empresa</h2>
</div>

<div class="card" style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 800px; margin: 0 auto;">
    
    <div style="text-align: center; margin-bottom: 2rem;">
        <?php if(file_exists(__DIR__ . '/../' . $config['logo_path']) || strpos($config['logo_path'], 'http') !== false): ?>
            <img src="<?php echo BASE_URL . $config['logo_path']; ?>" alt="Logo Actual" style="max-height: 100px;">
        <?php else: ?>
            <p style="color: #999;">Sin Logo</p>
        <?php endif; ?>
    </div>

    <form action="guardar.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $config['id']; ?>">

        <div style="display: flex; gap: 1rem;">
             <div class="form-group" style="flex:1;">
                <label>Nombre del Negocio *</label>
                <input type="text" name="nombre_negocio" class="form-control" value="<?php echo htmlspecialchars($config['nombre_negocio']); ?>" required>
            </div>
            <div class="form-group" style="flex:1;">
                <label>Razón Social (Para Facturas)</label>
                <input type="text" name="razon_social" class="form-control" value="<?php echo htmlspecialchars($config['razon_social'] ?? ''); ?>">
            </div>
        </div>

        <div style="display: flex; gap: 1rem;">
             <div class="form-group" style="flex:1;">
                <label>CUIT / NIT</label>
                <input type="text" name="cuit" class="form-control" value="<?php echo htmlspecialchars($config['cuit'] ?? ''); ?>">
            </div>
             <div class="form-group" style="flex:1;">
                <label>Teléfono / Celular</label>
                <input type="text" name="telefono" class="form-control" value="<?php echo htmlspecialchars($config['telefono'] ?? ''); ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label>Dirección</label>
            <input type="text" name="direccion" class="form-control" value="<?php echo htmlspecialchars($config['direccion'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label>Email de Contacto</label>
            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($config['email'] ?? ''); ?>">
        </div>
        
        <hr>

        <div class="form-group">
            <label>Cambiar Logo (JPG/PNG)</label>
            <input type="file" name="logo" class="form-control" accept="image/*">
            <small>Deje en blanco si no desea cambiarlo.</small>
        </div>

        <div style="display: flex; justify-content: flex-end; margin-top: 1rem;">
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
