<?php

class ParticipacionComunitaria {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM participacion_comunitaria";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM participacion_comunitaria WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO participacion_comunitaria (actividad, grupos_especificos, estrategias) VALUES (:actividad, :grupos_especificos, :estrategias)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':actividad', $data['actividad']);
        $stmt->bindParam(':grupos_especificos', $data['grupos_especificos']);
        $stmt->bindParam(':estrategias', $data['estrategias']);
        return $stmt->execute();
    }

    public function update($id, $data) {
        $query = "UPDATE participacion_comunitaria SET actividad = :actividad, grupos_especificos = :grupos_especificos, estrategias = :estrategias WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':actividad', $data['actividad']);
        $stmt->bindParam(':grupos_especificos', $data['grupos_especificos']);
        $stmt->bindParam(':estrategias', $data['estrategias']);
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM participacion_comunitaria WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
