<?php
// Archivo: api/get_report.php

try {
    // Obtener el nombre del reporte desde la URL
    $reporte = $_GET['reporte'];

    // Validar la entrada
    if (empty($reporte) || !preg_match('/^[a-z]+$/', $reporte)) {
        throw new Exception("Nombre de reporte inválido.");
    }

    // Seleccionar el archivo HTML correspondiente
    switch ($reporte) {
        case 'general':
            $archivo = 'temp/reporte_general_interactivo.html';
            break;
        case 'vih':
            $archivo = 'temp/reporte_vih_interactivo.html';
            break;
        // ... agregar más opciones de reportes
        default:
            throw new Exception("Reporte no encontrado.");
    }

    // Enviar el archivo HTML al navegador
    header('Content-Type: text/html');
    readfile($archivo);
} catch (Exception $e) {
    // Manejar el error
    http_response_code(404); // Establecer código de respuesta 404 Not Found
    echo "Error al cargar el reporte: " . $e->getMessage();
}
?>