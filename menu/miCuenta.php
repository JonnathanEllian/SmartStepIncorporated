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
    $name = $_POST['name'];
    $email = $_POST['email'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];

    $sql = "SELECT * FROM usuarios WHERE Usuario = ? AND Contraseña = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $_SESSION['user_name'], $current_password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $update_sql = "UPDATE usuarios SET Usuario = ?, Correo = ?, Contraseña = ? WHERE Usuario = ?";
        $stmt_update = $conn->prepare($update_sql);
        $stmt_update->bind_param('ssss', $name, $email, $new_password, $_SESSION['user_name']);

        if ($stmt_update->execute()) {
            $_SESSION['user_name'] = $name;
            $success_message = "Datos actualizados correctamente.";
        } else {
            $error_message = "Error al actualizar los datos.";
        }

        $stmt_update->close();
    } else {
        $error_message = "Contraseña actual incorrecta.";
    }

    $stmt->close();
}

$current_user_name = $_SESSION['user_name'];
$current_user_email = '';
$current_user_charge = ''; // Inicializamos la variable para el cargo

$sql_email_charge = "SELECT Correo, Cargo FROM usuarios WHERE Usuario = ?";
$stmt_email_charge = $conn->prepare($sql_email_charge);
$stmt_email_charge->bind_param('s', $current_user_name);
$stmt_email_charge->execute();
$result_email_charge = $stmt_email_charge->get_result();

if ($result_email_charge->num_rows > 0) {
    $row = $result_email_charge->fetch_assoc();
    $current_user_email = $row['Correo'];
    $current_user_charge = $row['Cargo']; // Asignamos el cargo
}

$stmt_email_charge->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Cuenta - SmartStep</title>
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
        .account-container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            display: flex;
            gap: 20px;
            position: relative;
        }
        .form-section {
            flex: 1;
        }
        .info-section {
            width: 200px;
            background-color: #f0f0f0;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            font-size: 14px;
            color: #555;
        }
        .info-section h2 {
            font-size: 16px;
            margin-bottom: 10px;
            color: #333;
        }
        .account-title {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-size: 24px;
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
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }
        .form-group input:focus {
            border-color: #e44d26;
            outline: none;
        }
        .update-button {
            width: 100%;
            padding: 14px;
            background-color: #e44d26;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 20px;
        }
        .update-button:hover {
            background-color: #c4371b;
            transform: translateY(-2px);
        }
        .logout-button {
            width: 100%;
            padding: 14px;
            background-color: #ff4d4d;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 10px;
        }
        .logout-button:hover {
            background-color: #d42e2e;
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

        .store-button {
            background-color: #4CAF50;
            color: white;
            margin-top: 8px;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .store-button:hover {
            background-color: #45a049;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="account-container">
        <div class="form-section">
            <h1 class="account-title">Mi Cuenta</h1>
            
            <?php if ($success_message): ?>
                <div class="alert success"><?php echo $success_message; ?></div>
            <?php elseif ($error_message): ?>
                <div class="alert error"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="name">Nombre</label>
                    <input type="text" id="name" name="name" value="<?php echo $_SESSION['user_name']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($current_user_email); ?>" required>
                </div>

                <div class="form-group">
                    <label for="current_password">Contraseña</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>

                <div class="form-group">
                    <label for="new_password">Nueva contraseña</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>

                <button type="submit" class="update-button">Actualizar datos</button>
            </form>

            <form method="POST" action="logout.php">
                <button type="submit" class="logout-button">Cerrar sesión</button>
            </form>

            <form method="GET" action="../index.php">
                <button type="submit" class="store-button">Volver a la tienda</button>
            </form>
        </div>

        <div class="info-section">
            <h2>Información Actual</h2>
            <p><strong>Nombre:</strong> <?php echo htmlspecialchars($current_user_name); ?></p>
            <p><strong>Correo:</strong> <?php echo htmlspecialchars($current_user_email); ?></p>
            <p><strong>Tipo de Cuenta:</strong> <?php echo htmlspecialchars($current_user_charge); ?></p>
        </div>
    </div>
</body>
</html>
