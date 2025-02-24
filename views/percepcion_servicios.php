<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.php");
    exit;
}

include '../config/config.php';

// **Revisamos las columnas reales de la tabla**
$query = "SHOW COLUMNS FROM percepcion_servicios";
$result_columns = $conn->query($query);
$columnas_validas = [];

if ($result_columns && $result_columns->num_rows > 0) {
    while ($row = $result_columns->fetch_assoc()) {
        $columnas_validas[] = $row['Field'];
    }
}

// **Verificamos qué columnas existen antes de hacer la consulta principal**
$columnas_existentes = array_intersect(
    ['calidad_servicio', 'servicios_mejorar', 'cambios_recientes'], 
    $columnas_validas
);

// Si la tabla no tiene columnas válidas, detenemos el proceso
if (empty($columnas_existentes)) {
    die("Error: No se encontraron columnas válidas en la tabla 'percepcion_servicios'.");
}

// Generamos la consulta solo con las columnas existentes
$query = "SELECT " . implode(', ', $columnas_existentes) . " FROM percepcion_servicios";
$result = $conn->query($query);

// Verificar si hay registros
$records = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
$total_registros = count($records);

// Consolidar datos para la tabla
$consolidado = [];
foreach ($columnas_existentes as $columna) {
    $consolidado[$columna] = [];
}

foreach ($records as $record) {
    foreach ($consolidado as $categoria => &$opciones) {
        if (!empty($record[$categoria])) {
            $valores = explode(',', $record[$categoria]);
            $valores = array_map('trim', $valores);
            $valores = array_unique($valores);

            foreach ($valores as $valor) {
                if (!empty($valor)) {
                    $opciones[$valor] = ($opciones[$valor] ?? 0) + 1;
                }
            }
        }
    }
}

// Calcular porcentajes
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
    <title>Percepción de Servicios</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const modal = document.getElementById("modal");
            document.getElementById("openModal").addEventListener("click", () => modal.style.display = "block");
            document.getElementById("closeModal").addEventListener("click", () => modal.style.display = "none");
            window.onclick = event => { if (event.target === modal) modal.style.display = "none"; };

            document.querySelector("form").addEventListener("submit", function (event) {
                event.preventDefault();
                fetch(this.action, { method: "POST", body: new FormData(this) })
                    .then(response => response.json())
                    .then(data => {
                        alert(data.success ?? data.error);
                        if (data.success) this.reset();
                    })
                    .catch(error => console.error("Error:", error));
            });
        });
    </script>
</head>

<body>
    <div class="container">
        <h1 class="title">Percepción de Servicios</h1>
        <p class="description">Esta sección recoge la opinión de los usuarios sobre la calidad de los servicios, las áreas a mejorar y los cambios recientes observados.</p>

        <!-- Formulario -->
        <form class="form-container" action="../api/percepcion_servicios.php" method="POST">
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

        <button id="openModal" class="btn btn-primary">Ver Consolidado</button>

        <!-- Modal de datos consolidados -->
        <div id="modal" class="modal" style="display: none;">
            <div class="modal-content">
                <span id="closeModal" class="close">&times;</span>
                <h2>Datos Consolidados de Percepción de Servicios</h2>
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>Indicador</th>
                            <th>Frecuencia</th>
                            <th>Porcentaje</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($consolidadoPorcentajes as $categoria => $opciones): ?>
                            <tr><th colspan="3"><?= ucwords(str_replace('_', ' ', $categoria)) ?> (Total: <?= $totalesPorCategoria[$categoria] ?>)</th></tr>
                            <?php foreach ($opciones as $opcion => $datos): ?>
                                <tr>
                                    <td><?= htmlspecialchars($opcion) ?></td>
                                    <td><?= $datos['cantidad'] ?></td>
                                    <td><?= $datos['porcentaje'] ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Botón para volver al módulo VIH -->
        <div class="actions">
            <a href="../views/modulo_vih.php" class="btn btn-secondary">Volver al Módulo VIH</a>
        </div>
    </div>
</body>
</html>
