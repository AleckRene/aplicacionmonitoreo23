<?php
class PercepcionServicios
{
    // Obtener todos los registros
    public static function getAll($conn)
    {
        $query = "SELECT * FROM percepcion_servicios";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Crear un nuevo registro
    public static function create($conn, $calidad_servicio, $servicios_mejorar, $cambios_recientes, $usuarioID, $fecha)
    {
        $query = "INSERT INTO percepcion_servicios (calidad_servicio, servicios_mejorar, cambios_recientes, usuario_id, fecha) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssis", $calidad_servicio, $servicios_mejorar, $cambios_recientes, $usuarioID, $fecha);
        
        if ($stmt->execute()) {
            return $stmt->insert_id; // Devuelve el ID del nuevo registro
        } else {
            throw new Exception("Error al insertar: " . $stmt->error);
        }
    }

    // Actualizar un registro existente
    public static function update($conn, $id, $calidad_servicio, $servicios_mejorar, $cambios_recientes, $usuarioID, $fecha)
    {
        $query = "UPDATE percepcion_servicios 
                  SET calidad_servicio = ?, servicios_mejorar = ?, cambios_recientes = ?, usuario_id = ?, fecha = ? 
                  WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssisi", $calidad_servicio, $servicios_mejorar, $cambios_recientes, $usuarioID, $fecha, $id);

        if ($stmt->execute()) {
            return true;
        } else {
            throw new Exception("Error al actualizar: " . $stmt->error);
        }
    }

    // Eliminar un registro
    public static function delete($conn, $id)
    {
        $query = "DELETE FROM percepcion_servicios WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            return true;
        } else {
            throw new Exception("Error al eliminar: " . $stmt->error);
        }
    }
}
?>
