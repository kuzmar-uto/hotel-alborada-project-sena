<?php
session_start();

// Destruir todas las variables de sesión
$_SESSION = array();

// Si se desea destruir la sesión completamente, borre también la cookie de sesión.
if (ini_get(option: "session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(name: session_name(), value: '', expires_or_options: time() - 42000,
        path: $params["path"], domain: $params["domain"],
        secure: $params["secure"], httponly: $params["httponly"]
    );
}

// Destruir la sesión
session_destroy();

// Eliminar cookie de recordar
setcookie(name: 'hotel_remember', value: '', expires_or_options: time() - 3600, path: "/");

// Redirigir al login
header(header: 'Location: ../../cuenta.html');
exit;
?>