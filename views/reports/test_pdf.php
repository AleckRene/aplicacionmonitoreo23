<?php
require '../../vendor/autoload.php';

use Dompdf\Dompdf;

// Crear una instancia de Dompdf
$dompdf = new Dompdf();

// Contenido HTML del PDF
$html = "
    <h1>Reporte de prueba</h1>
    <p>Este es un reporte de prueba generado con Dompdf.</p>
";

// Cargar el contenido HTML
$dompdf->loadHtml($html);

// Configurar el tamaño de la página y la orientación
$dompdf->setPaper('A4', 'portrait');

// Renderizar el PDF
$dompdf->render();

// Enviar el PDF al navegador
$dompdf->stream("reporte_prueba.pdf", ["Attachment" => false]);
