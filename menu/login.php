<?php
session_start();

$cartCount = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['quantity'];
    }
}

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] == 'Admin') {
        header("Location: admin.php");
        exit();
    } elseif ($_SESSION['user_role'] == 'Operador') {
        header("Location: operador.php");
        exit();
    } else {
        header("Location: miCuenta.php");
        exit();
    }
}

$host = 'localhost';
$dbname = 'smartstep';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['usuario']) && isset($_POST['password'])) {
        $usuario = trim($_POST['usuario']);
        $password = trim($_POST['password']);

        $sql = "SELECT * FROM usuarios WHERE Usuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if ($user['Contraseña'] === $password) {
                $_SESSION['user_id'] = $user['Usuario'];
                $_SESSION['user_name'] = $user['Usuario'];
                $_SESSION['user_role'] = $user['Cargo'];

                if ($user['Cargo'] == 'Admin') {
                    header("Location: admin.php");
                    exit();
                } elseif ($user['Cargo'] == 'Operador') {
                    header("Location: operador.php");
                    exit();
                } else {
                    header("Location: miCuenta.php");
                    exit();
                }
            } else {
                $error_message = "Nombre de usuario o contraseña incorrectos.";
            }
        } else {
            $error_message = "Nombre de usuario o contraseña incorrectos.";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - SmartStep</title>
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

        .login-container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            position: relative;
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            text-decoration: none;
            color: #333;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .back-button:hover {
            color: #e44d26;
        }

        .login-title {
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

        .login-button {
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

        .login-button:hover {
            background-color: #c4371b;
            transform: translateY(-2px);
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }

        .register-link a {
            color: #e44d26;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .error-alert {
            color: #e44d26;
            font-size: 14px;
            margin-top: 10px;
            text-align: center;
        }

        @media (max-width: 480px) {
            .login-container {
                margin: 20px;
                padding: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <a href="../index.php" class="back-button">
            <i class="fas fa-arrow-left"></i> Volver a la tienda
        </a>
        
        <h1 class="login-title">Iniciar Sesión</h1>
        
        <form id="loginForm" method="POST" action="">
            <div class="form-group">
                <label for="usuario">Nombre de usuario</label>
                <input type="text" id="usuario" name="usuario" required>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>

            <?php if ($error_message): ?>
                <div class="error-alert"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <button type="submit" class="login-button">Iniciar sesión</button>
        </form>

        <div class="register-link">
            ¿No tienes una cuenta? <a href="registro.php">Regístrate aquí</a>
        </div>
    </div>
</body>
</html>
