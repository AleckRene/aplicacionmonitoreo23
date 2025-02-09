<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Limpiar y validar las entradas
    $fecha_inicio = isset($_POST['fecha_inicio']) ? trim($_POST['fecha_inicio']) : '';
    $fecha_fin = isset($_POST['fecha_fin']) ? trim($_POST['fecha_fin']) : '';
    $periodicidad = isset($_POST['periodicidad']) ? trim($_POST['periodicidad']) : 'diario';
    $modulo = isset($_POST['modulo']) ? trim($_POST['modulo']) : 'general'; // Asegurar que el módulo es correcto

    // Lista de módulos válidos y sus archivos asociados
    $modulos_validos = [
        'general' => ['reporte' => 'reporte_general.php', 'modulo' => 'modulo_general.php'],
        'vih' => ['reporte' => 'reporte_vih.php', 'modulo' => 'modulo_vih.php']
    ];

    // Validar si el módulo es correcto
    if (!isset($modulos_validos[$modulo])) {
        die('<h2>Error: Módulo inválido.</h2><a href="../dashboard.php">Volver</a>');
    }

    // Validar el rango de fechas
    if (empty($fecha_inicio) || empty($fecha_fin)) {
        die('<h2>Error: Debes seleccionar un rango de fechas válido.</h2><a href="../'.$modulos_validos[$modulo]['modulo'].'">Volver</a>');
    }

    // Validar formato de fecha
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_inicio) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_fin)) {
        die('<h2>Error: Formato de fecha inválido.</h2><a href="../'.$modulos_validos[$modulo]['modulo'].'">Volver</a>');
    }

    // Validar que la fecha de inicio sea menor o igual a la fecha de fin
    if (strtotime($fecha_inicio) > strtotime($fecha_fin)) {
        die('<h2>Error: La fecha de inicio no puede ser posterior a la fecha de fin.</h2><a href="../'.$modulos_validos[$modulo]['modulo'].'">Volver</a>');
    }

    // Obtener el archivo de reporte según el módulo
    $reporte = $modulos_validos[$modulo]['reporte'];

    // Redirigir al reporte correspondiente con los parámetros
    header("Location: $reporte?fecha_inicio=" . urlencode($fecha_inicio) . "&fecha_fin=" . urlencode($fecha_fin) . "&periodicidad=" . urlencode($periodicidad) . "&modulo=" . urlencode($modulo));
    exit;
}
?>
