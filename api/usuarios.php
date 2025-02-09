<?php
include '../config/config.php'; // Corrección de la conexión

session_start(); // Inicia la sesión global

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        $action = $_GET['action'] ?? '';

        if ($action === 'register') {
            // Registro de usuario
            $data = $_POST;

            // Validación de campos requeridos
            if (empty($data['name']) || empty($data['localidad']) || empty($data['password'])) {
                header("Location: ../views/register.php?error=Por%20favor,%20completa%20todos%20los%20campos.");
                exit;
            }

            // Sanitizar entradas
            $name = trim($conn->real_escape_string($data['name']));
            $localidad = trim($conn->real_escape_string($data['localidad']));
            $password = password_hash($data['password'], PASSWORD_BCRYPT);
            $roleID = rand(1, 8000); // Generar un rol aleatorio
            $fecha_registro = date('Y-m-d');
            $ultima_fecha_ingreso = NULL;

            // Verificar si el usuario ya está registrado
            $checkUser = "SELECT id FROM usuarios WHERE name = ? AND localidad = ?";
            $stmtCheck = $conn->prepare($checkUser);
            $stmtCheck->bind_param("ss", $name, $localidad);
            $stmtCheck->execute();
            $resultCheck = $stmtCheck->get_result();

            if ($resultCheck->num_rows > 0) {
                header("Location: ../views/register.php?error=El%20usuario%20ya%20est%C3%A1%20registrado.");
                exit;
            }

            // Insertar usuario con fecha_registro y ultima_fecha_ingreso como NULL
            $sqlInsert = "INSERT INTO usuarios (name, localidad, password, roleID, fecha_registro, ultima_fecha_ingreso) 
                          VALUES (?, ?, ?, ?, ?, NULL)";
            $stmtInsert = $conn->prepare($sqlInsert);
            $stmtInsert->bind_param("sssis", $name, $localidad, $password, $roleID, $fecha_registro);

            if ($stmtInsert->execute()) {
                header("Location: ../views/login.php?success=Registro%20exitoso.%20Por%20favor,%20inicia%20sesi%C3%B3n.");
                exit;
            } else {
                header("Location: ../views/register.php?error=Error%20al%20registrar%20usuario.");
                exit;
            }
        } else {
            echo json_encode(["error" => "Acción no válida."]);
            exit;
        }

    case 'GET':
        // Obtener lista de usuarios
        $sql = "SELECT id, name, localidad, roleID, fecha_registro, ultima_fecha_ingreso FROM usuarios";
        $result = $conn->query($sql);

        if ($result) {
            $users = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($users);
        } else {
            echo json_encode(["error" => "Error al obtener usuarios: " . $conn->error]);
        }
        break;

    default:
        echo json_encode(["error" => "Método no soportado."]);
        break;
}

$conn->close();
?>
