<?php
session_start();

$cartCount = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['quantity'];
    }
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "smartstep";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['name'];
    $correo = $_POST['email'];
    $contraseña = $_POST['password'];
    $sql = "SELECT * FROM usuarios WHERE Correo = '$correo'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<script>alert('Este correo ya está registrado.');</script>";
    } else {
        $sql_insert = "INSERT INTO usuarios (Usuario, Correo, Contraseña, Cargo) 
                       VALUES ('$usuario', '$correo', '$contraseña', 'Usuario')";

        if ($conn->query($sql_insert) === TRUE) {
            echo "<script>
                    alert('Registro exitoso');
                    window.location.href = 'login.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Ha ocurrido un error. Por favor, inténtelo de nuevo.');
                  </script>";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - SmartStep</title>
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

        .register-container {
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

        .register-title {
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

        .register-button {
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

        .register-button:hover {
            background-color: #c4371b;
            transform: translateY(-2px);
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }

        .login-link a {
            color: #e44d26;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: #e44d26;
            font-size: 14px;
            margin-top: 5px;
            display: none;
        }

        .form-group.error input {
            border-color: #e44d26;
        }

        .form-group.error .error-message {
            display: block;
        }

        @media (max-width: 480px) {
            .register-container {
                margin: 20px;
                padding: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <a href="../index.php" class="back-button">
            <i class="fas fa-arrow-left"></i> Volver a la tienda
        </a>
        
        <h1 class="register-title">Crear cuenta</h1>
        
        <form action="registro.php" method="POST">
            <div class="form-group">
                <label for="name">Nombre completo</label>
                <input type="text" id="name" name="name" required>
                <div class="error-message">Por favor ingresa tu nombre completo</div>
            </div>

            <div class="form-group">
                <label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email" required>
                <div class="error-message">Por favor ingresa un correo electrónico válido</div>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
                <div class="error-message">La contraseña debe tener al menos 6 caracteres</div>
            </div>

            <div class="form-group">
                <label for="confirm-password">Confirmar contraseña</label>
                <input type="password" id="confirm-password" name="confirm-password" required>
                <div class="error-message">Las contraseñas no coinciden</div>
            </div>

            <button type="submit" class="register-button">Registrarse</button>
        </form>

        <div class="login-link">
            ¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a>
        </div>
    </div>

    <script>
        function validateForm(event) {
            event.preventDefault();
            
            const name = document.getElementById('name');
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm-password');
            let isValid = true;

            if (name.value.trim().length < 3) {
                name.parentElement.classList.add('error');
                isValid = false;
            } else {
                name.parentElement.classList.remove('error');
            }

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email.value)) {
                email.parentElement.classList.add('error');
                isValid = false;
            } else {
                email.parentElement.classList.remove('error');
            }

            if (password.value.length < 6) {
                password.parentElement.classList.add('error');
                isValid = false;
            } else {
                password.parentElement.classList.remove('error');
            }

            if (password.value !== confirmPassword.value) {
                confirmPassword.parentElement.classList.add('error');
                isValid = false;
            } else {
                confirmPassword.parentElement.classList.remove('error');
            }

            if (isValid) {
                alert('Registro exitoso');
                window.location.href = 'login.php';
            }

            return false;
        }
    </script>
</body>
</html>
