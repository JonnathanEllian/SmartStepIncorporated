<?php
session_start();

$cartCount = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['quantity'];
    }
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


if ($_SESSION['user_role'] === 'Operador') {
    header("Location: operador.php");
    exit();
}

if ($_SESSION['user_role'] === 'Admin') {
    header("Location: operador.php");
    exit();
}

$host = 'localhost';
$dbname = 'smartstep';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$success_message = '';
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_SESSION['user_name'];
    $asunto = $_POST['asunto'];
    $descripcion = $_POST['descripcion'];

    $sql = "INSERT INTO atencionAlCliente (Usuario, Asunto, Descripcion) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sss', $usuario, $asunto, $descripcion);

    if ($stmt->execute()) {
        $success_message = "Comentario enviado correctamente.";
    } else {
        $error_message = "Error al enviar el comentario. Intenta nuevamente.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atención al Cliente - SmartStep</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .form-container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
        }
        .form-container h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-size: 14px;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }
        .form-group input:focus, .form-group textarea:focus {
            border-color: #e44d26;
            outline: none;
        }
        .submit-button {
            width: 100%;
            padding: 14px;
            background-color: #e44d26;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .submit-button:hover {
            background-color: #c4371b;
            transform: translateY(-2px);
        }
        .alert {
            font-size: 14px;
            margin-top: 10px;
            text-align: center;
        }
        .alert.success {
            color: #28a745;
        }
        .alert.error {
            color: #e44d26;
        }
        .back-button {
            display: block;
            margin-top: 20px;
            text-align: center;
            width: 100%;
            padding: 14px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }
        .back-button:hover {
            background-color: #45a049;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Atención al Cliente</h1>
        
        <?php if ($success_message): ?>
            <div class="alert success"><?php echo $success_message; ?></div>
        <?php elseif ($error_message): ?>
            <div class="alert error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="asunto">Asunto</label>
                <input type="text" id="asunto" name="asunto" required>
            </div>

            <div class="form-group">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="4" required></textarea>
            </div>

            <button type="submit" class="submit-button">Enviar comentario</button>
        </form>

        <form method="GET" action="../index.php">
            <button type="submit" class="back-button">Volver al inicio</button>
        </form>
    </div>
</body>
</html>
