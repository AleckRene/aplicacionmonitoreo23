<?php
include '../config/config.php'; // Archivo de conexión

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_GET['action'] == 'register') {
    $name = $_POST['name'];
    $localidad = $_POST['localidad'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $fecha_registro = date('Y-m-d'); // Captura la fecha actual

    // Inserta el usuario con fecha_registro y ultima_fecha_ingreso igual a fecha_registro
    $sql = "INSERT INTO usuarios (nombre, localidad_id, contraseña, fecha_registro, ultima_fecha_ingreso) 
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisss", $name, $localidad, $password, $fecha_registro, $fecha_registro);

    if ($stmt->execute()) {
        header("Location: ../views/register.php?success=Registro exitoso");
    } else {
        header("Location: ../views/register.php?error=Error al registrar usuario");
    }
    
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script>
        // Función para limpiar los campos del formulario
        function clearForm() {
            document.querySelector('form').reset(); // Limpia todos los campos
        }

        // Redirigir al login después de 3 segundos si el registro es exitoso y limpiar el formulario
        window.onload = () => {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('success')) {
                clearForm(); // Limpia el formulario
                setTimeout(() => {
                    window.location.href = "../views/login.php";
                }, 3000); // Redirige después de 3 segundos
            }
        };
    </script>
</head>
<body>
    <div class="header">
    <img src="../assets/img/gobierno.png" alt="Gobierno">
    <img src="../assets/img/mcp.png" alt="MCP">
    <img src="../assets/img/organizacion.png" alt="Organización">
    </div>
    <div class="register-container">
        <h1>Registro</h1>
        <!-- Mostrar mensajes de éxito o error -->
        <?php if (isset($_GET['success'])): ?>
            <p class="success-message"><?= htmlspecialchars($_GET['success']) ?></p>
        <?php elseif (isset($_GET['error'])): ?>
            <p class="error-message"><?= htmlspecialchars($_GET['error']) ?></p>
        <?php endif; ?>

        <form action="../api/usuarios.php?action=register" method="POST" autocomplete="off">
            <input type="text" name="name" placeholder="Nombre" required autocomplete="off">
            <input type="text" name="localidad" placeholder="Localidad" required autocomplete="off">
            <input type="password" name="password" placeholder="Contraseña" required autocomplete="off">
            <button type="submit">Registrar</button>
        </form>

        <p>¿Ya tienes una cuenta? <a href="../views/login.php">Inicia sesión aquí</a></p>
    </div>

    <script>
        window.onload = () => {
            document.querySelectorAll('input').forEach(input => input.value = '');
        };
    </script>
    <script src="../assets/js/background.js"></script>
</body>
</html>