<?php
// clientes/eliminar.php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $database = new Database();
    $db = $database->getConnection();
    
    try {
        $stmt = $db->prepare("DELETE FROM clientes WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        header("Location: index.php?msg=Cliente eliminado correctamente");
    } catch (PDOException $e) {
        header("Location: index.php?error=No se puede eliminar el cliente porque tiene registros asociados");
    }
} else {
    header("Location: index.php");
}
exit;
?>
