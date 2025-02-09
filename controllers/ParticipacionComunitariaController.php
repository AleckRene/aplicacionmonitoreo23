<?php
class ParticipacionComunitaria {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Obtener todos los registros
    public function getAll() {
        $query = "SELECT * FROM participacion_comunitaria";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Crear un nuevo registro
    public function create($actividad, $compromiso, $estrategias) {
        $query = "INSERT INTO participacion_comunitaria (actividad, compromiso, estrategias) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$actividad, $compromiso, $estrategias]);
    }

    // Actualizar un registro existente
    public function update($id, $actividad, $compromiso, $estrategias) {
        $query = "UPDATE participacion_comunitaria SET actividad = ?, compromiso = ?, estrategias = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$actividad, $compromiso, $estrategias, $id]);
    }

    // Eliminar un registro
    public function delete($id) {
        $query = "DELETE FROM participacion_comunitaria WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id]);
    }
}
?>
