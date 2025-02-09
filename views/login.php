<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script>
        // Función para limpiar los campos del formulario al cargar la página
        window.onload = () => {
            document.querySelector('form').reset(); // Limpia todos los campos
        };
    </script>
</head>
<body>
    <div class="header">
        <img src="../assets/img/gobierno.png" alt="Gobierno">
        <img src="../assets/img/mcp.png" alt="MCP">
        <img src="../assets/img/organizacion.png" alt="Organización">
    </div>
    <div class="login-container">
        <h1>Iniciar Sesión</h1>
        <!-- Mostrar mensajes -->
        <?php if (isset($_GET['error'])): ?>
            <p class="error-message"><?= htmlspecialchars($_GET['error']) ?></p>
        <?php endif; ?>

        <form action="../api/login.php" method="POST" autocomplete="off">
            <input type="text" name="name" placeholder="Nombre" required autocomplete="off">
            <input type="text" name="localidad" placeholder="Localidad" required autocomplete="off">
            <input type="password" name="password" placeholder="Contraseña" required autocomplete="off">
            <button type="submit">Ingresar</button>
        </form>

        <p>¿No tienes una cuenta? <a href="../views/register.php">Regístrate aquí</a></p>
    </div>

    <script>
        window.onload = () => {
            document.querySelectorAll('input').forEach(input => input.value = '');
        };
    </script>
    <script src="../assets/js/background.js"></script>
</body>
</html>