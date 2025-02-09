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
    $method = $_SERVER['REQUEST_METHOD'];
    switch ($method) {
        case 'GET':
            getNecesidadesComunitarias($conn);
            break;
        case 'POST':
            addNecesidadComunitaria($conn);
            break;
        case 'DELETE':
            deleteNecesidadComunitaria($conn);
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

function getNecesidadesComunitarias($conn) {
    $result = $conn->query("SELECT * FROM necesidades_comunitarias");
    if (!$result) {
        http_response_code(500);
        throw new Exception("Error en la consulta: " . $conn->error);
    }
    http_response_code(200);
    echo json_encode(["status" => 200, "data" => $result->fetch_all(MYSQLI_ASSOC)]);
}

function addNecesidadComunitaria($conn) {
    if (!isset($_POST['descripcion'], $_POST['acciones'], $_POST['area_prioritaria'])) {
        http_response_code(400);
        echo json_encode(["status" => 400, "error" => "Faltan campos requeridos"]);
        exit;
    }
    $stmt = $conn->prepare("INSERT INTO necesidades_comunitarias (descripcion, acciones, area_prioritaria) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $_POST['descripcion'], $_POST['acciones'], $_POST['area_prioritaria']);
    if (!$stmt->execute()) {
        http_response_code(500);
        throw new Exception("Error al insertar: " . $stmt->error);
    }
    http_response_code(201);
    echo json_encode(["status" => 201, "message" => "Registro creado exitosamente."]);
}

function deleteNecesidadComunitaria($conn) {
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(["status" => 400, "error" => "Falta el ID del registro"]);
        exit;
    }
    $stmt = $conn->prepare("DELETE FROM necesidades_comunitarias WHERE id = ?");
    $stmt->bind_param("i", $_GET['id']);
    if (!$stmt->execute()) {
        http_response_code(500);
        throw new Exception("Error al eliminar: " . $stmt->error);
    }
    http_response_code(200);
    echo json_encode(["status" => 200, "message" => "Registro eliminado exitosamente."]);
}

?>
