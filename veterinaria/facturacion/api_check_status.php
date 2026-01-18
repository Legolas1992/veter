<?php
// facturacion/api_check_status.php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'ID no proporcionado']);
    exit;
}

$id = $_GET['id'];
$database = new Database();
$db = $database->getConnection();

try {
    $stmt = $db->prepare("SELECT estado FROM facturacion WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $factura = $stmt->fetch();

    if ($factura) {
        echo json_encode(['status' => $factura['estado']]);
    } else {
        echo json_encode(['error' => 'Factura no encontrada']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
