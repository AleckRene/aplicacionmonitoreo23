<?php
require_once '../../config/config.php';
require_once '../../vendor/autoload.php';

header('Content-Type: text/html; charset=UTF-8');

use Dompdf\Dompdf;

// Obtener fechas del formulario
$fecha_inicio = $_GET['fecha_inicio'] ?? 'Sin especificar';
$fecha_fin = $_GET['fecha_fin'] ?? 'Sin especificar';

// Consultas a la base de datos
$indicadores_uso = $conn->query("SELECT nivel_actividad, frecuencia_recomendaciones, calidad_uso FROM indicadores_uso")->fetch_all(MYSQLI_ASSOC);
$participacion_comunitaria = $conn->query("SELECT nivel_participacion, grupos_comprometidos, estrategias_mejora FROM participacion_comunitaria")->fetch_all(MYSQLI_ASSOC);
$eventos_salud = $conn->query("SELECT nombre_evento, descripcion, acciones FROM eventos_salud")->fetch_all(MYSQLI_ASSOC);
$necesidades_comunitarias = $conn->query("SELECT descripcion, acciones, area_prioritaria FROM necesidades_comunitarias")->fetch_all(MYSQLI_ASSOC);

// 🔹 **Mapeo de escala de Likert COMPLETO**
$mapeoLikert = [
    'nivel_actividad' => [1 => 'Nada activa', 2 => 'Poco activa', 3 => 'Moderadamente activa', 4 => 'Activa', 5 => 'Muy activa'],
    'frecuencia_recomendaciones' => [1 => 'Raramente', 2 => 'Ocasionalmente', 3 => 'Moderadamente frecuente', 4 => 'Frecuente', 5 => 'Muy frecuente'],
    'calidad_uso' => [1 => 'Deficiente', 2 => 'Aceptable', 3 => 'Buena', 4 => 'Muy buena', 5 => 'Excelente'],
    'nivel_participacion' => [1 => 'Bajo', 2 => 'Moderado', 3 => 'Alto'],
    'grupos_comprometidos' => [1 => 'Brigadistas', 2 => 'Líderes locales', 3 => 'Juntas comunitarias', 4 => 'ONGs locales', 5 => 'Voluntarios'],
    'estrategias_mejora' => [1 => 'Capacitaciones', 2 => 'Red de apoyo comunitario', 3 => 'Mayor difusión', 4 => 'Uso de herramientas digitales', 5 => 'Colaboración con entidades'],
    'area_prioritaria' => [1 => 'Salud', 2 => 'Educación', 3 => 'Infraestructura', 4 => 'Seguridad', 5 => 'Medio ambiente']
];

// 🔹 **Función para consolidar datos**
function consolidarDatos($data, $columnas, $mapeo = []) {
    $resultados = [];
    foreach ($columnas as $columna) {
        $valoresConvertidos = array_map(fn($item) => $mapeo[$columna][$item] ?? $item, array_column($data, $columna));

        $frecuencias = array_count_values($valoresConvertidos);
        ksort($frecuencias);
        $total = array_sum($frecuencias);

        $resultados[$columna] = ['frecuencias' => $frecuencias, 'total' => $total];
    }
    return $resultados;
}

// 🔹 **Aplicar la consolidación con el mapeo**
$consolidadoIndicadores = consolidarDatos($indicadores_uso, ['nivel_actividad', 'frecuencia_recomendaciones', 'calidad_uso'], $mapeoLikert);
$consolidadoParticipacion = consolidarDatos($participacion_comunitaria, ['nivel_participacion', 'grupos_comprometidos', 'estrategias_mejora'], $mapeoLikert);
$consolidadoEventos = consolidarDatos($eventos_salud, ['nombre_evento', 'descripcion', 'acciones']);
$consolidadoNecesidades = consolidarDatos($necesidades_comunitarias, ['descripcion', 'acciones', 'area_prioritaria'], $mapeoLikert);

// 🔹 **Función para generar tablas**
function generarTabla($titulo, $datosConsolidados, $isFirstTable = false) {
    $html = $isFirstTable ? "<h2>{$titulo}</h2>" : "<h2 class='page-break'>{$titulo}</h2>";

    if (!empty($datosConsolidados)) {
        foreach ($datosConsolidados as $indicador => $datos) {
            $totalGeneral = $datos['total'];
            $html .= "<div class='table-container'>
                        <table>
                        <thead>
                            <tr>
                                <th>{$indicador}</th>
                                <th>Frecuencia</th>
                                <th>Porcentaje</th>
                            </tr>
                        </thead>
                        <tbody>";
            foreach ($datos['frecuencias'] as $valor => $frecuencia) {
                $porcentaje = $totalGeneral > 0 ? round(($frecuencia / $totalGeneral) * 100, 2) : 0;
                $html .= "<tr><td>{$valor}</td><td>{$frecuencia}</td><td>{$porcentaje}%</td></tr>";
            }
            $html .= "<tr><td>Total</td><td>{$totalGeneral}</td><td>100%</td></tr>
                        </tbody></table></div>";
        }
    } else {
        $html .= '<p>No hay datos disponibles para esta sección.</p>';
    }
    return $html;
}

// 🔹 **Generar PDF**
try {
    $dompdf = new Dompdf();
    $dompdf->set_option('isHtml5ParserEnabled', true);
    $dompdf->set_option('isRemoteEnabled', true);
    
    // Cargar estilos CSS
    $css = file_get_contents('../../assets/css/pdf_styles.css');
    $html = "<html><head><title>Reporte General</title><style>{$css}</style></head><body>";

    // 🔹 **Encabezado del reporte SIN LOGOS**
    $html .= "<div class='container'>
                <h1>Reporte General</h1>
                <h3>Período: " . htmlspecialchars($fecha_inicio) . " - " . htmlspecialchars($fecha_fin) . "</h3>";

    // 🔹 **Añadir todas las tablas al reporte**
    $html .= generarTabla('Indicadores de Uso', $consolidadoIndicadores, true);
    $html .= generarTabla('Participación Comunitaria', $consolidadoParticipacion);
    $html .= generarTabla('Eventos de Salud', $consolidadoEventos);
    $html .= generarTabla('Necesidades Comunitarias', $consolidadoNecesidades);

    $html .= "</div></body></html>";

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $tempPath = '../../temp/reporte_general_consolidado.pdf';
    file_put_contents($tempPath, $dompdf->output());

    echo "<html><head><title>Reporte General</title><link rel='stylesheet' href='../../assets/css/pdf_styles.css'></head>
    <body><h1>Reporte generado con éxito</h1>
    <p>El reporte ha sido generado para el período: <strong>{$fecha_inicio} - {$fecha_fin}</strong></p>
    <a href='{$tempPath}' class='btn btn-primary' target='_blank'>Ver PDF</a>
    <a href='../modulo_general.php' class='btn btn-primary'>Volver al Módulo General</a>
    </body></html>";

} catch (Exception $e) {
    error_log("Error al generar el PDF: " . $e->getMessage());
    echo "Error al generar el PDF.";
}
?>
