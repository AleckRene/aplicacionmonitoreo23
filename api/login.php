<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/config.php';

$response = [
    'success' => false,
    'message' => 'No se pudo procesar la solicitud.',
];

try {
    // Verificar si la solicitud es POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido. Se requiere una solicitud POST.');
    }

    // Obtener el JSON enviado
    $inputJSON = file_get_contents("php://input");
    $input = json_decode($inputJSON, true);

    if (!$input || !isset($input['name']) || !isset($input['localidad']) || !isset($input['password'])) {
        throw new Exception('Datos incompletos. Se requieren "name", "localidad" y "password".');
    }

    $name = $conn->real_escape_string($input['name']);
    $localidad = $conn->real_escape_string($input['localidad']);
    $password = $conn->real_escape_string($input['password']);

    // Consulta para verificar el usuario en la base de datos
    $query = "SELECT id, name, localidad, password FROM usuarios WHERE name = '$name' AND localidad = '$localidad' LIMIT 1";
    $result = $conn->query($query);

    if (!$result || $result->num_rows === 0) {
        throw new Exception('Usuario no encontrado o credenciales incorrectas.');
    }

    $user = $result->fetch_assoc();

    // Verificar la contraseña (ajustar si está encriptada)
    if (!password_verify($password, $user['password'])) {
        throw new Exception('Contraseña incorrecta.');
    }

    // Respuesta de éxito
    $response = [
        'success' => true,
        'user_id' => $user['id'],
        'name' => $user['name'],
        'localidad' => $user['localidad'],
        'message' => 'Inicio de sesión exitoso',
    ];
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
} finally {
    // Cerrar conexión si es necesario
    if (isset($conn)) {
        $conn->close();
    }
    // Devolver respuesta JSON
    echo json_encode($response);
    exit;
}
