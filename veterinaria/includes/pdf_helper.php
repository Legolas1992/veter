<?php
// includes/pdf_helper.php
require_once __DIR__ . '/fpdf/fpdf.php';
require_once __DIR__ . '/../config/db.php';

class VeterinaryPDF extends FPDF {
    protected $config;

    function __construct() {
        parent::__construct();
        $db = (new Database())->getConnection();
        $this->config = $db->query("SELECT * FROM configuracion LIMIT 1")->fetch();
    }

    function Header() {
        // Logo
        $logo = __DIR__ . '/../' . ($this->config['logo_path'] ?? '');
        if (file_exists($logo)) {
            $this->Image($logo, 10, 6, 30);
        }

        // Company Details
        // STRICTLY USING ARIAL TO AVOID MISSING FONT FILES
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(80); // Move to right
        $this->Cell(30, 10, utf8_decode($this->config['nombre_negocio'] ?? 'Veterinaria'), 0, 0, 'C');
        $this->Ln(8);

        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 5, utf8_decode($this->config['direccion'] ?? ''), 0, 1, 'C');
        $this->Cell(0, 5, utf8_decode('CUIT: ' . ($this->config['cuit'] ?? '') . ' | Tel: ' . ($this->config['telefono'] ?? '')), 0, 1, 'C');
        $this->Cell(0, 5, utf8_decode($this->config['email'] ?? ''), 0, 1, 'C');
        
        $this->Line(10, 35, 200, 35);
        $this->Ln(15);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Sistema creado por Llanes Manuel Alexandro - Técnico Programador | Pág ' . $this->PageNo() . '/{nb}'), 0, 0, 'C');
    }
}
?>
