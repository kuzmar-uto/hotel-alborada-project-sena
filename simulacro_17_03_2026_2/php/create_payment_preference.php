<?php
/** @noinspection PhpUndefinedConstantInspection */
header('Content-Type: application/json; charset=utf-8');

// Solo acepta POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/mercadopago.php';

$db = new Database();
$conn = $db->getConnection();

// Obtener datos de la reserva
$data = json_decode(file_get_contents('php://input'), true);

$id_habitacion = isset($data['id_habitacion']) ? trim($data['id_habitacion']) : '';
$fecha_entrada = isset($data['fecha_entrada']) ? trim($data['fecha_entrada']) : '';
$fecha_salida = isset($data['fecha_salida']) ? trim($data['fecha_salida']) : '';
$nombre_completo = isset($data['nombre_completo']) ? trim($data['nombre_completo']) : '';
$adultos = isset($data['adultos']) ? (int)$data['adultos'] : 0;
$ninos = isset($data['ninos']) ? (int)$data['ninos'] : 0;
$email = isset($data['email']) ? trim($data['email']) : '';
$room_price = isset($data['room_price']) ? (float)$data['room_price'] : 0;
$room_name = isset($data['room_name']) ? trim($data['room_name']) : '';

// Validación básica
if (!$id_habitacion || !$fecha_entrada || !$fecha_salida || !$nombre_completo || !$email) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Faltan campos requeridos']);
    exit;
}

// Validar que el pago sea al menos $20 USD (monto mínimo de Mercado Pago)
$payment_amount = $room_price > 0 ? $room_price : MINIMUM_PAYMENT_AMOUNT;
$payment_amount = max($payment_amount, MINIMUM_PAYMENT_AMOUNT);  // Garantizar mínimo

try {
    // Construir URL base dinámicamente
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $base_url = $protocol . '://' . $host . '/update_18_02_2026';  // Ajusta la ruta según tu estructura

    // Crear tabla de reservas si no existe
    $sqlCreateReservas = "CREATE TABLE IF NOT EXISTS reservas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_habitacion VARCHAR(255) NOT NULL,
        fecha_entrada DATE NOT NULL,
        fecha_salida DATE NOT NULL,
        nombre_completo VARCHAR(255) NOT NULL,
        adultos INT DEFAULT 0,
        ninos INT DEFAULT 0,
        email VARCHAR(255) NOT NULL,
        estado VARCHAR(50) DEFAULT 'pendiente',
        pago_confirmado BOOLEAN DEFAULT 0,
        id_mercadopago VARCHAR(255) DEFAULT NULL,
        monto_pagado DECIMAL(10, 2) DEFAULT 0,
        fecha_pago TIMESTAMP NULL,
        fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_mercadopago (id_mercadopago)
    ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB";
    
    $conn->exec($sqlCreateReservas);

    // Crear tabla de pagos si no existe
    $conn->exec("DROP TABLE IF EXISTS pagos");
    $sqlCreatePagos = "CREATE TABLE pagos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_reserva INT NOT NULL,
        id_mercadopago VARCHAR(255) UNIQUE NOT NULL,
        monto DECIMAL(10, 2) NOT NULL,
        moneda VARCHAR(10) DEFAULT 'USD',
        estado VARCHAR(50) DEFAULT 'pendiente',
        metodo_pago VARCHAR(100),
        referencia_externa VARCHAR(255),
        json_respuesta LONGTEXT,
        fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (id_reserva) REFERENCES reservas(id_reserva) ON DELETE CASCADE,
        INDEX idx_mercadopago (id_mercadopago),
        INDEX idx_estado (estado)
    ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB";
    
    $conn->exec($sqlCreatePagos);

    // Preparar datos para Mercado Pago
    $preference = array(
        "items" => array(
            array(
                "title" => "Depósito inicial - " . $room_name,
                "description" => "Depósito de reserva para " . $nombre_completo . " del " . $fecha_entrada . " al " . $fecha_salida,
                "quantity" => 1,
                "unit_price" => $payment_amount
            )
        ),
        "payer" => array(
            "name" => $nombre_completo,
            "email" => $email
        ),
        "back_url" => array(
            "success" => $base_url . "/php/payment_success.php",
            "failure" => $base_url . "/php/payment_failure.php",
            "pending" => $base_url . "/php/payment_pending.php"
        ),
        "external_reference" => "reserva_" . date('YmdHis') . "_" . rand(1000, 9999),
        "notification_url" => $base_url . "/php/webhooks/mercadopago.php",
        "statement_descriptor" => "ALBORADA HOTEL"
    );

    // Enviar solicitud a Mercado Pago
    $curl = curl_init();
    
    curl_setopt_array($curl, array(
        CURLOPT_URL => MERCADOPAGO_API_URL . "/checkout/preferences",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($preference),
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
            "Authorization: Bearer " . MERCADOPAGO_ACCESS_TOKEN
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error al conectar con Mercado Pago: ' . $err
        ]);
        exit;
    }

    $mpResponse = json_decode($response, true);

    if (!isset($mpResponse['id'])) {
        http_response_code(500);
        $error_msg = isset($mpResponse['message']) ? $mpResponse['message'] : 'No se obtuvo respuesta válida';
        if (strpos(MERCADOPAGO_ACCESS_TOKEN, 'YOUR_ACCESS_TOKEN') !== false) {
            $error_msg = 'Token de Mercado Pago no configurado. Actualiza el archivo config/mercadopago.php con tus credenciales reales.';
        }
        echo json_encode([
            'success' => false,
            'message' => 'Error en la respuesta de Mercado Pago: ' . $error_msg,
            'details' => $mpResponse
        ]);
        exit;
    }

    // Crear registro de reserva con estado pendiente
    $estado_reserva = 'pendiente';
    $stmt = $conn->prepare('INSERT INTO reservas (
        id_habitacion, 
        fecha_entrada, 
        fecha_salida, 
        nombre_completo, 
        adultos, 
        ninos, 
        email,
        estado,
        id_mercadopago,
        monto_pagado
    ) VALUES (
        :id_habitacion,
        :fecha_entrada,
        :fecha_salida,
        :nombre_completo,
        :adultos,
        :ninos,
        :email,
        :estado,
        :id_mercadopago,
        :monto_pagado
    )');

    $stmt->bindParam(':id_habitacion', $id_habitacion);
    $stmt->bindParam(':fecha_entrada', $fecha_entrada);
    $stmt->bindParam(':fecha_salida', $fecha_salida);
    $stmt->bindParam(':nombre_completo', $nombre_completo);
    $stmt->bindParam(':adultos', $adultos, PDO::PARAM_INT);
    $stmt->bindParam(':ninos', $ninos, PDO::PARAM_INT);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':estado', $estado_reserva);
    $stmt->bindParam(':id_mercadopago', $mpResponse['id']);
    $stmt->bindParam(':monto_pagado', $payment_amount);

    $stmt->execute();
    $reserva_id = $conn->lastInsertId();

    // Crear registro de pago
    $estado_pago = 'pendiente';
    $moneda = PAYMENT_CURRENCY;
    $stmtPago = $conn->prepare('INSERT INTO pagos (
        id_reserva,
        id_mercadopago,
        monto,
        moneda,
        estado,
        json_respuesta
    ) VALUES (
        :id_reserva,
        :id_mercadopago,
        :monto,
        :moneda,
        :estado,
        :json_respuesta
    )');

    $stmtPago->bindParam(':id_reserva', $reserva_id, PDO::PARAM_INT);
    $stmtPago->bindParam(':id_mercadopago', $mpResponse['id']);
    $stmtPago->bindParam(':monto', $payment_amount);
    $stmtPago->bindParam(':moneda', $moneda);
    $stmtPago->bindParam(':estado', $estado_pago);
    $stmtPago->bindParam(':json_respuesta', $response);

    $stmtPago->execute();

    // Retornar la URL de pago de Mercado Pago
    echo json_encode([
        'success' => true,
        'message' => 'Preferencia de pago creada',
        'reserva_id' => $reserva_id,
        'preference_id' => $mpResponse['id'],
        'init_point' => $mpResponse['init_point'],
        'sandbox_init_point' => $mpResponse['sandbox_init_point']
    ]);
    exit;

} catch (PDOException $ex) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error del servidor: ' . $ex->getMessage()
    ]);
    exit;
} catch (Exception $ex) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error inesperado: ' . $ex->getMessage()
    ]);
    exit;
}

?>
