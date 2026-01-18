<?php
// clientes/index.php
include __DIR__ . '/../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$search = isset($_GET['search']) ? $_GET['search'] : '';
$query = "SELECT * FROM clientes WHERE nombre LIKE :search OR dni LIKE :search ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->bindValue(':search', "%$search%");
$stmt->execute();
$clientes = $stmt->fetchAll();
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Gestión de Clientes</h2>
    <a href="nuevo.php" class="btn btn-primary"><i class="fas fa-plus"></i> Nuevo Cliente</a>
</div>

<div class="search-box" style="margin-bottom: 1.5rem;">
    <form action="" method="GET" style="display: flex; gap: 1rem;">
        <input type="text" name="search" class="form-control" placeholder="Buscar por nombre o DNI..." value="<?php echo htmlspecialchars($search); ?>">
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
                <th>DNI</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($clientes) > 0): ?>
                <?php foreach($clientes as $cliente): ?>
                <tr>
                    <td><?php echo htmlspecialchars($cliente['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($cliente['dni']); ?></td>
                    <td><?php echo htmlspecialchars($cliente['telefono']); ?></td>
                    <td><?php echo htmlspecialchars($cliente['email']); ?></td>
                    <td>
                        <a href="editar.php?id=<?php echo $cliente['id']; ?>" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Editar</a>
                        <a href="eliminar.php?id=<?php echo $cliente['id']; ?>" class="btn btn-danger btn-delete" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center;">No se encontraron clientes.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
