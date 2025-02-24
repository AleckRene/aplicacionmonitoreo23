<?php
session_start(); // Inicia sesión

// Verificar autenticación del usuario
if (!isset($_SESSION['user_id'])) {
    header("Content-Type: application/json");
    echo json_encode(["error" => "Usuario no autenticado."]);
    exit;
}

require_once '../config/config.php'; // Configuración de la base de datos

try {
    $method = $_SERVER['REQUEST_METHOD'];

    switch ($method) {
        case 'GET':
            getParticipacionComunitaria($conn);
            break;
        case 'POST':
            addParticipacionComunitaria($conn);
            break;
        case 'DELETE':
            deleteParticipacionComunitaria($conn);
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

// Obtener registros de participación comunitaria
function getParticipacionComunitaria($conn) {
    $result = $conn->query("SELECT * FROM participacion_comunitaria");
    if (!$result) {
        http_response_code(500);
        throw new Exception("Error en la consulta: " . $conn->error);
    }
    http_response_code(200);
    echo json_encode(["status" => 200, "data" => $result->fetch_all(MYSQLI_ASSOC)]);
}

// Agregar nuevo registro de participación comunitaria
function addParticipacionComunitaria($conn) {
    if (!isset($_POST['nivel_participacion'], $_POST['grupos_comprometidos'], $_POST['estrategias_mejora'])) {
        http_response_code(400);
        echo json_encode(["status" => 400, "error" => "Faltan campos requeridos"]);
        exit;
    }

    // Verificar si el registro ya existe
    $stmt_check = $conn->prepare("SELECT id FROM participacion_comunitaria WHERE nivel_participacion = ? AND grupos_comprometidos = ? AND estrategias_mejora = ?");
    $stmt_check->bind_param("sss", $_POST['nivel_participacion'], $_POST['grupos_comprometidos'], $_POST['estrategias_mejora']);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        http_response_code(409);
        echo json_encode(["status" => 409, "error" => "Registro duplicado"]);
        exit;
    }

    // Insertar nuevo registro
    $stmt = $conn->prepare("INSERT INTO participacion_comunitaria (nivel_participacion, grupos_comprometidos, estrategias_mejora) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $_POST['nivel_participacion'], $_POST['grupos_comprometidos'], $_POST['estrategias_mejora']);

    if (!$stmt->execute()) {
        http_response_code(500);
        throw new Exception("Error al insertar: " . $stmt->error);
    }

    http_response_code(201);
    echo json_encode(["status" => 201, "message" => "Registro creado exitosamente."]);
}

// Eliminar un registro de participación comunitaria
function deleteParticipacionComunitaria($conn) {
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(["status" => 400, "error" => "Falta el ID del registro"]);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM participacion_comunitaria WHERE id = ?");
    $stmt->bind_param("i", $_GET['id']);

    if (!$stmt->execute()) {
        http_response_code(500);
        throw new Exception("Error al eliminar: " . $stmt->error);
    }

    http_response_code(200);
    echo json_encode(["status" => 200, "message" => "Registro eliminado exitosamente."]);
}
?>
