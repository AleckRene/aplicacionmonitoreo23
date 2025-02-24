<?php
include '../config/config.php'; // Archivo de conexión

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['action']) && $_GET['action'] == 'register') {
    $name = trim($_POST['name']);
    $localidad = trim($_POST['localidad']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
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

    // Asignar un rol aleatorio
    $roleID = rand(1, 8000);

    // Insertar el usuario con la fecha de registro y última fecha de ingreso como NULL
    $sqlInsert = "INSERT INTO usuarios (name, localidad, password, roleID, fecha_registro, ultima_fecha_ingreso) 
                  VALUES (?, ?, ?, ?, ?, ?)";
    $stmtInsert = $conn->prepare($sqlInsert);
    $stmtInsert->bind_param("sssisi", $name, $localidad, $password, $roleID, $fecha_registro, $ultima_fecha_ingreso);

    if ($stmtInsert->execute()) {
        header("Location: ../views/login.php?success=Registro%20exitoso.%20Por%20favor,%20inicia%20sesi%C3%B3n.");
        exit;
    } else {
        header("Location: ../views/register.php?error=Error%20al%20registrar%20usuario.");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $sql = "SELECT id, name, localidad, roleID, fecha_registro, ultima_fecha_ingreso FROM usuarios";
    $result = $conn->query($sql);

    if ($result) {
        $users = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($users);
    } else {
        echo json_encode(["error" => "Error al obtener usuarios: " . $conn->error]);
    }
}

$conn->close();
