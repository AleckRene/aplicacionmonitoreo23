<?php
session_start(); // Inicia la sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.php");
    exit;
}

include '../config/config.php';

// Consulta para obtener los registros
$query = "SELECT * FROM accesibilidad_calidad";
$result = $conn->query($query);

// Verificar si hay registros
$records = [];
if ($result && $result->num_rows > 0) {
    $records = $result->fetch_all(MYSQLI_ASSOC);
}

// Consolidar datos para la tabla
$total_registros = count($records);
$consolidado = [
    'accesibilidad_servicios' => [],
    'actitud_personal' => [],
    'tarifas_ocultas' => [],
    'factores_mejora' => [],
    'disponibilidad_herramientas' => []
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
    <title>Accesibilidad y Calidad</title>
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
        <h1 class="title">Accesibilidad y Calidad</h1>
        <p class="description">Este apartado recopila información sobre la accesibilidad y la calidad de los servicios de salud, así como los factores de mejora identificados.</p>

        <!-- Formulario -->
        <form class="form-container" action="../api/accesibilidad_calidad.php" method="POST">
            <div class="form-group">
                <label for="accesibilidad">Accesibilidad:</label>
                <select id="accesibilidad" name="accesibilidad_servicios" required>
                    <option value="">Seleccione una opción</option>
                    <option value="1">Nada accesibles</option>
                    <option value="2">Poco accesibles</option>
                    <option value="3">Moderadamente accesibles</option>
                    <option value="4">Accesibles</option>
                    <option value="5">Muy accesibles</option>
                </select>
            </div>
            <div class="form-group">
                <label for="actitud_personal">Actitud del Personal:</label>
                <select id="actitud_personal" name="actitud_personal" required>
                    <option value="">Seleccione una opción</option>
                    <option value="1">Muy inapropiada</option>
                    <option value="2">Inapropiada</option>
                    <option value="3">Neutral</option>
                    <option value="4">Apropiada</option>
                    <option value="5">Muy apropiada</option>
                </select>
            </div>
            <div class="form-group">
                <label for="tarifas">Tarifas Ocultas:</label>
                <select id="tarifas" name="tarifas_ocultas" required>
                    <option value="">Seleccione una opción</option>
                    <option value="1">Nunca</option>
                    <option value="2">Raramente</option>
                    <option value="3">Ocasionalmente</option>
                    <option value="4">Frecuentemente</option>
                    <option value="5">Siempre</option>
                </select>
            </div>
            <div class="form-group">
                <label for="factores_mejora">Factores de Mejora:</label>
                <select id="factores_mejora" name="factores_mejora" required>
                    <option value="">Seleccione una opción</option>
                    <option value="Mejorar horarios de atención">Mejorar horarios de atención</option>
                    <option value="Capacitar al personal">Capacitar al personal</option>
                    <option value="Eliminar costos ocultos">Eliminar costos ocultos</option>
                    <option value="Aumentar disponibilidad de medicamentos">Aumentar disponibilidad de medicamentos</option>
                    <option value="Otros">Otros</option>
                </select>
            </div>
            <div class="form-group">
                <label for="disponibilidad">Disponibilidad de Medicamentos:</label>
                <select id="disponibilidad" name="disponibilidad_herramientas" required>
                    <option value="">Seleccione una opción</option>
                    <option value="1">Muy deficiente</option>
                    <option value="2">Deficiente</option>
                    <option value="3">Regular</option>
                    <option value="4">Buena</option>
                    <option value="5">Excelente</option>
                </select>
            </div>
            <button class="btn btn-primary" type="submit">Agregar</button>
        </form>

        <!-- Mensaje de éxito -->
        <div id="successMessage" class="alert alert-success" style="display: none; margin-bottom: 20px;"></div>

        <!-- Botón para abrir el modal -->
        <button id="openModal" class="btn btn-primary">Ver Registros</button>

        <!-- Modal -->
        <div id="modal" class="modal" style="display: none;">
            <div class="modal-content">
                <span id="closeModal" class="close">&times;</span>
                <h2>Registros de Accesibilidad y Calidad</h2>
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>Opción</th>
                            <th>Cantidad</th>
                            <th>Porcentaje</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($consolidado)): ?>
                            <?php foreach ($consolidado as $categoria => $opciones): ?>
                                <tr><th colspan="3"><?= ucwords(str_replace('_', ' ', $categoria)) ?></th></tr>
                                <?php foreach ($opciones as $opcion => $datos): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($opcion) ?></td>
                                        <td><?= $datos['cantidad'] ?></td>
                                        <td><?= $datos['porcentaje'] ?>%</td>
                                    </tr>
                                <?php endforeach; ?>
                                <!-- Totales -->
                                <tr>
                                    <td><strong>Total</strong></td>
                                    <td><strong><?= $total_cantidad[$categoria] ?></strong></td>
                                    <td><strong><?= round($total_porcentaje[$categoria], 2) ?>%</strong></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3">No hay registros disponibles.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Botón para volver -->
        <div class="actions">
            <a href="../views/modulo_vih.php" class="btn btn-secondary">Volver al Módulo VIH</a>
        </div>
    </div>
</body>
</html>
