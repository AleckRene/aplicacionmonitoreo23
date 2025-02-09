<?php
class EventosSalud {
    public $id;
    public $eventosRecientes;
    public $medidasTomadas;
    public $preparacionComunidad;
    public $usuarioID;

    public function __construct($id, $eventosRecientes, $medidasTomadas, $preparacionComunidad, $usuarioID) {
        $this->id = $id;
        $this->eventosRecientes = $eventosRecientes;
        $this->medidasTomadas = $medidasTomadas;
        $this->preparacionComunidad = $preparacionComunidad;
        $this->usuarioID = $usuarioID;
    }

    public static function create($eventosRecientes, $medidasTomadas, $preparacionComunidad, $usuarioID, $conn) {
        $query = "INSERT INTO eventos_salud (eventos_recientes, medidas_tomadas, preparacion_comunidad, usuario_id) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssi", $eventosRecientes, $medidasTomadas, $preparacionComunidad, $usuarioID);
        return $stmt->execute();
    }

    public static function getAll($conn) {
        $query = "SELECT * FROM eventos_salud";
        $result = $conn->query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = new self($row['id'], $row['eventos_recientes'], $row['medidas_tomadas'], $row['preparacion_comunidad'], $row['usuario_id']);
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
            return new self($row['id'], $row['eventos_recientes'], $row['medidas_tomadas'], $row['preparacion_comunidad'], $row['usuario_id']);
        }
        return null;
    }

    public static function update($id, $eventosRecientes, $medidasTomadas, $preparacionComunidad, $usuarioID, $conn) {
        $query = "UPDATE eventos_salud SET eventos_recientes = ?, medidas_tomadas = ?, preparacion_comunidad = ?, usuario_id = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssii", $eventosRecientes, $medidasTomadas, $preparacionComunidad, $usuarioID, $id);
        return $stmt->execute();
    }

    public static function delete($id, $conn) {
        $query = "DELETE FROM eventos_salud WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
