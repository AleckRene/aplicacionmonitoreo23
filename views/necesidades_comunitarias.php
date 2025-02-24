<?php
session_start(); // Inicia la sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.php");
    exit;
}

include '../config/config.php';

// Consulta para obtener los registros
$query = "SELECT id, descripcion, acciones, area_prioritaria FROM necesidades_comunitarias";
$result = $conn->query($query);

// Verificar si hay registros
$records = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
$total_registros = count($records);

// Consolidar datos para la tabla
$consolidado = [
    'descripcion' => [],
    'acciones' => [],
    'area_prioritaria' => [],
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
    <title>Necesidades Comunitarias</title>
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
        <h1 class="title">Necesidades Comunitarias</h1>
        <p class="description">Identifique las principales necesidades comunitarias y las áreas prioritarias para atención inmediata.</p>

        <!-- Formulario de Registro -->
        <form class="form-container" action="../api/necesidades_comunitarias.php" method="POST">
            <div class="form-group">
                <label for="descripcion">Tipo de Necesidad</label>
                <select id="descripcion" name="descripcion" required>
                    <option value="">Seleccione una opción</option>
                    <option value="Falta de recursos médicos">Falta de recursos médicos</option>
                    <option value="Infraestructura dañada">Infraestructura dañada</option>
                    <option value="Carencia de agua potable">Carencia de agua potable</option>
                    <option value="Acceso limitado a educación">Acceso limitado a educación</option>
                    <option value="Otros">Otros</option>
                </select>
            </div>

            <div class="form-group">
                <label for="acciones">Acciones Tomadas</label>
                <select id="acciones" name="acciones" required>
                    <option value="">Seleccione una opción</option>
                    <option value="Reunión comunitaria">Reunión comunitaria</option>
                    <option value="Campaña de recaudación">Campaña de recaudación</option>
                    <option value="Limpieza o reparaciones">Limpieza o reparaciones</option>
                    <option value="Gestión de recursos externos">Gestión de recursos externos</option>
                    <option value="Ninguna">Ninguna</option>
                </select>
            </div>

            <div class="form-group">
                <label for="area_prioritaria">Área Prioritaria</label>
                <select id="area_prioritaria" name="area_prioritaria" required>
                    <option value="">Seleccione una opción</option>
                    <option value="Salud">Salud</option>
                    <option value="Educación">Educación</option>
                    <option value="Infraestructura">Infraestructura</option>
                    <option value="Medio Ambiente">Medio Ambiente</option>
                    <option value="Otro">Otro</option>
                </select>
            </div>

            <button class="btn btn-primary" type="submit">Agregar Necesidad</button>
        </form>

        <button id="openModal" class="btn btn-primary">Ver Consolidado de Necesidades</button>

        <!-- Modal de datos consolidados -->
        <div id="modal" class="modal" style="display: none;">
            <div class="modal-content">
                <span id="closeModal" class="close">&times;</span>
                <h2>Datos Consolidados de Necesidades</h2>
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
