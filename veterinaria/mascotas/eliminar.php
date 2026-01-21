<?php
// mascotas/eliminar.php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

// Auth check
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $database = new Database();
    $db = $database->getConnection();

    try {
        $stmt = $db->prepare("DELETE FROM mascotas WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        header("Location: index.php?msg=Mascota eliminada correctamente");
    } catch (PDOException $e) {
        // Log error instead of showing it
        error_log("Error deleting pet: " . $e->getMessage());
        header("Location: index.php?error=Error al eliminar la mascota");
    }
} else {
    header("Location: index.php");
}
exit;
?>
