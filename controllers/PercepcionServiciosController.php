<?php
require_once __DIR__ . '/../models/PercepcionServicios.php';
require_once __DIR__ . '/../config/config.php';

class PercepcionServiciosController
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Obtener todos los registros
    public function getAll()
    {
        try {
            $result = PercepcionServicios::getAll($this->db);
            echo json_encode(["status" => 200, "data" => $result]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    // Crear un nuevo registro
    public function create()
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);

            // Validación de datos
            if (!isset($input['calidad_servicio'], $input['servicios_mejorar'], $input['cambios_recientes'], $input['usuario_id'], $input['fecha'])) {
                http_response_code(400);
                echo json_encode(["error" => "Faltan campos requeridos"]);
                return;
            }

            $newId = PercepcionServicios::create(
                $this->db,
                $input['calidad_servicio'],
                $input['servicios_mejorar'],
                $input['cambios_recientes'],
                $input['usuario_id'], // 🟢 Se añadió el ID de usuario
                $input['fecha'] // 🟢 Se añadió la fecha
            );

            echo json_encode(["status" => 201, "message" => "Registro creado con ID: $newId"]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    // Actualizar un registro existente
    public function update($id)
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);

            if (!isset($input['calidad_servicio'], $input['servicios_mejorar'], $input['cambios_recientes'], $input['usuario_id'], $input['fecha'])) {
                http_response_code(400);
                echo json_encode(["error" => "Faltan campos requeridos"]);
                return;
            }

            $updated = PercepcionServicios::update(
                $this->db,
                $id,
                $input['calidad_servicio'],
                $input['servicios_mejorar'],
                $input['cambios_recientes'],
                $input['usuario_id'], // 🟢 Se añadió el ID de usuario
                $input['fecha'] // 🟢 Se añadió la fecha
            );

            if ($updated) {
                echo json_encode(["status" => 200, "message" => "Registro actualizado correctamente"]);
            } else {
                http_response_code(400);
                echo json_encode(["error" => "No se pudo actualizar el registro"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    // Eliminar un registro
    public function delete($id)
    {
        try {
            $deleted = PercepcionServicios::delete($this->db, $id);
            if ($deleted) {
                echo json_encode(["status" => 200, "message" => "Registro eliminado correctamente"]);
            } else {
                http_response_code(400);
                echo json_encode(["error" => "No se pudo eliminar el registro"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
}
?>
