<?php
class EventosSaludController
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Obtener todos los eventos de salud
    public function getAll()
    {
        try {
            $result = EventosSalud::getAll($this->db);
            echo json_encode($result);
        } catch (Exception $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    // Crear un nuevo evento de salud
    public function create($descripcion, $accionesTomadas, $usuarioID, $fecha)
    {
        try {
            $newId = EventosSalud::create($this->db, $descripcion, $accionesTomadas, $usuarioID, $fecha);
            echo json_encode(["success" => "Registro creado con ID: $newId"]);
        } catch (Exception $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    // Actualizar un evento de salud existente
    public function update($id, $descripcion, $accionesTomadas, $usuarioID, $fecha)
    {
        try {
            $updated = EventosSalud::update($this->db, $id, $descripcion, $accionesTomadas, $usuarioID, $fecha);
            if ($updated) {
                echo json_encode(["success" => "Registro actualizado correctamente."]);
            } else {
                echo json_encode(["error" => "No se pudo actualizar el registro."]);
            }
        } catch (Exception $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    // Eliminar un evento de salud
    public function delete($id)
    {
        try {
            $deleted = EventosSalud::delete($this->db, $id);
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
