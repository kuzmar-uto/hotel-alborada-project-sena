<?php
// Prueba de envío de correo con PHPMailer y Gmail

require_once __DIR__ . '/php/sentencias_email.php';

// Datos de prueba
$destinatario = 'kuzmarpolo9@gmail.com';
$nombre_cliente = 'Usuario de Prueba';
$numero_reserva = 'RES-2026-001';
$detalles_reserva = "Entrada: 12/03/2026\nSalida: 15/03/2026\nHabitación: Suite Deluxe\nDuración: 3 noches";

// Enviar correo de prueba
echo "<h2>Prueba de Envío de Correo</h2>";

if (enviarConfirmacionReserva($destinatario, $nombre_cliente, $numero_reserva, $detalles_reserva)) {
    echo "<p style='color: green; font-size: 18px;'><strong>✓ Correo enviado exitosamente a: " . $destinatario . "</strong></p>";
    echo "<p>El correo de confirmación de reserva ha sido enviado.</p>";
    echo "<hr>";
    echo "<h3>Detalles del envío:</h3>";
    echo "<ul>";
    echo "<li><strong>De:</strong> balatrez64@gmail.com (Hotel Alborada)</li>";
    echo "<li><strong>Para:</strong> $destinatario</li>";
    echo "<li><strong>Asunto:</strong> Confirmación de Reserva #$numero_reserva</li>";
    echo "<li><strong>Tipo:</strong> Confirmación de Reserva</li>";
    echo "</ul>";
} else {
    echo "<p style='color: red; font-size: 18px;'><strong>✗ Error al enviar el correo</strong></p>";
    echo "<p>Por favor, verifica:</p>";
    echo "<ul>";
    echo "<li>La contraseña sea correcta</li>";
    echo "<li>La cuenta de Gmail tenga habilitado el acceso de aplicaciones menos seguras</li>";
    echo "<li>O usa una contraseña de aplicación de Google en lugar de la contraseña real</li>";
    echo "</ul>";
}

?>
