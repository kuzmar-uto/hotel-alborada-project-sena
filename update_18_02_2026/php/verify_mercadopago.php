<?php
require_once __DIR__ . '/config/mercadopago.php';
require_once __DIR__ . '/config/database.php';

// Verificar Access Token
echo "=== VERIFICACIÓN DE INTEGRACIÓN MERCADO PAGO ===\n\n";

echo "1. Verificando Access Token...\n";
if (defined('MERCADOPAGO_ACCESS_TOKEN') && MERCADOPAGO_ACCESS_TOKEN !== 'YOUR_ACCESS_TOKEN_HERE') {
    echo "✓ Access Token configurado\n";
} else {
    echo "✗ Access Token NO configurado. Por favor actualiza php/config/mercadopago.php\n";
    exit(1);
}

// Verificar conectividad con API de Mercado Pago
echo "\n2. Verificando conectividad con API de Mercado Pago...\n";
$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => MERCADOPAGO_API_URL . "/v1/payments/search",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 5,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer " . MERCADOPAGO_ACCESS_TOKEN
    ),
));

$response = curl_exec($curl);
$err = curl_error($curl);
$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

if ($err) {
    echo "✗ Error de conectividad: " . $err . "\n";
} elseif ($http_code === 200 || $http_code === 400) {
    echo "✓ Conexión con Mercado Pago exitosa (HTTP $http_code)\n";
} else {
    echo "✗ Error de autenticación (HTTP $http_code). Verifica tu Access Token.\n";
    echo "  Respuesta: " . substr($response, 0, 200) . "...\n";
}

// Verificar base de datos
echo "\n3. Verificando base de datos...\n";
try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Crear tablas
    $sqlReservas = "CREATE TABLE IF NOT EXISTS reservas (
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
        id_mercadopago VARCHAR(255) DEFAULT NULL UNIQUE,
        monto_pagado DECIMAL(10, 2) DEFAULT 0,
        fecha_pago TIMESTAMP NULL,
        fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    
    $conn->exec($sqlReservas);
    echo "✓ Tabla 'reservas' lista\n";
    
    $sqlPagos = "CREATE TABLE IF NOT EXISTS pagos (
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
        FOREIGN KEY (id_reserva) REFERENCES reservas(id) ON DELETE CASCADE,
        INDEX idx_mercadopago (id_mercadopago),
        INDEX idx_estado (estado)
    ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    
    $conn->exec($sqlPagos);
    echo "✓ Tabla 'pagos' lista\n";
    
} catch (Exception $e) {
    echo "✗ Error con la base de datos: " . $e->getMessage() . "\n";
}

// Verificar directorio de logs
echo "\n4. Verificando directorio de logs...\n";
$log_dir = __DIR__ . '/../logs';
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0755, true);
    echo "✓ Directorio de logs creado en: " . $log_dir . "\n";
} else {
    echo "✓ Directorio de logs existe\n";
}

// Verificar permisos
if (is_writable($log_dir)) {
    echo "✓ Permisos de escritura en logs OK\n";
} else {
    echo "✗ Sin permisos de escritura en: " . $log_dir . "\n";
}

// Configuración resumen
echo "\n=== CONFIGURACIÓN ACTUAL ===\n";
echo "API URL: " . MERCADOPAGO_API_URL . "\n";
echo "Moneda: " . PAYMENT_CURRENCY . "\n";
echo "Monto Mínimo: \$" . MINIMUM_PAYMENT_AMOUNT . "\n";
echo "Webhook URL: " . MERCADOPAGO_WEBHOOK_URL . "\n";

echo "\n✓ Verificación completada. El sistema está listo para usar.\n";

?>
