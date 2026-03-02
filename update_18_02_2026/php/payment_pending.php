<?php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago Pendiente</title>
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
        .pending-icon {
            font-size: 60px;
            color: #ffc107;
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
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn-warning {
            background: #ffc107;
            color: #24303a;
        }
        .btn-warning:hover {
            background: #e0a800;
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
        <div class="pending-icon">
            <i class="fas fa-hourglass-half"></i>
        </div>
        <h1>Pago Pendiente</h1>
        <p>Tu pago está siendo procesado.</p>
        <p style="margin-top: 15px; font-size: 14px; color: #626e75;">
            Por favor, no cierres esta página ni realices otra transacción mientras se completa el proceso.
            <br><br>Recibirás una confirmación por correo en breve.
        </p>
        
        <a href="../index.php" class="btn btn-warning">Volver al Inicio</a>
    </div>
    
    <script>
        // Redireccionar después de 5 segundos
        setTimeout(() => {
            window.location.href = '../index.php';
        }, 5000);
    </script>
</body>
</html>
