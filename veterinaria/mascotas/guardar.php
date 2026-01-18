<?php
// mascotas/guardar.php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $cliente_id = $_POST['cliente_id'];
    $nombre = $_POST['nombre'];
    $especie = $_POST['especie'];
    $raza = $_POST['raza'];
    $edad = $_POST['edad'];
    $peso = $_POST['peso'];
    $sexo = $_POST['sexo'];

    $database = new Database();
    $db = $database->getConnection();

    try {
        if ($id) {
            // Update
            $sql = "UPDATE mascotas SET cliente_id=:cliente_id, nombre=:nombre, especie=:especie, raza=:raza, edad=:edad, peso=:peso, sexo=:sexo WHERE id=:id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id);
        } else {
            // Insert
            $sql = "INSERT INTO mascotas (cliente_id, nombre, especie, raza, edad, peso, sexo) VALUES (:cliente_id, :nombre, :especie, :raza, :edad, :peso, :sexo)";
            $stmt = $db->prepare($sql);
        }

        $stmt->bindParam(':cliente_id', $cliente_id);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':especie', $especie);
        $stmt->bindParam(':raza', $raza);
        $stmt->bindParam(':edad', $edad);
        $stmt->bindParam(':peso', $peso);
        $stmt->bindParam(':sexo', $sexo);

        $stmt->execute();

        header("Location: index.php?msg=Operacion realizada con exito");
        exit;

    } catch (PDOException $e) {
        header("Location: index.php?error=Error en la base de datos: " . $e->getMessage());
        exit;
    }
}
?>
