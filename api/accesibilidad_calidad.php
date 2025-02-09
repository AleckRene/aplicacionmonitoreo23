<?php
session_start(); // Inicia la sesión

// Verifica si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Content-Type: application/json");
    echo json_encode(["error" => "Usuario no autenticado."]);
    exit;
}

require_once '../config/config.php'; // Incluye la configuración de la base de datos

$method = $_SERVER['REQUEST_METHOD'];

try {
    $method = $_SERVER['REQUEST_METHOD'];
    switch ($method) {
        case 'GET':
            getAccesibilidadCalidad($conn);
            break;
        case 'POST':
            addAccesibilidadCalidad($conn);
            break;
        default:
            http_response_code(405);
            echo json_encode(["status" => 405, "error" => "Método no soportado"]);
            exit;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => 500, "error" => $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}

function getAccesibilidadCalidad($conn) {
    $result = $conn->query("SELECT * FROM accesibilidad_calidad");
    if (!$result) {
        http_response_code(500);
        throw new Exception("Error en la consulta: " . $conn->error);
    }
    http_response_code(200);
    echo json_encode(["status" => 200, "data" => $result->fetch_all(MYSQLI_ASSOC)]);
}

function addAccesibilidadCalidad($conn) {
    if (!isset($_POST['accesibilidad_servicios'], $_POST['actitud_personal'], $_POST['tarifas_ocultas'], $_POST['factores_mejora'], $_POST['disponibilidad_herramientas'])) {
        http_response_code(400);
        echo json_encode(["status" => 400, "error" => "Faltan campos requeridos"]);
        exit;
    }
    $stmt = $conn->prepare("INSERT INTO accesibilidad_calidad (accesibilidad_servicios, actitud_personal, tarifas_ocultas, factores_mejora, disponibilidad_herramientas) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiisi", $_POST['accesibilidad_servicios'], $_POST['actitud_personal'], $_POST['tarifas_ocultas'], $_POST['factores_mejora'], $_POST['disponibilidad_herramientas']);
    if (!$stmt->execute()) {
        http_response_code(500);
        throw new Exception("Error al insertar: " . $stmt->error);
    }
    http_response_code(201);
    echo json_encode(["status" => 201, "message" => "Registro creado exitosamente."]);
}
?>