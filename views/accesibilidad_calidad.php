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
            $consolidado[$key][$valor] = ($consolidado[$key][$valor] ?? 0) + 1;
        }
    }
}

// Calcular porcentajes correctamente
foreach ($consolidado as $categoria => &$opciones) {
    $total_categoria = array_sum($opciones);
    foreach ($opciones as &$datos) {
        $datos = [
            'cantidad' => $datos,
            'porcentaje' => $total_categoria > 0 ? round(($datos / $total_categoria) * 100, 2) : 0
        ];
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
            const openModalBtn = document.getElementById("openModal");
            const closeModalBtn = document.getElementById("closeModal");
            const successMessage = document.getElementById("successMessage");

            openModalBtn.addEventListener("click", function () {
                modal.style.display = "block";
            });

            closeModalBtn.addEventListener("click", function () {
                modal.style.display = "none";
            });

            window.addEventListener("click", function (event) {
                if (event.target === modal) {
                    modal.style.display = "none";
                }
            });

            document.querySelector("form").addEventListener("submit", function (event) {
                event.preventDefault();
                const formData = new FormData(this);

                fetch(this.action, {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        successMessage.textContent = data.success;
                        successMessage.style.display = "block";
                        setTimeout(() => successMessage.style.display = "none", 3000);
                        this.reset();
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

            <div class="form-group">
                <label for="disponibilidad">Disponibilidad de Medicamentos:</label>
                <select id="disponibilidad" name="disponibilidad_herramientas" required>
                    <option value="">Seleccione una opción</option>
                    <option value="Muy deficiente">Muy deficiente</option>
                    <option value="Deficiente">Deficiente</option>
                    <option value="Regular">Regular</option>
                    <option value="Buena">Buena</option>
                    <option value="Excelente">Excelente</option>
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
                <p>Registros cargados correctamente.</p>
            </div>
        </div>

        <!-- Botón para volver -->
        <div class="actions">
            <a href="../views/modulo_vih.php" class="btn btn-secondary">Volver al Módulo VIH</a>
        </div>
    </div>
</body>
</html>
