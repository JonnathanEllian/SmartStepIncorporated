<?php
session_start();

$cartCount = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['quantity'];
    }
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    header("refresh:3; url=../index.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "SmartStep";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $genero = $_POST['genero'];
    $marca = $_POST['marca'];
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $descripcion = $_POST['descripcion'];
    $talla_base = $_POST['talla_base'];
    $talla_extra = $_POST['talla_extra'];
    $stock = $_POST['stock'];
    $disponible = $_POST['disponible'];

    $tallas = $talla_base . '-' . $talla_extra;

    $sql_check = "SELECT * FROM catalogo WHERE Nombre = '$nombre'";
    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows > 0) {
        $mensaje = 'El producto con este nombre ya existe. Por favor elige otro nombre.';
        $tipo_mensaje = 'warning';
    } else {
        if (isset($_FILES['imagen'])) {
            $imagen = $_FILES['imagen'];
            $imagen_nombre = $nombre . '.' . pathinfo($imagen['name'], PATHINFO_EXTENSION);
            $ruta_imagen = 'multimedia/catalogo/' . $imagen_nombre;

            if (move_uploaded_file($imagen['tmp_name'], $ruta_imagen)) {
                $sql = "INSERT INTO catalogo (Genero, Marca, Nombre, Precio, Descripcion, Tallas, Stock, Disponible)
                        VALUES ('$genero', '$marca', '$nombre', '$precio', '$descripcion', '$tallas', '$stock', '$disponible')";
                if ($conn->query($sql) === TRUE) {
                    $mensaje = 'Nuevo producto agregado con éxito.';
                    $tipo_mensaje = 'success';
                } else {
                    $mensaje = 'Error: ' . $conn->error;
                    $tipo_mensaje = 'error';
                }
            } else {
                $mensaje = 'Error al subir la imagen.';
                $tipo_mensaje = 'error';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }

        .header {
            background-color: rgba(0, 0, 0, 0.9);
            padding: 1rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            color: white;
            font-size: 24px;
            font-weight: bold;
            text-decoration: none;
        }

        .form-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            font-weight: bold;
        }

        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .form-group input[type="file"] {
            border: none;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .submit-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .submit-btn:hover {
            background-color: #45a049;
        }

        .cancel-btn {
            background-color: #f44336;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .cancel-btn:hover {
            background-color: #e53935;
        }

        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 16px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
    </style>
</head>
<body>

<div class="header">
    <a href="../index.php" class="logo">SmartStep</a>
</div>

<div class="form-container">
    <h1>Agregar Nuevo Producto</h1>

    <?php if ($mensaje): ?>
        <div class="message <?php echo $tipo_mensaje; ?>">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="genero">Género</label>
            <select id="genero" name="genero" required>
                <option value="H">Hombre</option>
                <option value="M">Mujer</option>
                <option value="I">Infantil</option>
            </select>
        </div>

        <div class="form-group">
            <label for="marca">Marca</label>
            <input type="text" id="marca" name="marca" required>
        </div>

        <div class="form-group">
            <label for="nombre">Nombre del Producto</label>
            <input type="text" id="nombre" name="nombre" required>
        </div>

        <div class="form-group">
            <label for="precio">Precio</label>
            <input type="number" id="precio" name="precio" step="0.01" required>
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción</label>
            <textarea id="descripcion" name="descripcion" rows="4" required></textarea>
        </div>

        <div class="form-group">
            <label for="talla_base">Talla Base</label>
            <select id="talla_base" name="talla_base" required>
                <option value="24">24</option>
                <option value="25">25</option>
                <option value="26">26</option>
                <option value="27">27</option>
                <option value="28">28</option>
                <option value="29">29</option>
                <option value="30">30</option>
                <option value="31">31</option>
            </select>
        </div>

        <div class="form-group">
            <label for="talla_extra">Talla Extra</label>
            <select id="talla_extra" name="talla_extra" required>
                <option value="32">32</option>
                <option value="33">33</option>
                <option value="34">34</option>
                <option value="35">35</option>
            </select>
        </div>

        <div class="form-group">
            <label for="stock">Stock</label>
            <input type="number" id="stock" name="stock" value="0" min="0" required>
        </div>

        <div class="form-group">
            <label for="disponible">Disponible</label>
            <select id="disponible" name="disponible" required>
                <option value="Sí">Sí</option>
                <option value="No">No</option>
            </select>
        </div>

        <div class="form-group">
            <label for="imagen">Imagen del Producto</label>
            <input type="file" id="imagen" name="imagen" accept="image/*" required>
        </div>

        <div class="action-buttons">
            <button type="submit" class="submit-btn">Agregar Producto</button>
            <a href="admin.php" class="cancel-btn">Volver</a>
        </div>
    </form>
</div>

<script>
    document.getElementById('genero').addEventListener('change', function () {
        var genero = this.value;
        var tallaBase = document.getElementById('talla_base');
        var tallaExtra = document.getElementById('talla_extra');
        
        tallaBase.innerHTML = '';
        tallaExtra.innerHTML = '';

        var tallas = {
            'H': {
                base: ['24', '25', '26', '27', '28', '29', '30', '31'],
                extra: ['32', '33', '34', '35', '36']
            },
            'M': {
                base: ['23', '24', '25', '26', '27', '28', '29', '30'],
                extra: ['31', '32', '33', '34']
            },
            'I': {
                base: ['17', '18', '19', '20', '21', '22'],
                extra: ['23', '24', '25']
            }
        };

        tallas[genero].base.forEach(function (talla) {
            var option = document.createElement('option');
            option.value = talla;
            option.textContent = talla;
            tallaBase.appendChild(option);
        });

        tallas[genero].extra.forEach(function (talla) {
            var option = document.createElement('option');
            option.value = talla;
            option.textContent = talla;
            tallaExtra.appendChild(option);
        });
    });

    document.getElementById('genero').dispatchEvent(new Event('change'));
</script>

</body>
</html>

<?php
$conn->close();
?>
