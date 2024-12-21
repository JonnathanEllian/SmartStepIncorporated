<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] == 'Usuario') {
    header("Location: ../menu/login.php");
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

$tickets = [];
$sql = "SELECT atencionAlCliente.ID, atencionAlCliente.Asunto, atencionAlCliente.Descripcion, atencionAlCliente.Usuario, usuarios.Correo
        FROM atencionAlCliente
        JOIN usuarios ON atencionAlCliente.Usuario = usuarios.Usuario";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tickets[] = $row;
    }
} else {
    
}


if (isset($_POST['eliminar'])) {
    $ticket_id = $_POST['ticket_id_eliminar'];

    $sql = "DELETE FROM atencionAlCliente WHERE ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $ticket_id);

    if ($stmt->execute()) {
        $success_message = "Ticket eliminado correctamente.";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $error_message = "Error al eliminar el ticket. Intenta nuevamente.";
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
    <title>Operador - Tickets de Atención</title>
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
        .container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 900px;
            overflow-y: auto;
            max-height: 80vh;
        }
        .container2 {
            padding: 40px;
            border-radius: 10px;
            text-align: center;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .ticket {
            background-color: #f9f9f9;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .ticket h3 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .ticket p {
            font-size: 14px;
            color: #555;
        }
        .buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }
        .buttons button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: #e44d26;
            color: white;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .buttons button:hover {
            background-color: #c4371b;
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
            margin-top: 30px;
            text-align: center;
            padding: 12px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 10px;
            text-transform: uppercase;
            cursor: pointer;
            letter-spacing: 1px;
            text-decoration: none;
        }
        .back-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Atención al Cliente</h1>
        <div class="container2"><a href="../index.php" class="back-button">Volver al Inicio</a></div>
        <?php if ($success_message): ?>
            <div class="alert success"><?php echo $success_message; ?></div>
        <?php elseif ($error_message): ?>
            <div class="alert error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php foreach ($tickets as $ticket): ?>
            <div class="ticket">
                <h3>Asunto: <?php echo htmlspecialchars($ticket['Asunto']); ?></h3>
                <p><strong>Usuario:</strong> <?php echo htmlspecialchars($ticket['Usuario']); ?></p>
                <p><strong>Correo:</strong> <?php echo htmlspecialchars($ticket['Correo']); ?></p>
                <p><strong>Descripción:</strong> <?php echo htmlspecialchars($ticket['Descripcion']); ?></p>
                <div class="buttons">
                    <a href="mailto:<?php echo htmlspecialchars($ticket['Correo']); ?>?subject=Respuesta%20a%20tu%20ticket%20de%20atención" style="text-decoration: none;">
                        <button type="button">Responder</button>
                    </a>
                    <form method="POST" action="" style="margin: 0;">
                        <input type="hidden" name="ticket_id_eliminar" value="<?php echo htmlspecialchars($ticket['ID']); ?>">
                        <button type="submit" name="eliminar">Eliminar</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
