<?php
// Envío del código de recuperación

require_once __DIR__ . '/../sentencias_email.php';

$email_usuario = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';

if (empty($email_usuario)) {
    echo json_encode(['success' => false, 'message' => 'Email requerido']);
    exit;
}

// Código fijo de recuperación
$codigo_fijo = '342 405';

// Mensaje de email
$asunto = 'Tu código de recuperación - Hotel Alborada';
$mensaje = "
<html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; background-color: #f5f5f5; margin: 0; }
            .container { max-width: 500px; margin: 30px auto; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
            .header { text-align: center; margin-bottom: 30px; }
            .header h1 { color: #3d8da5; margin: 0; font-size: 28px; }
            .content { text-align: center; }
            .content p { color: #555; font-size: 16px; line-height: 1.6; margin: 15px 0; }
            .code-box { 
                background: linear-gradient(135deg, #3d8da5 0%, #2a6a7f 100%);
                border-radius: 10px; 
                padding: 30px; 
                margin: 30px 0;
                border: 2px solid #3d8da5;
            }
            .code { 
                font-size: 42px; 
                font-weight: bold; 
                color: white; 
                letter-spacing: 8px; 
                font-family: 'Courier New', monospace;
            }
            .instruction { 
                background-color: #f0f8fa; 
                border-left: 4px solid #3d8da5; 
                padding: 15px; 
                margin: 20px 0; 
                color: #333;
            }
            .footer { 
                text-align: center; 
                color: #999; 
                font-size: 12px; 
                margin-top: 30px; 
                padding-top: 20px; 
                border-top: 1px solid #eee;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🔐 Código de Recuperación</h1>
            </div>
            <div class='content'>
                <p>Hola,</p>
                <p>Recibimos una solicitud para recuperar tu contraseña en <strong>Hotel Alborada</strong>.</p>
                <p>Usa el siguiente código para completar el proceso de recuperación:</p>
                
                <div class='code-box'>
                    <div class='code'>$codigo_fijo</div>
                </div>
                
                <div class='instruction'>
                    <strong>Instrucciones:</strong>
                    <p style='margin: 10px 0;'>1. Copia el código anterior</p>
                    <p style='margin: 10px 0;'>2. Ingresa el código en la pantalla de recuperación</p>
                    <p style='margin: 10px 0;'>3. Crea una nueva contraseña</p>
                </div>
                
                <p style='color: #d32f2f; font-weight: bold;'>⚠️ Este código expira en 10 minutos</p>
                <p style='font-size: 14px; color: #666;'>Si no solicitaste cambiar tu contraseña, ignora este correo.</p>
            </div>
            <div class='footer'>
                <p>© 2026 Hotel Alborada Melgar - Todos los derechos reservados</p>
            </div>
        </div>
    </body>
</html>
";

// Enviar correo
if (enviarCorreo($email_usuario, $asunto, $mensaje)) {
    echo json_encode([
        'success' => true, 
        'message' => '✓ Código enviado a tu email',
        'code_sent' => true
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Error al enviar el correo'
    ]);
}

?>
