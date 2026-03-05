<?php
require_once __DIR__ . '/config/database.php';

$db = new Database();
$conn = $db->getConnection();

// Obtener parámetros de Mercado Pago
$collection_id = isset($_GET['collection_id']) ? $_GET['collection_id'] : '';
$collection_status = isset($_GET['collection_status']) ? $_GET['collection_status'] : '';
$external_reference = isset($_GET['external_reference']) ? $_GET['external_reference'] : '';

// Obtener detalles de la reserva
$stmt = $conn->prepare('SELECT r.*, p.estado as pago_estado 
    FROM reservas r 
    LEFT JOIN pagos p ON r.id = p.id_reserva 
    WHERE r.id_mercadopago = :id_mercadopago 
    LIMIT 1');
$stmt->bindParam(':id_mercadopago', $collection_id);
$stmt->execute();
$reserva = $stmt->fetch(PDO::FETCH_ASSOC);

$success = ($collection_status === 'approved' && $reserva && $reserva['pago_confirmado']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $success ? 'Pago Exitoso' : 'Pago Procesado'; ?></title>
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
        .success-icon {
            font-size: 60px;
            color: #28a745;
            margin-bottom: 20px;
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
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn-success {
            background: #28a745;
            color: white;
        }
        .btn-success:hover {
            background: #218838;
        }
        .btn-primary {
            background: #4197aa;
            color: white;
        }
        .btn-primary:hover {
            background: #2d7a8c;
        }
        .reservation-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: left;
        }
        .reservation-details p {
            margin: 8px 0;
        }
        .label {
            font-weight: 600;
            color: #4197aa;
        }
    </style>
</head>
<body>
    <div class="payment-result">
        <?php if ($success): ?>
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1>¡Pago Exitoso!</h1>
            <p>Tu reserva ha sido confirmada correctamente.</p>
            
            <?php if ($reserva): ?>
                <div class="reservation-details">
                    <p><span class="label">Número de Reserva:</span> #<?php echo htmlspecialchars($reserva['id']); ?></p>
                    <p><span class="label">Nombre:</span> <?php echo htmlspecialchars($reserva['nombre_completo']); ?></p>
                    <p><span class="label">Email:</span> <?php echo htmlspecialchars($reserva['email']); ?></p>
                    <p><span class="label">Check-in:</span> <?php echo date('d/m/Y', strtotime($reserva['fecha_entrada'])); ?></p>
                    <p><span class="label">Check-out:</span> <?php echo date('d/m/Y', strtotime($reserva['fecha_salida'])); ?></p>
                    <p><span class="label">Monto Pagado:</span> $<?php echo number_format($reserva['monto_pagado'], 2); ?></p>
                </div>
            <?php endif; ?>
            
            <p style="margin-top: 20px; font-size: 14px; color: #626e75;">
                Se ha enviado un correo de confirmación a <?php echo htmlspecialchars($reserva['email'] ?? 'tu email'); ?> con los detalles de tu reserva.
            </p>
            
            <a href="../index.php" class="btn btn-success">Volver al Inicio</a>
            <a href="reserva_confirmada.php?id=<?php echo htmlspecialchars($reserva['id']); ?>" class="btn btn-primary">Ver Detalles</a>

        <?php else: ?>
            <div class="error-icon">
                <i class="fas fa-times-circle"></i>
            </div>
            <h1>Pago Pendiente o Rechazado</h1>
            <p>Tu pago está siendo procesado o ha sido rechazado.</p>
            <p style="margin-top: 20px; font-size: 14px; color: #626e75;">
                Si el problema persiste, contáctanos para más información.
            </p>
            <a href="../index.php" class="btn btn-primary">Volver al Inicio</a>
        <?php endif; ?>
    </div>
</body>
</html>
