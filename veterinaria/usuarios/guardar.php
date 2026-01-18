<?php
// usuarios/guardar.php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

// Auth Check - Admin Only
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] != 'admin') {
    header("Location: ../dashboard/");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = $_POST['password']; // Raw password
    $rol = $_POST['rol'];

    $database = new Database();
    $db = $database->getConnection();

    try {
        if ($id) {
            // Update
            $sql = "UPDATE usuarios SET nombre=:nombre, email=:email, rol=:rol WHERE id=:id";
            
            // If password is provided, update it too
            if (!empty($password)) {
                 $sql = "UPDATE usuarios SET nombre=:nombre, email=:email, rol=:rol, password=:password WHERE id=:id";
            }
            
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id);
        } else {
            // Insert
            $sql = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (:nombre, :email, :password, :rol)";
            $stmt = $db->prepare($sql);
        }

        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':rol', $rol);
        
        if (!empty($password) || !$id) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bindParam(':password', $hashed_password);
        }

        $stmt->execute();

        header("Location: index.php?msg=Usuario guardado correctamente");
        exit;

    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
             header("Location: index.php?error=El email ya esta registrado");
        } else {
             header("Location: index.php?error=Error: " . $e->getMessage());
        }
        exit;
    }
}
?>
