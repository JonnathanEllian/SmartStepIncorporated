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
    die("Conexión fallida: " . $conn->connect_error);
}

$sql = "SELECT * FROM catalogo WHERE Genero='H'";
$result = $conn->query($sql);

$products = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
} else {
    echo "0 resultados";
}

$conn->close();


function getImagePath($productName) {
    $basePath = '../multimedia/catalogo/';
    $extensions = ['jpg', 'jpeg', 'jfif', 'png'];

    foreach ($extensions as $ext) {
        $imagePath = $basePath . $productName . '.' . $ext;
        if (file_exists($imagePath)) {
            return $imagePath;
        }
    }

    return '../multimedia/default.jpg';
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartStep</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        .header {
            background-color: rgba(0, 0, 0, 0.9);
            padding: 1rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 90%;
            top: 0;
            z-index: 1000;
        }

        .logo {
            color: white;
            font-size: 24px;
            text-decoration: none;
            font-weight: bold;
        }

        .nav-buttons {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .menu-button {
            background-color: transparent;
            border: 2px solid white;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .menu-button:hover {
            background-color: white;
            color: black;
        }

        .search-box {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .search-box input {
            padding: 8px 15px;
            border: 2px solid white;
            border-radius: 5px;
            background-color: transparent;
            color: white;
            font-size: 14px;
            outline: none;
            width: 200px;
            transition: all 0.3s;
        }

        .search-box input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .search-box button {
            background-color: transparent;
            border: 2px solid white;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .search-box button:hover {
            background-color: white;
            color: black;
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

        .menu-dropdown {
            position: fixed;
            top: 0;
            left: -300px;
            width: 250px;
            background-color: white;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            transition: left 0.3s ease;
            z-index: 1001;
            height: 100vh;
        }

        .menu-dropdown.active {
            left: 0;
        }

        .menu-content {
            padding: 80px 0 20px 0;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 25px;
            color: #333;
            text-decoration: none;
            transition: all 0.3s;
        }

        .menu-item:hover {
            background-color: #f5f5f5;
            color: #D4AF37;
        }

        .menu-item i {
            font-size: 20px;
            width: 24px;
            text-align: center;
        }

        .collections-nav {
            background-color: white;
            padding: 15px 0;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            position: fixed;
            width: 100%;
            top: 70px;
            z-index: 999;
        }

        .collections-nav a {
            color: #333;
            text-decoration: none;
            padding: 10px 20px;
            margin: 0 15px;
            font-weight: 500;
            transition: color 0.3s;
        }

        .collections-nav a:hover {
            color: #D4AF37;
        }

        .hero-section {
    height: 50vh;
    background-image: url('../multimedia/imagenes menu/bannerM.png');
    background-size: cover;
    background-position: center;
    position: relative;
    margin-top: 120px;
}


        .hero-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: white;
        }

        .hero-text h1 {
            font-size: 40px;
            margin-bottom: 20px;
        }

        .hero-text p {
            font-size: 20px;
        }

        .menu-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .menu-overlay.active {
            display: block;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            max-width: 1400px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .product-card {
    background-color: white;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 380px;
    transition: transform 0.3s ease;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.product-card img {
    width: 100%;
    max-height: 200px;
    object-fit: contain;
    margin-bottom: 15px;
}

.product-card h3 {
    margin: 10px 0;
    color: #333;
    font-size: 18px;
    font-weight: bold;
    min-height: 40px;
}

.product-card p {
    color: #e44d26;
    font-weight: bold;
    font-size: 16px;
    margin-top: 10px;
}

.product-card a {
    text-decoration: none;
}

.product-card a:focus, .product-card a:hover {
    outline: none;
    text-decoration: none;
}



        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                padding: 1rem 2%;
            }

            .nav-buttons {
                flex-wrap: wrap;
                justify-content: center;
                margin-top: 10px;
                gap: 15px;
            }

            .search-box {
                order: 3;
                width: 100%;
                max-width: none;
            }

            .collections-nav {
                top: 140px;
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .hero-section {
                margin-top: 200px;
            }

            .menu-dropdown {
                width: 100%;
                left: -100%;
            }
        }

    footer {
        background-color: #333;
        color: white;
        padding: 20px;
        text-align: center;
        position: relative;
        bottom: 0;
        width: 100%;
    }

    .footer-content {
        max-width: 1200px;
        margin: 0 auto;
    }

    .footer-text {
        font-size: 16px;
        margin-bottom: 10px;
    }

    .footer-links {
        display: flex;
        justify-content: center;
        gap: 20px;
    }

    .footer-link {
        color: white;
        text-decoration: none;
        font-size: 14px;
        transition: color 0.3s;
    }

    .footer-link:hover {
        color: #D4AF37;
    }
    </style>
</head>

<body>
    <header class="header">
        <a href="../../index.php" class="logo">SmartStep</a>
        <div class="nav-buttons">
        <a href="../../menu/carrito.php">
            <div class="cart-container">
                <i class="fa-solid fa-cart-shopping"></i>
                <span class="cart-count"><?php echo $cartCount; ?></span>
            </div>
            </a>
            <div class="menu-button" onclick="toggleMenu()">
                <i class="fa-solid fa-bars"></i>
            </div>
        </div>
    </header>

    <div class="menu-overlay" id="menu-overlay" onclick="toggleMenu()"></div>

    <div class="menu-dropdown" id="menu-dropdown">
        <div class="menu-content">
        <a href="../../index.php" class="menu-item"><i class="fa-solid fa-house"></i>Inicio</a>
        <a href="../../menu/tienda.php" class="menu-item"><i class="fa-solid fa-store"></i>Tienda</a>
        <a href="../../menu/login.php" class="menu-item"><i class="fa-solid fa-user"></i>Cuenta</a>
        <a href="../../menu/atencionAlCliente.php" class="menu-item"><i class="fa-solid fa-phone"></i>Contacto</a>
    </div>

    </div>

    <div class="collections-nav">
        <a href="hombre.php">Hombres</a>
        <a href="mujer.php">Mujeres</a>
        <a href="infantil.php">Niños</a>
    </div>

    <div class="hero-section">
        <div class="hero-text">
            <h1>Descubre Nuestra Colección para Hombres</h1>
            <p>Encuentra el Calzado Perfecto para Ti</p>
        </div>
    </div>

    <div class="products-grid">
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <a href="../../menu/productos.php?Nombre=<?php echo urlencode($product['Nombre']); ?>">
                    <img src="<?php echo getImagePath($product['Nombre']); ?>" alt="<?php echo $product['Nombre']; ?>">
                    <h3><?php echo $product['Nombre']; ?></h3>
                    <p>$<?php echo number_format($product['Precio'], 2); ?></p>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>




    <script>
        function toggleMenu() {
            const menu = document.getElementById('menu-dropdown');
            const overlay = document.getElementById('menu-overlay');
            menu.classList.toggle('active');
            overlay.classList.toggle('active');
        }
    </script>
    <footer>
    <div class="footer-content">
        <p class="footer-text">SmartStep - Todos los derechos reservados</p>
        <div class="footer-links">
            <a href="../../menu/atencionAlCliente.php" class="footer-link">Atención al Cliente</a>
            <a href="../../menu/miCuenta.php" class="footer-link">Mi Cuenta</a>
        </div>
    </div>
</footer>

</body>
</html>
