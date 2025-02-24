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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos de Salud</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1 class="title">Eventos de Salud</h1>
        <p class="description">Registra los eventos de salud y revisa los registros existentes.</p>

        <form class="form-container" action="../api/eventos_salud.php" method="POST">
            <label for="nombre_evento">Tipo de Evento:</label>
            <select id="nombre_evento" name="nombre_evento" required>
                <option value="">Seleccione una opción</option>
                <option value="Vacunación comunitaria">Vacunación comunitaria</option>
                <option value="Jornada de limpieza">Jornada de limpieza</option>
                <option value="Campaña de sensibilización">Campaña de sensibilización</option>
                <option value="Otros">Otros</option>
            </select>
            
            <label for="descripcion">Impacto del Evento:</label>
            <select id="descripcion" name="descripcion" required>
                <option value="">Seleccione una opción</option>
                <option value="Muy alto">Muy alto</option>
                <option value="Alto">Alto</option>
                <option value="Moderado">Moderado</option>
                <option value="Bajo">Bajo</option>
                <option value="Muy bajo">Muy bajo</option>
            </select>

            <label for="fecha">Fecha del Evento:</label>
            <input type="date" id="fecha" name="fecha" required>

            <label for="acciones">Acciones Tomadas:</label>
            <select id="acciones" name="acciones" required>
                <option value="">Seleccione una opción</option>
                <option value="Vacunación">Vacunación</option>
                <option value="Limpieza de áreas">Limpieza de áreas</option>
                <option value="Distribución de materiales">Distribución de materiales</option>
                <option value="Charlas y capacitaciones">Charlas y capacitaciones</option>
                <option value="Otras">Otras</option>
            </select>

            <button class="btn btn-primary" type="submit">Registrar Evento</button>
        </form>

        <h2>Registros de Eventos</h2>
        <?php if (!empty($records)): ?>
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Tipo de Evento</th>
                        <th>Impacto</th>
                        <th>Fecha</th>
                        <th>Acciones Tomadas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $record): ?>
                        <tr>
                            <td><?= htmlspecialchars($record['nombre_evento']) ?></td>
                            <td><?= htmlspecialchars($record['descripcion']) ?></td>
                            <td><?= htmlspecialchars($record['fecha']) ?></td>
                            <td><?= htmlspecialchars($record['acciones']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No hay eventos registrados aún.</p>
        <?php endif; ?>

        <div class="actions">
            <a href="../views/modulo_general.php" class="btn btn-secondary">Volver al Módulo General</a>
        </div>
    </div>
</body>
</html>
