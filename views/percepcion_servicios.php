<?php
session_start(); // Inicia la sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.php");
    exit;
}

include '../config/config.php';

// Consulta para obtener los registros
$query = "SELECT * FROM percepcion_servicios";
$result = $conn->query($query);

// Verificar si hay registros
$records = [];
if ($result && $result->num_rows > 0) {
    $records = $result->fetch_all(MYSQLI_ASSOC);
}

// Consolidar datos para la tabla
$total_registros = count($records);
$consolidado = [
    'calidad_servicio' => [],
    'servicios_mejorar' => [],
    'cambios_recientes' => []
];

foreach ($records as $record) {
    foreach (['calidad_servicio', 'servicios_mejorar', 'cambios_recientes'] as $key) {
        $valor = $record[$key] ?? null;
        if ($valor !== null) {
            if (!isset($consolidado[$key][$valor])) {
                $consolidado[$key][$valor] = 0;
            }
            $consolidado[$key][$valor]++;
        }
    }
}

// Calcular porcentajes
foreach ($consolidado as $categoria => &$opciones) {
    foreach ($opciones as $opcion => &$datos) {
        $datos = [
            'cantidad' => $datos,
            'porcentaje' => $total_registros > 0 ? round(($datos / $total_registros) * 100, 2) : 0,
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Percepción de Servicios</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.getElementById("percepcionForm");
            const successMessage = document.getElementById("successMessage");
            const errorMessage = document.getElementById("errorMessage");
            const modal = document.getElementById("modal");
            const openModalBtn = document.getElementById("openModal");
            const closeModalBtn = document.getElementById("closeModal");

            // Mostrar modal
            openModalBtn.addEventListener("click", function () {
                modal.style.display = "block";
            });

            // Cerrar modal
            closeModalBtn.addEventListener("click", function () {
                modal.style.display = "none";
            });

            window.addEventListener("click", function (event) {
                if (event.target === modal) {
                    modal.style.display = "none";
                }
            });

            // Manejo del formulario
            form.addEventListener("submit", function (event) {
                event.preventDefault();
                const formData = new FormData(form);

                fetch("../api/percepcion_servicios.php", {
                    method: "POST",
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        successMessage.textContent = data.success;
                        successMessage.style.display = "block";
                        errorMessage.style.display = "none";
                        form.reset();
                    } else if (data.error) {
                        errorMessage.textContent = data.error;
                        errorMessage.style.display = "block";
                        successMessage.style.display = "none";
                    }
                })
                .catch(error => {
                    errorMessage.textContent = "Error al enviar los datos.";
                    errorMessage.style.display = "block";
                    successMessage.style.display = "none";
                    console.error("Error:", error);
                });
            });
        });
    </script>
</head>
<body>
    <div class="container">
        <h1 class="title">Percepción de Servicios</h1>
        <p class="description">Esta sección recoge la opinión de los usuarios sobre la calidad de los servicios, las áreas a mejorar y los cambios recientes observados.</p>

        <!-- Formulario -->
        <form id="percepcionForm" class="form-container">
            <div class="form-group">
                <label for="calidad_servicio">Calidad del Servicio</label>
                <select id="calidad_servicio" name="calidad_servicio" required>
                    <option value="">Seleccione una opción</option>
                    <option value="Muy mala">Muy mala</option>
                    <option value="Mala">Mala</option>
                    <option value="Regular">Regular</option>
                    <option value="Buena">Buena</option>
                    <option value="Muy buena">Muy buena</option>
                </select>
            </div>

            <div class="form-group">
                <label for="servicios_mejorar">Servicios a Mejorar</label>
                <select id="servicios_mejorar" name="servicios_mejorar" required>
                    <option value="">Seleccione una opción</option>
                    <option value="Atención al cliente">Atención al cliente</option>
                    <option value="Tiempos de espera">Tiempos de espera</option>
                    <option value="Disponibilidad de recursos">Disponibilidad de recursos</option>
                    <option value="Infraestructura">Infraestructura</option>
                    <option value="Otros">Otros</option>
                </select>
            </div>

            <div class="form-group">
                <label for="cambios_recientes">Cambios Recientes</label>
                <select id="cambios_recientes" name="cambios_recientes" required>
                    <option value="">Seleccione una opción</option>
                    <option value="Mejoras en atención">Mejoras en atención</option>
                    <option value="Reducción de tiempos de espera">Reducción de tiempos de espera</option>
                    <option value="Actualización de infraestructura">Actualización de infraestructura</option>
                    <option value="Mayor disponibilidad de recursos">Mayor disponibilidad de recursos</option>
                    <option value="No se han observado cambios">No se han observado cambios</option>
                </select>
            </div>

            <button class="btn btn-primary" type="submit">Agregar Percepción</button>
        </form>

        <!-- Contenedores de mensajes -->
        <div id="successMessage" class="alert alert-success" style="display: none;"></div>
        <div id="errorMessage" class="alert alert-danger" style="display: none;"></div>

        <!-- Botones adicionales -->
        <div class="actions">
            <button id="openModal" class="btn btn-primary">Ver Registros</button>
            <a href="../views/modulo_vih.php" class="btn btn-secondary">Volver al Módulo VIH</a>
        </div>

        <!-- Modal -->
        <div id="modal" class="modal" style="display: none;">
            <div class="modal-content">
                <span id="closeModal" class="close">&times;</span>
                <h2>Registros de Percepción</h2>
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
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3">No hay registros disponibles.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>    
</body>
</html>
