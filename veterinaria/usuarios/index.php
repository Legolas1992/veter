<?php
// usuarios/index.php
include __DIR__ . '/../includes/header.php';

// Only Admin access
if ($_SESSION['usuario_rol'] != 'admin') {
    header("Location: " . BASE_URL . "dashboard/");
    exit;
}

$database = new Database();
$db = $database->getConnection();

$stmt = $db->query("SELECT * FROM usuarios ORDER BY nombre ASC");
$usuarios = $stmt->fetchAll();
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Gestión de Usuarios</h2>
    <a href="nuevo.php" class="btn btn-primary"><i class="fas fa-user-plus"></i> Nuevo Usuario</a>
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
                <th>Email</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($usuarios as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['nombre']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td>
                    <span style="padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; background: #e0f7fa; color: #006064;">
                        <?php echo ucfirst($user['rol']); ?>
                    </span>
                </td>
                <td>
                    <a href="editar.php?id=<?php echo $user['id']; ?>" class="btn btn-secondary" style="padding: 0.5rem 1rem;">Editar</a>
                    <?php if($_SESSION['usuario_id'] != $user['id']): // Prevent self-delete ?>
                        <a href="eliminar.php?id=<?php echo $user['id']; ?>" class="btn btn-danger btn-delete" style="padding: 0.5rem 1rem;">Eliminar</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
