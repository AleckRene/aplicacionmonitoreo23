<?php
session_start(); // Inicia la sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.php");
    exit;
}

include '../config/config.php';

// Consulta para obtener los registros
$query = "SELECT * FROM necesidades_comunitarias";
$result = $conn->query($query);

// Verificar si hay registros
$records = [];
if ($result && $result->num_rows > 0) {
    $records = $result->fetch_all(MYSQLI_ASSOC);
}

// Consolidar datos para la tabla
$total_registros = count($records);
$consolidado = [
    'descripcion' => [],
    'acciones' => [],
    'area_prioritaria' => [],
];

foreach ($records as $record) {
    foreach (['descripcion', 'acciones', 'area_prioritaria'] as $key) {
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
    <title>Necesidades Comunitarias</title>
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
        <h1 class="title">Necesidades Comunitarias</h1>
        <p class="description">Identifique las principales necesidades comunitarias y las áreas prioritarias para atención inmediata.</p>

        <!-- Sección de Formulario -->
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

        <!-- Mensaje de éxito -->
        <div id="successMessage" class="alert alert-success" style="display: none; margin-bottom: 20px;"></div>

        <!-- Botón para abrir el modal -->
        <button id="openModal" class="btn btn-primary">Ver Registros</button>

        <!-- Modal -->
        <div id="modal" class="modal" style="display: none;">
            <div class="modal-content">
                <span id="closeModal" class="close">&times;</span>
                <h2>Registros de Necesidades</h2>
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
            <a href="../views/modulo_general.php" class="btn btn-secondary">Volver al Módulo General</a>
        </div>
    </div>
</body>
</html>
