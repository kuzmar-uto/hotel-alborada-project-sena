<?php
include 'config/conexion.php';
header('Content-Type: application/json');

$result = $conn->query("SELECT * FROM habitaciones ORDER BY nombre");
$habitaciones = [];

while ($row = $result->fetch_assoc()) {
    $habitaciones[] = $row;
}

echo json_encode($habitaciones);
$conn->close();