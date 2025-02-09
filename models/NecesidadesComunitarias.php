<?php
class NecesidadesComunitarias {
    private $conn;
    private $table = "necesidades_comunitarias";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($descripcion, $impacto, $propuestas) {
        $query = "INSERT INTO " . $this->table . " (descripcion, impacto, propuestas) VALUES (:descripcion, :impacto, :propuestas)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":descripcion", $descripcion);
        $stmt->bindParam(":impacto", $impacto);
        $stmt->bindParam(":propuestas", $propuestas);
        return $stmt->execute();
    }

    public function update($id, $descripcion, $impacto, $propuestas) {
        $query = "UPDATE " . $this->table . " SET descripcion = :descripcion, impacto = :impacto, propuestas = :propuestas WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":descripcion", $descripcion);
        $stmt->bindParam(":impacto", $impacto);
        $stmt->bindParam(":propuestas", $propuestas);
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>
