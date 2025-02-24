<?php
session_start(); // Inicia la sesión
require_once '../config/config.php'; // Corrección de la conexión

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $localidad = filter_input(INPUT_POST, 'localidad', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    if (empty($name) || empty($localidad) || empty($password)) {
        header("Location: ../views/login.php?error=Por%20favor,%20completa%20todos%20los%20campos.");
        exit;
    }

    // Consulta para obtener datos del usuario
    $query = "SELECT id, name, localidad, password, roleID, ultima_fecha_ingreso, fecha_registro FROM usuarios WHERE name = ? AND localidad = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $name, $localidad);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $ultima_fecha = $user['ultima_fecha_ingreso'];
        $fecha_registro = $user['fecha_registro'];
        $hoy = date('Y-m-d');

        // Si es la primera vez que inicia sesión, actualizar la última fecha de ingreso
        if (is_null($ultima_fecha) || $ultima_fecha == $fecha_registro) {
            $update_sql = "UPDATE usuarios SET ultima_fecha_ingreso = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("si", $hoy, $user['id']);
            $update_stmt->execute();
        } else {
            // Aplicar restricción de 3 meses si no es el primer login
            $fecha_permitida = date('Y-m-d', strtotime($ultima_fecha . ' +3 months'));
            if (strtotime($hoy) < strtotime($fecha_permitida)) {
                header("Location: ../views/login.php?error=No%20puedes%20ingresar%20hasta%20" . $fecha_permitida);
                exit();
            }

            // Actualizar la última fecha de ingreso
            $update_sql = "UPDATE usuarios SET ultima_fecha_ingreso = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("si", $hoy, $user['id']);
            $update_stmt->execute();
        }

        // Iniciar sesión y almacenar información del usuario
        $_SESSION['loggedin'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role_id'] = $user['roleID'];
        $_SESSION['username'] = $user['name'];
        $_SESSION['localidad'] = $user['localidad'];

        // Redirigir al consentimiento informado antes del acceso al dashboard
        header("Location: ../views/consentimiento_informado.php");
        exit();
    } else {
        header("Location: ../views/login.php?error=Nombre,%20localidad%20o%20contrase%C3%B1a%20incorrectos.");
        exit();
    }
}

header("Location: ../views/login.php?error=M%C3%A9todo%20no%20permitido.");
exit;
