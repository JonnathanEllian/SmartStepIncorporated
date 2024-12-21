<?php
session_start();
require('fpdf/fpdf.php');

$successMessage = "";
$errorMessage = "";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$total = 0;
$items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pay'])) {
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        $host = 'localhost';
        $user = 'root';
        $password = '';
        $dbname = 'smartstep';
        $conn = new mysqli($host, $user, $password, $dbname);
        if ($conn->connect_error) {
            die("Conexión fallida: " . $conn->connect_error);
        }
        $transactionSuccess = true;
        foreach ($_SESSION['cart'] as $item) {
            $productName = $item['name'];
            $quantity = $item['quantity'];
            $query = "UPDATE catalogo SET Stock = Stock - ? WHERE Nombre = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("is", $quantity, $productName);
            if (!$stmt->execute()) {
                $transactionSuccess = false;
                break;
            }
        }
        if ($transactionSuccess) {
            $_SESSION['cart'] = [];
            $successMessage = "Pago realizado con éxito";
            $pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(200, 10, 'SmartStep - Ticket de Pago', 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetLineWidth(0.5);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(5);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(50, 10, 'Nombre:', 0, 0);
$pdf->Cell(140, 10, $_POST['name'], 0, 1);

$pdf->Cell(50, 10, 'Direccion:', 0, 0);
$pdf->Cell(140, 10, $_POST['address'], 0, 1);

$pdf->Cell(50, 10, 'Ciudad:', 0, 0);
$pdf->Cell(140, 10, $_POST['city'], 0, 1);

$pdf->Cell(50, 10, 'Telefono:', 0, 0);
$pdf->Cell(140, 10, $_POST['phone'], 0, 1);

$pdf->Ln(5);
$pdf->SetLineWidth(0.5);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(200, 10, 'Resumen del Pedido', 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Ln(5);

foreach ($_SESSION['cart'] as $item) {
    $pdf->Cell(130, 10, $item['name'] . ' x' . $item['quantity'], 0, 0);
    $pdf->Cell(40, 10, '$' . number_format($item['price'] * $item['quantity'], 2), 0, 1);
}

$pdf->Ln(5);
$pdf->SetLineWidth(0.5);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(130, 10, 'Total:', 0, 0);
$pdf->Cell(40, 10, '$' . number_format($total, 2), 0, 1);

$pdf->Ln(5);
$pdf->SetLineWidth(0.5);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(5);

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(200, 10, 'Fecha: ' . date('Y-m-d H:i:s'), 0, 1, 'C');

$pdf->Ln(10);
$pdf->SetLineWidth(1);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());

$pdf->Output('D', 'ticket_' . time() . '.pdf');
echo "<script>window.location.reload();</script>";

        }
        $conn->close();
    } else {
        $errorMessage = "El carrito está vacío";
    }
}

$cartCount = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['quantity'];
    }
}

$total = 0;
$items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
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
    <title>Pago - SmartStep</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f5f5f5; }
        .header { display: flex; justify-content: space-between; align-items: center; padding: 20px; background-color: rgba(0, 0, 0, 0.9); }
        .logo { color: white; font-family: 'Times New Roman', Times, serif; font-size: 24px; }
        .payment-container { max-width: 900px; margin: 40px auto; padding: 30px; background-color: white; border-radius: 10px; box-shadow: 0 0 20px rgba(0, 0, 0, 0.1); }
        .payment-title { text-align: center; color: #333; margin-bottom: 30px; font-size: 28px; }
        .payment-sections { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
        .section { padding: 20px; }
        .section-title { font-size: 18px; color: #333; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e44d26; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: #555; font-size: 14px; }
        .form-group input { width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 5px; font-size: 16px; transition: all 0.3s ease; box-sizing: border-box; }
        .form-group input:focus { border-color: #e44d26; outline: none; }
        .card-details { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 10px; }
        .order-summary { background-color: #f9f9f9; padding: 20px; border-radius: 5px; }
        .order-item { display: flex; justify-content: space-between; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #ddd; }
        .total { font-size: 18px; font-weight: bold; margin-top: 20px; padding-top: 20px; border-top: 2px solid #ddd; }
        .pay-button { width: 100%; padding: 14px; background-color: #e44d26; color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; transition: all 0.3s ease; text-transform: uppercase; letter-spacing: 1px; margin-top: 20px; }
        .pay-button:hover { background-color: #c4371b; transform: translateY(-2px); }
        .back-button { text-decoration: none; color: white; display: flex; align-items: center; gap: 5px; font-size: 14px; transition: all 0.3s ease; }
        .back-button:hover { color: #e44d26; }
        .message { text-align: center; font-size: 18px; padding: 10px; margin-bottom: 20px; }
        .success { background-color: #4CAF50; color: white; }
        .error { background-color: #f44336; color: white; }
        @media (max-width: 768px) { .payment-sections { grid-template-columns: 1fr; } .payment-container { margin: 20px; } .card-details { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="header">
        <a href="carrito.php" class="back-button">
            <i class="fas fa-arrow-left"></i> Volver al carrito
        </a>
        <div class="logo">SmartStep</div>
        <div style="width: 100px;"></div>
    </div>

    <div class="payment-container">
        <h1 class="payment-title">Finalizar Compra</h1>

        <?php if ($successMessage): ?>
            <div class="message success">
                <?php echo $successMessage; ?>
            </div>
        <?php elseif ($errorMessage): ?>
            <div class="message error">
                <?php echo $errorMessage; ?>
            </div>
        <?php endif; ?>

        <form id="paymentForm" method="POST" onsubmit="return validateForm();">
            <div class="payment-sections">
                <div class="section">
                    <h2 class="section-title">Información de Envío</h2>
                    <div class="form-group">
                        <label for="name">Nombre completo</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Dirección</label>
                        <input type="text" id="address" name="address" required>
                    </div>
                    <div class="form-group">
                        <label for="city">Ciudad</label>
                        <input type="text" id="city" name="city" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Teléfono</label>
                        <input type="tel" id="phone" name="phone" pattern="^\d{10}$" required title="Debe ingresar un teléfono válido de 10 dígitos">
                    </div>
                </div>

                <div class="section">
                    <h2 class="section-title">Información de Pago</h2>
                    <div class="form-group">
                        <label for="card">Número de tarjeta</label>
                        <input type="text" id="card" maxlength="16" name="card" pattern="\d{16}" required title="Debe ingresar un número de tarjeta de 16 dígitos">
                    </div>
                    <div class="card-details">
                        <div class="form-group">
                            <label for="cardName">Titular de la tarjeta</label>
                            <input type="text" id="cardName" name="cardName" required>
                        </div>
                        <div class="form-group">
                            <label for="expiry">Vencimiento</label>
                            <input type="text" id="expiry" name="expiry" placeholder="MM/AA" maxlength="5" pattern="\d{2}/\d{2}" required title="Debe ingresar la fecha de vencimiento en formato MM/AA">
                        </div>
                        <div class="form-group">
                            <label for="cvv">CVV</label>
                            <input type="text" id="cvv" maxlength="3" name="cvv" pattern="\d{3}" required title="Debe ingresar un CVV de 3 dígitos">
                        </div>
                    </div>
                </div>
            </div>

            <div class="section">
                <h2 class="section-title">Resumen del Pedido</h2>
                <div class="order-summary">
                    <?php if (empty($items)): ?>
                        <p>No hay artículos en el carrito.</p>
                    <?php else: ?>
                        <?php foreach ($items as $item): ?>
                            <div class="order-item">
                                <span><?php echo $item['name']; ?> x<?php echo $item['quantity']; ?></span>
                                <span>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <div class="total">Total: $<?php echo number_format($total, 2); ?></div>
                </div>
            </div>

            <button type="submit" class="pay-button" name="pay">Pagar</button>

        </form>
    </div>

    <script>
        function validateForm() {
            let fields = ['name', 'address', 'city', 'phone', 'card', 'cardName', 'expiry', 'cvv'];
            for (let field of fields) {
                if (!document.getElementById(field).value.trim()) {
                    alert('Por favor, complete todos los campos requeridos');
                    return false;
                }
            }
            return true;
        }
    </script>
</body>
</html>
