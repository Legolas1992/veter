<?php
// inventario/nuevo_producto.php
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';

$database = new Database();
$db = $database->getConnection();
$rubros = $db->query("SELECT * FROM rubros ORDER BY nombre ASC")->fetchAll();
?>

<div class="page-header">
    <h2>Nuevo Producto</h2>
</div>

<div class="card" style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto;">
    
    <form action="guardar_producto.php" method="POST">
        <div class="form-group">
            <label for="rubro_id">Rubro / Categoría *</label>
            <select name="rubro_id" class="form-control" required>
                <?php foreach($rubros as $r): ?>
                    <option value="<?php echo $r['id']; ?>"><?php echo htmlspecialchars($r['nombre']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="nombre">Nombre del Producto *</label>
            <input type="text" name="nombre" class="form-control" required placeholder="Ej: Vacuna Triple Felina">
        </div>
        
        <div class="form-group">
            <label for="presentacion">Presentación / Medida</label>
            <input type="text" name="presentacion" class="form-control" placeholder="Ej: 1 Dosis, Botella 1L, Caja 10u">
        </div>
        
        <div style="display: flex; gap: 1rem;">
             <div class="form-group" style="flex:1;">
                <label for="precio_compra">Costo ($)</label>
                <input type="number" step="0.01" name="precio_compra" class="form-control" value="0.00">
            </div>
            <div class="form-group" style="flex:1;">
                <label for="precio_venta">Precio Venta ($) *</label>
                <input type="number" step="0.01" name="precio_venta" class="form-control" required value="0.00">
            </div>
        </div>

        <div style="display: flex; gap: 1rem;">
             <div class="form-group" style="flex:1;">
                <label for="stock_inicial">Stock Inicial</label>
                <input type="number" name="stock_inicial" class="form-control" value="0">
            </div>
            <div class="form-group" style="flex:1;">
                <label for="stock_minimo">Stock Mínimo (Alerta)</label>
                <input type="number" name="stock_minimo" class="form-control" value="5">
            </div>
        </div>
        
        <div style="display: flex; gap: 1rem; justify-content: flex-end;">
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar Producto</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
