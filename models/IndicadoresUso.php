<?php
class IndicadoresUso {
    public $id;
    public $numeroUsuarios;
    public $nivelActividad;
    public $frecuenciaRecomendaciones;
    public $calidadUso;
    public $usuarioID;

    public function __construct($id = null, $numeroUsuarios = null, $nivelActividad = null, $frecuenciaRecomendaciones = null, $calidadUso = null, $usuarioID = null) {
        $this->id = $id;
        $this->numeroUsuarios = $numeroUsuarios;
        $this->nivelActividad = $nivelActividad;
        $this->frecuenciaRecomendaciones = $frecuenciaRecomendaciones;
        $this->calidadUso = $calidadUso;
        $this->usuarioID = $usuarioID;
    }

    public function create($conn) {
        $query = "INSERT INTO indicadores_uso (numero_usuarios, nivel_actividad, frecuencia_recomendaciones, calidad_uso, usuario_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isssi", $this->numeroUsuarios, $this->nivelActividad, $this->frecuenciaRecomendaciones, $this->calidadUso, $this->usuarioID);
        return $stmt->execute();
    }

    public static function getAll($conn) {
        $query = "SELECT * FROM indicadores_uso";
        $result = $conn->query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = new self($row['id'], $row['numero_usuarios'], $row['nivel_actividad'], $row['frecuencia_recomendaciones'], $row['calidad_uso'], $row['usuario_id']);
        }
        return $data;
    }

    public static function getById($id, $conn) {
        $query = "SELECT * FROM indicadores_uso WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return new self($row['id'], $row['numero_usuarios'], $row['nivel_actividad'], $row['frecuencia_recomendaciones'], $row['calidad_uso'], $row['usuario_id']);
        }
        return null;
    }

    public function update($conn) {
        $query = "UPDATE indicadores_uso SET numero_usuarios = ?, nivel_actividad = ?, frecuencia_recomendaciones = ?, calidad_uso = ?, usuario_id = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isssii", $this->numeroUsuarios, $this->nivelActividad, $this->frecuenciaRecomendaciones, $this->calidadUso, $this->usuarioID, $this->id);
        return $stmt->execute();
    }

    public static function delete($id, $conn) {
        $query = "DELETE FROM indicadores_uso WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
