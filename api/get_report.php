<?php
// Archivo: api/get_report.php
require_once '../config/config.php';

header('Content-Type: text/html');

try {
    // Obtener y validar el nombre del reporte
    $reporte = isset($_GET['reporte']) ? preg_replace('/[^a-z_]/', '', $_GET['reporte']) : '';
    $modulo = isset($_GET['modulo']) ? preg_replace('/[^a-z_]/', '', $_GET['modulo']) : 'general';

    if (empty($reporte) || empty($modulo)) {
        throw new Exception("Parámetros inválidos.");
    }

    // Seleccionar el archivo HTML correspondiente
    $archivo = "../temp/reporte_{$modulo}_interactivo.html";
    if (!file_exists($archivo)) {
        throw new Exception("Reporte no encontrado.");
    }

    // Enviar el archivo HTML al navegador
    readfile($archivo);
} catch (Exception $e) {
    http_response_code(404);
    echo "Error al cargar el reporte: " . htmlspecialchars($e->getMessage());
}
?>
