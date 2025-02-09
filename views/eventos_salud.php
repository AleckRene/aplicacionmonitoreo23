<?php
session_start(); // Inicia la sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.php");
    exit;
}
include '../config/config.php';

// Consulta para obtener los registros
$query = "
    SELECT 
        id,
        nombre_evento,
        descripcion,
        fecha,
        acciones
    FROM eventos_salud";
$result = $conn->query($query);

// Verificar si hay registros
$records = [];
if ($result && $result->num_rows > 0) {
    $records = $result->fetch_all(MYSQLI_ASSOC);
}

// Consolidar datos para la tabla
$total_registros = count($records);
$consolidado = [
    'nombre_evento' => [],
    'descripcion' => [],
    'acciones' => [],
];

foreach ($records as $record) {
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
    <title>Eventos de Salud</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const successMessage = document.getElementById("successMessage");
            const modal = document.getElementById("modal");
            const openModalBtn = document.getElementById("openModal");
            const closeModalBtn = document.getElementById("closeModal");

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
        <h1 class="title">Eventos de Salud</h1>
        <p class="description">Este apartado recopila información sobre los eventos realizados en la comunidad, incluyendo su tipo, impacto y las acciones ejecutadas para su desarrollo.</p>

        <!-- Mensaje de éxito -->
        <div id="successMessage" class="alert alert-success" style="display: none; margin-bottom: 20px;"></div>

        <!-- Sección de Formulario -->
        <form class="form-container" action="../api/eventos_salud.php" method="POST">
            <div class="form-group">
                <label for="nombre_evento">Tipo de Evento:</label>
                <select id="nombre_evento" name="nombre_evento" required>
                    <option value="">Seleccione una opción</option>
                    <option value="Vacunación comunitaria">Vacunación comunitaria</option>
                    <option value="Jornada de limpieza">Jornada de limpieza</option>
                    <option value="Campaña de sensibilización">Campaña de sensibilización</option>
                    <option value="Otros">Otros</option>
                </select>
            </div>

            <div class="form-group">
                <label for="descripcion">Impacto del Evento:</label>
                <select id="descripcion" name="descripcion" required>
                    <option value="">Seleccione una opción</option>
                    <option value="Muy alto">Muy alto</option>
                    <option value="Alto">Alto</option>
                    <option value="Moderado">Moderado</option>
                    <option value="Bajo">Bajo</option>
                    <option value="Muy bajo">Muy bajo</option>
                </select>
            </div>

            <div class="form-group">
                <label for="fecha">Fecha del Evento:</label>
                <input type="date" id="fecha" name="fecha" required>
            </div>

            <div class="form-group">
                <label for="acciones">Acciones Tomadas:</label>
                <select id="acciones" name="acciones" required>
                    <option value="">Seleccione una opción</option>
                    <option value="Vacunación">Vacunación</option>
                    <option value="Limpieza de áreas">Limpieza de áreas</option>
                    <option value="Distribución de materiales">Distribución de materiales</option>
                    <option value="Charlas y capacitaciones">Charlas y capacitaciones</option>
                    <option value="Otras">Otras</option>
                </select>
            </div>

            <button class="btn btn-primary" type="submit">Registrar Evento</button>
        </form>

        <!-- Botón para abrir el modal -->
        <button id="openModal" class="btn btn-primary">Ver Registros</button>

        <!-- Modal -->
        <div id="modal" class="modal" style="display: none;">
            <div class="modal-content">
                <span id="closeModal" class="close">&times;</span>
                <h2>Registros Consolidados</h2>
                <?php if (!empty($consolidado)): ?>
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
                                    <!-- Fila de totalización -->
                                    <tr>
                                        <td><strong>Total</strong></td>
                                        <td><strong><?= $total_cantidad ?></strong></td>
                                        <td><strong><?= round($total_porcentaje, 2) ?>%</strong></td>
                                    </tr>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3">No hay datos disponibles para esta categoría.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No hay datos consolidados disponibles.</p>
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
