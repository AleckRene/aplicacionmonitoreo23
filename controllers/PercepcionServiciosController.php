<?php
require_once __DIR__ . '/../models/PercepcionServicios.php';

class PercepcionServiciosController
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAll()
    {
        try {
            $result = PercepcionServicios::getAll($this->db);
            echo json_encode($result);
        } catch (Exception $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    public function create($descripcion, $calificacion, $usuarioID, $fecha)
    {
        try {
            $newId = PercepcionServicios::create($this->db, $descripcion, $calificacion, $usuarioID, $fecha);
            echo json_encode(["success" => "Registro creado con ID: $newId"]);
        } catch (Exception $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    public function update($id, $descripcion, $calificacion, $usuarioID, $fecha)
    {
        try {
            $updated = PercepcionServicios::update($this->db, $id, $descripcion, $calificacion, $usuarioID, $fecha);
            if ($updated) {
                echo json_encode(["success" => "Registro actualizado correctamente."]);
            } else {
                echo json_encode(["error" => "No se pudo actualizar el registro."]);
            }
        } catch (Exception $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        try {
            $deleted = PercepcionServicios::delete($this->db, $id);
            if ($deleted) {
                echo json_encode(["success" => "Registro eliminado correctamente."]);
            } else {
                echo json_encode(["error" => "No se pudo eliminar el registro."]);
            }
        } catch (Exception $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
}

