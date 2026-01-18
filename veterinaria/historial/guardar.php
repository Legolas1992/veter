<?php
// historial/guardar.php update
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $mascota_id = $_POST['mascota_id'];
    $diagnostico = $_POST['diagnostico'];
    $tratamiento = $_POST['tratamiento'];
    $observaciones = $_POST['observaciones'];
    
    // Insumos
    $productos = isset($_POST['productos']) ? $_POST['productos'] : [];
    $cantidades = isset($_POST['cantidades']) ? $_POST['cantidades'] : [];

    $database = new Database();
    $db = $database->getConnection();

    try {
        $db->beginTransaction();

        if ($id) {
            // Update
            $sql = "UPDATE historial_medico SET diagnostico=:diagnostico, tratamiento=:tratamiento, observaciones=:observaciones WHERE id=:id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id);
            // Note: Editing historical product usage is complex (restore stock? re-deduct?). 
            // For simplicity in this version, we don't handle editing product usage, only adding on new.
        } else {
            // Insert
            $sql = "INSERT INTO historial_medico (mascota_id, diagnostico, tratamiento, observaciones, fecha) VALUES (:mascota_id, :diagnostico, :tratamiento, :observaciones, NOW())";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':mascota_id', $mascota_id);
        }

        $stmt->bindParam(':diagnostico', $diagnostico);
        $stmt->bindParam(':tratamiento', $tratamiento);
        $stmt->bindParam(':observaciones', $observaciones);
        $stmt->execute();

        $historial_id = $id ? $id : $db->lastInsertId();

        // Process Products (Only for new inserts to stay simple, or separate logic)
        // We iterate through submitted products
        if (!$id) {
            for ($i = 0; $i < count($productos); $i++) {
                $pid = $productos[$i];
                $cant = $cantidades[$i];

                if (!empty($pid) && $cant > 0) {
                    // Check Stock
                    $check = $db->prepare("SELECT stock_actual, nombre FROM productos WHERE id = :pid");
                    $check->bindParam(':pid', $pid);
                    $check->execute();
                    $prod = $check->fetch();

                    if ($prod['stock_actual'] < $cant) {
                        throw new Exception("Stock insuficiente para " . $prod['nombre']);
                    }

                    // Deduct Stock
                    $update = $db->prepare("UPDATE productos SET stock_actual = stock_actual - :cant WHERE id = :pid");
                    $update->bindParam(':cant', $cant);
                    $update->bindParam(':pid', $pid);
                    $update->execute();

                    // Record Movement
                    $mov = $db->prepare("INSERT INTO movimientos_stock (producto_id, tipo, cantidad, motivo, referencia_id) VALUES (:pid, 'uso_interno', :cant, 'Consulta #$historial_id', :ref)");
                    $mov->bindParam(':pid', $pid);
                    $mov->bindParam(':cant', $cant);
                    $mov->bindParam(':ref', $historial_id);
                    $mov->execute();

                    // Link to History
                    $link = $db->prepare("INSERT INTO diagnostico_productos (historial_id, producto_id, cantidad) VALUES (:hid, :pid, :cant)");
                    $link->bindParam(':hid', $historial_id);
                    $link->bindParam(':pid', $pid);
                    $link->bindParam(':cant', $cant);
                    $link->execute();
                }
            }
        }

        $db->commit();
        header("Location: index.php?msg=Registro guardado correctamente&mascota_id=" . $mascota_id);
        exit;

    } catch (Exception $e) {
        $db->rollBack();
        header("Location: index.php?error=Error: " . $e->getMessage());
        exit;
    }
}
?>
