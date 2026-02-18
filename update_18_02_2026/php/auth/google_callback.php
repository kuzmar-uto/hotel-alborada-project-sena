<?php
session_start();
header('Content-Type: application/json');

// Config y base de datos
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/google.php';

// Leer JSON entrante
$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['id_token'])) {
    echo json_encode(['success' => false, 'message' => 'Falta id_token.']);
    exit;
}

$id_token = $input['id_token'];

// Verificar token con Google (tokeninfo)
$tokeninfo_url = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($id_token);
$response = @file_get_contents($tokeninfo_url);
if ($response === false) {
    echo json_encode(['success' => false, 'message' => 'No se pudo validar el token con Google.']);
    exit;
}

$payload = json_decode($response, true);

// Comprobar audiencia
if (!isset($payload['aud']) || $payload['aud'] !== GOOGLE_CLIENT_ID) {
    echo json_encode(['success' => false, 'message' => 'Client ID inválido para este token.']);
    exit;
}

// Verificar email
if (!isset($payload['email']) || (!isset($payload['email_verified']) || ($payload['email_verified'] !== 'true' && $payload['email_verified'] !== true))) {
    echo json_encode(['success' => false, 'message' => 'Email no verificado por Google.']);
    exit;
}

$email = $payload['email'];

try {
    $database = new Database();
    $db = $database->getConnection();

    // Buscar usuario existente
    $stmt = $db->prepare('SELECT id FROM usuarios_alborada WHERE Correo = :email');
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $userId = $row['id'];

        // Actualizar Contraseña a NULL para indicar cuenta gestionada por Google (opcional)
        $upd = $db->prepare('UPDATE usuarios_alborada SET Contraseña = NULL WHERE id = :id');
        $upd->bindParam(':id', $userId);
        $upd->execute();
    } else {
        // Insertar nuevo usuario con Contraseña = NULL
        $ins = $db->prepare('INSERT INTO usuarios_alborada (Correo, Contraseña) VALUES (:email, NULL)');
        $ins->bindParam(':email', $email);
        $ins->execute();
        $userId = $db->lastInsertId();
    }

    // Iniciar sesión
    $_SESSION['usuario_id'] = $userId;
    $_SESSION['usuario_email'] = $email;
    $_SESSION['logged_in'] = true;

    echo json_encode(['success' => true, 'message' => 'Login con Google exitoso.', 'redirect' => 'php/dashboard.php']);
    exit;

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error de BD: ' . $e->getMessage()]);
    exit;
}

?>
