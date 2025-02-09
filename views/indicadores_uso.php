<?php
session_start(); // Inicia la sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.php"); // Redirige al login si no está autenticado
    exit;
}
include '../config/config.php';

// Consulta para obtener los registros
$query = "
    SELECT 
        id,
        numero_usuarios,
        nivel_actividad,
        frecuencia_recomendaciones,
        calidad_uso
    FROM indicadores_uso";

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
    // Sumar el total de usuarios
    $total_numero_usuarios += $record['numero_usuarios'];

    foreach (array_keys($consolidado) as $key) {
        // Validar que la clave exista en el registro
        $valor = $record[$key] ?? null;
        if ($valor !== null) {
            if (!isset($consolidado[$key][$valor])) {
                $consolidado[$key][$valor] = 0;
            }
            $consolidado[$key][$valor]++;
        }
    }
}

// Calcular porcentajes y totales
$total_cantidad = [];
$total_porcentaje = [];

foreach ($consolidado as $categoria => &$opciones) {
    $total_cantidad[$categoria] = 0;
    $total_porcentaje[$categoria] = 0;

    foreach ($opciones as $opcion => $cantidad) {
        $porcentaje = $total_registros > 0 ? round(($cantidad / $total_registros) * 100, 2) : 0;
        $opciones[$opcion] = [
            'cantidad' => $cantidad,
            'porcentaje' => $porcentaje,
        ];
        $total_cantidad[$categoria] += $cantidad;
        $total_porcentaje[$categoria] += $porcentaje;
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
            const successMessage = document.getElementById("successMessage");
            const modal = document.querySelector(".modal");
            const openModalBtn = document.getElementById("openModal");
            const closeModalBtn = document.querySelector(".close-modal");

            // Mostrar el modal al hacer clic en el botón
            openModalBtn.addEventListener("click", function () {
                modal.style.display = "block";
            });

            // Ocultar el modal al hacer clic en el botón de cerrar
            closeModalBtn.addEventListener("click", function () {
                modal.style.display = "none";
            });

            // Ocultar el modal si se hace clic fuera del contenido
            window.addEventListener("click", function (event) {
                if (event.target === modal) {
                    modal.style.display = "none";
                }
            });

            // Mostrar mensaje de éxito al enviar el formulario
            const form = document.querySelector("form");
            form.addEventListener("submit", function (event) {
                event.preventDefault();

                const formData = new FormData(form);

                fetch(form.action, {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        successMessage.textContent = data.success;
                        successMessage.style.display = "block";
                        setTimeout(() => successMessage.style.display = "none", 3000);
                        form.reset();
                    } else if (data.error) {
                        alert(data.error);
                    }
                })
                .catch(error => console.error("Error en la solicitud:", error));
            });
        });
    </script>
</head>

<body>
    <div class="container">
        <h1 class="title">Indicadores de Uso</h1>
        <p class="description">Aquí se miden aspectos relacionados con el uso de herramientas implementadas, incluyendo número de usuarios, nivel de actividad y calidad del uso. Seleccione las respuestas de acuerdo con su experiencia en el uso de la aplicación.</p>

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
                    <option value="Moderadamente bajo">Moderadamente bajo</option>
                    <option value="Moderado">Moderado</option>
                    <option value="Moderadamente alto">Moderadamente alto</option>
                    <option value="Alto">Alto</option>
                </select>
            </div>
            <div class="form-group">
                <label for="frecuenciaRecomendaciones">Frecuencia de Recomendaciones</label>
                <select id="frecuenciaRecomendaciones" name="frecuencia_recomendaciones" required>
                    <option value="">Seleccione una opción</option>
                    <option value="Raramente">Raramente</option>
                    <option value="Ocasionalmente">Ocasionalmente</option>
                    <option value="Moderadamente frecuente">Moderadamente frecuente</option>
                    <option value="Frecuente">Frecuente</option>
                    <option value="Muy frecuente">Muy frecuente</option>
                </select>
            </div>
            <div class="form-group">
                <label for="calidadUso">Calidad del Uso</label>
                <select id="calidadUso" name="calidad_uso" required>
                    <option value="">Seleccione una opción</option>
                    <option value="Deficiente">Deficiente</option>
                    <option value="Aceptable">Aceptable</option>
                    <option value="Buena">Buena</option>
                    <option value="Muy buena">Muy buena</option>
                    <option value="Excelente">Excelente</option>
                </select>
            </div>
            <button class="btn btn-primary" type="submit">Agregar Registro</button>
        </form>

        <!-- Mensaje de éxito -->
        <div id="successMessage" class="alert alert-success" style="display: none;"></div>

        <!-- Botón para abrir el modal -->
        <button id="openModal" class="btn btn-primary">Ver Registros</button>

        <!-- Modal -->
        <div class="modal" style="display: none;">
            <div class="modal-content">
                <span class="close-modal" style="cursor: pointer; float: right;">&times;</span>
                <h2>Datos Consolidados</h2>
                <!-- Total de Usuarios -->
                <h3>Número de Usuarios</h3>
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>Total de Usuarios</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $total_numero_usuarios ?></td>
                        </tr>
                    </tbody>
                </table>
                <!-- Nivel de Actividad, Frecuencia de Recomendaciones y Calidad de Uso -->
                <?php if (!empty($consolidado) && is_array($consolidado)): ?>
                    <?php foreach ($consolidado as $categoria => $opciones): ?>
                        <h3><?= ucwords(str_replace('_', ' ', $categoria)) ?></h3>
                        <table class="styled-table">
                            <thead>
                                <tr>
                                    <th>Opción</th>
                                    <th>Cantidad</th>
                                    <th>Porcentaje</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $total_cantidad = 0;
                                $total_porcentaje = 0;
                                ?>
                                <?php if (is_array($opciones)): ?>
                                    <?php foreach ($opciones as $opcion => $datos): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($opcion) ?></td>
                                            <td><?= $datos['cantidad'] ?></td>
                                            <td><?= $datos['porcentaje'] ?>%</td>
                                        </tr>
                                        <?php 
                                        $total_cantidad += $datos['cantidad'];
                                        $total_porcentaje += $datos['porcentaje'];
                                        ?>
                                    <?php endforeach; ?>
                                    <!-- Totalización -->
                                    <tr>
                                        <td><strong>Total</strong></td>
                                        <td><strong><?= $total_cantidad ?></strong></td>
                                        <td><strong><?= round($total_porcentaje, 2) ?>%</strong></td>
                                    </tr>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3">No hay datos disponibles.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No hay datos consolidados para mostrar.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Botón para volver -->
        <div class="actions">
            <a href="../views/modulo_general.php" class="btn btn-secondary">Volver al Módulo General</a>
        </div>
    </div>
</body>
</html>
