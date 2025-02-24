<?php
class EventosSalud {
    public $id;
    public $nombreEvento;
    public $descripcion;
    public $fecha;
    public $acciones;
    public $usuarioID;

    public function __construct($id, $nombreEvento, $descripcion, $fecha, $acciones, $usuarioID) {
        $this->id = $id;
        $this->nombreEvento = $nombreEvento;
        $this->descripcion = $descripcion;
        $this->fecha = $fecha;
        $this->acciones = $acciones;
        $this->usuarioID = $usuarioID;
    }

    public static function create($nombreEvento, $descripcion, $fecha, $acciones, $usuarioID, $conn) {
        $query = "INSERT INTO eventos_salud (nombre_evento, descripcion, fecha, acciones, usuario_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssi", $nombreEvento, $descripcion, $fecha, $acciones, $usuarioID);
        return $stmt->execute();
    }

    public static function getAll($conn) {
        $query = "SELECT * FROM eventos_salud";
        $result = $conn->query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = new self($row['id'], $row['nombre_evento'], $row['descripcion'], $row['fecha'], $row['acciones'], $row['usuario_id']);
        }
        return $data;
    }

    public static function getById($id, $conn) {
        $query = "SELECT * FROM eventos_salud WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return new self($row['id'], $row['nombre_evento'], $row['descripcion'], $row['fecha'], $row['acciones'], $row['usuario_id']);
        }
        return null;
    }

    public static function update($id, $nombreEvento, $descripcion, $fecha, $acciones, $usuarioID, $conn) {
        $query = "UPDATE eventos_salud SET nombre_evento = ?, descripcion = ?, fecha = ?, acciones = ?, usuario_id = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssii", $nombreEvento, $descripcion, $fecha, $acciones, $usuarioID, $id);
        return $stmt->execute();
    }

    public static function delete($id, $conn) {
        $query = "DELETE FROM eventos_salud WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
