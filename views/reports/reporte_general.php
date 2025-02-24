<?php
require_once '../../config/config.php';
require_once '../../vendor/autoload.php';

header('Content-Type: text/html; charset=UTF-8');

use Dompdf\Dompdf;

// 🔹 **Validar y obtener fechas del formulario**
$fecha_inicio = isset($_GET['fecha_inicio']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['fecha_inicio'])
    ? $conn->real_escape_string($_GET['fecha_inicio'])
    : 'Sin especificar';

$fecha_fin = isset($_GET['fecha_fin']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['fecha_fin'])
    ? $conn->real_escape_string($_GET['fecha_fin'])
    : 'Sin especificar';

// 🔹 **Verificar si la columna "fecha" existe en cada tabla**
function columnaFechaExiste($conn, $tabla) {
    $result = $conn->query("SHOW COLUMNS FROM $tabla LIKE 'fecha'");
    return $result && $result->num_rows > 0;
}

$tablas = ['indicadores_uso', 'participacion_comunitaria', 'eventos_salud', 'necesidades_comunitarias'];
$filtros = [];

foreach ($tablas as $tabla) {
    if (columnaFechaExiste($conn, $tabla) && $fecha_inicio !== 'Sin especificar' && $fecha_fin !== 'Sin especificar') {
        $filtros[$tabla] = " WHERE fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    } else {
        $filtros[$tabla] = "";
    }
}

// 🔹 **Obtener datos de la base de datos**
function fetchData($conn, $query) {
    try {
        $result = $conn->query($query);
        if (!$result) {
            error_log("Error en la consulta: " . $conn->error);
            return [];
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    } catch (Exception $e) {
        error_log("Excepción en la consulta: " . $e->getMessage());
        return [];
    }
}

$indicadores_uso = fetchData($conn, "SELECT nivel_actividad, frecuencia_recomendaciones, calidad_uso FROM indicadores_uso" . $filtros['indicadores_uso']);
$participacion_comunitaria = fetchData($conn, "SELECT nivel_participacion, grupos_comprometidos, estrategias_mejora FROM participacion_comunitaria" . $filtros['participacion_comunitaria']);
$eventos_salud = fetchData($conn, "SELECT nombre_evento, descripcion, acciones FROM eventos_salud" . $filtros['eventos_salud']);
$necesidades_comunitarias = fetchData($conn, "SELECT descripcion, acciones, area_prioritaria FROM necesidades_comunitarias" . $filtros['necesidades_comunitarias']);

// 🔹 **Función para consolidar datos**
function consolidarDatos($data, $columnas) {
    $resultados = [];
    foreach ($columnas as $columna) {
        $frecuencias = array_count_values(array_column($data, $columna));
        ksort($frecuencias);
        $total = array_sum($frecuencias);
        $resultados[$columna] = ['frecuencias' => $frecuencias, 'total' => $total];
    }
    return $resultados;
}

$consolidadoIndicadores = consolidarDatos($indicadores_uso, ['nivel_actividad', 'frecuencia_recomendaciones', 'calidad_uso']);
$consolidadoParticipacion = consolidarDatos($participacion_comunitaria, ['nivel_participacion', 'grupos_comprometidos', 'estrategias_mejora']);
$consolidadoEventos = consolidarDatos($eventos_salud, ['nombre_evento', 'descripcion', 'acciones']);
$consolidadoNecesidades = consolidarDatos($necesidades_comunitarias, ['descripcion', 'acciones', 'area_prioritaria']);

// 🔹 **Función para generar tablas**
function generarTabla($titulo, $datosConsolidados) {
    $html = "<h2>{$titulo}</h2>";
    if (!empty($datosConsolidados)) {
        foreach ($datosConsolidados as $indicador => $datos) {
            $totalGeneral = $datos['total'];
            $html .= "<table border='1' cellpadding='5' cellspacing='0'>
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
            $html .= "<tr><td><strong>Total</strong></td><td><strong>{$totalGeneral}</strong></td><td><strong>100%</strong></td></tr>
                        </tbody></table><br>";
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

    $css = file_get_contents('../../assets/css/pdf_styles.css');
    $html = "<html><head><title>Reporte General</title><style>{$css}</style></head><body>
            <div>
            <h1>Reporte General</h1>
            <h3>Período: " . htmlspecialchars($fecha_inicio) . " - " . htmlspecialchars($fecha_fin) . "</h3>";

    $html .= generarTabla('Indicadores de Uso', $consolidadoIndicadores);
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
    <body>
    <h1>Reporte generado con éxito</h1>
    <p>El reporte ha sido generado para el período: <strong>{$fecha_inicio} - {$fecha_fin}</strong></p>
    <a href='{$tempPath}' class='btn btn-primary' target='_blank'>Ver PDF</a>
    <a href='../modulo_general.php' class='btn btn-primary'>Volver al Módulo General</a>
    </body></html>";

} catch (Exception $e) {
    error_log("Error al generar el PDF: " . $e->getMessage());
    echo "Error al generar el PDF.";
}
?>
