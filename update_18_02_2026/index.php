<?php
// Conexión a la base de datos
$servername = "127.0.0.1";
$username   = "root";
$password   = "";
$dbname     = "alborada";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>
<!DOCTYPE html>
<html lang="es-CO">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Alborada Hotel">
    <meta name="theme-color" content="#3d8da5">
    <title>Alborada Hotel en Melgar - Uno de los mejores hoteles en Melgar Tolima</title>
    <meta name="description"
        content="Disfruta del mejor hotel en Melgar con piscinas, tobogán, sauna, jacuzzi, restaurante y eventos. Reserva ahora tu estadía en Alborada Hotel.">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <!-- Top Bar removed -->

    <!-- Header -->
    <header>
        <div class="container">
            <div id="main-header">
                <div class="logo">
                    <a href="#">
                        <img src="https://alboradahotel.com/wp-content/uploads/2023/09/Alborada-Original-en-MELGAR-hoteles-en-melgar.png"
                            alt="Alborada Hotel Melgar">
                    </a>
                </div>
                <nav>
                    <button class="mobile-menu-btn"><i class="fas fa-bars"></i></button>
                    <ul>
                        <li><a href="#home">Inicio</a></li>
                        <li><a href="#about">Nosotros</a></li>
                        <li><a href="habitaciones.php">Habitaciones</a></li>
                        <li><a href="#services">Servicios</a></li>
                        <li><a href="#contact">Contacto</a></li>
                        <li class="account-menu">
                            <a href="cuenta.html" class="btn btn-account">Mi Cuenta</a>
                            <a href="#" id="google-profile-btn" class="google-profile-icon" title="Perfil de Google">
                                <i class="fab fa-google"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
                <div class="overlay"></div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Alborada Hotel en Melgar</h1>
                <p>Disfruta de uno de los mejores hoteles en Melgar con dos piscinas, tobogán, sauna, turco, jacuzzi,
                    bar, gimnasio, restaurante a la carta y más.</p>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about">
        <div class="container">
            <div class="section-title">
                <h2>Bienvenido a Alborada Hotel</h2>
            </div>
            <div class="about-content">
                <div class="about-text">
                    <h3>Tu destino de descanso en Melgar</h3>
                    <p>Ubicado en el corazón de Melgar, Tolima, Alborada Hotel te ofrece la combinación perfecta entre
                        confort, diversión y descanso. Con más de 10 años de experiencia en el sector turístico, nos
                        hemos consolidado como uno de los hoteles preferidos por las familias colombianas.</p>
                    <p>Nuestras instalaciones cuentan con amplias zonas verdes, dos piscinas (una para adultos y otra
                        para niños), tobogán acuático, sauna, turco, jacuzzi, gimnasio, bar y un restaurante con
                        deliciosa comida a la carta.</p>
                    <p>Ya sea que busques un fin de semana de relax, celebrar un evento especial o simplemente escapar
                        de la rutina, en Alborada Hotel encontrarás todo lo que necesitas para una experiencia
                        inolvidable.</p>
                    <a href="#contact" class="btn">Contáctanos</a>
                </div>
                <div class="about-image">
                    <img src="https://alboradahotel.com/wp-content/uploads/2023/09/HOTEL-EN-MELGAR-ALBORADA-HOTEL-6.jpg"
                        alt="Alborada Hotel Melgar">
                </div>
            </div>
        </div>
    </section>

    <!-- Rooms Section - DINÁMICO -->
    <section id="rooms">
        <div class="container">
            <div class="section-title">
                <h2>Nuestras Habitaciones</h2>
                <p>Elige entre nuestras cómodas habitaciones diseñadas para tu máximo confort</p>
            </div>
            <div class="rooms-grid">
                <?php
                $result = $conn->query("SELECT * FROM habitaciones ORDER BY precio ASC LIMIT 6");

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()):
                        $img = !empty($row['imagen']) ? $row['imagen'] : 'https://via.placeholder.com/500x400/cccccc/ffffff?text=Habitación';
                        $nombre = htmlspecialchars($row['nombre']);
                        $descripcion = htmlspecialchars($row['descripcion']);
                        $precio = number_format($row['precio'], 0);
                        $tipo = htmlspecialchars($row['tipo_de_habitacion'] ?? 'Estándar');
                        $disponibles = isset($row['habitaciones_disponibles']) ? intval($row['habitaciones_disponibles']) : 0;
                ?>
                <div class="room-card">
                    <div class="room-image">
                        <img src="<?= htmlspecialchars($img) ?>" alt="<?= $nombre ?>">
                        <?php if ($disponibles > 0): ?>
                            <span class="availability-badge">Disponible</span>
                        <?php else: ?>
                            <span class="availability-badge unavailable">No disponible</span>
                        <?php endif; ?>
                    </div>
                    <div class="room-info">
                        <div class="room-header">
                            <h3><?= $nombre ?></h3>
                            <span class="room-type"><?= $tipo ?></span>
                        </div>
                        <p><?= $descripcion ?></p>
                        <div class="room-price">
                            <span class="price">$<?= $precio ?></span>
                            <span class="per-night">por noche</span>
                        </div>
                        <a href="habitaciones.php" class="btn">Ver Detalles</a>
                    </div>
                </div>
                <?php 
                    endwhile;
                } else {
                    echo '<div style="grid-column: 1/-1; text-align: center; padding: 40px;">
                            <p style="color: #999; font-size: 1.1rem;">No hay habitaciones disponibles en este momento</p>
                          </div>';
                }
                ?>
            </div>
            <div style="text-align: center; margin-top: 50px;">
                <a href="habitaciones.php" class="btn" style="background-color: var(--primary);">Ver Todas las Habitaciones</a>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services">
        <div class="container">
            <div class="section-title">
                <h2>Nuestros Servicios</h2>
                <p>Disfruta de todas nuestras instalaciones y servicios durante tu estadía</p>
            </div>
           
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon"><i class="fas fa-swimming-pool"></i></div>
                    <h3>Piscinas y Tobogán</h3>
                    <p>Dos piscinas, una para adultos y otra para niños, con tobogán acuático para diversión garantizada.</p>
                </div>
               
                <div class="service-card">
                    <div class="service-icon"><i class="fas fa-spa"></i></div>
                    <h3>Zona de Spa</h3>
                    <p>Relájate en nuestro sauna, turco y jacuzzi después de un día de diversión en las piscinas.</p>
                </div>
               
                <div class="service-card">
                    <div class="service-icon"><i class="fas fa-utensils"></i></div>
                    <h3 style="font-size: 1.3rem; word-break: break-word;">Restaurante</h3>
                    <p>Disfruta de deliciosos platos a la carta en nuestro restaurante con los mejores sabores de la región.</p>
                </div>
               
                <div class="service-card">
                    <div class="service-icon"><i class="fas fa-dumbbell"></i></div>
                    <h3>Gimnasio</h3>
                    <p>Mantén tu rutina de ejercicios en nuestro gimnasio completamente equipado.</p>
                </div>
               
                <div class="service-card">
                    <div class="service-icon"><i class="fas fa-cocktail"></i></div>
                    <h3>Bar</h3>
                    <p>Disfruta de una amplia variedad de bebidas y cócteles en nuestro acogedor bar.</p>
                </div>
               
                <div class="service-card">
                    <div class="service-icon"><i class="fas fa-parking"></i></div>
                    <h3>Parqueadero Gratuito</h3>
                    <p>Estacionamiento seguro y gratuito para todos nuestros huéspedes durante su estadía.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact">
        <div class="container">
            <div class="section-title">
                <h2>Contáctanos</h2>
                <p>Estamos aquí para ayudarte a planificar tu estadía perfecta</p>
            </div>
            <div class="contact-container">
                <div class="contact-info">
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h3>Dirección</h3>
                            <p>Calle 7 # 7-18, Melgar, Tolima</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div>
                            <h3>Teléfonos</h3>
                            <p>(60) 82450096</p>
                            <p>3204276289</p>
                            <p>3176210927 (WhatsApp)</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <h3>Email</h3>
                            <p>recepcion@alboradahotel.com</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <h3>Horario de Atención</h3>
                            <p>Lunes a Domingo: 7:00 AM - 10:00 PM</p>
                        </div>
                    </div>
                </div>
                <div class="map-container">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3977.367662347227!2d-74.64854592501856!3d4.204533841875019!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e3efc4c0e0e0e0e%3A0x5b4b5b5b5b5b5b5b!2sAlborada+Hotel+Melgar!5e0!3m2!1ses!2sco!4v1700000000000"
                        allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>Alborada Hotel</h3>
                    <p>Uno de los mejores hoteles en Melgar, Tolima, con más de 10 años ofreciendo experiencias
                        inolvidables a nuestras familias visitantes.</p>
                    <div class="social-links">
                        <a href="http://www.facebook.com/alboradahotelmelgar" target="_blank"><i
                                class="fab fa-facebook-f"></i></a>
                        <a href="http://www.instagram.com/alborada_hotel_melgar" target="_blank"><i
                                class="fab fa-instagram"></i></a>
                        <a href="http://www.tiktok.com/alboradahotelmelgar" target="_blank"><i
                                class="fab fa-tiktok"></i></a>
                    </div>
                </div>
                <div class="footer-column">
                    <h3>Enlaces Rápidos</h3>
                    <ul class="footer-links">
                        <li><a href="#home">Inicio</a></li>
                        <li><a href="#about">Nosotros</a></li>
                        <li><a href="#rooms">Habitaciones</a></li>
                        <li><a href="#services">Servicios</a></li>
                        <li><a href="#contact">Contacto</a></li>
                        <li><a href="cuenta.html">Mi Cuenta</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Servicios</h3>
                    <ul class="footer-links">
                        <li><a href="#services">Piscinas y Tobogán</a></li>
                        <li><a href="#services">Zona de Spa</a></li>
                        <li><a href="#services">Restaurante</a></li>
                        <li><a href="#services">Gimnasio</a></li>
                        <li><a href="#services">Bar</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Contacto</h3>
                    <ul class="footer-links">
                        <li><i class="fas fa-map-marker-alt"></i> Calle 7 # 7-18, Melgar</li>
                        <li><i class="fas fa-phone"></i> 3204276289</li>
                        <li><i class="fab fa-whatsapp"></i> 3176210927</li>
                        <li><i class="fas fa-envelope"></i> recepcion@alboradahotel.com</li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2025 Alborada Hotel Melgar - AMA Hoteles. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- WhatsApp Button -->
    <a href="https://wa.link/5h3lyk" class="whatsapp-btn" target="_blank">
        <i class="fab fa-whatsapp"></i>
    </a>

    <script src="script.js"></script>
</body>

</html>
