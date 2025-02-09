<?php
session_start(); // Asegura que las sesiones estén habilitadas

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    // Redirigir al login si no está autenticado
    header('Location: ../views/login.php');
    exit(); // Asegura que no se siga ejecutando el resto del script
}

// Puedes añadir más lógica aquí, como verificar roles de usuario si es necesario
?>

