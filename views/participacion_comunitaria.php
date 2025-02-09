<?php
session_start(); // Inicia la sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.php");
    exit;
}
include '../config/config.php';

// Consulta para obtener los registros
$query = "
    SELECT DISTINCT 
        id,
        nivel_participacion,
        estrategias_mejora,
        grupos_comprometidos
    FROM participacion_comunitaria";

$result = $conn->query($query);

// Verificar si hay registros
$records = [];
if ($result && $result->num_rows > 0) {
    $records = $result->fetch_all(MYSQLI_ASSOC);
}

// Consolidar datos para la tabla
$total_registros = count($records);
$consolidado = [
    'nivel_participacion' => [],
    'grupos_comprometidos' => [],
    'estrategias_mejora' => [],
];

$total_registros = count($records);

foreach ($records as $record) {
    foreach ($consolidado as $categoria => &$opciones) {
        if (!empty($record[$categoria])) {
            $valores = explode(',', $record[$categoria]); // Separar valores por coma
            $valores = array_map('trim', $valores); // Eliminar espacios en blanco
            $valores = array_unique($valores); // Evitar duplicados dentro del mismo registro

            foreach ($valores as $valor) {
                if (!empty($valor)) {
                    if (!isset($opciones[$valor])) {
                        $opciones[$valor] = ['cantidad' => 0, 'porcentaje' => 0];
                    }
                    $opciones[$valor]['cantidad']++; // Contar correctamente los valores
                }
            }
        }
    }
}

// Calcular porcentajes correctamente
foreach ($consolidado as $categoria => &$opciones) {
    $total_categoria = array_sum(array_column($opciones, 'cantidad')); // Sumar la cantidad total por categoría

    foreach ($opciones as &$datos) {
        $datos['porcentaje'] = ($total_categoria > 0) ? round(($datos['cantidad'] / $total_categoria) * 100, 2) : 0;
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
        <h1 class="title">Participación Comunitaria</h1>
        <p class="description">Esta sección evalúa el nivel de participación de la comunidad, los grupos involucrados y las estrategias que podrían mejorar su implicación.</p>

        <!-- Mensaje de éxito -->
        <div id="successMessage" class="alert alert-success" style="display: none; margin-bottom: 20px;"></div>

        <!-- Sección de Formulario -->
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
                <label for="estrategias_mejora">Estrategias para Mejorar</label>
                <select id="estrategias_mejora" name="estrategias_mejora" required>
                    <option value="">Seleccione una opción</option>
                    <option value="Capacitaciones">Capacitaciones</option>
                    <option value="Reuniones periódicas">Reuniones periódicas</option>
                    <option value="Campañas de sensibilización">Campañas de sensibilización</option>
                    <option value="Aumentar recursos">Aumentar recursos</option>
                    <option value="Ninguna">Ninguna</option>
                </select>
            </div>

            <button class="btn btn-primary" type="submit">Agregar Participación</button>
        </form>

        <!-- Botón para abrir el modal -->
        <button id="openModal" class="btn btn-primary">Ver Registros</button>

        <!-- Modal -->
        <div id="modal" class="modal" style="display: none;">
            <div class="modal-content">
                <span id="closeModal" class="close">&times;</span>
                <h2>Registros de Participación</h2>
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
                                <?php if (is_array($opciones) && !empty($opciones)): ?>
                                    <?php foreach ($opciones as $opcion => $datos): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($opcion) ?></td>
                                            <td><?= $datos['cantidad'] ?></td>
                                            <td><?= $datos['porcentaje'] ?>%</td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="3">No hay datos disponibles para esta categoría.</td></tr>
                                <?php endif; ?>
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
