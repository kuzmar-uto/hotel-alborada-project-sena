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
$id_habitacion = isset($_POST['id_habitacion']) ? trim($_POST['id_habitacion']) : '';
$fecha_entrada = isset($_POST['fecha_entrada']) ? trim($_POST['fecha_entrada']) : '';
$fecha_salida = isset($_POST['fecha_salida']) ? trim($_POST['fecha_salida']) : '';
$nombre_completo = isset($_POST['nombre_completo']) ? trim($_POST['nombre_completo']) : '';
$adultos = isset($_POST['adultos']) ? (int) $_POST['adultos'] : 0;
$ninos = isset($_POST['ninos']) ? (int) $_POST['ninos'] : 0;
$email = isset($_POST['email']) ? trim($_POST['email']) : '';

// información extra de habitación (preview)
$room_name = isset($_POST['room_name']) ? trim($_POST['room_name']) : '';
$room_desc = isset($_POST['room_desc']) ? trim($_POST['room_desc']) : '';
$room_price = isset($_POST['room_price']) ? trim($_POST['room_price']) : '';
$room_img = isset($_POST['room_img']) ? trim($_POST['room_img']) : '';
$room_caracs = isset($_POST['room_caracs']) ? trim($_POST['room_caracs']) : '';
$room_tipo = isset($_POST['room_tipo']) ? trim($_POST['room_tipo']) : '';
$room_disponibles = isset($_POST['room_disponibles']) ? (int) $_POST['room_disponibles'] : 0;
$room_max = isset($_POST['room_max']) ? (int) $_POST['room_max'] : 0;
// validación básica
if (!$id_habitacion || !$fecha_entrada || !$fecha_salida || !$nombre_completo || !$email) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Faltan campos requeridos']);
    exit;
}

if (strtotime($fecha_salida) <= strtotime($fecha_entrada)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Fecha de salida debe ser posterior a entrada']);
    exit;
}

try {
    // crear tabla si no existe (es una suposición razonable)
    $sqlCreate = "CREATE TABLE IF NOT EXISTS reservas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_habitacion VARCHAR(255) NOT NULL,
        fecha_entrada DATE NOT NULL,
        fecha_salida DATE NOT NULL,
        nombre_completo VARCHAR(255) NOT NULL,
        adultos INT DEFAULT 0,
        ninos INT DEFAULT 0,
        email VARCHAR(255) NOT NULL,
        fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        -- campos antiguos/opcionales
        room VARCHAR(255) DEFAULT NULL,
        room_name VARCHAR(255) DEFAULT NULL,
        room_desc TEXT DEFAULT NULL,
        room_price VARCHAR(50) DEFAULT NULL,
        room_img VARCHAR(255) DEFAULT NULL,
        room_caracs TEXT DEFAULT NULL,
        room_tipo VARCHAR(50) DEFAULT NULL,
        room_disponibles INT DEFAULT 0,
        room_max INT DEFAULT 0,
        price VARCHAR(50) DEFAULT NULL,
        documento_tipo VARCHAR(50) DEFAULT NULL,
        documento_numero VARCHAR(100) DEFAULT NULL,
        tipo_habitacion VARCHAR(50) DEFAULT NULL
    ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    $conn->exec($sqlCreate);
    // ensure columns exist on existing table
    $alterSqls = [
        "ALTER TABLE reservas ADD COLUMN IF NOT EXISTS id_habitacion VARCHAR(255) NOT NULL",
        "ALTER TABLE reservas ADD COLUMN IF NOT EXISTS fecha_entrada DATE NOT NULL",
        "ALTER TABLE reservas ADD COLUMN IF NOT EXISTS fecha_salida DATE NOT NULL",
        "ALTER TABLE reservas ADD COLUMN IF NOT EXISTS nombre_completo VARCHAR(255) NOT NULL",
        "ALTER TABLE reservas ADD COLUMN IF NOT EXISTS adultos INT DEFAULT 0",
        "ALTER TABLE reservas ADD COLUMN IF NOT EXISTS ninos INT DEFAULT 0",
        "ALTER TABLE reservas ADD COLUMN IF NOT EXISTS email VARCHAR(255) NOT NULL",
        "ALTER TABLE reservas ADD COLUMN IF NOT EXISTS fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP"
    ];
    foreach ($alterSqls as $a) {
        try { $conn->exec($a); } catch (PDOException $ignored) {}
    }

    // insert only the required Spanish-named fields
    $stmt = $conn->prepare('INSERT INTO reservas (id_habitacion, fecha_entrada, fecha_salida, nombre_completo, adultos, ninos, email) VALUES (:id_habitacion, :fecha_entrada, :fecha_salida, :nombre_completo, :adultos, :ninos, :email)');
    $stmt->bindParam(':id_habitacion', $id_habitacion);
    $stmt->bindParam(':fecha_entrada', $fecha_entrada);
    $stmt->bindParam(':fecha_salida', $fecha_salida);
    $stmt->bindParam(':nombre_completo', $nombre_completo);
    $stmt->bindParam(':adultos', $adultos, PDO::PARAM_INT);
    $stmt->bindParam(':ninos', $ninos, PDO::PARAM_INT);
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