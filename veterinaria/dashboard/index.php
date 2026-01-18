<?php
// dashboard/index.php
include __DIR__ . '/../includes/header.php';

$database = new Database();
$db = $database->getConnection();

// Fetch Stats
$stats = [
    'clientes' => 0,
    'mascotas' => 0,
    'turnos_hoy' => 0,
    'ingresos_hoy' => 0
];

try {
    $stats['clientes'] = $db->query("SELECT COUNT(*) FROM clientes")->fetchColumn();
    $stats['mascotas'] = $db->query("SELECT COUNT(*) FROM mascotas")->fetchColumn();
    $stats['turnos_hoy'] = $db->query("SELECT COUNT(*) FROM turnos WHERE DATE(fecha_hora) = CURDATE()")->fetchColumn();
    $stmt = $db->query("SELECT SUM(monto) FROM facturacion WHERE DATE(fecha) = CURDATE() AND estado = 'pagado'");
    $stats['ingresos_hoy'] = $stmt->fetchColumn() ?: 0;
} catch(PDOException $e) {
    // Handle error silently or log
}
?>

<div class="dashboard-container">
    <h2>Resumen General</h2>
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info">
                <h3><?php echo $stats['clientes']; ?></h3>
                <p>Clientes Registrados</p>
            </div>
            <div class="stat-icon">
                <i class="fas fa-users fa-2x" style="color: var(--primary-color);"></i>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-info">
                <h3><?php echo $stats['mascotas']; ?></h3>
                <p>Pacientes (Mascotas)</p>
            </div>
            <div class="stat-icon">
                <i class="fas fa-paw fa-2x" style="color: var(--warning);"></i>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-info">
                <h3><?php echo $stats['turnos_hoy']; ?></h3>
                <p>Turnos Hoy</p>
            </div>
            <div class="stat-icon">
                <i class="fas fa-calendar-check fa-2x" style="color: var(--accent-color);"></i>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-info">
                <h3>$<?php echo number_format($stats['ingresos_hoy'], 2); ?></h3>
                <p>Ingresos Hoy</p>
            </div>
            <div class="stat-icon">
                <i class="fas fa-dollar-sign fa-2x" style="color: var(--success);"></i>
            </div>
        </div>
    </div>

    <!-- Quick Actions or Recent Activity could go here -->
    <div style="margin-top: 2rem;">
        <h3>Acciones Rápidas</h3>
        <div style="display: flex; gap: 1rem; margin-top: 1rem;">
            <a href="<?php echo BASE_URL; ?>turnos/nuevo.php" class="btn btn-primary"><i class="fas fa-plus"></i> Nuevo Turno</a>
            <a href="<?php echo BASE_URL; ?>clientes/nuevo.php" class="btn btn-secondary"><i class="fas fa-user-plus"></i> Nuevo Cliente</a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
