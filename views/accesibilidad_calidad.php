<?php
session_start(); // Inicia la sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.php");
    exit;
}

include '../config/config.php';

// Consulta para obtener los registros
$query = "SELECT id, accesibilidad_servicios, actitud_personal, tarifas_ocultas, factores_mejora, disponibilidad_herramientas FROM accesibilidad_calidad";
$result = $conn->query($query);

// Verificar si hay registros
$records = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
$total_registros = count($records);

// Consolidar datos para la tabla
$consolidado = [
    'accesibilidad_servicios' => [],
    'actitud_personal' => [],
    'tarifas_ocultas' => [],
    'factores_mejora' => [],
    'disponibilidad_herramientas' => []
];

foreach ($records as $record) {
    foreach ($consolidado as $categoria => &$opciones) {
        if (!empty($record[$categoria])) {
            $valores = explode(',', $record[$categoria]); // Separar valores por coma
            $valores = array_map('trim', $valores); // Limpiar espacios en blanco
            $valores = array_unique($valores); // Evitar duplicados dentro del mismo registro

            foreach ($valores as $valor) {
                if (!empty($valor)) {
                    $opciones[$valor] = ($opciones[$valor] ?? 0) + 1;
                }
            }
        }
    }
}

// Calcular totales y porcentajes
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
    <title>Accesibilidad y Calidad</title>
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
        <h1 class="title">Accesibilidad y Calidad</h1>
        <p class="description">Este apartado recopila información sobre la accesibilidad y la calidad de los servicios de salud, así como los factores de mejora identificados.</p>

        <!-- Formulario -->
        <form class="form-container" action="../api/accesibilidad_calidad.php" method="POST">
            <div class="form-group">
                <label for="accesibilidad">Accesibilidad:</label>
                <select id="accesibilidad" name="accesibilidad_servicios" required>
                    <option value="">Seleccione una opción</option>
                    <option value="Nada accesibles">Nada accesibles</option>
                    <option value="Poco accesibles">Poco accesibles</option>
                    <option value="Moderadamente accesibles">Moderadamente accesibles</option>
                    <option value="Accesibles">Accesibles</option>
                    <option value="Muy accesibles">Muy accesibles</option>
                </select>
            </div>

            <div class="form-group">
                <label for="actitud_personal">Actitud del Personal:</label>
                <select id="actitud_personal" name="actitud_personal" required>
                    <option value="">Seleccione una opción</option>
                    <option value="Muy inapropiada">Muy inapropiada</option>
                    <option value="Inapropiada">Inapropiada</option>
                    <option value="Neutral">Neutral</option>
                    <option value="Apropiada">Apropiada</option>
                    <option value="Muy apropiada">Muy apropiada</option>
                </select>
            </div>

            <div class="form-group">
                <label for="tarifas">Tarifas Ocultas:</label>
                <select id="tarifas" name="tarifas_ocultas" required>
                    <option value="">Seleccione una opción</option>
                    <option value="Nunca">Nunca</option>
                    <option value="Raramente">Raramente</option>
                    <option value="Ocasionalmente">Ocasionalmente</option>
                    <option value="Frecuentemente">Frecuentemente</option>
                    <option value="Siempre">Siempre</option>
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

            <button class="btn btn-primary" type="submit">Agregar</button>
        </form>

        <button id="openModal" class="btn btn-primary">Ver Consolidado</button>

        <!-- Modal de datos consolidados -->
        <div id="modal" class="modal" style="display: none;">
            <div class="modal-content">
                <span id="closeModal" class="close">&times;</span>
                <h2>Datos Consolidados de Accesibilidad y Calidad</h2>
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
