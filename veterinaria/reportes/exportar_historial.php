<?php
// reportes/exportar_historial.php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/pdf_helper.php';

// Auth Check
if (!isset($_SESSION['usuario_id'])) {
    header("Location: " . BASE_URL . "auth/login.php");
    exit;
}

if (!isset($_GET['mascota_id'])) {
    die("Falta ID de mascota");
}

$id = $_GET['mascota_id'];
$database = new Database();
$db = $database->getConnection();

// Datos Mascota
$stmt = $db->prepare("SELECT m.*, c.nombre as dueno, c.dni FROM mascotas m JOIN clientes c ON m.cliente_id = c.id WHERE m.id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$mascota = $stmt->fetch();

if (!$mascota) die("Mascota no encontrada");

// Historial
$hist = $db->prepare("SELECT * FROM historial_medico WHERE mascota_id = :id ORDER BY fecha DESC");
$hist->bindParam(':id', $id);
$hist->execute();
$entradas = $hist->fetchAll();

$pdf = new VeterinaryPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, utf8_decode('Historial Clínico'), 0, 1, 'C');
$pdf->Ln(5);

// Header Info
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(100, 10, utf8_decode('Paciente: ' . $mascota['nombre']), 0, 0);
$pdf->Cell(0, 10, utf8_decode('Especie: ' . $mascota['especie'] . ' (' . $mascota['raza'] . ')'), 0, 1);
$pdf->Cell(100, 10, utf8_decode('Dueño: ' . $mascota['dueno']), 0, 0);
$pdf->Cell(0, 10, utf8_decode('DNI: ' . $mascota['dni']), 0, 1);
$pdf->Line(10, 55, 200, 55); // Adjusted Y pos since logo adds height
$pdf->Ln(10);

// Entries
$pdf->SetFont('Arial', '', 10);

if (count($entradas) > 0) {
    foreach($entradas as $e) {
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetFillColor(230, 230, 230);
        $pdf->Cell(0, 8, utf8_decode('Fecha: ' . date('d/m/Y H:i', strtotime($e['fecha']))), 1, 1, 'L', true);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(40, 8, utf8_decode('Diagnóstico:'), 0, 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->MultiCell(0, 8, utf8_decode($e['diagnostico']), 0, 'L');

        if (!empty($e['tratamiento'])) {
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(40, 8, utf8_decode('Tratamiento:'), 0, 0);
            $pdf->SetFont('Arial', '', 10);
            $pdf->MultiCell(0, 8, utf8_decode($e['tratamiento']), 0, 'L');
        }

        // Get Products Used
        $prodParams = [':hid' => $e['id']];
        $prods = $db->prepare("SELECT dp.cantidad, p.nombre FROM diagnostico_productos dp JOIN productos p ON dp.producto_id = p.id WHERE dp.historial_id = :hid");
        $prods->execute($prodParams);
        $insumos = $prods->fetchAll();

        if (count($insumos) > 0) {
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(40, 6, utf8_decode('Insumos:'), 0, 0);
            $pdf->SetFont('Arial', 'I', 9);
            $txt = "";
            foreach($insumos as $i) {
                $txt .= $i['nombre'] . " (x" . $i['cantidad'] . "), ";
            }
            $pdf->MultiCell(0, 6, utf8_decode(rtrim($txt, ', ')), 0, 'L');
        }

        $pdf->Ln(5);
    }
} else {
    $pdf->Cell(0, 10, utf8_decode('No hay registros médicos para esta mascota.'), 0, 1, 'C');
}

ob_clean();
$pdf->Output('I', 'Historial_' . $mascota['nombre'] . '.pdf');
?>
