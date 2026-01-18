<?php
// configuracion/guardar.php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

// Auth Check Admin
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] != 'admin') {
    header("Location: ../dashboard/");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nombre_negocio = $_POST['nombre_negocio'];
    $razon_social = $_POST['razon_social'];
    $cuit = $_POST['cuit'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];

    $database = new Database();
    $db = $database->getConnection();

    // Handle Logo Upload
    $logo_sql = "";
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['logo']['tmp_name'];
        $fileName = $_FILES['logo']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Directory relative to root
            $uploadFileDir = __DIR__ . '/../assets/resources/';
            if (!file_exists($uploadFileDir)) {
                mkdir($uploadFileDir, 0777, true);
            }
            // Sanitize name or use fixed name to avoid clutter
            $newFileName = 'logo.' . $fileExtension;
            $dest_path = $uploadFileDir . $newFileName;

            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                $logo_path = 'assets/resources/' . $newFileName;
                $logo_sql = ", logo_path = :logo_path";
            }
        }
    }

    try {
        $sql = "UPDATE configuracion SET nombre_negocio=:nombre, razon_social=:razon, cuit=:cuit, direccion=:direccion, telefono=:telefono, email=:email $logo_sql WHERE id=:id";
        
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':nombre', $nombre_negocio);
        $stmt->bindParam(':razon', $razon_social);
        $stmt->bindParam(':cuit', $cuit);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id', $id);
        
        if ($logo_sql) {
            $stmt->bindParam(':logo_path', $logo_path);
        }

        $stmt->execute();

        header("Location: index.php?msg=Configuración actualizada correctamente");

    } catch (PDOException $e) {
        header("Location: index.php?error=Error: " . $e->getMessage());
    }
}
?>
