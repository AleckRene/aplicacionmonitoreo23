<?php
class NecesidadesComunitarias {
    private $conn;
    private $table = "necesidades_comunitarias";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener todas las necesidades comunitarias
    public function getAll() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Crear una nueva necesidad comunitaria
    public function create($descripcion, $acciones, $area_prioritaria) {
        $query = "INSERT INTO " . $this->table . " (descripcion, acciones, area_prioritaria) VALUES (:descripcion, :acciones, :area_prioritaria)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":descripcion", $descripcion);
        $stmt->bindParam(":acciones", $acciones);
        $stmt->bindParam(":area_prioritaria", $area_prioritaria);

        return $stmt->execute();
    }

    // Obtener una necesidad comunitaria por ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar una necesidad comunitaria
    public function update($id, $descripcion, $acciones, $area_prioritaria) {
        $query = "UPDATE " . $this->table . " SET descripcion = :descripcion, acciones = :acciones, area_prioritaria = :area_prioritaria WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindParam(":descripcion", $descripcion);
        $stmt->bindParam(":acciones", $acciones);
        $stmt->bindParam(":area_prioritaria", $area_prioritaria);

        return $stmt->execute();
    }

    // Eliminar una necesidad comunitaria
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
