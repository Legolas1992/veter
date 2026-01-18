<!-- includes/navbar.php -->
<nav class="sidebar">
    <div style="text-align: center; padding: 1rem 0;">
        <h2 style="font-size: 1.5rem; margin:0;">🐾 Menú</h2>
    </div>
    <ul>
        <li><a href="<?php echo BASE_URL; ?>dashboard/" class="<?php echo (strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false) ? 'active' : ''; ?>">📊 Dashboard</a></li>
        
        <li><a href="<?php echo BASE_URL; ?>clientes/" class="<?php echo (strpos($_SERVER['REQUEST_URI'], 'clientes') !== false) ? 'active' : ''; ?>">👥 Clientes</a></li>
        
        <li><a href="<?php echo BASE_URL; ?>mascotas/" class="<?php echo (strpos($_SERVER['REQUEST_URI'], 'mascotas') !== false) ? 'active' : ''; ?>">🐕 Mascotas</a></li>
        
        <li><a href="<?php echo BASE_URL; ?>turnos/" class="<?php echo (strpos($_SERVER['REQUEST_URI'], 'turnos') !== false) ? 'active' : ''; ?>">📅 Turnos</a></li>
        
        <li><a href="<?php echo BASE_URL; ?>historial/" class="<?php echo (strpos($_SERVER['REQUEST_URI'], 'historial') !== false) ? 'active' : ''; ?>">🩺 Historial</a></li>
        
        <!-- Billing & Stock: Open to Admin & Receptionist (and Vet if needed, but per request, Vet focuses on History) -->
        <!-- Logic: If Vet, maybe hide Billing? User said: "el q atiende (Recepcionista) carga cto sale". -->
        <!-- So we assume Vets CAN access but primarily Receptionist uses it. We won't block it hard, just list it. -->
        
        <li><a href="<?php echo BASE_URL; ?>facturacion/" class="<?php echo (strpos($_SERVER['REQUEST_URI'], 'facturacion') !== false) ? 'active' : ''; ?>">💰 Facturación</a></li>
        
        <li><a href="<?php echo BASE_URL; ?>inventario/" class="<?php echo (strpos($_SERVER['REQUEST_URI'], 'inventario') !== false) ? 'active' : ''; ?>">📦 Stock</a></li>
        
        <?php if(isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] == 'admin'): ?>
            <li class="separator" style="border-top: 1px solid rgba(255,255,255,0.1); margin: 0.5rem 0;"></li>
            <li><a href="<?php echo BASE_URL; ?>usuarios/" class="<?php echo (strpos($_SERVER['REQUEST_URI'], 'usuarios') !== false) ? 'active' : ''; ?>">🔐 Usuarios</a></li>
            <li><a href="<?php echo BASE_URL; ?>configuracion/" class="<?php echo (strpos($_SERVER['REQUEST_URI'], 'configuracion') !== false) ? 'active' : ''; ?>">⚙️ Configuración</a></li>
        <?php endif; ?>
        
        <li><a href="<?php echo BASE_URL; ?>auth/logout.php" style="margin-top: 2rem; background: rgba(255,50,50,0.1); color: #ff8a80;">🚪 Cerrar Sesión</a></li>
    </ul>
</nav>
