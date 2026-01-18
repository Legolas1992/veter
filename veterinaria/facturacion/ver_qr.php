<?php
// facturacion/ver_qr.php
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];
$database = new Database();
$db = $database->getConnection();

$stmt = $db->prepare("SELECT f.*, c.nombre as cliente_nombre, c.dni FROM facturacion f JOIN clientes c ON f.cliente_id = c.id WHERE f.id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$factura = $stmt->fetch();

if (!$factura) {
    echo "Factura no encontrada.";
    exit;
}

// URL that the user would "scan". In a real local setup, use IP if testing from mobile, or localhost if testing in another tab.
// We will use localhost because the user is testing locally on the same machine.
$simulated_payment_url = BASE_URL . "facturacion/pago_simulado.php?id=" . $id;
?>

<div class="page-header">
    <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
</div>

<div class="card" style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 500px; margin: 2rem auto; text-align: center;">
    
    <h2>Cobro con QR</h2>
    <p style="color: #666; margin-bottom: 1rem;">Cliente: <strong><?php echo htmlspecialchars($factura['cliente_nombre']); ?></strong></p>
    
    <div style="font-size: 2.5rem; color: var(--primary-dark); font-weight: bold; margin-bottom: 1.5rem;">
        $<?php echo number_format($factura['monto'], 2); ?>
    </div>

    <!-- QR Container -->
    <div id="qrcode" style="display: flex; justify-content: center; margin-bottom: 2rem;"></div>

    <p style="font-size: 0.9rem; color: #888;">Escanee este código con su billetera virtual</p>
    
    <div id="status-message" style="margin-top: 1rem; padding: 1rem; border-radius: 4px; display: none;">
        <!-- Status updates here -->
    </div>
    
    <div style="margin-top: 2rem; padding-top: 1rem; border-top: 1px solid #eee;">
        <a href="<?php echo $simulated_payment_url; ?>" target="_blank" class="btn btn-primary" style="background-color: #009ee3;">
            <i class="fas fa-external-link-alt"></i> Simular Escaneo (Abrir App de Pago)
        </a>
        <p style="font-size: 0.8rem; margin-top: 0.5rem; color: #666;">(Abre el link del QR en nueva pestaña)</p>
    </div>

</div>

<!-- QR Code Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    // Generate QR
    var qrData = "<?php echo $simulated_payment_url; ?>";
    new QRCode(document.getElementById("qrcode"), {
        text: qrData,
        width: 200,
        height: 200,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });

    // Polling System
    const invoiceId = <?php echo $id; ?>;
    const statusDiv = document.getElementById('status-message');
    
    function checkStatus() {
        fetch('api_check_status.php?id=' + invoiceId)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'pagado') {
                    statusDiv.style.display = 'block';
                    statusDiv.style.backgroundColor = '#e8f5e9';
                    statusDiv.style.color = '#2e7d32';
                    statusDiv.innerHTML = '<i class="fas fa-check-circle"></i> <strong>¡Pago Aprobado!</strong><br>Redireccionando...';
                    
                    clearInterval(pollInterval);
                    setTimeout(() => {
                        window.location.href = 'index.php?msg=Pago registrado exitosamente';
                    }, 2000);
                }
            })
            .catch(error => console.error('Error polling:', error));
    }

    // Check every 3 seconds
    const pollInterval = setInterval(checkStatus, 3000);
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
