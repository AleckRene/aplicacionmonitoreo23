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
    switch ($method) {
        case 'GET':
            getAccesibilidadCalidad($conn);
            break;
        case 'POST':
            addAccesibilidadCalidad($conn);
            break;
        default:
            http_response_code(405);
            echo json_encode(["status" => 405, "error" => "Método no permitido"]);
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

/**
 * Obtener todos los registros de accesibilidad y calidad.
 */
function getAccesibilidadCalidad($conn) {
    $query = "SELECT * FROM accesibilidad_calidad";
    $result = $conn->query($query);

    if (!$result) {
        http_response_code(500);
        echo json_encode(["status" => 500, "error" => "Error en la consulta: " . $conn->error]);
        return;
    }

    http_response_code(200);
    echo json_encode(["status" => 200, "data" => $result->fetch_all(MYSQLI_ASSOC)]);
}

/**
 * Agregar un nuevo registro de accesibilidad y calidad.
 */
function addAccesibilidadCalidad($conn) {
    // Validar que los datos requeridos estén presentes
    $requiredFields = ['accesibilidad_servicios', 'actitud_personal', 'tarifas_ocultas', 'factores_mejora', 'disponibilidad_herramientas'];
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
            http_response_code(400);
            echo json_encode(["status" => 400, "error" => "El campo '$field' es obligatorio"]);
            return;
        }
    }

    // Prepara la consulta
    $stmt = $conn->prepare("INSERT INTO accesibilidad_calidad (accesibilidad_servicios, actitud_personal, tarifas_ocultas, factores_mejora, disponibilidad_herramientas) 
                            VALUES (?, ?, ?, ?, ?)");

    if (!$stmt) {
        http_response_code(500);
        echo json_encode(["status" => 500, "error" => "Error en la preparación de la consulta: " . $conn->error]);
        return;
    }

    // Vincular parámetros y ejecutar consulta
    $stmt->bind_param("sssss", $_POST['accesibilidad_servicios'], $_POST['actitud_personal'], $_POST['tarifas_ocultas'], $_POST['factores_mejora'], $_POST['disponibilidad_herramientas']);

    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(["status" => 500, "error" => "Error al insertar datos: " . $stmt->error]);
        return;
    }

    http_response_code(201);
    echo json_encode(["status" => 201, "message" => "Registro creado exitosamente."]);
}
?>
