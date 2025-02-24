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

// Obtener todas las necesidades comunitarias
function getNecesidadesComunitarias($conn) {
    $stmt = $conn->prepare("SELECT * FROM necesidades_comunitarias");
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        http_response_code(500);
        echo json_encode(["error" => "Error en la consulta: " . $conn->error]);
        return;
    }

    http_response_code(200);
    echo json_encode(["status" => 200, "data" => $result->fetch_all(MYSQLI_ASSOC)]);
}

// Agregar una nueva necesidad comunitaria
function addNecesidadComunitaria($conn) {
    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input['descripcion'], $input['acciones'], $input['area_prioritaria'])) {
        http_response_code(400);
        echo json_encode(["error" => "Faltan campos requeridos"]);
        return;
    }

    $stmt = $conn->prepare("INSERT INTO necesidades_comunitarias (descripcion, acciones, area_prioritaria) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $input['descripcion'], $input['acciones'], $input['area_prioritaria']);

    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(["error" => "Error al insertar: " . $stmt->error]);
        return;
    }

    http_response_code(201);
    echo json_encode(["message" => "Registro creado exitosamente."]);
}

// Eliminar una necesidad comunitaria por ID
function deleteNecesidadComunitaria($conn) {
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(["error" => "Falta el ID del registro"]);
        return;
    }

    $stmt = $conn->prepare("DELETE FROM necesidades_comunitarias WHERE id = ?");
    $stmt->bind_param("i", $_GET['id']);

    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(["error" => "Error al eliminar: " . $stmt->error]);
        return;
    }

    http_response_code(200);
    echo json_encode(["message" => "Registro eliminado exitosamente."]);
}
?>
