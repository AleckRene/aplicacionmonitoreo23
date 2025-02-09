<?php
require_once '../../config/config.php';
require_once '../../vendor/autoload.php';

header('Content-Type: text/html; charset=UTF-8');

use Dompdf\Dompdf;

// Inicializar variable HTML
$html = '';

// Obtener fechas
$fecha_inicio = $_GET['fecha_inicio'] ?? 'Sin especificar';
$fecha_fin = $_GET['fecha_fin'] ?? 'Sin especificar';

// Obtener datos de la base de datos
$accesibilidad_calidad = $conn->query("SELECT accesibilidad_servicios, actitud_personal, tarifas_ocultas, factores_mejora, disponibilidad_herramientas FROM accesibilidad_calidad")->fetch_all(MYSQLI_ASSOC);
$percepcion_servicios = $conn->query("SELECT calidad_servicio, servicios_mejorar, cambios_recientes FROM percepcion_servicios")->fetch_all(MYSQLI_ASSOC);

// Definir el mapeo de la escala de Likert
$mapeoLikert = [
    'accesibilidad_servicios' => [
        1 => 'Muy difícil', 2 => 'Difícil', 3 => 'Neutral', 4 => 'Fácil', 5 => 'Muy fácil'
    ],
    'actitud_personal' => [
        1 => 'Muy mala', 2 => 'Mala', 3 => 'Neutral', 4 => 'Buena', 5 => 'Muy buena'
    ],
    'tarifas_ocultas' => [
        1 => 'Nunca', 2 => 'Raramente', 3 => 'A veces', 4 => 'Frecuentemente', 5 => 'Siempre'
    ],
    'factores_mejora' => [
        1 => 'Mayor disponibilidad', 2 => 'Atención rápida', 3 => 'Reducción de costos', 
        4 => 'Mayor capacitación', 5 => 'Mejor tecnología'
    ],
    'disponibilidad_herramientas' => [
        1 => 'Nunca disponibles', 2 => 'Raramente disponibles', 3 => 'A veces disponibles', 
        4 => 'Frecuentemente disponibles', 5 => 'Siempre disponibles'
    ],
    'calidad_servicio' => [
        1 => 'Muy mala', 2 => 'Mala', 3 => 'Regular', 4 => 'Buena', 5 => 'Excelente'
    ],
    'servicios_mejorar' => [
        1 => 'Atención médica', 2 => 'Disponibilidad de insumos', 3 => 'Tiempo de espera', 
        4 => 'Capacitación del personal', 5 => 'Infraestructura'
    ],
    'cambios_recientes' => [
        1 => 'Empeorado', 2 => 'Sin cambios', 3 => 'Mejorado ligeramente', 
        4 => 'Mejorado moderadamente', 5 => 'Mejorado significativamente'
    ]
];

// Función para consolidar datos con mapeo de Likert
function consolidarDatos($data, $columnas, $mapeo = []) {
    $resultados = [];
    foreach ($columnas as $columna) {
        $valoresConvertidos = array_map(function($item) use ($columna, $mapeo) {
            return $mapeo[$columna][$item] ?? $item;
        }, array_column($data, $columna));

        $frecuencias = array_count_values($valoresConvertidos);
        ksort($frecuencias);
        $total = array_sum($frecuencias);

        $resultados[$columna] = [
            'frecuencias' => $frecuencias,
            'total' => $total,
        ];
    }
    return $resultados;
}

// Aplicar consolidación con mapeo
$consolidadoAccesibilidad = consolidarDatos($accesibilidad_calidad, [
    'accesibilidad_servicios', 'actitud_personal', 'tarifas_ocultas', 
    'factores_mejora', 'disponibilidad_herramientas'
], $mapeoLikert);

$consolidadoPercepcion = consolidarDatos($percepcion_servicios, [
    'calidad_servicio', 'servicios_mejorar', 'cambios_recientes'
], $mapeoLikert);

// Generar tabla
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

// Generar PDF con Dompdf
try {
    $dompdf = new Dompdf();
    
    $html = "<html><head><title>Reporte VIH</title>
            <link rel='stylesheet' href='../../assets/css/pdf_styles.css'>
            </head><body>";

    // Agregar logos en la parte superior
    $html .= '<div style="text-align: center; margin-bottom: 20px;">
                <img src="../../assets/img/gobierno.png" style="height: 50px; margin-right: 15px;">
                <img src="../../assets/img/mcp.png" style="height: 50px; margin-right: 15px;">
                <img src="../../assets/img/organizacion.png" style="height: 50px;">
              </div>';

    $html .= "<h1>Reporte Consolidado de VIH</h1>
              <h3>Período: " . htmlspecialchars($fecha_inicio) . " - " . htmlspecialchars($fecha_fin) . "</h3>";

    $html .= generarTabla('Accesibilidad y Calidad', $consolidadoAccesibilidad, true);
    $html .= generarTabla('Percepción de Servicios', $consolidadoPercepcion);
    $html .= '</body></html>';

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $tempPath = '../../temp/reporte_vih_consolidado.pdf';
    if (!is_dir('../../temp')) {
        mkdir('../../temp', 0777, true);
    }
    file_put_contents($tempPath, $dompdf->output());

    echo "<html><head><title>Reporte VIH</title><link rel='stylesheet' href='../../assets/css/pdf_styles.css'></head>
    <body><h1>Reporte generado con éxito</h1>
    <p>El reporte ha sido generado para el período: <strong>{$fecha_inicio} - {$fecha_fin}</strong></p>
    <a href='{$tempPath}' class='btn btn-primary' target='_blank'>Ver PDF</a>
    <a href='../modulo_vih.php' class='btn btn-primary'>Volver al Módulo VIH</a>
    </body></html>";

} catch (Exception $e) {
    error_log("Error al generar el PDF: " . $e->getMessage());
    echo "Error al generar el PDF.";
}
?>
