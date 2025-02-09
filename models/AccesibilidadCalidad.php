<?php
class AccesibilidadCalidad {
    public $id;
    public $accesibilidad_servicios;
    public $actitud_personal;
    public $tarifas_ocultas;
    public $factores_mejora;
    public $disponibilidad_herramientas;

    public function __construct($id, $accesibilidad_servicios, $actitud_personal, $tarifas_ocultas, $factores_mejora, $disponibilidad_herramientas) {
        $this->id = $id;
        $this->accesibilidad_servicios = $accesibilidad_servicios;
        $this->actitud_personal = $actitud_personal;
        $this->tarifas_ocultas = $tarifas_ocultas;
        $this->factores_mejora = $factores_mejora;
        $this->disponibilidad_herramientas = $disponibilidad_herramientas;
    }

    public static function getAll($conn) {
        $query = "SELECT * FROM accesibilidad_calidad";
        $result = $conn->query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = new self(
                $row['id'],
                $row['accesibilidad_servicios'],
                $row['actitud_personal'],
                $row['tarifas_ocultas'],
                $row['factores_mejora'],
                $row['disponibilidad_herramientas']
            );
        }
        return $data;
    }

    public static function create($accesibilidad_servicios, $actitud_personal, $tarifas_ocultas, $factores_mejora, $disponibilidad_herramientas, $conn) {
        $query = "INSERT INTO accesibilidad_calidad (accesibilidad_servicios, actitud_personal, tarifas_ocultas, factores_mejora, disponibilidad_herramientas) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssss", $accesibilidad_servicios, $actitud_personal, $tarifas_ocultas, $factores_mejora, $disponibilidad_herramientas);
        return $stmt->execute();
    }

    public static function update($id, $accesibilidad_servicios, $actitud_personal, $tarifas_ocultas, $factores_mejora, $disponibilidad_herramientas, $conn) {
        $query = "UPDATE accesibilidad_calidad 
                  SET accesibilidad_servicios = ?, actitud_personal = ?, tarifas_ocultas = ?, factores_mejora = ?, disponibilidad_herramientas = ? 
                  WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssi", $accesibilidad_servicios, $actitud_personal, $tarifas_ocultas, $factores_mejora, $disponibilidad_herramientas, $id);
        return $stmt->execute();
    }

    public static function delete($id, $conn) {
        $query = "DELETE FROM accesibilidad_calidad WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
