<?php
// reportes/exportar_stock.php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/pdf_helper.php';

// Auth Check
if (!isset($_SESSION['usuario_id'])) {
    header("Location: " . BASE_URL . "auth/login.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();
$productos = $db->query("SELECT p.*, r.nombre as rubro FROM productos p LEFT JOIN rubros r ON p.rubro_id = r.id ORDER BY p.nombre ASC")->fetchAll();

// Extends VeterinaryPDF to inherit Header/Footer
class StockPDF extends VeterinaryPDF {
    function SubHeader() {
        $this->SetFont('Arial','B',15);
        $this->Cell(0,10,'Reporte de Stock',0,0,'C');
        $this->Ln(15);

        // Col headers
        $this->SetFont('Arial','B',10);
        $this->Cell(60,10,'Producto',1);
        $this->Cell(40,10,'Rubro',1);
        $this->Cell(40,10,utf8_decode('Presentación'),1);
        $this->Cell(20,10,'Stock',1);
        $this->Cell(30,10,'Precio Venta',1);
        $this->Ln();
    }
}

$pdf = new StockPDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SubHeader();
$pdf->SetFont('Arial','',10);

foreach($productos as $p) {
    if ($p['stock_actual'] <= $p['stock_minimo']) {
        $pdf->SetTextColor(200,0,0);
    } else {
        $pdf->SetTextColor(0,0,0);
    }

    $pdf->Cell(60,10,utf8_decode($p['nombre']),1);
    $pdf->Cell(40,10,utf8_decode($p['rubro'] ?? ''),1);
    $pdf->Cell(40,10,utf8_decode($p['presentacion'] ?? ''),1);
    $pdf->Cell(20,10,$p['stock_actual'],1, 0, 'C');
    $pdf->Cell(30,10,'$' . number_format($p['precio_venta'], 2),1, 0, 'R');
    $pdf->Ln();
}

ob_clean();
$pdf->Output('I', 'Reporte_Stock.pdf');
?>
