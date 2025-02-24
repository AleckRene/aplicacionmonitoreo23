<?php
session_start(); // Inicia la sesión

// Verifica si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Content-Type: application/json");
    echo json_encode(["error" => "Usuario no autenticado."]);
    exit;
}

require_once '../config/config.php'; // Configuración de la base de datos

// Configuración de cabeceras
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Determinar el método HTTP
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            getPercepcionServicios($conn);
            break;
        case 'POST':
            addPercepcionServicio($conn);
            break;
        case 'DELETE':
            deletePercepcionServicio($conn);
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

// Obtener registros
function getPercepcionServicios($conn) {
    $query = "SELECT * FROM percepcion_servicios";
    $result = $conn->query($query);

    if (!$result) {
        throw new Exception("Error en la consulta: " . $conn->error);
    }

    http_response_code(200);
    echo json_encode(["status" => 200, "data" => $result->fetch_all(MYSQLI_ASSOC)]);
}

// Agregar nuevo registro
function addPercepcionServicio($conn) {
    // Verificar si los datos están completos
    if (!isset($_POST['calidad_servicio'], $_POST['servicios_mejorar'], $_POST['cambios_recientes'])) {
        http_response_code(400);
        echo json_encode(["status" => 400, "error" => "Faltan campos requeridos"]);
        exit;
    }

    // Preparar la consulta SQL
    $stmt = $conn->prepare("INSERT INTO percepcion_servicios (calidad_servicio, servicios_mejorar, cambios_recientes) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $_POST['calidad_servicio'], $_POST['servicios_mejorar'], $_POST['cambios_recientes']);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(["status" => 201, "message" => "Registro creado exitosamente."]);
    } else {
        throw new Exception("Error al insertar: " . $stmt->error);
    }
}

// Eliminar un registro por ID
function deletePercepcionServicio($conn) {
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(["status" => 400, "error" => "Falta el ID del registro"]);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM percepcion_servicios WHERE id = ?");
    $stmt->bind_param("i", $_GET['id']);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(["status" => 200, "message" => "Registro eliminado exitosamente."]);
    } else {
        throw new Exception("Error al eliminar: " . $stmt->error);
    }
}
?>
