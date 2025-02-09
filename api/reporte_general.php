<?php
require_once '../vendor/autoload.php';
require_once '../config/config.php';

use Dompdf\Dompdf;

try {
    // Generar contenido HTML
    ob_start();
    include '../views/reports/reporte_general.php';
    $html = ob_get_clean();

    // Crear instancia de Dompdf
    $dompdf = new Dompdf();

    // Configurar opciones (opcional)
    $dompdf->set_option('defaultFont', 'Arial');
    $dompdf->set_option('isRemoteEnabled', true);

    $dompdf->loadHtml($html);

    // Configurar tamaño de papel y orientación
    $dompdf->setPaper('A4', 'landscape');

    // Renderizar el HTML como PDF
    $dompdf->render();

    // Enviar el archivo PDF al navegador
    $dompdf->stream("reporte_general.pdf", ["Attachment" => false]);

} catch (Dompdf\Exception $e) {
    // Manejar errores específicos de Dompdf
    error_log("Error de Dompdf al generar PDF: " . $e->getMessage()); // Registrar el error
    echo "Error al generar el PDF. Por favor, inténtalo de nuevo más tarde."; // Mostrar un mensaje genérico al usuario
} catch (Exception $e) {
    // Manejar errores generales
    error_log("Error general al generar PDF: " . $e->getMessage()); // Registrar el error
    echo "Error al generar el PDF. Por favor, inténtalo de nuevo más tarde."; // Mostrar un mensaje genérico al usuario
}
?>