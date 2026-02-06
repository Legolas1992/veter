<?php
// turnos/eliminar.php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $database = new Database();
    $db = $database->getConnection();
    
    try {
        $stmt = $db->prepare("DELETE FROM turnos WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        header("Location: index.php?msg=Turno eliminado correctamente");
    } catch (PDOException $e) {
        header("Location: index.php?error=Error: " . $e->getMessage());
    }
} else {
    header("Location: index.php");
}
exit;
?>
