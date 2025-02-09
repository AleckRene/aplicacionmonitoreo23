<?php
include_once '../models/AccesibilidadCalidad.php';
include_once '../config/config.php';

class AccesibilidadCalidadController {
    public function getAll() {
        global $conn;
        $data = AccesibilidadCalidad::getAll($conn);
        echo json_encode($data);
    }

    public function create() {
        global $conn;
        $input = json_decode(file_get_contents("php://input"), true);

        if (!isset($input['accesibilidad_servicios'], $input['actitud_personal'], $input['tarifas_ocultas'], $input['factores_mejora'], $input['disponibilidad_herramientas'])) {
            echo json_encode(["error" => "Faltan campos requeridos"]);
            return;
        }

        $result = AccesibilidadCalidad::create(
            $input['accesibilidad_servicios'],
            $input['actitud_personal'],
            $input['tarifas_ocultas'],
            $input['factores_mejora'],
            $input['disponibilidad_herramientas'],
            $conn
        );

        echo $result ? json_encode(["message" => "Registro creado con éxito"]) : json_encode(["error" => "Error al crear registro"]);
    }

    public function update($id) {
        global $conn;
        $input = json_decode(file_get_contents("php://input"), true);

        if (!isset($input['accesibilidad_servicios'], $input['actitud_personal'], $input['tarifas_ocultas'], $input['factores_mejora'], $input['disponibilidad_herramientas'])) {
            echo json_encode(["error" => "Faltan campos requeridos"]);
            return;
        }

        $result = AccesibilidadCalidad::update(
            $id,
            $input['accesibilidad_servicios'],
            $input['actitud_personal'],
            $input['tarifas_ocultas'],
            $input['factores_mejora'],
            $input['disponibilidad_herramientas'],
            $conn
        );

        echo $result ? json_encode(["message" => "Registro actualizado con éxito"]) : json_encode(["error" => "Error al actualizar registro"]);
    }

    public function delete($id) {
        global $conn;
        $result = AccesibilidadCalidad::delete($id, $conn);
        echo $result ? json_encode(["message" => "Registro eliminado con éxito"]) : json_encode(["error" => "Error al eliminar registro"]);
    }
}
