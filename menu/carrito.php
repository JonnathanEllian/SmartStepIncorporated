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

if (isset($_SESSION['cart'])==null) {
    header("Location: ../index.php");
    exit();
}

if (isset($_SESSION['added_to_cart']) && $_SESSION['added_to_cart'] === true) {
    header("Location: ../index.php");
    exit();
    $_SESSION['added_to_cart'] = false;
}

function obtenerRutaImagen($nombreProducto) {
    $directorio = 'multimedia/catalogo/';
    $extensionesPermitidas = ['jpg', 'jfif', 'png', 'jpeg'];
    foreach ($extensionesPermitidas as $extension) {
        $rutaImagen = $directorio . $nombreProducto . '.' . $extension;
        if (file_exists($rutaImagen)) {
            return $rutaImagen;
        }
    }
    return $directorio . 'default.png';
}

function obtenerStockYPrecio($nombreProducto) {
    $conn = new mysqli('localhost', 'root', '', 'SmartStep');
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    $sql = "SELECT Precio, Stock FROM catalogo WHERE Nombre = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $nombreProducto);
    $stmt->execute();
    $result = $stmt->get_result();
    $producto = $result->fetch_assoc();
    $conn->close();

    return $producto;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productName = $_POST['productName'] ?? null;
    $productSize = $_POST['productSize'] ?? null;
    if ($productName && $productSize) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $producto = obtenerStockYPrecio($productName);
        $productPrice = $producto['Precio'];
        $productStock = $producto['Stock'];

        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['name'] === $productName && $item['size'] === $productSize) {
                if ($item['quantity'] < $productStock) {
                    $item['quantity']++;
                } else {
                    $item['quantity'] = $productStock;
                }
                $found = true;
                break;
            }
        }

        if (!$found) {
            $_SESSION['cart'][] = [
                'name' => $productName,
                'size' => $productSize,
                'quantity' => 1,
                'price' => $productPrice,
                'image' => obtenerRutaImagen($productName),
            ];
        }
    }
}

$total = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras - SmartStep</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --color-dorado: #D4AF37;
            --color-dorado-claro: #F4D03F;
            --color-dorado-oscuro: #B8860B;
            --color-blanco: #FFFFFF;
            --color-texto: #212529;
        }
        .menu-item i {
            font-size: 20px;
            width: 24px;
            text-align: center;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
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
            text-decoration: none;
        }
        .nav-buttons {
            display: flex;
            gap: 20px;
        }
        .button {
            background-color: transparent;
            border: 2px solid white;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            transition: all 0.3s;
        }
        .button:hover {
            background-color: white;
            color: black;
        }
        .carrito-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .carrito-titulo {
            color: black;
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
            font-weight: bold;
        }
        .carrito-items {
            margin-bottom: 30px;
        }
        .carrito-item {
            display: flex;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #eee;
            position: relative;
        }
        .carrito-item img {
            width: 100px;
            height: 100px;
            object-fit: contain;
            border-radius: 8px;
            margin-right: 20px;
        }
        .item-details {
            flex-grow: 1;
        }
        .item-details h3 {
            margin: 0 0 10px 0;
            color: var(--color-texto);
        }
        .color-muestra {
            width: 20px;
            height: 20px;
            display: inline-block;
            border: 1px solid #ddd;
            border-radius: 50%;
            vertical-align: middle;
            margin-left: 5px;
        }
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: 10px;
        }
        .quantity-controls button {
            background-color: black;
            color: white;
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s;
        }
        .quantity-controls button:hover {
            background-color: #333;
        }
        .quantity-controls span {
            font-size: 16px;
            min-width: 20px;
            text-align: center;
        }
        .remove-item {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: black;
            color: white;
            border: none;
            width: 35px;
            height: 35px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }
        .remove-item:hover {
            background-color: #333;
        }
        .carrito-total {
            text-align: right;
            padding: 20px;
            font-size: 24px;
            color: black;
            border-top: 2px solid #eee;
            font-weight: bold;
        }
        .carrito-acciones {
            display: flex;
            justify-content: flex-end;
            gap: 20px;
            padding: 20px;
            text-decoration: none;
        }
        .btn-comprar {
            background-color: black;
            text-decoration: none;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            transition: background-color 0.3s;
        }
        .btn-comprar:hover {
            background-color: #333;
        }
        .carrito-vacio {
            text-align: center;
            padding: 50px;
            color: #666;
        }
        @media (max-width: 768px) {
            .carrito-item {
                flex-direction: column;
                text-align: center;
            }
            .carrito-item img {
                margin-right: 0;
                margin-bottom: 15px;
            }
            .remove-item {
                position: static;
                margin-top: 15px;
            }
            .quantity-controls {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="../index.php" class="logo">SmartStep</a>
        <div class="nav-buttons">
            <a href="../index.php" class="button">Volver</a>
        </div>
    </div>
    <div class="carrito-container">
        <h1 class="carrito-titulo">Tu Carrito</h1>
        <div class="carrito-items">
            <?php
            if (count($_SESSION['cart']) > 0) {
                foreach ($_SESSION['cart'] as $index => $item) {
                    echo "
                        <div class='carrito-item'>
                            <img src='" . obtenerRutaImagen($item['name']) . "' alt='" . $item['name'] . "'>
                            <div class='item-details'>
                                <h3>{$item['name']}</h3>
                                <p>Precio: \${$item['price']}</p>
                                <p>Talla: {$item['size']}</p>
                            </div>
                            <button class='remove-item' onclick='removeItem($index)'>X</button>
                        </div>";
                }
            } else {
                echo "<p class='carrito-vacio'>Tu carrito está vacío</p>";
            }
            ?>
        </div>
        <div class="carrito-total">
            Total: $<?php echo number_format($total, 2); ?>
        </div>
        <div class="carrito-acciones">
            <form action="pago.php" method="POST" id="comprarForm">
                <?php
                foreach ($_SESSION['cart'] as $index => $item) {
                    echo "<input type='hidden' name='cart[$index][name]' value='{$item['name']}'>";
                    echo "<input type='hidden' name='cart[$index][size]' value='{$item['size']}'>";
                    echo "<input type='hidden' name='cart[$index][quantity]' value='{$item['quantity']}'>";
                    echo "<input type='hidden' name='cart[$index][price]' value='{$item['price']}'>";
                }
                ?>
                <button type="submit" class="btn-comprar" onclick="return checkCarrito()">Comprar Ahora</button>
            </form>
        </div>
    </div>
    <script>
        function removeItem(index) {
            fetch('remove_item.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ index: index })
            }).then(() => location.reload());
        }

        function checkCarrito() {
            const total = <?php echo $total; ?>;
            if (total === 0) {
                alert('No tienes nada en el carrito');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
