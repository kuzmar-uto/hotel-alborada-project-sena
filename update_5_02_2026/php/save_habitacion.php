<?php
include 'config/conexion.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['nombre']) || !isset($data['precio'])) {
    echo json_encode(["success" => false, "error" => "Faltan campos obligatorios (nombre y precio)"]);
    exit;
}

$nombre         = $conn->real_escape_string($data['nombre']);
$descripcion    = $conn->real_escape_string($data['descripcion'] ?? '');
$caracteristicas = $conn->real_escape_string($data['caracteristicas'] ?? '');
$precio         = floatval($data['precio']);
$imagen         = $conn->real_escape_string($data['imagen'] ?? null); // por ahora null

if (empty($data['id'])) {
    // INSERT
    $sql = "INSERT INTO habitaciones 
            (nombre, descripcion, caracteristicas, precio, imagen) 
            VALUES ('$nombre', '$descripcion', '$caracteristicas', $precio, " . 
            ($imagen ? "'$imagen'" : "NULL") . ")";
} else {
    // UPDATE
    $id = intval($data['id']);
    $sql = "UPDATE habitaciones SET 
            nombre = '$nombre',
            descripcion = '$descripcion',
            caracteristicas = '$caracteristicas',
            precio = $precio,
            imagen = " . ($imagen ? "'$imagen'" : "NULL") . "
            WHERE id_habitacion = $id";
}

if ($conn->query($sql)) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $conn->error]);
}

$conn->close();