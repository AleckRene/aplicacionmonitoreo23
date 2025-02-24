<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lista de módulos válidos y sus archivos asociados
    $modulos_validos = [
        'general' => ['reporte' => 'reporte_general.php', 'modulo' => 'modulo_general.php'],
        'vih' => ['reporte' => 'reporte_vih.php', 'modulo' => 'modulo_vih.php']
    ];

    // Sanitizar entradas
    $fecha_inicio = filter_input(INPUT_POST, 'fecha_inicio', FILTER_SANITIZE_STRING);
    $fecha_fin = filter_input(INPUT_POST, 'fecha_fin', FILTER_SANITIZE_STRING);
    $periodicidad = filter_input(INPUT_POST, 'periodicidad', FILTER_SANITIZE_STRING) ?? 'diario';
    $modulo = filter_input(INPUT_POST, 'modulo', FILTER_SANITIZE_STRING) ?? 'general';

    // Validar módulo
    if (!array_key_exists($modulo, $modulos_validos)) {
        http_response_code(400);
        echo json_encode(["error" => "Módulo inválido."]);
        exit;
    }

    // Validar fechas
    if (!$fecha_inicio || !$fecha_fin || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_inicio) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_fin)) {
        http_response_code(400);
        echo json_encode(["error" => "Formato de fecha inválido."]);
        exit;
    }

    // Validar que la fecha de inicio no sea mayor a la fecha de fin
    if (strtotime($fecha_inicio) > strtotime($fecha_fin)) {
        http_response_code(400);
        echo json_encode(["error" => "La fecha de inicio no puede ser posterior a la fecha de fin."]);
        exit;
    }

    // Obtener el archivo de reporte según el módulo
    $reporte = $modulos_validos[$modulo]['reporte'];

    // Redirigir al reporte correspondiente con los parámetros
    header("Location: $reporte?fecha_inicio=" . urlencode($fecha_inicio) . "&fecha_fin=" . urlencode($fecha_fin) . "&periodicidad=" . urlencode($periodicidad) . "&modulo=" . urlencode($modulo));
    exit;
}
?>
