<?php
require_once '../../config/config.php';
require_once '../../vendor/autoload.php';

use Dompdf\Dompdf;

// Función para calcular porcentajes
function calculatePercentages($data, $total) {
    $percentages = [];
    foreach ($data as $key => $value) {
        $percentages[$key] = ($total > 0) ? round(($value / $total) * 100, 2) : 0;
    }
    return $percentages;
}

// Obtener datos de las tablas
$indicadores_uso = $conn->query("SELECT numero_usuarios, nivel_actividad, frecuencia_recomendaciones, calidad_uso FROM indicadores_uso")->fetch_all(MYSQLI_ASSOC);
$participacion_comunitaria = $conn->query("SELECT nivel_participacion, grupos_comprometidos, estrategias_mejora FROM participacion_comunitaria")->fetch_all(MYSQLI_ASSOC);
$eventos_salud = $conn->query("SELECT nombre_evento, descripcion FROM eventos_salud")->fetch_all(MYSQLI_ASSOC);
$necesidades_comunitarias = $conn->query("SELECT descripcion, acciones, area_prioritaria FROM necesidades_comunitarias")->fetch_all(MYSQLI_ASSOC);

// Consolidar y calcular porcentajes para Indicadores de Uso
$indicadoresConsolidados = [];
foreach (['nivel_actividad', 'frecuencia_recomendaciones', 'calidad_uso'] as $key) {
    $indicadoresConsolidados[$key] = array_count_values(array_column($indicadores_uso, $key));
}
$totalIndicadores = count($indicadores_uso);
$indicadoresPercentages = [];
foreach ($indicadoresConsolidados as $key => $values) {
    $indicadoresPercentages[$key] = calculatePercentages($values, $totalIndicadores);
}

// Consolidar y calcular porcentajes para Participación Comunitaria
$participacionConsolidada = [];
foreach (['nivel_participacion', 'grupos_comprometidos', 'estrategias_mejora'] as $key) {
    $participacionConsolidada[$key] = array_count_values(array_column($participacion_comunitaria, $key));
}
$totalParticipacion = count($participacion_comunitaria);
$participacionPercentages = [];
foreach ($participacionConsolidada as $key => $values) {
    $participacionPercentages[$key] = calculatePercentages($values, $totalParticipacion);
}

// Consolidar y calcular porcentajes para Eventos de Salud
$eventosConsolidados = [];
foreach (['nombre_evento', 'descripcion'] as $key) {
    $eventosConsolidados[$key] = array_count_values(array_column($eventos_salud, $key));
}
$totalEventos = count($eventos_salud);
$eventosPercentages = [];
foreach ($eventosConsolidados as $key => $values) {
    $eventosPercentages[$key] = calculatePercentages($values, $totalEventos);
}

// Consolidar y calcular porcentajes para Necesidades Comunitarias
$necesidadesConsolidadas = [];
foreach (['descripcion', 'acciones', 'area_prioritaria'] as $key) {
    $necesidadesConsolidadas[$key] = array_count_values(array_column($necesidades_comunitarias, $key));
}
$totalNecesidades = count($necesidades_comunitarias);
$necesidadesPercentages = [];
foreach ($necesidadesConsolidadas as $key => $values) {
    $necesidadesPercentages[$key] = calculatePercentages($values, $totalNecesidades);
}

// Construcción del reporte
$css = '<style>
    body { font-family: Arial, sans-serif; margin: 20px; color: #333; }
    h1, h2 { color: #007BFF; text-align: center; }
    table { width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 12px; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #007BFF; color: white; }
    tr:nth-child(even) { background-color: #f2f2f2; }
</style>';

$html = "<html><head><title>Reporte Consolidado</title>{$css}</head><body>";
$html .= '<h1>Reporte Consolidado con Porcentajes</h1>';

// Indicadores de Uso
$html .= '<h2>Indicadores de Uso</h2><table><thead><tr><th>Indicador</th><th>Frecuencia</th><th>Porcentaje (%)</th></tr></thead><tbody>';
foreach ($indicadoresConsolidados as $key => $values) {
    foreach ($values as $indicator => $frequency) {
        $html .= "<tr><td>{$indicator}</td><td>{$frequency}</td><td>{$indicadoresPercentages[$key][$indicator]}%</td></tr>";
    }
}
$html .= '</tbody></table>';

// Participación Comunitaria
$html .= '<h2>Participación Comunitaria</h2><table><thead><tr><th>Indicador</th><th>Frecuencia</th><th>Porcentaje (%)</th></tr></thead><tbody>';
foreach ($participacionConsolidada as $key => $values) {
    foreach ($values as $indicator => $frequency) {
        $html .= "<tr><td>{$indicator}</td><td>{$frequency}</td><td>{$participacionPercentages[$key][$indicator]}%</td></tr>";
    }
}
$html .= '</tbody></table>';

// Eventos de Salud
$html .= '<h2>Eventos de Salud</h2><table><thead><tr><th>Indicador</th><th>Frecuencia</th><th>Porcentaje (%)</th></tr></thead><tbody>';
foreach ($eventosConsolidados as $key => $values) {
    foreach ($values as $indicator => $frequency) {
        $html .= "<tr><td>{$indicator}</td><td>{$frequency}</td><td>{$eventosPercentages[$key][$indicator]}%</td></tr>";
    }
}
$html .= '</tbody></table>';

// Necesidades Comunitarias
$html .= '<h2>Necesidades Comunitarias</h2><table><thead><tr><th>Indicador</th><th>Frecuencia</th><th>Porcentaje (%)</th></tr></thead><tbody>';
foreach ($necesidadesConsolidadas as $key => $values) {
    foreach ($values as $indicator => $frequency) {
        $html .= "<tr><td>{$indicator}</td><td>{$frequency}</td><td>{$necesidadesPercentages[$key][$indicator]}%</td></tr>";
    }
}
$html .= '</tbody></table>';

$html .= '</body></html>';

// Generar PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("reporte_consolidado.pdf", ["Attachment" => false]);
?>
