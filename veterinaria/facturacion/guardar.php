<?php
// facturacion/guardar.php update
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cliente_id = $_POST['cliente_id'];
    $monto_total = $_POST['monto_total'];
    
    // Arrays of items
    $tipos = $_POST['tipos'];
    $descripciones = $_POST['descripciones'];
    $cantidades = $_POST['cantidades'];
    $precios = $_POST['precios'];
    $productos_ids = $_POST['productos_ids'];

    // LOGGING START
    $logFile = __DIR__ . '/debug_factura.txt';
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - INICIO SAVE\n", FILE_APPEND);
    file_put_contents($logFile, "POST DATA: " . print_r($_POST, true) . "\n", FILE_APPEND);

    $database = new Database();
    $db = $database->getConnection();

    try {
        $db->beginTransaction();
        file_put_contents($logFile, "Transaction Started\n", FILE_APPEND);

        // 1. Create Invoice Header
        $concepto = count($descripciones) > 0 ? "Factura: " . $descripciones[0] . (count($descripciones) > 1 ? "..." : "") : "Servicios Varios";
        
        $sql = "INSERT INTO facturacion (cliente_id, concepto, monto, fecha, estado) VALUES (:cid, :concepto, :monto, NOW(), 'pendiente')";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':cid', $cliente_id);
        $stmt->bindParam(':concepto', $concepto);
        $stmt->bindParam(':monto', $monto_total);
        $stmt->execute();
        $factura_id = $db->lastInsertId();
        
        file_put_contents($logFile, "Factura Header Inserted ID: $factura_id\n", FILE_APPEND);

        // 2. Process Items
        for ($i = 0; $i < count($tipos); $i++) {
            $tipo = $tipos[$i];
            $desc = $descripciones[$i];
            $cant = $cantidades[$i];
            $precio = $precios[$i];
            $pid = $productos_ids[$i];
            $subtotal = $cant * $precio;
            
            file_put_contents($logFile, "Processing Item $i: Type=$tipo, PID=$pid, Qty=$cant\n", FILE_APPEND);

            if ($cant > 0) {
                // Determine Reference ID and check Stock if Product
                $ref_id = null;
                
                if ($tipo == 'producto' && !empty($pid)) {
                    $ref_id = $pid;
                    
                    // Stock Check
                    $check = $db->prepare("SELECT stock_actual, nombre FROM productos WHERE id = :pid");
                    $check->bindParam(':pid', $pid);
                    $check->execute();
                    $prod = $check->fetch();
                    
                    if (!$prod) {
                        throw new Exception("Producto ID $pid no encontrado");
                    }
                    
                    file_put_contents($logFile, "Stock Check: Found " . $prod['stock_actual'] . " Need $cant\n", FILE_APPEND);

                    if ($prod['stock_actual'] < $cant) {
                        throw new Exception("Stock insuficiente para: " . $prod['nombre']);
                    }

                    // Deduct Stock
                    $update = $db->prepare("UPDATE productos SET stock_actual = stock_actual - :cant WHERE id = :pid");
                    $update->bindParam(':cant', $cant);
                    $update->bindParam(':pid', $pid);
                    $update->execute();

                    // Movement
                    $mov = $db->prepare("INSERT INTO movimientos_stock (producto_id, tipo, cantidad, motivo, referencia_id) VALUES (:pid, 'venta', :cant, 'Factura #$factura_id', :ref)");
                    $mov->bindParam(':pid', $pid);
                    $mov->bindParam(':cant', $cant);
                    $mov->bindParam(':ref', $factura_id);
                    $mov->execute();
                }

                // Insert Detail
                $det = $db->prepare("INSERT INTO factura_detalles (factura_id, tipo, referencia_id, descripcion, cantidad, precio_unitario, subtotal) VALUES (:fid, :tipo, :ref, :desc, :cant, :precio, :sub)");
                $det->bindParam(':fid', $factura_id);
                $det->bindParam(':tipo', $tipo);
                $det->bindParam(':ref', $ref_id);
                $det->bindParam(':desc', $desc);
                $det->bindParam(':cant', $cant);
                $det->bindParam(':precio', $precio);
                $det->bindParam(':sub', $subtotal);
                $det->execute();
            }
        }

        $db->commit();
        file_put_contents($logFile, "COMMIT SUCCESS\n", FILE_APPEND);
        header("Location: index.php?msg=Factura generada correctamente");

    } catch (Exception $e) {
        $db->rollBack();
        file_put_contents($logFile, "ERROR ROLLBACK: " . $e->getMessage() . "\n", FILE_APPEND);
        header("Location: index.php?error=" . urlencode($e->getMessage()));
    }
}
?>
