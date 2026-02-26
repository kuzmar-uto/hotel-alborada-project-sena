<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: ../cuenta.html');
    exit;
}

$database = new Database();
$db = $database->getConnection();

$query = "SELECT correo FROM usuarios_alborada WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $_SESSION['usuario_id']);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es-CO">
<head>
<meta charset="UTF-8">
<title>Mi Cuenta</title>
</head>
<body>
<h1>Bienvenido <?php echo $usuario['correo']; ?></h1>
<a href="auth/logout.php">Cerrar sesión</a>
</body>
</html>
