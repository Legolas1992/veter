<?php
// historial/index.php
include __DIR__ . '/../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$mascota_id = isset($_GET['mascota_id']) ? $_GET['mascota_id'] : null;
$search = isset($_GET['search']) ? $_GET['search'] : '';

$where = "";
$params = [];

if ($mascota_id) {
    $where = "WHERE h.mascota_id = :mascota_id";
    $params[':mascota_id'] = $mascota_id;
} elseif ($search) {
    $where = "WHERE m.nombre LIKE :search OR c.nombre LIKE :search";
    $params[':search'] = "%$search%";
}

$query = "SELECT h.*, m.nombre as mascota_nombre, c.nombre as dueno_nombre 
          FROM historial_medico h 
          JOIN mascotas m ON h.mascota_id = m.id 
          JOIN clientes c ON m.cliente_id = c.id 
          $where 
          ORDER BY h.fecha DESC";

$stmt = $db->prepare($query);
foreach($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->execute();
$historial = $stmt->fetchAll();
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Historial Médico <?php echo $mascota_id ? "(Mascota #$mascota_id)" : ""; ?></h2>
    <a href="nuevo.php<?php echo $mascota_id ? "?mascota_id=$mascota_id" : ""; ?>" class="btn btn-primary"><i class="fas fa-notes-medical"></i> Nueva Consulta</a>
</div>

<div class="search-box" style="margin-bottom: 1.5rem;">
    <div style="display: flex; gap: 1rem;">
        <input type="text" id="searchInput" class="form-control" placeholder="Buscar por mascota, dueño o diagnóstico..." value="<?php echo htmlspecialchars($search); ?>">
        <input type="hidden" id="mascotaId" value="<?php echo htmlspecialchars($mascota_id); ?>">
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('searchInput');
    const tbody = document.querySelector('tbody');
    const mascotaId = document.getElementById('mascotaId').value;
    
    let timeout = null;

    input.addEventListener('keyup', function() {
        clearTimeout(timeout);
        const val = this.value;
        
        timeout = setTimeout(function() {
            const formData = new FormData();
            formData.append('search', val);
            formData.append('mascota_id', mascotaId);
            
            fetch('buscar.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(html => {
                tbody.innerHTML = html;
            })
            .catch(error => {
                console.error('Error:', error);
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; color:red;">Error al buscar. Intente de nuevo.</td></tr>';
            });
        }, 300); // 300ms delay debounce
    });
});
</script>


<?php if(isset($_GET['msg'])): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($_GET['msg']); ?>
    </div>
<?php endif; ?>

<div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Mascota</th>
                <th>Diagnóstico</th>
                <th>Tratamiento</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($historial) > 0): ?>
                <?php foreach($historial as $entry): ?>
                <tr>
                    <td><?php echo date('d/m/Y H:i', strtotime($entry['fecha'])); ?></td>
                    <td><?php echo htmlspecialchars($entry['mascota_nombre']); ?> <small>(<?php echo htmlspecialchars($entry['dueno_nombre']); ?>)</small></td>
                    <td><?php echo htmlspecialchars(substr($entry['diagnostico'], 0, 50)) . '...'; ?></td>
                    <td><?php echo htmlspecialchars(substr($entry['tratamiento'] ?? '', 0, 50)) . '...'; ?></td>
                    <td>
                        <a href="editar.php?id=<?php echo $entry['id']; ?>" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Ver/Editar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center;">No hay registros médicos.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
