<?php
// Validación del código de recuperación

session_start();

$code_input = isset($_POST['code']) ? trim($_POST['code']) : '';

// Validar que el código no esté vacío
if (empty($code_input)) {
    echo json_encode(['success' => false, 'message' => 'Código requerido']);
    exit;
}

// Validar que exista una sesión de recuperación activa
if (!isset($_SESSION['recovery_code']) || !isset($_SESSION['recovery_code_timestamp'])) {
    echo json_encode(['success' => false, 'message' => 'No hay una sesión de recuperación activa. Solicita un nuevo código.']);
    exit;
}

// Validar que el código no haya expirado (10 minutos = 600 segundos)
$time_elapsed = time() - $_SESSION['recovery_code_timestamp'];
if ($time_elapsed > 600) {
    unset($_SESSION['recovery_code']);
    unset($_SESSION['recovery_email']);
    unset($_SESSION['recovery_code_timestamp']);
    echo json_encode(['success' => false, 'message' => 'El código ha expirado. Solicita uno nuevo.']);
    exit;
}

// Limpiar espacios del código ingresado
$code_without_spaces = str_replace(' ', '', $code_input);

// Comparar con el código almacenado
if ($code_without_spaces === $_SESSION['recovery_code']) {
    // Código válido - limpiar sesión de recuperación
    unset($_SESSION['recovery_code']);
    unset($_SESSION['recovery_code_timestamp']);
    // Mantener el email para el siguiente paso
    echo json_encode(['success' => true, 'message' => 'Código validado correctamente.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Código incorrecto. Por favor intenta de nuevo.']);
}
?>
