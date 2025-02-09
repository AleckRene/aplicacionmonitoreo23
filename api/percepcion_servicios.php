<?php
session_start(); // Inicia la sesión

// Verifica si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Content-Type: application/json");
    echo json_encode(["error" => "Usuario no autenticado."]);
    exit;
}

require_once '../config/config.php';

// Configuración de cabeceras
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$method = $_SERVER['REQUEST_METHOD'];

try {
    $method = $_SERVER['REQUEST_METHOD'];
    switch ($method) {
        case 'GET':
            getPercepcionServicios($conn);
            break;
        case 'POST':
            addPercepcionServicio($conn);
            break;
        default:
            http_response_code(405);
            throw new Exception("Método no soportado");
    }
} catch (Exception $e) {
    echo json_encode(["status" => http_response_code(), "error" => $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}

function getPercepcionServicios($conn) {
    $result = $conn->query("SELECT * FROM percepcion_servicios");
    if (!$result) {
        throw new Exception("Error en la consulta: " . $conn->error);
    }
    echo json_encode(["status" => 200, "data" => $result->fetch_all(MYSQLI_ASSOC)]);
}

function addPercepcionServicio($conn) {
    if (!isset($_POST['calidad_servicio'], $_POST['servicios_mejorar'], $_POST['cambios_recientes'])) {
        http_response_code(400);
        throw new Exception("Faltan campos requeridos");
    }
    $stmt = $conn->prepare("INSERT INTO percepcion_servicios (calidad_servicio, servicios_mejorar, cambios_recientes) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $_POST['calidad_servicio'], $_POST['servicios_mejorar'], $_POST['cambios_recientes']);
    if (!$stmt->execute()) {
        throw new Exception("Error al insertar: " . $stmt->error);
    }
    http_response_code(201);
    echo json_encode(["status" => 201, "message" => "Registro creado exitosamente."]);
}

?>
