<?php
// usuarios/eliminar.php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] != 'admin') {
    header("Location: ../dashboard/");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    if ($id == $_SESSION['usuario_id']) {
        header("Location: index.php?error=No puedes eliminarte a ti mismo");
        exit;
    }

    $database = new Database();
    $db = $database->getConnection();
    
    try {
        $stmt = $db->prepare("DELETE FROM usuarios WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        header("Location: index.php?msg=Usuario eliminado correctamente");
    } catch (PDOException $e) {
        header("Location: index.php?error=Error: " . $e->getMessage());
    }
} else {
    header("Location: index.php");
}
exit;
?>
