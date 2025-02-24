<?php
// Inicia sesión y verifica si el usuario está logueado
session_start();
if (empty($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php?error=Debe iniciar sesión primero");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo General</title>
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
        .module-buttons, .actions, .reports {
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
        .btn-secondary {
            background-color: #28a745;
        }
        .btn:hover {
            opacity: 0.8;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
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
        input, select, button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #007bff;
            color: white;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }
        button:hover {
            background-color: #0056b3;
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
        <h1>Módulo General</h1>
        <p>Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>. Aquí puedes gestionar y visualizar las secciones relacionadas con:</p>

        <div class="module-buttons">
            <a href="indicadores_uso.php" class="btn">Indicadores de Uso</a>
            <a href="participacion_comunitaria.php" class="btn">Participación Comunitaria</a>
            <a href="eventos_salud.php" class="btn">Eventos de Salud</a>
            <a href="necesidades_comunitarias.php" class="btn">Necesidades Comunitarias</a>
        </div>

        <div class="reports">
            <h2>Reportes</h2>
            <button id="abrirModalReporte" class="btn btn-secondary">Generar Reporte General</button>
        </div>

        <div id="modalReporte" class="modal">
            <div class="modal-content">
                <span class="close" onclick="cerrarModal()">&times;</span>
                <h2>Generar Reporte</h2>
                <form id="formReporte" action="./reports/generar_reporte.php" method="POST">
                    <label for="fecha_inicio">Fecha de Inicio:</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" required>

                    <label for="fecha_fin">Fecha de Fin:</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" required>

                    <label for="periodicidad">Periodicidad:</label>
                    <select id="periodicidad" name="periodicidad">
                        <option value="diario">Diario</option>
                        <option value="semanal">Semanal</option>
                        <option value="mensual">Mensual</option>
                        <option value="bimensual">Bimensual</option>
                        <option value="trimestral">Trimestral</option>
                        <option value="semestral">Semestral</option>
                        <option value="anual">Anual</option>
                    </select>

                    <input type="hidden" name="modulo" value="general">

                    <button type="submit">Generar Reporte</button>
                </form>
            </div>
        </div>

        <div class="actions">
            <a href="../views/dashboard.php" class="btn btn-primary">Volver al Dashboard</a>
        </div>
    </div>

    <script>
        document.getElementById("abrirModalReporte").addEventListener("click", function() {
            document.getElementById("modalReporte").style.display = "block";
        });

        function cerrarModal() {
            document.getElementById("modalReporte").style.display = "none";
        }

        window.onclick = function(event) {
            let modal = document.getElementById("modalReporte");
            if (event.target === modal) {
                cerrarModal();
            }
        };
    </script>
</body>
</html>
