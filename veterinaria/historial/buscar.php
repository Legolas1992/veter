<?php
// historial/buscar.php
// This endpoint returns HTML rows for the table based on search query
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

$search = isset($_POST['search']) ? $_POST['search'] : '';
$mascota_id = isset($_POST['mascota_id']) ? $_POST['mascota_id'] : '';

$database = new Database();
$db = $database->getConnection();

$where = "";
$params = [];

if ($mascota_id) {
    $where = "WHERE h.mascota_id = :mascota_id";
    $params[':mascota_id'] = $mascota_id;
    if ($search) {
        $where .= " AND (h.diagnostico LIKE :search OR h.tratamiento LIKE :search)";
        $params[':search'] = "%$search%";
    }
} elseif ($search) {
    $where = "WHERE m.nombre LIKE :search OR c.nombre LIKE :search OR c.dni LIKE :search";
    $params[':search'] = "%$search%";
}

$query = "SELECT h.*, m.nombre as mascota_nombre, c.nombre as dueno_nombre 
          FROM historial_medico h 
          JOIN mascotas m ON h.mascota_id = m.id 
          JOIN clientes c ON m.cliente_id = c.id 
          $where 
          ORDER BY h.fecha DESC LIMIT 50";

$stmt = $db->prepare($query);
foreach($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->execute();
$historial = $stmt->fetchAll();

if (count($historial) > 0) {
    foreach($historial as $entry) {
        $fecha = date('d/m/Y H:i', strtotime($entry['fecha']));
        $mascota = htmlspecialchars($entry['mascota_nombre']);
        $dueno = htmlspecialchars($entry['dueno_nombre']);
        $diag = htmlspecialchars(substr($entry['diagnostico'], 0, 50)) . '...';
        $trat = htmlspecialchars(substr($entry['tratamiento'] ?? '', 0, 50)) . '...';
        $id = $entry['id'];
        
        echo "<tr>
                <td>$fecha</td>
                <td>$mascota <small>($dueno)</small></td>
                <td>$diag</td>
                <td>$trat</td>
                <td>
                    <a href='editar.php?id=$id' class='btn btn-secondary' style='padding: 0.5rem 1rem; font-size: 0.9rem;'>Ver/Editar</a>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='5' style='text-align: center;'>No se encontraron resultados.</td></tr>";
}
?>
