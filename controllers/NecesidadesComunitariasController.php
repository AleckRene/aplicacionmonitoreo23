<?php
// Incluye las dependencias necesarias
include_once '../config/Database.php';
include_once '../models/NecesidadesComunitarias.php';

class NecesidadesComunitariasController {
    private $db;
    private $model;

    public function __construct() {
        // Inicializa la conexión a la base de datos
        $database = new Database();
        $this->db = $database->getConnection();

        // Instancia el modelo con la conexión
        $this->model = new NecesidadesComunitarias($this->db);
    }

    public function getAll() {
        // Llama al método del modelo para obtener todas las necesidades comunitarias
        return $this->model->getAll();
    }

    public function create($descripcion, $impacto, $propuestas) {
        // Llama al método del modelo para crear una nueva necesidad comunitaria
        return $this->model->create($descripcion, $impacto, $propuestas);
    }

    public function update($id, $descripcion, $impacto, $propuestas) {
        // Llama al método del modelo para actualizar una necesidad comunitaria
        return $this->model->update($id, $descripcion, $impacto, $propuestas);
    }

    public function delete($id) {
        // Llama al método del modelo para eliminar una necesidad comunitaria
        return $this->model->delete($id);
    }
}
?>
