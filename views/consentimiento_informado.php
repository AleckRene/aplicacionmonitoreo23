<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['consent'])) {
    $_SESSION['consent_accepted'] = true; // Marcar consentimiento como aceptado
    header("Location: dashboard.php"); // Redirigir al dashboard
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consentimiento Informado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }

        .consent-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            text-align: left;
        }

        .consent-container h1 {
            font-size: 24px;
            color: #333;
            text-align: center;
        }

        .consent-container p, .consent-container ul {
            font-size: 16px;
            color: #555;
            line-height: 1.5;
            margin-bottom: 15px;
        }

        .consent-container ul {
            list-style-type: disc;
            margin-left: 20px;
        }

        .consent-container label {
            font-size: 16px;
            color: #555;
        }

        .consent-container .btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            display: block;
            margin: 20px auto 0;
            text-align: center;
            text-decoration: none;
        }

        .consent-container .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="consent-container">
        <h1>Consentimiento Informado</h1>
        <p>Gracias por participar en este estudio. La información que proporcione será utilizada únicamente con fines de investigación y para informar a las autoridades responsables, permitiendo analizar los datos recopilados y brindar respuestas efectivas a las inquietudes detectadas.</p>
        <p>Al continuar, usted acepta lo siguiente:</p>
        <ul>
            <li>Comprende el propósito del estudio y cómo se utilizará su información.</li>
            <li>Da su consentimiento para participar de manera voluntaria.</li>
            <li>Entiende que puede retirarse del estudio en cualquier momento sin consecuencias negativas.</li>
        </ul>
        <form action="consentimiento_informado.php" method="POST">
            <label>
                <input type="checkbox" name="consent" required>
                He leído y acepto los términos del consentimiento informado.
            </label>
            <br><br>
            <button type="submit" class="btn">Aceptar y Continuar</button>
        </form>
    </div>
</body>
</html>
