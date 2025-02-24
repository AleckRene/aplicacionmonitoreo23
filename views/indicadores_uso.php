<?php
session_start(); // Inicia la sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.php"); // Redirige al login si no está autenticado
    exit;
}
include '../config/config.php';

// Consulta para obtener los registros
$query = "SELECT id, numero_usuarios, nivel_actividad, frecuencia_recomendaciones, calidad_uso FROM indicadores_uso";
$result = $conn->query($query);

// Verificar si hay registros
$records = [];
if ($result && $result->num_rows > 0) {
    $records = $result->fetch_all(MYSQLI_ASSOC);
}

// Consolidar datos para la tabla
$total_registros = count($records);
$total_numero_usuarios = 0;
$consolidado = [
    'nivel_actividad' => [],
    'frecuencia_recomendaciones' => [],
    'calidad_uso' => [],
];

foreach ($records as $record) {
    $total_numero_usuarios += $record['numero_usuarios'];
    foreach (array_keys($consolidado) as $key) {
        $valor = $record[$key] ?? null;
        if ($valor !== null) {
            if (!isset($consolidado[$key][$valor])) {
                $consolidado[$key][$valor] = 0;
            }
            $consolidado[$key][$valor]++;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Indicadores de Uso</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const modal = document.querySelector(".modal");
            const openModalBtn = document.getElementById("openModal");
            const closeModalBtn = document.querySelector(".close-modal");

            openModalBtn.addEventListener("click", function () {
                modal.style.display = "block";
            });

            closeModalBtn.addEventListener("click", function () {
                modal.style.display = "none";
            });

            window.addEventListener("click", function (event) {
                if (event.target === modal) {
                    modal.style.display = "none";
                }
            });
        });
    </script>
</head>
<body>
    <div class="container">
        <h1 class="title">Indicadores de Uso</h1>
        <p class="description">Aquí se miden aspectos relacionados con el uso de herramientas implementadas.</p>
        <form class="form-container" action="../api/indicadores_uso.php" method="POST">
            <div class="form-group">
                <label for="numeroUsuarios">Número de Usuarios</label>
                <input type="number" id="numeroUsuarios" name="numero_usuarios" required placeholder="Ejemplo: 50">
            </div>
            <div class="form-group">
                <label for="nivelActividad">Nivel de Actividad</label>
                <select id="nivelActividad" name="nivel_actividad" required>
                    <option value="">Seleccione una opción</option>
                    <option value="Bajo">Bajo</option>
                    <option value="Moderado">Moderado</option>
                    <option value="Alto">Alto</option>
                </select>
            </div>
            <button class="btn btn-primary" type="submit">Agregar Registro</button>
        </form>
        <button id="openModal" class="btn btn-primary">Ver Registros</button>
        <div class="modal" style="display: none;">
            <div class="modal-content">
                <span class="close-modal" style="cursor: pointer; float: right;">&times;</span>
                <h2>Datos Consolidados</h2>
                <table class="styled-table">
                    <thead>
                        <tr><th>Total de Usuarios</th></tr>
                    </thead>
                    <tbody>
                        <tr><td><?= $total_numero_usuarios ?></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="actions">
            <a href="../views/modulo_general.php" class="btn btn-secondary">Volver al Módulo General</a>
        </div>
    </div>
</body>
</html>
