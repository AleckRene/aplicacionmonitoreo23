<?php
class User {
    public $id;
    public $nombre;
    public $localidad;
    public $password;
    public $rol;
    public $fecha_registro;
    public $ultima_fecha_ingreso;

    public function __construct($id, $nombre, $localidad, $password, $rol, $fecha_registro, $ultima_fecha_ingreso) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->localidad = $localidad;
        $this->password = $password;
        $this->rol = $rol;
        $this->fecha_registro = $fecha_registro;
        $this->ultima_fecha_ingreso = $ultima_fecha_ingreso;
    }

    public static function findByLocalidad($nombre, $localidad, $conn) {
        $query = "SELECT * FROM usuarios WHERE nombre = ? AND localidad = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $nombre, $localidad);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return new self($row['id'], $row['nombre'], $row['localidad'], $row['password'], $row['rol'], $row['fecha_registro'], $row['ultima_fecha_ingreso']);
        }
        return null;
    }

    public static function create($nombre, $localidad, $password, $rol, $conn) {
        $query = "INSERT INTO usuarios (nombre, localidad, password, rol, fecha_registro, ultima_fecha_ingreso) VALUES (?, ?, ?, ?, NOW(), NOW())";
        $stmt = $conn->prepare($query);
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt->bind_param("sssi", $nombre, $localidad, $hashedPassword, $rol);
        return $stmt->execute();
    }

    public static function getById($id, $conn) {
        $query = "SELECT * FROM usuarios WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return new self($row['id'], $row['nombre'], $row['localidad'], $row['password'], $row['rol'], $row['fecha_registro'], $row['ultima_fecha_ingreso']);
        }
        return null;
    }

    public static function getAll($conn) {
        $query = "SELECT * FROM usuarios";
        $result = $conn->query($query);
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = new self($row['id'], $row['nombre'], $row['localidad'], $row['password'], $row['rol'], $row['fecha_registro'], $row['ultima_fecha_ingreso']);
        }
        return $users;
    }

    public static function update($id, $nombre, $localidad, $password, $rol, $conn) {
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $query = "UPDATE usuarios SET nombre = ?, localidad = ?, password = ?, rol = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssii", $nombre, $localidad, $hashedPassword, $rol, $id);
        } else {
            $query = "UPDATE usuarios SET nombre = ?, localidad = ?, rol = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssii", $nombre, $localidad, $rol, $id);
        }
        return $stmt->execute();
    }

    public static function delete($id, $conn) {
        $query = "DELETE FROM usuarios WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
