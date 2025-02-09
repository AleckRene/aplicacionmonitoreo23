<?php
// Configuración de la base de datos
$host = "localhost";
$user = "root";
$password = "";
$dbname = "aplicacionmonitoreo";

// Crear una conexión
$conn = new mysqli($host, $user, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    error_log("Error en la conexión: " . $conn->connect_error, 3, "../logs/error.log");
    die("Error al conectar con la base de datos. Por favor, contacte al administrador.");
}

// Establecer el conjunto de caracteres
if (!$conn->set_charset("utf8mb4")) {
    error_log("Error al establecer el conjunto de caracteres: " . $conn->error, 3, "../logs/error.log");
    die("Error al configurar la conexión. Por favor, contacte al administrador.");
}
?>

