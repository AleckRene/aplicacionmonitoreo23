<?php
session_start(); // Inicia la sesión

// Verifica si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Content-Type: application/json");
    echo json_encode(["error" => "Usuario no autenticado."]);
    exit;
}

require_once '../config/config.php'; // Configuración de la base de datos
require_once '../controllers/IndicadoresUsoController.php';

$method = $_SERVER['REQUEST_METHOD'];
$controller = new IndicadoresUsoController();

try {
    switch ($method) {
        case 'GET':
            echo json_encode(["status" => 200, "data" => $controller->getAll()]);
            break;

        case 'POST':
            $data = json_decode(file_get_contents("php://input"), true);
            if (!$data) {
                http_response_code(400);
                echo json_encode(["status" => 400, "error" => "Datos de entrada no válidos"]);
                exit;
            }
            $result = $controller->create($data);
            echo json_encode(["status" => 201, "message" => "Registro creado exitosamente.", "data" => $result]);
            break;

        case 'PUT':
            $data = json_decode(file_get_contents("php://input"), true);
            if (!isset($data['id'])) {
                http_response_code(400);
                echo json_encode(["status" => 400, "error" => "ID faltante para actualizar"]);
                exit;
            }
            $result = $controller->update($data);
            echo json_encode(["status" => 200, "message" => "Registro actualizado correctamente.", "data" => $result]);
            break;

        case 'DELETE':
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(["status" => 400, "error" => "Falta el ID del registro"]);
                exit;
            }
            $result = $controller->delete($_GET['id']);
            echo json_encode(["status" => 200, "message" => "Registro eliminado exitosamente.", "data" => $result]);
            break;

        default:
            http_response_code(405);
            echo json_encode(["status" => 405, "error" => "Método no soportado"]);
            exit;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => 500, "error" => $e->getMessage()]);
}
