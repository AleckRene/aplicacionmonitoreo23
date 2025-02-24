<?php
session_start(); // Inicia la sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.php"); // Redirige al login si no está autenticado
    exit;
}

include '../config/config.php';

// Mapeo de valores numéricos a descripciones en frecuencia de recomendaciones
$mapeoFrecuencia = [
    1 => "Raramente",
    2 => "Ocasionalmente",
    3 => "Moderadamente frecuente",
    4 => "Frecuente",
    5 => "Muy frecuente"
];

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

        // Convertir los valores de frecuencia de recomendaciones a texto
        if ($key === 'frecuencia_recomendaciones' && isset($mapeoFrecuencia[$valor])) {
            $valor = $mapeoFrecuencia[$valor];
        }

        if ($valor !== null) {
            if (!isset($consolidado[$key][$valor])) {
                $consolidado[$key][$valor] = 0;
            }
            $consolidado[$key][$valor]++;
        }
    }
}

// Calcular porcentajes y totales por categoría
$consolidadoPorcentajes = [];
$totalesPorCategoria = [];

foreach ($consolidado as $categoria => $opciones) {
    $totalCategoria = array_sum($opciones);
    $totalesPorCategoria[$categoria] = $totalCategoria;

    foreach ($opciones as $opcion => $cantidad) {
        $porcentaje = ($totalCategoria > 0) ? round(($cantidad / $totalCategoria) * 100, 2) : 0;
        $consolidadoPorcentajes[$categoria][$opcion] = ['cantidad' => $cantidad, 'porcentaje' => $porcentaje];
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
            const modal = document.getElementById("modal");
            const openModalBtn = document.getElementById("openModal");
            const closeModalBtn = document.getElementById("closeModal");

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

        <!-- Formulario de Registro -->
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
            <div class="form-group">
                <label for="frecuenciaRecomendaciones">Frecuencia de Recomendaciones</label>
                <select id="frecuenciaRecomendaciones" name="frecuencia_recomendaciones" required>
                    <option value="">Seleccione una opción</option>
                    <option value="1">Raramente</option>
                    <option value="2">Ocasionalmente</option>
                    <option value="3">Moderadamente frecuente</option>
                    <option value="4">Frecuente</option>
                    <option value="5">Muy frecuente</option>
                </select>
            </div>
            <div class="form-group">
                <label for="calidadUso">Calidad de Uso</label>
                <select id="calidadUso" name="calidad_uso" required>
                    <option value="">Seleccione una opción</option>
                    <option value="Deficiente">Deficiente</option>
                    <option value="Aceptable">Aceptable</option>
                    <option value="Buena">Buena</option>
                    <option value="Excelente">Excelente</option>
                </select>
            </div>
            <button class="btn btn-primary" type="submit">Agregar Registro</button>
        </form>

        <!-- Botón para abrir modal de registros -->
        <button id="openModal" class="btn btn-primary">Ver Registros</button>

        <!-- Modal de datos consolidados -->
        <div id="modal" class="modal" style="display: none;">
            <div class="modal-content">
                <span id="closeModal" class="close-modal" style="cursor: pointer;">&times;</span>
                <h2>Datos Consolidados</h2>
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>Indicador</th>
                            <th>Frecuencia</th>
                            <th>Porcentaje</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Total de Usuarios</strong></td>
                            <td colspan="2"><?= $total_numero_usuarios ?></td>
                        </tr>
                        <?php foreach ($consolidadoPorcentajes as $categoria => $opciones): ?>
                            <tr><th colspan="3"><?= ucwords(str_replace('_', ' ', $categoria)) ?></th></tr>
                            <?php foreach ($opciones as $opcion => $datos): ?>
                                <tr>
                                    <td><?= htmlspecialchars($opcion) ?></td>
                                    <td><?= $datos['cantidad'] ?></td>
                                    <td><?= $datos['porcentaje'] ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td><strong>Total <?= ucwords(str_replace('_', ' ', $categoria)) ?></strong></td>
                                <td><strong><?= $totalesPorCategoria[$categoria] ?></strong></td>
                                <td><strong>100%</strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Botón para regresar al módulo general -->
        <div class="actions">
            <a href="../views/modulo_general.php" class="btn btn-secondary">Volver al Módulo General</a>
        </div>
    </div>
</body>
</html>
