<?php
// clientes/nuevo.php
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h2>Nuevo Cliente</h2>
</div>

<div class="card" style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto;">
    
    <?php if(isset($_GET['error'])): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>

    <form action="guardar.php" method="POST">
        <div class="form-group">
            <label for="nombre">Nombre Completo *</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="dni">DNI / Documento *</label>
            <input type="text" name="dni" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="telefono">Teléfono *</label>
            <input type="text" name="telefono" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" class="form-control">
        </div>
        
        <div class="form-group">
            <label for="direccion">Dirección</label>
            <textarea name="direccion" class="form-control" rows="3"></textarea>
        </div>
        
        <div style="display: flex; gap: 1rem; justify-content: flex-end;">
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar Cliente</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
