<?php
class PercepcionServicios
{
    public static function getAll($conn)
    {
        $query = "SELECT * FROM percepcion_servicios";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public static function create($conn, $descripcion, $calificacion, $usuarioID, $fecha)
    {
        $query = "INSERT INTO percepcion_servicios (descripcion, calificacion, usuario_id, fecha) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssis", $descripcion, $calificacion, $usuarioID, $fecha);
        $stmt->execute();
        return $stmt->insert_id;
    }

    public static function update($conn, $id, $descripcion, $calificacion, $usuarioID, $fecha)
    {
        $query = "UPDATE percepcion_servicios SET descripcion = ?, calificacion = ?, usuario_id = ?, fecha = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssisi", $descripcion, $calificacion, $usuarioID, $fecha, $id);
        return $stmt->execute();
    }

    public static function delete($conn, $id)
    {
        $query = "DELETE FROM percepcion_servicios WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}

?>
