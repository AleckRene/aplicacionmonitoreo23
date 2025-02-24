<?php
session_start(); // Inicia la sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.php");
    exit;
}

include '../config/config.php';

// Consulta para obtener los registros
$query = "SELECT id, nivel_participacion, estrategias_mejora, grupos_comprometidos FROM participacion_comunitaria";
$result = $conn->query($query);

// Verificar si hay registros
$records = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
$total_registros = count($records);

// Consolidar datos para la tabla
$consolidado = [
    'nivel_participacion' => [],
    'grupos_comprometidos' => [],
    'estrategias_mejora' => [],
];

foreach ($records as $record) {
    foreach ($consolidado as $categoria => &$opciones) {
        if (!empty($record[$categoria])) {
            $valores = explode(',', $record[$categoria]); // Separar valores por coma
            $valores = array_map('trim', $valores); // Limpiar espacios en blanco
            $valores = array_unique($valores); // Evitar duplicados dentro del mismo registro

            foreach ($valores as $valor) {
                if (!empty($valor)) {
                    if (!isset($opciones[$valor])) {
                        $opciones[$valor] = 0;
                    }
                    $opciones[$valor]++;
                }
            }
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
    <title>Participación Comunitaria</title>
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
        <h1 class="title">Participación Comunitaria</h1>
        <p class="description">Evalúa la participación de la comunidad y sus estrategias de mejora.</p>

        <!-- Formulario de Registro -->
        <form class="form-container" action="../api/participacion_comunitaria.php" method="POST">
            <div class="form-group">
                <label for="nivel_participacion">Nivel de Participación</label>
                <select id="nivel_participacion" name="nivel_participacion" required>
                    <option value="">Seleccione una opción</option>
                    <option value="Nada activa">Nada activa</option>
                    <option value="Poco activa">Poco activa</option>
                    <option value="Moderadamente activa">Moderadamente activa</option>
                    <option value="Activa">Activa</option>
                    <option value="Muy activa">Muy activa</option>
                </select>
            </div>

            <div class="form-group">
                <label for="grupos_comprometidos">Grupos Comprometidos</label>
                <select id="grupos_comprometidos" name="grupos_comprometidos" required>
                    <option value="">Seleccione una opción</option>
                    <option value="Juntas comunitarias">Juntas comunitarias</option>
                    <option value="Líderes locales">Líderes locales</option>
                    <option value="Brigadistas">Brigadistas</option>
                    <option value="Población en general">Población en general</option>
                    <option value="Otros">Otros</option>
                </select>
            </div>

            <div class="form-group">
                <label for="estrategias_mejora">Estrategias de Mejora</label>
                <select id="estrategias_mejora" name="estrategias_mejora" required>
                    <option value="">Seleccione una opción</option>
                    <option value="Capacitaciones">Capacitaciones</option>
                    <option value="Reuniones periódicas">Reuniones periódicas</option>
                    <option value="Campañas de sensibilización">Campañas de sensibilización</option>
                    <option value="Aumentar recursos">Aumentar recursos</option>
                    <option value="Ninguna">Ninguna</option>
                </select>
            </div>

            <button class="btn btn-primary" type="submit">Registrar Participación</button>
        </form>

        <button id="openModal" class="btn btn-primary">Ver Registros</button>

        <!-- Modal de datos consolidados -->
        <div id="modal" class="modal" style="display: none;">
            <div class="modal-content">
                <span id="closeModal" class="close">&times;</span>
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
