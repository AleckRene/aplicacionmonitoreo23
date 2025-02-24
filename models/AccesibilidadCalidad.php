<?php
class AccesibilidadCalidad {
    private $conn;
    private $table = "accesibilidad_calidad";

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Obtener todos los registros de accesibilidad y calidad.
     */
    public function getAll() {
        try {
            $query = "SELECT * FROM " . $this->table;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }

    /**
     * Crear un nuevo registro.
     */
    public function create($accesibilidad_servicios, $actitud_personal, $tarifas_ocultas, $factores_mejora, $disponibilidad_herramientas) {
        try {
            $query = "INSERT INTO " . $this->table . " 
                      (accesibilidad_servicios, actitud_personal, tarifas_ocultas, factores_mejora, disponibilidad_herramientas) 
                      VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("iiisi", $accesibilidad_servicios, $actitud_personal, $tarifas_ocultas, $factores_mejora, $disponibilidad_herramientas);

            if ($stmt->execute()) {
                return ["message" => "Registro creado exitosamente"];
            } else {
                return ["error" => "Error al insertar el registro"];
            }
        } catch (Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }

    /**
     * Actualizar un registro existente.
     */
    public function update($id, $accesibilidad_servicios, $actitud_personal, $tarifas_ocultas, $factores_mejora, $disponibilidad_herramientas) {
        try {
            $query = "UPDATE " . $this->table . " 
                      SET accesibilidad_servicios = ?, actitud_personal = ?, tarifas_ocultas = ?, factores_mejora = ?, disponibilidad_herramientas = ? 
                      WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("iiisii", $accesibilidad_servicios, $actitud_personal, $tarifas_ocultas, $factores_mejora, $disponibilidad_herramientas, $id);

            if ($stmt->execute()) {
                return ["message" => "Registro actualizado exitosamente"];
            } else {
                return ["error" => "Error al actualizar el registro"];
            }
        } catch (Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }

    /**
     * Eliminar un registro.
     */
    public function delete($id) {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                return ["message" => "Registro eliminado exitosamente"];
            } else {
                return ["error" => "Error al eliminar el registro"];
            }
        } catch (Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }
}
?>
