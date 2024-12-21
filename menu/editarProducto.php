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

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "SmartStep";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['Nombre'])) {
    $nombre_producto = $_GET['Nombre'];
    $sql = "SELECT * FROM catalogo WHERE Nombre = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $nombre_producto);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $producto = $resultado->fetch_assoc();
    } else {
        echo "Producto no encontrado.";
        exit();
    }
} else {
    echo "Producto no encontrado.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['actualizar_producto'])) {
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $precio = $_POST['precio'];
        $tallas = $_POST['tallas'];
        $stock = $_POST['stock'];
        $disponible = $_POST['disponible'];
        $genero = $_POST['genero'];

        $imagen = $_FILES['imagen']['name'];

        if ($imagen) {
            $image_base_name = $nombre_producto; 
            $possible_extensions = ['png', 'jfif', 'jpg', 'jpeg'];
            $image_path = '';
            foreach ($possible_extensions as $extension) {
                if (file_exists("multimedia/catalogo/{$image_base_name}.{$extension}")) {
                    $image_path = "multimedia/catalogo/{$image_base_name}.{$extension}";
                    break;
                }
            }
            if ($image_path) {
                unlink($image_path);
            }
        
            $imagen_temp = $_FILES['imagen']['tmp_name'];
            $imagen_size = $_FILES['imagen']['size'];
            $imagen_type = $_FILES['imagen']['type'];
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        
            if (in_array($imagen_type, $allowed_types) && $imagen_size <= 5000000) {
                $imagen_ext = pathinfo($imagen, PATHINFO_EXTENSION);
                $new_image_name = $nombre . '.' . $imagen_ext;
                $target_path = "multimedia/catalogo/" . $new_image_name;
        
                if (move_uploaded_file($imagen_temp, $target_path)) {
                    $imagen = $new_image_name;
                } else {
                    echo "Error al subir la imagen.";
                    exit();
                }
            } else {
                echo "Archivo no permitido o tamaño excesivo. Solo se permiten imágenes JPEG, PNG y JFIF de menos de 5MB.";
                exit();
            }
        } else {
            $ext_original = pathinfo($producto['Nombre'], PATHINFO_EXTENSION);
            $imagen = $producto['Nombre'] . '.' . $ext_original;
        }

        $sql = "UPDATE catalogo SET Nombre = ?, Descripcion = ?, Precio = ?, Tallas = ?, Stock = ?, Disponible = ?, Genero = ? WHERE Nombre = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssss", $nombre, $descripcion, $precio, $tallas, $stock, $disponible, $genero, $nombre_producto);

        if ($stmt->execute()) {
            echo "<script>alert('Producto actualizado correctamente.');</script>";
        } else {
            echo "<script>alert('Error al actualizar el producto.');</script>";
        }
    }

    if (isset($_POST['borrar_producto'])) {
        $image_base_name = $producto['Nombre'];
        $possible_extensions = ['png', 'jfif', 'jpg', 'jpeg'];
        $image_path = '';
        foreach ($possible_extensions as $extension) {
            if (file_exists("multimedia/catalogo/{$image_base_name}.{$extension}")) {
                $image_path = "multimedia/catalogo/{$image_base_name}.{$extension}";
                break;
            }
        }
        if ($image_path) {
            unlink($image_path);
        }

        $sql = "DELETE FROM catalogo WHERE Nombre = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $nombre_producto);
        if ($stmt->execute()) {
            header("Location: admin.php");
            exit();
        } else {
            echo '<div style="color: red; text-align: center; font-size: 18px; margin-top: 20px;">Error al eliminar el producto.</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #e44d26;
            outline: none;
        }

        .product-image {
            width: 100%;
            max-width: 200px;
            margin-top: 10px;
            border-radius: 5px;
        }

        .submit-btn,
        .cancel-btn {
            width: 100%;
            padding: 14px;
            background-color: #e44d26;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-decoration: none;
        }

        .cancel-btn2 {
            width: 100%;
            padding: 14px;
            background-color:rgb(245, 35, 35);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;    
            text-decoration: none;
        }

        .submit-btn:hover,
        .cancel-btn:hover {
            background-color: #c4371b;
            transform: translateY(-2px);
        }

        .cancel-btn {
            background-color: #ff4d4d;
        }

        .form-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .error-message {
            color: red;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <br><br><br><br><br>
        <h1>Editor de Productos</h1>
        <div class="form-container">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nombre">Nombre del Producto</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo $producto['Nombre']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" required><?php echo $producto['Descripcion']; ?></textarea>
                </div>

                <div class="form-group">
                    <label for="precio">Precio</label>
                    <input type="number" id="precio" name="precio" value="<?php echo $producto['Precio']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="tallas">Tallas("BASE-EXTRA")</label>
                    <input type="text" id="tallas" name="tallas" value="<?php echo $producto['Tallas']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="stock">Stock</label>
                    <input type="number" id="stock" name="stock" value="<?php echo $producto['Stock']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="disponible">Disponible</label>
                    <select id="disponible" name="disponible" required>
                        <option value="Sí" <?php echo ($producto['Disponible'] === 'Sí') ? 'selected' : ''; ?>>Sí</option>
                        <option value="No" <?php echo ($producto['Disponible'] === 'No') ? 'selected' : ''; ?>>No</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="genero">Género</label>
                    <select id="genero" name="genero" required>
                        <option value="H" <?php echo ($producto['Genero'] === 'H') ? 'selected' : ''; ?>>Hombre</option>
                        <option value="M" <?php echo ($producto['Genero'] === 'M') ? 'selected' : ''; ?>>Mujer</option>
                        <option value="I" <?php echo ($producto['Genero'] === 'I') ? 'selected' : ''; ?>>Infantil</option>
                    </select>
                </div>

                <div class="form-group">
                 <label for="imagen">Imagen</label>
                 <input type="file" id="imagen" name="imagen" required>
                </div>


                <button type="submit" name="actualizar_producto" class="submit-btn">Actualizar Producto</button>
            </form>

            <form action="" method="POST">
                <button type="submit" name="borrar_producto" class="cancel-btn2">Eliminar Producto</button>
            </form>

            <div class="action-buttons">
                <a href="admin.php" class="cancel-btn">Volver</a>
            </div>
        </div>
    </div>
</body>
</html>
