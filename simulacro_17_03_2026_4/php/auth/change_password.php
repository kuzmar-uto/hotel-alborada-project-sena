<?php
// Cambiar contraseña después de recuperación

session_start();

$newPassword = isset($_POST['newPassword']) ? trim($_POST['newPassword']) : '';

// Validar que la contraseña no esté vacía
if (empty($newPassword)) {
    echo json_encode(['success' => false, 'message' => 'Contraseña requerida']);
    exit;
}

// Validar longitud mínima
if (strlen($newPassword) < 6) {
    echo json_encode(['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres']);
    exit;
}

// Validar que exista una sesión de recuperación con email
if (!isset($_SESSION['recovery_email'])) {
    echo json_encode(['success' => false, 'message' => 'Sesión de recuperación inválida. Solicita un nuevo código.']);
    exit;
}

$email = $_SESSION['recovery_email'];

require_once __DIR__ . '/../config/database.php';

$database = new Database();
$pdo = $database->getConnection();

try {
    // Verificar que el usuario existe
    $stmt = $pdo->prepare("SELECT id FROM usuarios_alborada WHERE Correo = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
        exit;
    }

    // Hash de la nueva contraseña
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Actualizar la contraseña
    $stmt = $pdo->prepare("UPDATE usuarios_alborada SET Contraseña = ? WHERE Correo = ?");
    $stmt->execute([$hashedPassword, $email]);

    // Limpiar sesión de recuperación
    unset($_SESSION['recovery_email']);

    echo json_encode(['success' => true, 'message' => 'Contraseña cambiada exitosamente.']);
} catch (Exception $e) {
    error_log("Error cambiando contraseña: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
}
?>