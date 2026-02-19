<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();

    $usuario_id = $_SESSION['usuario_id'];

    // Obtener información del usuario
    $stmt = $db->prepare('SELECT id, Correo, Nombre, Telefono, Contraseña FROM usuarios_alborada WHERE id = :id');
    $stmt->bindParam(':id', $usuario_id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Determinar si es usuario de Google (sin contraseña real)
        $is_google_user = empty($user['Contraseña']) || $user['Contraseña'] === '';
        
        echo json_encode([
            'success' => true,
            'user' => [
                'id' => $user['id'],
                'email' => $user['Correo'],
                'nombre' => $user['Nombre'] ?? 'Usuario',
                'telefono' => $user['Telefono'] ?? '',
                'is_google_user' => $is_google_user
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error de BD: ' . $e->getMessage()]);
    exit;
}

?>
