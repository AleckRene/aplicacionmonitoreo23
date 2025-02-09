<?php
class User {
    public $id;
    public $nombre;
    public $email;
    public $password;
    public $rol;

    public function __construct($id, $nombre, $email, $password, $rol) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->email = $email;
        $this->password = $password;
        $this->rol = $rol;
    }

    public static function findByEmail($email, $conn) {
        $query = "SELECT * FROM usuarios WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return new self($row['id'], $row['nombre'], $row['email'], $row['password'], $row['rol']);
        }
        return null;
    }

    public static function create($nombre, $email, $password, $rol, $conn) {
        $query = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt->bind_param("sssi", $nombre, $email, $hashedPassword, $rol);
        return $stmt->execute();
    }

    public static function getById($id, $conn) {
        $query = "SELECT * FROM usuarios WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return new self($row['id'], $row['nombre'], $row['email'], $row['password'], $row['rol']);
        }
        return null;
    }

    public static function getAll($conn) {
        $query = "SELECT * FROM usuarios";
        $result = $conn->query($query);
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = new self($row['id'], $row['nombre'], $row['email'], $row['password'], $row['rol']);
        }
        return $users;
    }

    public static function update($id, $nombre, $email, $password, $rol, $conn) {
        $query = "UPDATE usuarios SET nombre = ?, email = ?, password = ?, rol = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt->bind_param("sssii", $nombre, $email, $hashedPassword, $rol, $id);
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
