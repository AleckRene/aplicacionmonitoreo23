<?php
include_once '../config/Database.php';
include_once '../models/IndicadoresUso.php';

class IndicadoresUsoController {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAll() {
        return IndicadoresUso::getAll($this->conn);
    }

    public function create($data) {
        $indicador = new IndicadoresUso(
            null,
            $data['numero_usuarios'],
            $data['nivel_actividad'],
            $data['frecuencia_recomendaciones'],
            $data['calidad_uso'],
            $data['usuario_id']
        );
        return $indicador->create($this->conn);
    }

    public function update($data) {
        $indicador = new IndicadoresUso(
            $data['id'],
            $data['numero_usuarios'],
            $data['nivel_actividad'],
            $data['frecuencia_recomendaciones'],
            $data['calidad_uso'],
            $data['usuario_id']
        );
        return $indicador->update($this->conn);
    }

    public function delete($id) {
        return IndicadoresUso::delete($id, $this->conn);
    }
}

// Manejo de solicitudes HTTP
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new IndicadoresUsoController();
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['action'])) {
        switch ($data['action']) {
            case 'getAll':
                echo json_encode($controller->getAll());
                break;
            case 'create':
                echo json_encode($controller->create($data));
                break;
            case 'update':
                echo json_encode($controller->update($data));
                break;
            case 'delete':
                if (isset($data['id'])) {
                    echo json_encode($controller->delete($data['id']));
                } else {
                    echo json_encode(['error' => 'Falta el ID para eliminar un registro']);
                }
                break;
            default:
                echo json_encode(['error' => 'Acción no válida']);
        }
    } else {
        echo json_encode(['error' => 'No se proporcionó una acción']);
    }
}
?>
