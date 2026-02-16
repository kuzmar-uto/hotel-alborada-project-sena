<?php
// habitaciones.php - Página pública elegante de habitaciones

// Conexión a la base de datos
$servername = "127.0.0.1";
$username   = "root";           // ¡cambia según tu configuración!
$password   = "";
$dbname     = "alborada";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Habitaciones - Alborada Hotel</title>
    <link rel="stylesheet" href="styles.css">  <!-- Asegúrate de que incluya los estilos para .container, #main-header, .logo, nav, .mobile-menu-btn, .btn-account, etc. -->
    <!-- Si usas Font Awesome para el ícono de menú hamburguesa -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Tus otros <link> o <style> si los tienes -->
    
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>




        :root {
            --primary:    #2c5282;     /* azul elegante */
            --primary-dark: #1a365d;
            --accent:     #d69e2e;     /* dorado suave */
            --light:      #f7fafc;
            --gray:       #4a5568;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: var(--light);
            color: var(--gray);
        }

        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.55), rgba(0,0,0,0.55)), 
                        url('https://images.unsplash.com/photo-1578683015146-b0b3a0696490?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 9rem 0 6rem;
            text-align: center;
        }

        .hero-section h1 {
            font-weight: 700;
            letter-spacing: -1px;
        }

        .card-room {
            border: none;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: all 0.35s ease;
            background: white;
        }

        .card-room:hover {
            transform: translateY(-12px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.12);
        }

        .card-img-top {
            height: 280px;
            object-fit: cover;
        }

        .card-body {
            padding: 1.75rem 1.75rem 2rem;
        }

        .price {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary);
        }

        .features-list {
            font-size: 0.95rem;
            line-height: 1.7;
            color: #4a5568;
        }

        .features-list li {
            margin-bottom: 0.6rem;
            position: relative;
            padding-left: 1.8rem;
        }

        .features-list li::before {
            content: "✓";
            color: var(--accent);
            position: absolute;
            left: 0;
            font-weight: bold;
        }

        .btn-reserve {
            background: var(--primary);
            border: none;
            border-radius: 50px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-reserve:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        footer {
            background: var(--primary-dark);
            color: white;
            padding: 3rem 0 2rem;
        }

        @media (max-width: 768px) {
            .hero-section { padding: 6rem 0 4rem; }
            .card-img-top { height: 220px; }
        }
    </style>
</head>
<body>

    <!-- Barra de navegación COPIADA exactamente de habitaciones.html -->
    <header>
        <div class="container">
            <div id="main-header">
                <div class="logo">
                    <a href="index.html">
                        <img src="https://alboradahotel.com/wp-content/uploads/2023/09/Alborada-Original-en-MELGAR-hoteles-en-melgar.png"
                            alt="Alborada Hotel Melgar">
                    </a>
                </div>
                <nav>
                    <button class="mobile-menu-btn"><i class="fas fa-bars"></i></button>
                    <ul>
                        <li><a href="index.html">Inicio</a></li>
                        <li><a href="index.html#about">Nosotros</a></li>
                        <li><a href="habitaciones.php">Habitaciones</a></li>
                        <li><a href="index.html#services">Servicios</a></li>
                        <li><a href="index.html#contact">Contacto</a></li>
                        <li>
                            <a href="cuenta.html" class="btn btn-account"
                               style="background:#ffd500;color:#111;border:1px solid rgba(0,0,0,0.08);">Mi Cuenta</a>
                        </li>
                    </ul>
                </nav>
                <div class="overlay"></div>
            </div>
        </div>
    </header>

    <!-- Aquí continúa el resto de tu página: hero, sección de habitaciones dinámicas, whatsapp float, etc. -->

<!-- Habitaciones -->
<section class="py-5 py-lg-5">
    <div class="container">

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 g-xl-5">

            <?php
            $result = $conn->query("SELECT * FROM habitaciones ORDER BY precio ASC");

            if ($result->num_rows === 0) {
                echo '<div class="col-12 text-center py-5">
                        <h3 class="text-muted">Aún no hay habitaciones disponibles</h3>
                      </div>';
            }

            while ($row = $result->fetch_assoc()):
                $img = $row['imagen'] ?: 'https://via.placeholder.com/800x600/cccccc/ffffff?text=Habitación';
                $caracteristicas = nl2br(htmlspecialchars($row['caracteristicas'] ?? ''));
                $caracteristicas = str_replace("\n", "</li>\n<li>", $caracteristicas);
                $tipo = htmlspecialchars($row['tipo_de_habitacion'] ?? '');
                $disponibles = isset($row['habitaciones_disponibles']) ? intval($row['habitaciones_disponibles']) : 0;
                $max = isset($row['max_habitaciones']) ? intval($row['max_habitaciones']) : 0;
            ?>

            <div class="col">
                <div class="card-room h-100 d-flex flex-column">
                    <img src="<?= htmlspecialchars($img) ?>" 
                         class="card-img-top" 
                         alt="<?= htmlspecialchars($row['nombre']) ?>">
                    
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h3 class="card-title fw-bold mb-0">
                                <?= htmlspecialchars($row['nombre']) ?>
                            </h3>
                            <?php if ($tipo): ?><span class="badge bg-primary"><?= $tipo ?></span><?php endif; ?>
                        </div>
                        
                        <p class="text-muted mb-4 flex-grow-1">
                            <?= nl2br(htmlspecialchars($row['descripcion'])) ?>
                        </p>

                        <div class="mb-3">
                            <?php if ($disponibles > 0): ?>
                                <small class="text-success"><i class="bi bi-check-circle"></i> <?= $disponibles ?> disponible(s)
                                    <?php if($max > 0): ?> / <?= $max ?> total<?php endif; ?></small>
                            <?php else: ?>
                                <small class="text-danger"><i class="bi bi-x-circle"></i> Sin disponibilidad</small>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($row['caracteristicas'])): ?>
                        <ul class="features-list list-unstyled mb-4">
                            <li><?= $caracteristicas ?></li>
                        </ul>
                        <?php endif; ?>

                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-end mb-4">
                                <div>
                                    <small class="text-muted">por noche</small>
                                    <div class="price">$<?= number_format($row['precio'], 2) ?></div>
                                </div>
                            </div>
                            
                            <button type="button" 
                                    class="btn btn-reserve w-100 text-white open-reserve-btn" 
                                    data-id="<?= $row['id_habitacion'] ?>"
                                    data-name="<?= htmlspecialchars($row['nombre']) ?>"
                                    data-price="<?= number_format($row['precio'], 2, '.', '') ?>"
                                    data-tipo="<?= htmlspecialchars($tipo) ?>"
                                    data-max="<?= $max ?>"
                                    <?= $disponibles == 0 ? 'disabled' : '' ?>>
                                <?= $disponibles > 0 ? 'Reservar ahora' : 'No disponible' ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <?php endwhile; ?>

        </div>
    </div>
</section>

<!-- Footer simple -->
<footer class="text-center">
    <div class="container">
        <p class="mb-1">© <?= date('Y') ?> Hotel Alborada • Tolima, Colombia</p>
        <p class="small opacity-75">Diseñado con elegancia y cariño</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Reserva Modal -->
<div class="modal fade" id="reserveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:12px;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(90deg,var(--primary),var(--primary-dark));color:white;border-bottom:none;">
                <h5 class="modal-title">Confirmar Reserva</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-4">
                <form id="reserveForm">
                    <input type="hidden" name="room" id="res-room">

                    <div class="mb-3">
                        <label class="form-label">Tipo de documento</label>
                        <select class="form-select" name="documento_tipo" id="res-doc-type" required>
                            <option value="tarjeta_identidad">Tarjeta de identidad</option>
                            <option value="cedula">Cédula</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Número de documento</label>
                        <input type="text" name="documento_numero" id="res-doc-number" class="form-control" pattern="\d+" inputmode="numeric" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tipo de habitación</label>
                        <select class="form-select" name="tipo_habitacion" id="res-room-type" required>
                            <option value="sencilla">Sencilla</option>
                            <option value="doble">Doble</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Precio por noche</label>
                        <input type="text" name="price" id="res-price" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nombre del titular</label>
                        <input type="text" name="name" id="res-name" class="form-control" required>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Confirmar reserva</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    var reserveModal = new bootstrap.Modal(document.getElementById('reserveModal'));

    document.querySelectorAll('.open-reserve-btn').forEach(function(btn){
        btn.addEventListener('click', function(){
            var id = this.getAttribute('data-id');
            var name = this.getAttribute('data-name');
            var price = this.getAttribute('data-price');
            var tipo = this.getAttribute('data-tipo') || '';

            document.getElementById('res-room').value = id;
            document.getElementById('res-name').value = name || '';
            document.getElementById('res-price').value = price;
            if (tipo) {
                // seleccionar tipo si coincide
                var sel = document.getElementById('res-room-type');
                for (var i=0;i<sel.options.length;i++){
                    if (sel.options[i].value.toLowerCase() === tipo.toLowerCase()){
                        sel.selectedIndex = i; break;
                    }
                }
            }
            reserveModal.show();
        });
    });

    // si cambia tipo de habitación, actualizar precio (puede ampliarse para mapear precios)
    document.getElementById('res-room-type').addEventListener('change', function(){
        // por ahora, simplemente mantenemos el precio original (se puede ajustar según reglas)
        // placeholder para lógica de precios por tipo
        // document.getElementById('res-price').value = computedPrice;
    });

    // enviar formulario por AJAX
    document.getElementById('reserveForm').addEventListener('submit', function(e){
        e.preventDefault();
        var form = e.target;
        var data = new FormData(form);
        // añadir campos por defecto de fechas / personas si no existen
        if (!data.has('checkin')) data.append('checkin', new Date().toISOString().slice(0,10));
        if (!data.has('checkout')) data.append('checkout', new Date().toISOString().slice(0,10));
        if (!data.has('adults')) data.append('adults', 1);
        if (!data.has('children')) data.append('children', 0);
        if (!data.has('email')) data.append('email', '');

        fetch('php/reservar.php', {
            method: 'POST',
            body: data
        }).then(function(res){ return res.json(); }).then(function(json){
            if (json && json.success){
                alert('Reserva confirmada — ID: ' + (json.id || 'N/A'));
                reserveModal.hide();
            } else {
                alert('Error: ' + (json.message || 'No se pudo completar la reserva'));
            }
        }).catch(function(err){
            console.error(err);
            alert('Error de red al enviar la reserva');
        });
    });
});
</script>
</body>
</html>

<?php $conn->close(); ?>