<?php
// reset_admin.php
require_once 'config/config.php';
require_once 'config/db.php';

$database = new Database();
$db = $database->getConnection();

$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);
$email = 'admin@veterinaria.com';

try {
    // Check if user exists
    $stmt = $db->prepare("SELECT id FROM usuarios WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    if ($stmt->fetch()) {
        // Update
        $update = $db->prepare("UPDATE usuarios SET password = :pass WHERE email = :email");
        $update->bindParam(':pass', $hash);
        $update->bindParam(':email', $email);
        $update->execute();
        echo "<h1>Contraseña actualizada correctamente</h1>";
    } else {
        // Create if missing
        $insert = $db->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES ('Administrador', :email, :pass, 'admin')");
        $insert->bindParam(':email', $email);
        $insert->bindParam(':pass', $hash);
        $insert->execute();
        echo "<h1>Usuario Admin creado correctamente</h1>";
    }
    
    echo "<p>Email: $email</p>";
    echo "<p>Password: $password</p>";
    echo "<br><a href='auth/login.php'>Ir al Login</a>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
