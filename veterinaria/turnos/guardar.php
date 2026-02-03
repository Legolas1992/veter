<?php
// turnos/guardar.php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $mascota_id = $_POST['mascota_id'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $motivo = $_POST['motivo'];
    $estado = isset($_POST['estado']) ? $_POST['estado'] : 'pendiente';

    $fecha_hora = $fecha . ' ' . $hora;

    $database = new Database();
    $db = $database->getConnection();

    try {
        if ($id) {
            // Update
            $sql = "UPDATE turnos SET mascota_id=:mascota_id, fecha_hora=:fecha_hora, motivo=:motivo, estado=:estado WHERE id=:id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':estado', $estado);
        } else {
            // Insert
            $sql = "INSERT INTO turnos (mascota_id, fecha_hora, motivo, estado) VALUES (:mascota_id, :fecha_hora, :motivo, 'pendiente')";
            $stmt = $db->prepare($sql);
        }

        $stmt->bindParam(':mascota_id', $mascota_id);
        $stmt->bindParam(':fecha_hora', $fecha_hora);
        $stmt->bindParam(':motivo', $motivo);

        $stmt->execute();

        header("Location: index.php?msg=Turno guardado correctamente");
        exit;

    } catch (PDOException $e) {
        header("Location: index.php?error=Error: " . $e->getMessage());
        exit;
    }
}
?>
