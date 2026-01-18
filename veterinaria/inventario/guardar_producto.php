<?php
// inventario/guardar_producto.php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $nombre = $_POST['nombre'];
    $rubro_id = $_POST['rubro_id'];
    $presentacion = $_POST['presentacion'];
    $precio_compra = $_POST['precio_compra'];
    $precio_venta = $_POST['precio_venta'];
    $stock_minimo = $_POST['stock_minimo'];
    // Stock actual is typically initialized to 0 and adjusted via movements, 
    // OR set initially. Let's allow initial set on creation only.
    $stock_inicial = isset($_POST['stock_inicial']) ? $_POST['stock_inicial'] : 0;

    $database = new Database();
    $db = $database->getConnection();

    try {
        if ($id) {
            // Update
            $sql = "UPDATE productos SET nombre=:nombre, rubro_id=:rubro_id, presentacion=:presentacion, precio_compra=:precio_compra, precio_venta=:precio_venta, stock_minimo=:stock_minimo WHERE id=:id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id);
        } else {
            // Insert
            $sql = "INSERT INTO productos (nombre, rubro_id, presentacion, precio_compra, precio_venta, stock_minimo, stock_actual) VALUES (:nombre, :rubro_id, :presentacion, :precio_compra, :precio_venta, :stock_minimo, :stock_inicial)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':stock_inicial', $stock_inicial);
        }

        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':rubro_id', $rubro_id);
        $stmt->bindParam(':presentacion', $presentacion);
        $stmt->bindParam(':precio_compra', $precio_compra);
        $stmt->bindParam(':precio_venta', $precio_venta);
        $stmt->bindParam(':stock_minimo', $stock_minimo);

        $stmt->execute();
        
        // If new and stock > 0, record movement
        if (!$id && $stock_inicial > 0) {
            $new_id = $db->lastInsertId();
            $mov = $db->prepare("INSERT INTO movimientos_stock (producto_id, tipo, cantidad, motivo) VALUES (:id, 'entrada', :cant, 'Inventario Inicial')");
            $mov->bindParam(':id', $new_id);
            $mov->bindParam(':cant', $stock_inicial);
            $mov->execute();
        }

        header("Location: index.php?msg=Producto guardado correctamente");
        exit;

    } catch (PDOException $e) {
        header("Location: index.php?error=Error: " . $e->getMessage());
        exit;
    }
}
?>
