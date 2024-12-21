<?php
session_start();

$cartCount = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['quantity'];
    }
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
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

if (isset($_GET['eliminar'])) {
    $usuarioEliminar = $_GET['eliminar'];
    $queryEliminar = "DELETE FROM usuarios WHERE Usuario = ?";
    $stmt = $conn->prepare($queryEliminar);
    $stmt->bind_param("s", $usuarioEliminar);
    $stmt->execute();
    $stmt->close();
    header("Location: admin.php");
    exit();
}

if (isset($_POST['cambiar_cargo'])) {
    $usuarioActualizar = $_POST['usuario'];
    $nuevoCargo = $_POST['nuevo_cargo'];
    $queryActualizar = "UPDATE usuarios SET Cargo = ? WHERE Usuario = ?";
    $stmt = $conn->prepare($queryActualizar);
    $stmt->bind_param("ss", $nuevoCargo, $usuarioActualizar);
    $stmt->execute();
    $stmt->close();
    header("Location: admin.php");
    exit();
}

function obtenerImagenProducto($nombreProducto) {
    $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'jfif'];
    foreach ($extensionesPermitidas as $ext) {
        $rutaImagen = "multimedia/catalogo/" . $nombreProducto . "." . $ext;
        if (file_exists($rutaImagen)) {
            return $rutaImagen;
        }
    }
    return "multimedia/default.png"; 
}

$queryProductos = "SELECT * FROM catalogo";
$resultProductos = $conn->query($queryProductos);

$queryUsuarios = "SELECT * FROM usuarios WHERE Usuario != ? ORDER BY Usuario ASC";
$stmt = $conn->prepare($queryUsuarios);
$stmt->bind_param("s", $_SESSION['user_id']);
$stmt->execute();
$resultUsuarios = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - SmartStep</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            text-align: center;
            color: #333;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        .card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        table th {
            background-color: #f4f4f4;
        }
        .action-btn {
            padding: 5px 10px;
            border-radius: 5px;
            color: #fff;
            font-size: 14px;
            margin-right: 5px;
            text-decoration: none;
        }
        .btn-password { background-color: #ffc107; }
        .btn-delete { background-color: #e44d26; }
        select {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 150px;
            background-color: #f9f9f9;
            font-size: 14px;
        }
        .plus-icon {
            font-size: 24px;
            margin-left: 5px;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0); 
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
            border-radius: 10px;
        }

        .plus-icon {
            color: green;
            font-size: 94px;
            margin-left: 5px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Panel - SmartStep</h1>

        <h2>Productos</h2>
        <div class="grid">
            <?php while ($producto = $resultProductos->fetch_assoc()): ?>
                <div class="card" onclick="window.location.href='editarProducto.php?Nombre=<?php echo urlencode($producto['Nombre']); ?>'">
                    <h3><?php echo htmlspecialchars($producto['Nombre']); ?></h3>
                    <p><?php echo htmlspecialchars($producto['Descripcion']); ?></p>
                    <p><strong>Precio:</strong> $<?php echo htmlspecialchars($producto['Precio']); ?></p>

                    <?php
                        $nombreProducto = $producto['Nombre'];
                        $rutaImagen = obtenerImagenProducto($nombreProducto);
                        echo "<img src='$rutaImagen' alt='" . htmlspecialchars($producto['Nombre']) . "' style='max-width: 100%; height: auto; border-radius: 5px; margin-top: 10px;'>";
                    ?>
                </div>
            <?php endwhile; ?>
            <div class="card" onclick="window.location.href='agregarProducto.php'">
                <br><br><br>
                <p><b>Agregar Producto <br><br><br><span class="plus-icon">+</span></p>
            </div>
        </div>

        <h2>Usuarios</h2>
        <table>
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Correo</th>
                    <th>Cargo</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($usuario = $resultUsuarios->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($usuario['Usuario']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['Correo']); ?></td>
                        <td>
                            <form method="POST" action="admin.php" style="display: inline;">
                                <input type="hidden" name="usuario" value="<?php echo htmlspecialchars($usuario['Usuario']); ?>">
                                <select name="nuevo_cargo" onchange="this.form.submit()">
                                    <option value="Admin" <?php echo $usuario['Cargo'] === 'Admin' ? 'selected' : ''; ?>>Admin</option>
                                    <option value="Operador" <?php echo $usuario['Cargo'] === 'Operador' ? 'selected' : ''; ?>>Operador</option>
                                    <option value="Usuario" <?php echo $usuario['Cargo'] === 'Usuario' ? 'selected' : ''; ?>>Usuario</option>
                                </select>
                                <input type="hidden" name="cambiar_cargo" value="1">
                            </form>
                        </td>
                        <td>
                            <a href="javascript:void(0);" class="action-btn btn-password" onclick="mostrarContraseña('<?php echo htmlspecialchars($usuario['Contraseña']); ?>')">Ver Contraseña</a>
                            <a href="admin.php?eliminar=<?php echo urlencode($usuario['Usuario']); ?>" class="action-btn btn-delete">Eliminar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div id="modalContraseña" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModal()">&times;</span>
            <p><strong>Contraseña:</strong> <span id="contraseñaTexto"></span></p>
        </div>
    </div>

    <script>
        function mostrarContraseña(contraseña) {
            document.getElementById("contraseñaTexto").innerText = contraseña;
            document.getElementById("modalContraseña").style.display = "block";
        }

        function cerrarModal() {
            document.getElementById("modalContraseña").style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById("modalContraseña")) {
                cerrarModal();
            }
        }
    </script>
    
    <div style="text-align: center; margin-top: 20px;">
        <a href="../index.php" class="action-btn" style="background-color: #4CAF50;">Volver al Inicio</a>
    </div>
    <br><br><br><br><br>
</body>
</html>

<?php
$resultProductos->free();
$resultUsuarios->free();
$conn->close();
?>
