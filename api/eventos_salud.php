<?php
session_start(); // Inicia la sesión

// Verifica si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Content-Type: application/json");
    echo json_encode(["error" => "Usuario no autenticado."]);
    exit;
}

require_once '../config/config.php'; // Configuración de la base de datos

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            getEventosSalud($conn);
            break;
        case 'POST':
            addEventoSalud($conn);
            break;
        case 'DELETE':
            deleteEventoSalud($conn);
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

function getEventosSalud($conn) {
    $stmt = $conn->prepare("SELECT id, nombre_evento, descripcion, fecha, acciones FROM eventos_salud");
    $stmt->execute();
    $result = $stmt->get_result();
    if (!$result) {
        http_response_code(500);
        throw new Exception("Error en la consulta: " . $conn->error);
    }
    http_response_code(200);
    echo json_encode(["status" => 200, "data" => $result->fetch_all(MYSQLI_ASSOC)]);
}

function addEventoSalud($conn) {
    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input['nombre_evento'], $input['descripcion'], $input['fecha'], $input['acciones'])) {
        http_response_code(400);
        echo json_encode(["status" => 400, "error" => "Faltan campos requeridos"]);
        exit;
    }
    
    $stmt = $conn->prepare("INSERT INTO eventos_salud (nombre_evento, descripcion, fecha, acciones) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $input['nombre_evento'], $input['descripcion'], $input['fecha'], $input['acciones']);
    
    if (!$stmt->execute()) {
        http_response_code(500);
        throw new Exception("Error al insertar: " . $stmt->error);
    }
    http_response_code(201);
    echo json_encode(["status" => 201, "message" => "Evento registrado exitosamente."]);
}

function deleteEventoSalud($conn) {
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(["status" => 400, "error" => "Falta el ID del registro"]);
        exit;
    }
    
    $stmt = $conn->prepare("DELETE FROM eventos_salud WHERE id = ?");
    $stmt->bind_param("i", $_GET['id']);
    
    if (!$stmt->execute()) {
        http_response_code(500);
        throw new Exception("Error al eliminar: " . $stmt->error);
    }
    http_response_code(200);
    echo json_encode(["status" => 200, "message" => "Evento eliminado exitosamente."]);
}
