<?php
session_start();

// Verificar si hay una sesión activa
if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['loggedin'])) {
    // Destruir todas las variables de sesión
    session_unset(); // Limpia las variables de sesión
    session_destroy(); // Destruye la sesión

    // Redirigir al inicio de sesión con un mensaje de éxito
    header("Location: ../views/login.php?success=Sesión cerrada exitosamente");
    exit;
} else {
    // Si no hay una sesión activa, redirigir al inicio de sesión con un mensaje de información
    header("Location: ../views/login.php?info=No había sesión activa");
    exit;
}
?>
