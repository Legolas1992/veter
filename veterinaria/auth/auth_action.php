<?php
// auth/auth_action.php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'login') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        header("Location: login.php?error=Todos los campos son obligatorios");
        exit;
    }

    $database = new Database();
    $db = $database->getConnection();

    try {
        $stmt = $db->prepare("SELECT id, nombre, password, rol FROM usuarios WHERE email = :email");
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['usuario_nombre'] = $user['nombre'];
            $_SESSION['usuario_rol'] = $user['rol'];
            
            header("Location: " . BASE_URL . "dashboard/");
            exit;
        } else {
            header("Location: login.php?error=Credenciales incorrectas");
            exit;
        }

    } catch (PDOException $e) {
        header("Location: login.php?error=Error del sistema: " . $e->getMessage());
        exit;
    }
} else {
    header("Location: login.php");
    exit;
}
?>
