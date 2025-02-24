<?php
require_once '../config/config.php';
require_once '../models/ParticipacionComunitaria.php';

class ParticipacionComunitariaController {
    private $db;
    private $model;

    public function __construct($db) {
        $this->db = $db;
        $this->model = new ParticipacionComunitaria($this->db);
    }

    // Obtener todos los registros
    public function getAll() {
        try {
            $result = $this->model->getAll();
            echo json_encode(["status" => 200, "data" => $result]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => 500, "error" => $e->getMessage()]);
        }
    }

    // Crear un nuevo registro
    public function create($data) {
        try {
            // Validar datos
            if (!isset($data['nivel_participacion'], $data['grupos_comprometidos'], $data['estrategias_mejora'])) {
                http_response_code(400);
                echo json_encode(["status" => 400, "error" => "Faltan campos requeridos"]);
                return;
            }

            // Llamar al modelo para insertar el registro
            $result = $this->model->create($data);
            if ($result) {
                http_response_code(201);
                echo json_encode(["status" => 201, "message" => "Registro creado exitosamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["status" => 500, "error" => "Error al crear el registro"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => 500, "error" => $e->getMessage()]);
        }
    }

    // Actualizar un registro existente
    public function update($id, $data) {
        try {
            // Validar datos
            if (!isset($data['nivel_participacion'], $data['grupos_comprometidos'], $data['estrategias_mejora'])) {
                http_response_code(400);
                echo json_encode(["status" => 400, "error" => "Faltan campos requeridos"]);
                return;
            }

            // Llamar al modelo para actualizar
            $result = $this->model->update($id, $data);
            if ($result) {
                http_response_code(200);
                echo json_encode(["status" => 200, "message" => "Registro actualizado correctamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["status" => 500, "error" => "No se pudo actualizar el registro"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => 500, "error" => $e->getMessage()]);
        }
    }

    // Eliminar un registro
    public function delete($id) {
        try {
            $result = $this->model->delete($id);
            if ($result) {
                http_response_code(200);
                echo json_encode(["status" => 200, "message" => "Registro eliminado correctamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["status" => 500, "error" => "No se pudo eliminar el registro"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => 500, "error" => $e->getMessage()]);
        }
    }
}

// Manejo de solicitudes HTTP
$controller = new ParticipacionComunitariaController($conn);
$inputData = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller->getAll();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->create($inputData);
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $id = $_GET['id'] ?? null;
    if ($id) {
        $controller->update($id, $inputData);
    } else {
        http_response_code(400);
        echo json_encode(["status" => 400, "error" => "Falta el ID para actualizar"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = $_GET['id'] ?? null;
    if ($id) {
        $controller->delete($id);
    } else {
        http_response_code(400);
        echo json_encode(["status" => 400, "error" => "Falta el ID para eliminar"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["status" => 405, "error" => "Método no soportado"]);
}
?>
