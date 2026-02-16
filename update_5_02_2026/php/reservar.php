<?php
header('Content-Type: application/json; charset=utf-8');
// aceptar solo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

require_once __DIR__ . '/config/database.php';

$db = new Database();
$conn = $db->getConnection();

// recolectar datos
$room = isset($_POST['room']) ? trim($_POST['room']) : '';
$price = isset($_POST['price']) ? trim($_POST['price']) : '';
$checkin = isset($_POST['checkin']) ? trim($_POST['checkin']) : '';
$checkout = isset($_POST['checkout']) ? trim($_POST['checkout']) : '';
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$adults = isset($_POST['adults']) ? (int) $_POST['adults'] : 0;
$children = isset($_POST['children']) ? (int) $_POST['children'] : 0;
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
// nuevos campos
$documento_tipo = isset($_POST['documento_tipo']) ? trim($_POST['documento_tipo']) : '';
$documento_numero = isset($_POST['documento_numero']) ? trim($_POST['documento_numero']) : '';
$tipo_habitacion = isset($_POST['tipo_habitacion']) ? trim($_POST['tipo_habitacion']) : '';

// validación básica
if (!$room || !$checkin || !$checkout || !$name || !$email) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Faltan campos requeridos']);
    exit;
}

if (strtotime($checkout) <= strtotime($checkin)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Fecha de salida debe ser posterior a entrada']);
    exit;
}

try {
    // crear tabla si no existe (es una suposición razonable)
    $sqlCreate = "CREATE TABLE IF NOT EXISTS reservas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        room VARCHAR(255) NOT NULL,
        price VARCHAR(50) DEFAULT NULL,
        documento_tipo VARCHAR(50) DEFAULT NULL,
        documento_numero VARCHAR(100) DEFAULT NULL,
        tipo_habitacion VARCHAR(50) DEFAULT NULL,
        checkin DATE NOT NULL,
        checkout DATE NOT NULL,
        guest_name VARCHAR(255) NOT NULL,
        adults INT DEFAULT 0,
        children INT DEFAULT 0,
        email VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    $conn->exec($sqlCreate);

    $stmt = $conn->prepare('INSERT INTO reservas (room, price, documento_tipo, documento_numero, tipo_habitacion, checkin, checkout, guest_name, adults, children, email) VALUES (:room, :price, :documento_tipo, :documento_numero, :tipo_habitacion, :checkin, :checkout, :guest_name, :adults, :children, :email)');
    $stmt->bindParam(':room', $room);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':documento_tipo', $documento_tipo);
    $stmt->bindParam(':documento_numero', $documento_numero);
    $stmt->bindParam(':tipo_habitacion', $tipo_habitacion);
    $stmt->bindParam(':checkin', $checkin);
    $stmt->bindParam(':checkout', $checkout);
    $stmt->bindParam(':guest_name', $name);
    $stmt->bindParam(':adults', $adults, PDO::PARAM_INT);
    $stmt->bindParam(':children', $children, PDO::PARAM_INT);
    $stmt->bindParam(':email', $email);

    $stmt->execute();
    $insertId = $conn->lastInsertId();

    echo json_encode(['success' => true, 'message' => 'Reserva guardada', 'id' => $insertId]);
    exit;
} catch (PDOException $ex) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error del servidor: ' . $ex->getMessage()]);
    exit;
}

?>