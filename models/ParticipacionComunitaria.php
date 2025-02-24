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

    // Obtener un registro por ID
    public function getById($id) {
        $query = "SELECT * FROM participacion_comunitaria WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear un nuevo registro
    public function create($data) {
        $query = "INSERT INTO participacion_comunitaria (nivel_participacion, grupos_comprometidos, estrategias_mejora) 
                  VALUES (:nivel_participacion, :grupos_comprometidos, :estrategias_mejora)";
        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':nivel_participacion', $data['nivel_participacion']);
        $stmt->bindParam(':grupos_comprometidos', $data['grupos_comprometidos']);
        $stmt->bindParam(':estrategias_mejora', $data['estrategias_mejora']);

        return $stmt->execute();
    }

    // Actualizar un registro existente
    public function update($id, $data) {
        $query = "UPDATE participacion_comunitaria 
                  SET nivel_participacion = :nivel_participacion, 
                      grupos_comprometidos = :grupos_comprometidos, 
                      estrategias_mejora = :estrategias_mejora 
                  WHERE id = :id";
        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nivel_participacion', $data['nivel_participacion']);
        $stmt->bindParam(':grupos_comprometidos', $data['grupos_comprometidos']);
        $stmt->bindParam(':estrategias_mejora', $data['estrategias_mejora']);

        return $stmt->execute();
    }

    // Eliminar un registro
    public function delete($id) {
        $query = "DELETE FROM participacion_comunitaria WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
