<?php
$servername = "localhost";
$username = "root";          // Cambia si tienes otro usuario
$password = "";              // Tu contraseña (muchos usan vacío en local)
$dbname = "hotel_la_alborada";  // Cambia al nombre de tu BD (ej: la_alborada, hotel_db, etc.)

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "error" => "Conexión fallida: " . $conn->connect_error]));
}

// Para usar UTF-8
$conn->set_charset("utf8mb4");
?>