<?php
require_once '../vendor/autoload.php';
require_once '../config/config.php';

use Dompdf\Dompdf;

try {
    // Verificar si el archivo de reporte existe
    $reporte_path = '../views/reports/reporte_general.php';
    if (!file_exists($reporte_path)) {
        throw new Exception("El archivo de reporte no se encuentra en la ruta especificada.");
    }

    // Generar contenido HTML
    ob_start();
    include $reporte_path;
    $html = ob_get_clean();

    // Verificar si el HTML se generó correctamente
    if (empty($html)) {
        throw new Exception("El contenido del reporte está vacío.");
    }

    // Crear instancia de Dompdf
    $dompdf = new Dompdf();

    // Configurar opciones de Dompdf
    $dompdf->set_option('defaultFont', 'Arial');
    $dompdf->set_option('isRemoteEnabled', true);
    $dompdf->set_option('isHtml5ParserEnabled', true);

    // Cargar contenido HTML al generador de PDF
    $dompdf->loadHtml($html);

    // Configurar tamaño de papel y orientación
    $dompdf->setPaper('A4', 'landscape');

    // Renderizar el HTML como PDF
    $dompdf->render();

    // Enviar el archivo PDF al navegador
    $dompdf->stream("reporte_general.pdf", ["Attachment" => false]);

} catch (Exception $e) {
    // Manejo de errores generales y específicos de Dompdf
    http_response_code(500);
    error_log("Error al generar PDF: " . $e->getMessage());
    echo "Error al generar el PDF. Por favor, inténtalo de nuevo más tarde.";
}
?>
