<?php
require_once __DIR__ . '/config/database.php';

$db = new Database();
$conn = $db->getConnection();

// Obtener parámetros de Mercado Pago
$collection_id = isset($_GET['collection_id']) ? $_GET['collection_id'] : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago Fallido</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../styles.css">
    <style>
        .payment-result {
            max-width: 500px;
            margin: 100px auto;
            padding: 40px;
            text-align: center;
            border-radius: 12px;
            background: #f6f7f9;
        }
        .error-icon {
            font-size: 60px;
            color: #dc3545;
            margin-bottom: 20px;
        }
        .payment-result h1 {
            color: #24303a;
            margin-bottom: 10px;
        }
        .payment-result p {
            color: #626e75;
            font-size: 16px;
            line-height: 1.6;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            margin-top: 20px;
            margin-right: 10px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        .btn-primary {
            background: #4197aa;
            color: white;
        }
        .btn-primary:hover {
            background: #2d7a8c;
        }
    </style>
</head>
<body>
    <div class="payment-result">
        <div class="error-icon">
            <i class="fas fa-times-circle"></i>
        </div>
        <h1>¡Pago Rechazado!</h1>
        <p>Lamentablemente, tu pago ha sido rechazado.</p>
        <p style="margin-top: 15px; font-size: 14px; color: #626e75;">
            Esto puede ocurrir por varios motivos:
            <br>- Fondos insuficientes
            <br>- Datos de pago incorrectos
            <br>- Transacción sospechosa
        </p>
        <p style="margin-top: 20px; font-size: 14px; color: #626e75;">
            Por favor, intenta de nuevo o usa otro método de pago.
        </p>
        
        <a href="habitaciones.html" class="btn btn-danger">Reintentar Pago</a>
        <a href="../index.php" class="btn btn-primary">Volver al Inicio</a>
    </div>
</body>
</html>
