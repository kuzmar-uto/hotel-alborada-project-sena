<?php
// login.php — versión que inyecta GOOGLE_CLIENT_ID desde configuración
require_once __DIR__ . '/php/config/google.php';
?>
<!DOCTYPE html>
<html lang="es-CO">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#3d8da5">
    <title>Iniciar Sesión - Alborada Hotel Melgar</title>
    <meta name="description" content="Inicia sesión en Alborada Hotel para acceder a tu cuenta y gestionar tus reservas.">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <meta name="google-client-id" content="<?php echo defined('GOOGLE_CLIENT_ID') ? GOOGLE_CLIENT_ID : 'YOUR_GOOGLE_CLIENT_ID.apps.googleusercontent.com'; ?>">
    <style>
        /* Estilos específicos para la página de login (copiados de login.html) */
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(rgba(61, 141, 165, 0.1), rgba(166, 65, 147, 0.1));
            padding: 40px 20px;
        }

        .login-box {
            background: var(--white);
            padding: 50px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: fit-content;
            text-align: center;
        }

        .login-logo {
            margin-bottom: 30px;
        }

        .login-logo img {
            max-height: 80px;
        }

        .login-title {
            color: var(--dark);
            margin-bottom: 10px;
            font-size: 2rem;
        }

        .login-subtitle {
            color: var(--text);
            margin-bottom: 30px;
            font-size: 1rem;
        }

        .form-group {
            margin-bottom: 25px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--dark);
            font-weight: 600;
            font-size: 0.9rem;
        }

        .form-control {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: var(--transition);
            background-color: var(--light);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            background-color: var(--white);
        }

        .password-toggle {
            position: relative;
        }

        .password-toggle .toggle-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--text);
            background: none;
            border: none;
            font-size: 1rem;
            z-index: 2;
        }

        .login-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            font-size: 0.9rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .remember-me input {
            margin: 0;
        }

        .forgot-password {
            color: var(--primary);
            text-decoration: none;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        .login-btn {
            width: 100%;
            padding: 15px;
            background-color: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            margin-bottom: 20px;
        }

        .login-btn:hover {
            background-color: #2d7a8c;
            transform: translateY(-2px);
        }

        .divider {
            margin: 30px 0;
            position: relative;
            text-align: center;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background-color: #e0e0e0;
        }

        .divider span {
            background: var(--white);
            padding: 0 15px;
            color: var(--text);
            font-size: 0.9rem;
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
        }

        .register-link a {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .back-to-home {
            display: inline-block;
            margin-top: 20px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .back-to-home:hover {
            text-decoration: underline;
        }

        /* Modal de recuperación de contraseña */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: var(--white);
            padding: 40px;
            border-radius: 15px;
            width: 90%;
            max-width: 400px;
            text-align: center;
            position: relative;
        }

        .modal h3 {
            margin-bottom: 15px;
            color: var(--dark);
        }

        .modal p {
            margin-bottom: 25px;
            color: var(--text);
        }

        .close-modal {
            position: absolute;
            top: 15px;
            right: 15px;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .login-box {
                padding: 30px 20px;
            }

            .login-options {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
        }
    </style>
</head>

<body>
    <?php
    // Incluir el HTML del formulario de login (se reproduce desde login.html para mantener diseño)
    ?>
    <?php readfile(__DIR__ . '/login_fragment.html'); // archivo auxiliar con el cuerpo HTML (si existe) ?>

    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <script>
        // Manejo del response del cliente Google
        window.handleCredentialResponse = function (response) {
            if (!response || !response.credential) {
                alert('Error al recibir credencial de Google.');
                return;
            }

            fetch('php/auth/google_callback.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_token: response.credential })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        window.location.href = data.redirect || 'php/dashboard.php';
                    } else {
                        alert(data.message || 'No se pudo autenticar con Google.');
                    }
                })
                .catch(err => { console.error(err); alert('Error en la verificación con el servidor.'); });
        };

        // Inicializar SDK cuando esté disponible
        (function initGoogleWithRetry() {
            const meta = document.querySelector('meta[name="google-client-id"]');
            const clientId = meta ? meta.getAttribute('content') : 'YOUR_GOOGLE_CLIENT_ID.apps.googleusercontent.com';
            let attempts = 0, maxAttempts = 20, delayMs = 200;
            function tryInit() {
                attempts++;
                if (window.google && google.accounts && google.accounts.id) {
                    google.accounts.id.initialize({ client_id: clientId, callback: handleCredentialResponse });
                    const btn = document.getElementById('googleSignInButton');
                    if (btn) google.accounts.id.renderButton(btn, { theme: 'outline', size: 'large' });
                    return;
                }
                if (attempts < maxAttempts) setTimeout(tryInit, delayMs);
            }
            tryInit();
        })();

        // Manejador del formulario de login tradicional
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            if (loginForm) {
                loginForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const email = document.getElementById('email').value;
                    const password = document.getElementById('password').value;
                    const remember = document.getElementById('remember').checked;
                    
                    const formData = new FormData();
                    formData.append('email', email);
                    formData.append('password', password);
                    formData.append('remember', remember ? '1' : '0');
                    
                    fetch('php/auth/login.php', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert(data.message || 'Login exitoso');
                                window.location.href = data.redirect || 'index.php';
                            } else {
                                alert(data.message || 'Error al iniciar sesión');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error de conexión. Intenta nuevamente.');
                        });
                });
            }
            
            // Manejador del toggle de contraseña
            const togglePassword = document.getElementById('togglePassword');
            if (togglePassword) {
                togglePassword.addEventListener('click', function() {
                    const passwordInput = document.getElementById('password');
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    this.innerHTML = type === 'password' ? '<i class="far fa-eye"></i>' : '<i class="far fa-eye-slash"></i>';
                });
            }
            
            // Manejador del enlace "Olvide mi contraseña"
            const forgotPasswordLink = document.getElementById('forgotPassword');
            if (forgotPasswordLink) {
                forgotPasswordLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    const modal = document.getElementById('passwordModal');
                    if (modal) modal.classList.add('active');
                });
            }
            
            // Cerrar modal
            const closeModal = document.getElementById('closeModal');
            if (closeModal) {
                closeModal.addEventListener('click', function() {
                    const modal = document.getElementById('passwordModal');
                    if (modal) modal.classList.remove('active');
                });
            }
        });
    </script>
</body>

</html>
