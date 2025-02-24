<?php
class EventosSaludController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener todos los eventos de salud
    public function getAll() {
        try {
            $result = EventosSalud::getAll($this->conn);
            echo json_encode(["status" => 200, "data" => $result]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => 500, "error" => $e->getMessage()]);
        }
    }

    // Crear un nuevo evento de salud
    public function create($data) {
        try {
            if (!isset($data['nombre_evento'], $data['descripcion'], $data['fecha'], $data['acciones'], $data['usuario_id'])) {
                throw new Exception("Faltan campos requeridos");
            }
            
            $created = EventosSalud::create($data['nombre_evento'], $data['descripcion'], $data['fecha'], $data['acciones'], $data['usuario_id'], $this->conn);
            if ($created) {
                echo json_encode(["status" => 201, "message" => "Evento registrado exitosamente."]);
            } else {
                throw new Exception("No se pudo registrar el evento");
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => 500, "error" => $e->getMessage()]);
        }
    }

    // Actualizar un evento de salud existente
    public function update($data) {
        try {
            if (!isset($data['id'], $data['nombre_evento'], $data['descripcion'], $data['fecha'], $data['acciones'], $data['usuario_id'])) {
                throw new Exception("Faltan campos requeridos");
            }
            
            $updated = EventosSalud::update($data['id'], $data['nombre_evento'], $data['descripcion'], $data['fecha'], $data['acciones'], $data['usuario_id'], $this->conn);
            if ($updated) {
                echo json_encode(["status" => 200, "message" => "Evento actualizado correctamente."]);
            } else {
                throw new Exception("No se pudo actualizar el evento");
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => 500, "error" => $e->getMessage()]);
        }
    }

    // Eliminar un evento de salud
    public function delete($id) {
        try {
            if (!$id) {
                throw new Exception("Falta el ID del evento");
            }
            
            $deleted = EventosSalud::delete($id, $this->conn);
            if ($deleted) {
                echo json_encode(["status" => 200, "message" => "Evento eliminado correctamente."]);
            } else {
                throw new Exception("No se pudo eliminar el evento");
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => 500, "error" => $e->getMessage()]);
        }
    }
}
