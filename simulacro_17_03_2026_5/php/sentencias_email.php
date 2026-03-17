<?php
// Funciones para enviar correos con PHPMailer

require_once __DIR__ . '/config/email.php';
require_once __DIR__ . '/../PHPmailer/PHPMailer.php';
require_once __DIR__ . '/../PHPmailer/SMTP.php';
require_once __DIR__ . '/../PHPmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Envía un correo electrónico usando PHPMailer y Gmail
 * 
 * @param string $destinatario Email del destinatario
 * @param string $asunto Asunto del correo
 * @param string $mensaje Contenido del correo (en HTML)
 * @param array $adjuntos (Opcional) Array con rutas de archivos a adjuntar
 * @return bool true si se envió correctamente, false en caso contrario
 */
function enviarCorreo($destinatario, $asunto, $mensaje, $adjuntos = array()) {
    try {
        $mail = new PHPMailer(true);
        
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = EMAIL_FROM;
        $mail->Password = EMAIL_PASSWORD;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port = SMTP_PORT;
        
        // Remitente
        $mail->setFrom(EMAIL_FROM, EMAIL_FROM_NAME);
        
        // Destinatario
        $mail->addAddress($destinatario);
        
        // Adjuntos (si los hay)
        if (!empty($adjuntos)) {
            foreach ($adjuntos as $ruta_archivo) {
                if (file_exists($ruta_archivo)) {
                    $mail->addAttachment($ruta_archivo);
                }
            }
        }
        
        // Contenido del correo
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $asunto;
        $mail->Body = $mensaje;
        
        // Texto alternativo para clientes que no soportan HTML
        $mail->AltBody = strip_tags($mensaje);
        
        // Enviar
        if ($mail->send()) {
            return true;
        } else {
            return false;
        }
        
    } catch (Exception $e) {
        error_log("Error al enviar correo: " . $e->getMessage());
        return false;
    }
}

/**
 * Envía un correo de confirmación de reserva
 */
function enviarConfirmacionReserva($destinatario, $nombre_cliente, $numero_reserva, $detalles_reserva) {
    $asunto = "Confirmación de Reserva #" . $numero_reserva;
    
    $mensaje = "
    <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                .container { max-width: 600px; margin: 0 auto; border: 1px solid #ddd; padding: 20px; }
                .header { background-color: #4CAF50; color: white; padding: 10px; text-align: center; }
                .content { padding: 20px; }
                .footer { background-color: #f0f0f0; padding: 10px; text-align: center; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Confirmación de Reserva</h2>
                </div>
                <div class='content'>
                    <p>¡Hola, $nombre_cliente!</p>
                    <p>Tu reserva ha sido confirmada exitosamente.</p>
                    <p><strong>Número de Reserva:</strong> $numero_reserva</p>
                    <p><strong>Detalles:</strong></p>
                    <pre>$detalles_reserva</pre>
                    <p>Si tienes alguna pregunta, no dudes en contactarnos.</p>
                </div>
                <div class='footer'>
                    <p>© 2026 Hotel Alborada. Todos los derechos reservados.</p>
                </div>
            </div>
        </body>
    </html>
    ";
    
    return enviarCorreo($destinatario, $asunto, $mensaje);
}

?>
