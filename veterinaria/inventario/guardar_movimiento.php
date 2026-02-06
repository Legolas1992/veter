<?php
// inventario/guardar_movimiento.php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $producto_id = $_POST['producto_id'];
    $tipo = $_POST['tipo'];
    $cantidad = (int)$_POST['cantidad'];
    $motivo = $_POST['motivo'];

    if ($cantidad <= 0) {
         header("Location: movimientos.php?error=La cantidad debe ser mayor a 0");
         exit;
    }

    $database = new Database();
    $db = $database->getConnection();

    try {
        $db->beginTransaction();

        // 1. Insert movement
        $stmt = $db->prepare("INSERT INTO movimientos_stock (producto_id, tipo, cantidad, motivo) VALUES (:pid, :tipo, :cant, :motivo)");
        $stmt->bindParam(':pid', $producto_id);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':cant', $cantidad);
        $stmt->bindParam(':motivo', $motivo);
        $stmt->execute();

        // 2. Update Product Stock
        $operator = ($tipo == 'entrada') ? '+' : '-';
        
        // Prevent negative stock for outgoing
        if ($operator == '-') {
            $check = $db->prepare("SELECT stock_actual, nombre FROM productos WHERE id = :pid");
            $check->bindParam(':pid', $producto_id);
            $check->execute();
            $prod = $check->fetch();
            if ($prod['stock_actual'] < $cantidad) {
                throw new Exception("Stock insuficiente para realizar esta salida. Stock actual: " . $prod['stock_actual']);
            }
        }

        $update = $db->prepare("UPDATE productos SET stock_actual = stock_actual $operator :cant WHERE id = :pid");
        $update->bindParam(':cant', $cantidad);
        $update->bindParam(':pid', $producto_id);
        $update->execute();

        $db->commit();
        header("Location: movimientos.php?msg=Movimiento registrado correctamente");

    } catch (Exception $e) {
        $db->rollBack();
        header("Location: movimientos.php?error=" . urlencode($e->getMessage()));
    }
}
?>
