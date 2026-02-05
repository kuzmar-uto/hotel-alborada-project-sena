<?php
session_start();
header('Content-Type: application/json');

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener y sanitizar datos
    $fullname = filter_var($_POST['fullname'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);

    // Validaciones
    $errors = [];

    if (empty($fullname) || empty($email) || empty($password)) {
        $errors[] = "Todos los campos son obligatorios.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "El formato del email no es válido.";
    }

    if (strlen($password) < 6) {
        $errors[] = "La contraseña debe tener al menos 6 caracteres.";
    }

    if (!empty($errors)) {
        echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
        exit;
    }

    try {
        $database = new Database();
        $db = $database->getConnection();

        // Verificar si el email ya existe
        $checkQuery = "SELECT id FROM usuarios_alborada WHERE Correo = :email";
        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->bindParam(':email', $email);
        $checkStmt->execute();

        if ($checkStmt->rowCount() > 0) {
            echo json_encode(['success' => false, 'message' => 'Ya existe una cuenta con este email.']);
            exit;
        }

        // Hash de la contraseña
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insertar nuevo usuario en usuarios_alborada
        $insertQuery = "INSERT INTO usuarios_alborada (Correo, Contraseña) 
                       VALUES (:email, :password)";
        
        $insertStmt = $db->prepare($insertQuery);
        $insertStmt->bindParam(':email', $email);
        $insertStmt->bindParam(':password', $hashed_password);

        if ($insertStmt->execute()) {
            // Obtener el ID del nuevo usuario
            $usuario_id = $db->lastInsertId();
            
            // Iniciar sesión automáticamente después del registro
            $_SESSION['usuario_id'] = $usuario_id;
            $_SESSION['usuario_email'] = $email;
            $_SESSION['usuario_nombre'] = $fullname;
            $_SESSION['logged_in'] = true;

            echo json_encode([
                'success' => true, 
                'message' => '¡Registro exitoso! Bienvenido a Alborada Hotel.',
                'redirect' => 'dashboard.php'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al crear la cuenta. Intenta nuevamente.']);
        }

    } catch (PDOException $exception) {
        echo json_encode(['success' => false, 'message' => 'Error del servidor: ' . $exception->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}
?>