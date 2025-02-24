<?php
session_start();

// Validar si el usuario ha iniciado sesión
if (empty($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php?error=Debe iniciar sesión primero");
    exit;
}

// Validar si el usuario ha aceptado el consentimiento informado
if (!isset($_SESSION['consent_accepted']) || $_SESSION['consent_accepted'] !== true) {
    header("Location: consentimiento_informado.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Módulos</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            text-align: center;
        }
        .auth-container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .module-buttons, .actions {
            margin-top: 20px;
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            font-size: 16px;
            margin: 10px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .btn.active {
            background-color: #28a745;
        }
        .btn.disabled {
            background-color: #6c757d;
            cursor: not-allowed;
            opacity: 0.5;
        }
        .btn:hover:not(.disabled) {
            opacity: 0.8;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 2;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background: white;
            width: 90%;
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        .close {
            float: right;
            font-size: 24px;
            cursor: pointer;
        }
        @media (max-width: 600px) {
            .auth-container {
                width: 95%;
            }
            .btn {
                width: 100%;
                display: block;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <h1>Bienvenido al Dashboard</h1>
        <p>Hola, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>. Selecciona un módulo para comenzar:</p>

        <div class="module-buttons">
            <a href="consentimiento_informado.php" class="btn">Consentimiento Informado</a>
            <a href="modulo_general.php" class="btn active">Módulo General</a>
            <a href="modulo_vih.php" class="btn active">Módulo VIH</a>

            <button class="btn disabled" onclick="showModal('Módulo TB')">Módulo TB</button>
            <button class="btn disabled" onclick="showModal('Módulo Malaria')">Módulo Malaria</button>
            <button class="btn disabled" onclick="showModal('Módulo Pandemias')">Módulo Pandemias</button>
        </div>

        <div class="actions">
            <a href="../logout.php" class="btn btn-danger">Cerrar sesión</a>
        </div>
    </div>

    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModal()">&times;</span>
            <h2 id="modalTitle">Módulo en Construcción</h2>
            <p id="modalMessage">Este módulo está en construcción. Por favor, inténtalo más tarde.</p>
            <button onclick="cerrarModal()" class="btn">Entendido</button>
        </div>
    </div>

    <script>
        function showModal(moduleName) {
            document.getElementById("modalTitle").innerText = moduleName;
            document.getElementById("modal").style.display = "block";
        }

        function cerrarModal() {
            document.getElementById("modal").style.display = "none";
        }

        window.onclick = function(event) {
            let modal = document.getElementById("modal");
            if (event.target === modal) {
                cerrarModal();
            }
        };
    </script>
</body>
</html>
