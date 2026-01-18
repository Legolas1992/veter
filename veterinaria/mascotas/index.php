<?php
// mascotas/index.php
include __DIR__ . '/../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$search = isset($_GET['search']) ? $_GET['search'] : '';
$query = "SELECT m.*, c.nombre as dueno_nombre 
          FROM mascotas m 
          JOIN clientes c ON m.cliente_id = c.id 
          WHERE m.nombre LIKE :search OR c.nombre LIKE :search 
          ORDER BY m.created_at DESC";
$stmt = $db->prepare($query);
$stmt->bindValue(':search', "%$search%");
$stmt->execute();
$mascotas = $stmt->fetchAll();
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Gestión de Mascotas</h2>
    <a href="nuevo.php" class="btn btn-primary"><i class="fas fa-paw"></i> Nueva Mascota</a>
</div>

<div class="search-box" style="margin-bottom: 1.5rem;">
    <form action="" method="GET" style="display: flex; gap: 1rem;">
        <input type="text" name="search" class="form-control" placeholder="Buscar por mascota o dueño..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn btn-secondary">Buscar</button>
    </form>
</div>

<?php if(isset($_GET['msg'])): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($_GET['msg']); ?>
    </div>
<?php endif; ?>

<div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Especie</th>
                <th>Raza</th>
                <th>Dueño</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($mascotas) > 0): ?>
                <?php foreach($mascotas as $mascota): ?>
                <tr>
                    <td><?php echo htmlspecialchars($mascota['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($mascota['especie']); ?></td>
                    <td><?php echo htmlspecialchars($mascota['raza']); ?></td>
                    <td><a href="../clientes/editar.php?id=<?php echo $mascota['cliente_id']; ?>"><?php echo htmlspecialchars($mascota['dueno_nombre']); ?></a></td>
                    <td>
                        <a href="<?php echo BASE_URL; ?>reportes/exportar_historial.php?mascota_id=<?php echo $mascota['id']; ?>" target="_blank" class="btn btn-info" style="padding: 0.5rem 0.5rem; font-size: 0.9rem; background-color: #7b1fa2; color: white;" title="PDF Historial"><i class="fas fa-file-pdf"></i></a>
                        <a href="editar.php?id=<?php echo $mascota['id']; ?>" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Editar</a>
                        <a href="eliminar.php?id=<?php echo $mascota['id']; ?>" class="btn btn-danger btn-delete" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center;">No se encontraron mascotas.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
