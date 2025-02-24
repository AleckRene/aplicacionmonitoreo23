<?php
include_once '../models/AccesibilidadCalidad.php';
include_once '../config/config.php';

class AccesibilidadCalidadController {
    private $model;

    public function __construct($db) {
        $this->model = new AccesibilidadCalidad($db);
    }

    /**
     * Obtener todos los registros
     */
    public function getAll() {
        $data = $this->model->getAll();
        echo json_encode($data);
    }

    /**
     * Crear un nuevo registro
     */
    public function create() {
        $input = json_decode(file_get_contents("php://input"), true);

        if (!isset($input['accesibilidad_servicios'], $input['actitud_personal'], $input['tarifas_ocultas'], $input['factores_mejora'], $input['disponibilidad_herramientas'])) {
            echo json_encode(["error" => "Faltan campos requeridos"]);
            return;
        }

        $result = $this->model->create(
            $input['accesibilidad_servicios'],
            $input['actitud_personal'],
            $input['tarifas_ocultas'],
            $input['factores_mejora'],
            $input['disponibilidad_herramientas']
        );

        echo json_encode($result);
    }

    /**
     * Actualizar un registro existente
     */
    public function update($id) {
        $input = json_decode(file_get_contents("php://input"), true);

        if (!isset($input['accesibilidad_servicios'], $input['actitud_personal'], $input['tarifas_ocultas'], $input['factores_mejora'], $input['disponibilidad_herramientas'])) {
            echo json_encode(["error" => "Faltan campos requeridos"]);
            return;
        }

        $result = $this->model->update(
            $id,
            $input['accesibilidad_servicios'],
            $input['actitud_personal'],
            $input['tarifas_ocultas'],
            $input['factores_mejora'],
            $input['disponibilidad_herramientas']
        );

        echo json_encode($result);
    }

    /**
     * Eliminar un registro
     */
    public function delete($id) {
        $result = $this->model->delete($id);
        echo json_encode($result);
    }
}
?>
