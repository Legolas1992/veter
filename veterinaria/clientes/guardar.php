<?php
// clientes/guardar.php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $nombre = $_POST['nombre'];
    $dni = $_POST['dni'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $direccion = $_POST['direccion'];

    $database = new Database();
    $db = $database->getConnection();

    try {
        if ($id) {
            // Update
            $sql = "UPDATE clientes SET nombre=:nombre, dni=:dni, telefono=:telefono, email=:email, direccion=:direccion WHERE id=:id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id);
        } else {
            // Insert
            $sql = "INSERT INTO clientes (nombre, dni, telefono, email, direccion) VALUES (:nombre, :dni, :telefono, :email, :direccion)";
            $stmt = $db->prepare($sql);
        }

        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':dni', $dni);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':direccion', $direccion);

        $stmt->execute();

        header("Location: index.php?msg=Operacion realizada con exito");
        exit;

    } catch (PDOException $e) {
        // Check for duplicate entry
        if ($e->getCode() == 23000) {
             header("Location: nuevo.php?error=El DNI ya esta registrado" . ($id ? "&id=$id" : ""));
        } else {
             header("Location: index.php?error=Error en la base de datos: " . $e->getMessage());
        }
        exit;
    }
}
?>
