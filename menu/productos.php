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
$dbname = "SmartStep";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$nombre = $_GET['Nombre'] ?? '';

if (empty($nombre)) {
    die("Producto no encontrado");
}

$sql = "SELECT * FROM catalogo WHERE Nombre = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $nombre);
$stmt->execute();
$result = $stmt->get_result();

$product = $result->fetch_assoc();
if (!$product) {
    die("Producto no encontrado");
}

function getProductImages($nombre) {
    $imagePaths = [];
    $extensions = ['jpg', 'jpeg', 'png', 'jfif'];

    foreach ($extensions as $ext) {
        $imagePath = "multimedia/catalogo/" . $nombre . "." . $ext;
        if (file_exists($imagePath)) {
            $imagePaths[] = $imagePath;
        }
    }
    return $imagePaths ?: ["multimedia/default.png"];
}

function generateSizes($tallas) {
    if (strpos($tallas, '-') !== false) {
        [$start, $end] = explode('-', $tallas);
        return range((int)$start, (int)$end);
    }
    return [$tallas];
}

$productImages = getProductImages($product['Nombre']);
$sizes = generateSizes($product['Tallas']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $size = $_POST['size'] ?? '';
    if (!$size) {
        die("Talla no seleccionada");
    }

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $tempId = uniqid();

    $_SESSION['cart'][] = [
        'id' => $tempId,
        'name' => $product['Nombre'],
        'size' => $size,
        'price' => $product['Precio'],
        'image' => $productImages[0],
        'quantity' => 1,
    ];

    header("Location: carrito.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['Nombre']; ?></title>
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
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo {
            color: white;
            font-size: 24px;
            font-weight: bold;
            text-decoration: none;
        }

        .cart-container {
            position: relative;
            color: white;
            text-decoration: none;
            font-size: 20px;
            border: 2px solid white;
            padding: 8px 15px;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .cart-container:hover {
            background-color: white;
            color: black;
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: red;
            color: white;
            font-size: 12px;
            padding: 2px 6px;
            border-radius: 50%;
        }

        .product-detail {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            display: grid;
            grid-template-columns: 60% 40%;
            gap: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .product-images {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .main-image {
            width: 100%;
            height: 400px;
            object-fit: contain;
            border-radius: 10px;
        }

        .thumbnail-container {
            display: flex;
            gap: 10px;
            overflow-x: auto;
        }

        .thumbnail {
            width: 80px;
            height: 80px;
            object-fit: cover;
            cursor: pointer;
            border-radius: 5px;
            border: 2px solid transparent;
        }

        .thumbnail:hover {
            border-color: #333;
        }

        .product-info {
            padding: 20px;
        }

        .product-title {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .product-price {
            font-size: 28px;
            color: #e44d26;
            margin-bottom: 20px;
        }

        .product-description {
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .size-options {
            margin-bottom: 30px;
        }

        .size-buttons {
            display: flex;
            gap: 10px;
        }

        .size-button {
            padding: 10px 20px;
            border: 2px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
            background: white;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .add-to-cart {
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
            background-color: orange;
        }

        @media (max-width: 768px) {
            .product-detail {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <header class="header">
        <a href="../index.php" class="logo">SmartStep</a>
        <a href="carrito.php">
            <div class="cart-container">
                <i class="fa-solid fa-cart-shopping"></i>
                <span class="cart-count"><?php echo $cartCount; ?></span>
            </div>
        </a>
    </header>

    <div class="product-detail">
        <div class="product-images">
            <img src="<?php echo $productImages[0]; ?>" alt="Producto" class="main-image">
            <div class="thumbnail-container">
                <?php foreach ($productImages as $image) : ?>
                    <img src="<?php echo $image; ?>" alt="Miniatura" class="thumbnail">
                <?php endforeach; ?>
            </div>
        </div>
        <div class="product-info">
            <h2 class="product-title"><?php echo $product['Nombre']; ?></h2>
            <p class="product-price">$<?php echo number_format($product['Precio'], 2); ?></p>
            <p class="product-description"><?php echo $product['Descripcion']; ?></p>
            <form method="POST">
                <div class="size-options">
                    <label for="size">Selecciona una talla:</label>
                    <select name="size" id="size" required>
                        <?php foreach ($sizes as $size) : ?>
                            <option value="<?php echo $size; ?>"><?php echo $size; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="add-to-cart">Agregar al carrito</button>
            </form>
        </div>
    </div>
</body>

</html>
