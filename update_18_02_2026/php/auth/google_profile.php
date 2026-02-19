<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../index.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    $usuario_id = $_SESSION['usuario_id'];

    // Obtener información del usuario
    $stmt = $db->prepare('SELECT id, Correo, Nombre, Telefono FROM usuarios_alborada WHERE id = :id');
    $stmt->bindParam(':id', $usuario_id);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        header('Location: ../../index.php');
        exit;
    }

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $nombre = $user['Nombre'] ?? 'Usuario';
    $email = $user['Correo'];
    $telefono = $user['Telefono'] ?? '';

} catch (PDOException $e) {
    header('Location: ../../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es-CO">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil de Google - Alborada Hotel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../styles.css">
    <style>
        .profile-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(rgba(61, 141, 165, 0.1), rgba(166, 65, 147, 0.1));
            padding: 40px 20px;
            margin-top: 80px;
        }

        .profile-box {
            background: var(--white);
            padding: 50px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }

        .profile-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3d8da5, #ffd500);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: var(--white);
            font-size: 45px;
        }

        .profile-header h1 {
            color: var(--dark);
            margin-bottom: 10px;
            font-size: 1.8rem;
        }

        .profile-header .google-badge {
            display: inline-block;
            background-color: #DB4437;
            color: var(--white);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-top: 10px;
            gap: 5px;
            display: inline-flex;
            align-items: center;
        }

        .profile-info {
            margin-bottom: 30px;
        }

        .info-item {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 8px;
            border-left: 4px solid var(--primary);
        }

        .info-label {
            color: var(--primary);
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .info-value {
            color: var(--dark);
            font-size: 1.1rem;
            word-break: break-all;
        }

        .profile-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn-profile {
            flex: 1;
            padding: 15px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-profile.primary {
            background-color: var(--primary);
            color: var(--white);
        }

        .btn-profile.primary:hover {
            background-color: #2d7a8c;
            transform: translateY(-2px);
        }

        .btn-profile.secondary {
            background-color: #e0e0e0;
            color: var(--dark);
        }

        .btn-profile.secondary:hover {
            background-color: #d0d0d0;
            transform: translateY(-2px);
        }

        .btn-profile.danger {
            background-color: #dc3545;
            color: var(--white);
        }

        .btn-profile.danger:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }

        .profile-header {
            position: relative;
        }

        .back-btn {
            position: absolute;
            top: 0;
            left: 0;
            background: none;
            border: none;
            color: var(--primary);
            font-size: 1.5rem;
            cursor: pointer;
            padding: 10px;
        }

        .back-btn:hover {
            color: #2d7a8c;
        }

        .reservas-section {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid #e0e0e0;
        }

        .reservas-section h3 {
            color: var(--dark);
            margin-bottom: 20px;
        }

        .reserva-item {
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 8px;
            border-left: 4px solid var(--accent);
            margin-bottom: 15px;
        }

        @media (max-width: 768px) {
            .profile-box {
                padding: 30px 20px;
            }

            .profile-header h1 {
                font-size: 1.5rem;
            }

            .profile-actions {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header>
        <div class="container">
            <div id="main-header">
                <div class="logo">
                    <a href="../../index.php">
                        <img src="https://alboradahotel.com/wp-content/uploads/2023/09/Alborada-Original-en-MELGAR-hoteles-en-melgar.png"
                            alt="Alborada Hotel Melgar">
                    </a>
                </div>
                <nav>
                    <button class="mobile-menu-btn"><i class="fas fa-bars"></i></button>
                    <ul>
                        <li><a href="../../index.php#home">Inicio</a></li>
                        <li><a href="../../index.php#about">Nosotros</a></li>
                        <li><a href="../../habitaciones.php">Habitaciones</a></li>
                        <li><a href="../../index.php#services">Servicios</a></li>
                        <li><a href="../../index.php#contact">Contacto</a></li>
                        <li class="account-menu">
                            <a href="../../cuenta.html" class="btn btn-account">Mi Cuenta</a>
                            <a href="google_profile.php" id="google-profile-btn" class="google-profile-icon" title="Mi Perfil">
                                <i class="fab fa-google"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
                <div class="overlay"></div>
            </div>
        </div>
    </header>

    <!-- Profile Section -->
    <section class="profile-container">
        <div class="profile-box">
            <div class="profile-header">
                <button class="back-btn" onclick="history.back()"><i class="fas fa-arrow-left"></i></button>
                <div class="profile-avatar">
                    <i class="fab fa-google"></i>
                </div>
                <h1><?php echo htmlspecialchars($nombre); ?></h1>
                <div class="google-badge">
                    <i class="fab fa-google"></i> Cuenta de Google
                </div>
            </div>

            <div class="profile-info">
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-envelope"></i> Email</div>
                    <div class="info-value"><?php echo htmlspecialchars($email); ?></div>
                </div>

                <?php if (!empty($telefono)): ?>
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-phone"></i> Teléfono</div>
                    <div class="info-value"><?php echo htmlspecialchars($telefono); ?></div>
                </div>
                <?php endif; ?>

                <div class="info-item">
                    <div class="info-label"><i class="fas fa-id-card"></i> ID de Usuario</div>
                    <div class="info-value">#<?php echo htmlspecialchars($usuario_id); ?></div>
                </div>
            </div>

            <div class="profile-actions">
                <a href="../../cuenta.html" class="btn-profile primary">
                    <i class="fas fa-edit"></i> Editar Perfil
                </a>
                <a href="logout.php" class="btn-profile danger">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </a>
            </div>
        </div>
    </section>

    <script src="../../script.js"></script>
</body>

</html>
