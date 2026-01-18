<?php
// usuarios/nuevo.php
include __DIR__ . '/../includes/header.php';

// Only Admin access
if ($_SESSION['usuario_rol'] != 'admin') {
    header("Location: " . BASE_URL . "dashboard/");
    exit;
}
?>

<div class="page-header">
    <h2>Nuevo Usuario</h2>
</div>

<div class="card" style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto;">
    
    <form action="guardar.php" method="POST">
        <div class="form-group">
            <label for="nombre">Nombre Completo *</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="password">Contraseña *</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="rol">Rol *</label>
            <select name="rol" class="form-control" required>
                <option value="recepcionista">Recepcionista</option>
                <option value="veterinario">Veterinario</option>
                <option value="admin">Administrador</option>
            </select>
        </div>
        
        <div style="display: flex; gap: 1rem; justify-content: flex-end;">
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Crear Usuario</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
