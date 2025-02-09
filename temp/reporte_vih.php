<?php
require_once '../../config/config.php';
require_once '../../vendor/autoload.php';

use Dompdf\Dompdf;

// Consultas con consolidación de datos
$accesibilidad_calidad = $conn->query("
    SELECT 
        AVG(accesibilidad_servicios) as promedio_accesibilidad,
        AVG(actitud_personal) as promedio_actitud,
        AVG(tarifas_ocultas) as promedio_tarifas,
        COUNT(*) as total_registros
    FROM accesibilidad_calidad
")->fetch_assoc();

$percepcion_servicios = $conn->query("
    SELECT 
        AVG(calidad_servicio) as promedio_calidad,
        COUNT(*) as total_registros,
        servicios_mejorar,
        COUNT(servicios_mejorar) as total_servicios
    FROM percepcion_servicios
    GROUP BY servicios_mejorar
")->fetch_all(MYSQLI_ASSOC);

// Estilos CSS
$css = '
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; color: #333; }
        h1, h2 { color: #007BFF; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 12px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #007BFF; color: white; }
    </style>
';

// Construcción del reporte consolidado
$html = '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte VIH Consolidado</title>
    ' . $css . '
</head>
<body>
    <h1>Reporte VIH Consolidado</h1>';

// Accesibilidad y Calidad
$html .= '<h2>Accesibilidad y Calidad</h2>';
$html .= '<table><thead><tr><th>Promedio Accesibilidad</th><th>Promedio Actitud Personal</th><th>Promedio Tarifas Ocultas</th><th>Total Registros</th></tr></thead><tbody>';
$html .= "<tr><td>{$accesibilidad_calidad['promedio_accesibilidad']}</td><td>{$accesibilidad_calidad['promedio_actitud']}</td><td>{$accesibilidad_calidad['promedio_tarifas']}</td><td>{$accesibilidad_calidad['total_registros']}</td></tr>";
$html .= '</tbody></table>';

// Percepción de Servicios
$html .= '<h2>Percepción de Servicios</h2>';
$html .= '<table><thead><tr><th>Servicio a Mejorar</th><th>Promedio Calidad</th><th>Total Registros</th></tr></thead><tbody>';
foreach ($percepcion_servicios as $row) {
    $html .= "<tr><td>{$row['servicios_mejorar']}</td><td>{$row['promedio_calidad']}</td><td>{$row['total_servicios']}</td></tr>";
}
$html .= '</tbody></table>';

$html .= '</body></html>';

// Generar PDF con Dompdf
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Guardar el PDF
$pdfFilePath = '../../temp/reporte_vih_consolidado.pdf';
file_put_contents($pdfFilePath, $dompdf->output());

// Opciones de navegación
echo '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Opciones del Reporte</title>
</head>
<body>
    <h1>Reporte Generado Exitosamente</h1>
    <a href="' . $pdfFilePath . '" target="_blank">Ver Reporte</a>
    <br>
    <a href="../modulo_vih.php">Volver al Módulo VIH</a>
</body>
</html>';
?>