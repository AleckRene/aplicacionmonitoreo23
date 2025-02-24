<?php
// Incluir dependencias
include_once '../config/Database.php';
include_once '../models/NecesidadesComunitarias.php';

class NecesidadesComunitariasController {
    private $db;
    private $model;

    public function __construct() {
        // Inicializar la conexión con la base de datos
        $database = new Database();
        $this->db = $database->getConnection();

        // Instanciar el modelo
        $this->model = new NecesidadesComunitarias($this->db);
    }

    // Obtener todas las necesidades comunitarias
    public function getAll() {
        try {
            return json_encode(["status" => 200, "data" => $this->model->getAll()]);
        } catch (Exception $e) {
            return json_encode(["status" => 500, "error" => $e->getMessage()]);
        }
    }

    // Crear una nueva necesidad comunitaria
    public function create($data) {
        if (!isset($data['descripcion'], $data['acciones'], $data['area_prioritaria'])) {
            return json_encode(["status" => 400, "error" => "Faltan campos requeridos"]);
        }

        try {
            $success = $this->model->create($data['descripcion'], $data['acciones'], $data['area_prioritaria']);
            return json_encode(["status" => 201, "message" => "Registro creado exitosamente"]);
        } catch (Exception $e) {
            return json_encode(["status" => 500, "error" => $e->getMessage()]);
        }
    }

    // Actualizar una necesidad comunitaria existente
    public function update($id, $data) {
        if (!isset($data['descripcion'], $data['acciones'], $data['area_prioritaria'])) {
            return json_encode(["status" => 400, "error" => "Faltan campos requeridos"]);
        }

        try {
            $success = $this->model->update($id, $data['descripcion'], $data['acciones'], $data['area_prioritaria']);
            return json_encode(["status" => 200, "message" => "Registro actualizado correctamente"]);
        } catch (Exception $e) {
            return json_encode(["status" => 500, "error" => $e->getMessage()]);
        }
    }

    // Eliminar una necesidad comunitaria
    public function delete($id) {
        if (!$id) {
            return json_encode(["status" => 400, "error" => "ID requerido"]);
        }

        try {
            $success = $this->model->delete($id);
            return json_encode(["status" => 200, "message" => "Registro eliminado correctamente"]);
        } catch (Exception $e) {
            return json_encode(["status" => 500, "error" => $e->getMessage()]);
        }
    }
}

// Manejo de solicitudes HTTP
$controller = new NecesidadesComunitariasController();

$method = $_SERVER['REQUEST_METHOD'];
$inputData = json_decode(file_get_contents("php://input"), true);

switch ($method) {
    case 'GET':
        echo $controller->getAll();
        break;
    case 'POST':
        echo $controller->create($inputData);
        break;
    case 'PUT':
        if (isset($_GET['id'])) {
            echo $controller->update($_GET['id'], $inputData);
        } else {
            echo json_encode(["status" => 400, "error" => "ID requerido para actualizar"]);
        }
        break;
    case 'DELETE':
        if (isset($_GET['id'])) {
            echo $controller->delete($_GET['id']);
        } else {
            echo json_encode(["status" => 400, "error" => "ID requerido para eliminar"]);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(["status" => 405, "error" => "Método no permitido"]);
        break;
}
?>
