<?php
session_start();
header('Content-Type: application/json');

// Incluir configuración de base de datos
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']) ? true : false;

    // Validaciones básicas
    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Por favor, complete todos los campos.']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'El formato del email no es válido.']);
        exit;
    }

    try {
        // Conectar a la base de datos
        $database = new Database();
        $db = $database->getConnection();

        // Buscar usuario por correo en usuarios_alborada
        $query = "SELECT id, Correo, Contraseña FROM usuarios_alborada WHERE Correo = :email";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificar contraseña
            if (password_verify($password, $usuario['Contraseña'])) {

                // Iniciar sesión
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_email'] = $usuario['Correo'];
                $_SESSION['logged_in'] = true;

                // Cookie para recordar sesión (30 días)
                if ($remember) {
                    $cookie_value = base64_encode($usuario['id'] . ':' . hash('sha256', $usuario['Contraseña']));
                    setcookie('hotel_remember', $cookie_value, time() + (30 * 24 * 60 * 60), "/");
                }

                echo json_encode([
                    'success' => true,
                    'message' => '¡Login exitoso!',
                    'redirect' => '/update_18_02_2026/index.php'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Contraseña incorrecta.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'No existe una cuenta con este email.']);
        }
    } catch (PDOException $exception) {
        echo json_encode(['success' => false, 'message' => 'Error del servidor: ' . $exception->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}
?>