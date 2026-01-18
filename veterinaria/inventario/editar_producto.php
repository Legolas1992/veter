<?php
// inventario/editar_producto.php
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}
$id = $_GET['id'];
$database = new Database();
$db = $database->getConnection();

$stmt = $db->prepare("SELECT * FROM productos WHERE id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$p = $stmt->fetch();

if (!$p) die("Producto no encontrado");

$rubros = $db->query("SELECT * FROM rubros ORDER BY nombre ASC")->fetchAll();
?>

<div class="page-header">
    <h2>Editar Producto: <?php echo htmlspecialchars($p['nombre']); ?></h2>
</div>

<div class="card" style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto;">
    
    <form action="guardar_producto.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $p['id']; ?>">

        <div class="form-group">
            <label for="rubro_id">Rubro / Categoría *</label>
            <select name="rubro_id" class="form-control" required>
                <?php foreach($rubros as $r): ?>
                    <option value="<?php echo $r['id']; ?>" <?php echo ($r['id'] == $p['rubro_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($r['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="nombre">Nombre del Producto *</label>
            <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($p['nombre']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="presentacion">Presentación / Medida</label>
            <input type="text" name="presentacion" class="form-control" value="<?php echo htmlspecialchars($p['presentacion']); ?>">
        </div>
        
        <div style="display: flex; gap: 1rem;">
             <div class="form-group" style="flex:1;">
                <label for="precio_compra">Costo ($)</label>
                <input type="number" step="0.01" name="precio_compra" class="form-control" value="<?php echo $p['precio_compra']; ?>">
            </div>
            <div class="form-group" style="flex:1;">
                <label for="precio_venta">Precio Venta ($) *</label>
                <input type="number" step="0.01" name="precio_venta" class="form-control" required value="<?php echo $p['precio_venta']; ?>">
            </div>
        </div>

        <div style="display: flex; gap: 1rem;">
             <div class="form-group" style="flex:1;">
                <label>Stock Actual</label>
                <input type="text" class="form-control" value="<?php echo $p['stock_actual']; ?>" disabled>
                <small>Para ajustar, use "Movimientos"</small>
            </div>
            <div class="form-group" style="flex:1;">
                <label for="stock_minimo">Stock Mínimo</label>
                <input type="number" name="stock_minimo" class="form-control" value="<?php echo $p['stock_minimo']; ?>">
            </div>
        </div>
        
        <div style="display: flex; gap: 1rem; justify-content: flex-end;">
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Actualizar Producto</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
