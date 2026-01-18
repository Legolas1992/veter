<?php
// download_fpdf.php
$url = 'https://raw.githubusercontent.com/Setasign/FPDF/master/fpdf.php';
$dest = 'includes/fpdf/fpdf.php';

echo "Downloading FPDF...\n";
$content = file_get_contents($url);
if ($content === false) {
    die("Error downloading FPDF.");
}
if (file_put_contents($dest, $content) === false) {
    die("Error saving FPDF to $dest");
}
echo "FPDF downloaded successfully to $dest";
?>
