<?php
// Conexión a la base de datos
$servername = "127.0.0.1";
$username   = "root";          // ¡cambia si es necesario!
$password   = "";
$dbname     = "alborada";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}



// Directorio para imágenes
$uploadDir = 'uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Función para subir imagen
function uploadImage($file) {
    global $uploadDir;
    if ($file['error'] == 0) {
        $fileName   = basename($file['name']);
        $targetFile = $uploadDir . time() . '_' . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        if (in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                return $targetFile;
            }
        }
    }
    return null;
}

// Manejar acciones
$action = isset($_GET['action']) ? $_GET['action'] : 'habitaciones';
$id     = isset($_GET['id']) ? intval($_GET['id']) : 0;
$mensaje = '';
// permitir pasar mensajes vía query string (ej. después de redirecciones)
if (isset($_GET['msg'])) {
    $mensaje = $_GET['msg'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre                   = trim($_POST['nombre']);
    $descripcion              = trim($_POST['descripcion']);
    $precio                   = floatval($_POST['precio']);
    $caracteristicas          = trim($_POST['caracteristicas'] ?? '');
    $disponibles              = intval($_POST['habitaciones_disponibles'] ?? 0);
    // nuevo campo total/max de habitaciones (se llamará max_habitaciones en la BD)
    $max_habitaciones         = intval($_POST['max_habitaciones'] ?? 0);
    $imagen                   = isset($_FILES['imagen']) && $_FILES['imagen']['size'] > 0 ? uploadImage($_FILES['imagen']) : null;

    if ($action == 'create') {
        $stmt = $conn->prepare("INSERT INTO habitaciones (nombre, descripcion, caracteristicas, precio, habitaciones_disponibles, max_habitaciones, imagen) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdsiis", $nombre, $descripcion, $caracteristicas, $precio, $disponibles, $max_habitaciones, $imagen);
        if ($stmt->execute()) {
            $mensaje = '<div class="alert alert-success">Habitación creada correctamente.</div>';
        } else {
            $mensaje = '<div class="alert alert-danger">Error al crear: ' . $conn->error . '</div>';
        }
    } elseif ($action == 'edit' && $id > 0) {
        if ($imagen) {
            // Nueva imagen → actualizar todo
            $stmt = $conn->prepare("UPDATE habitaciones SET nombre=?, descripcion=?, caracteristicas=?, precio=?, habitaciones_disponibles=?, max_habitaciones=?, imagen=? WHERE id_habitacion=?");
            $stmt->bind_param("ssdsiisi", $nombre, $descripcion, $caracteristicas, $precio, $disponibles, $max_habitaciones, $imagen, $id);
        } else {
            // Mantener imagen anterior
            $stmt = $conn->prepare("UPDATE habitaciones SET nombre=?, descripcion=?, caracteristicas=?, precio=?, habitaciones_disponibles=?, max_habitaciones=? WHERE id_habitacion=?");
            $stmt->bind_param("ssdisii", $nombre, $descripcion, $caracteristicas, $precio, $disponibles, $max_habitaciones, $id);
        }
        if ($stmt->execute()) {
            $mensaje = '<div class="alert alert-success">Habitación actualizada correctamente.</div>';
        } else {
            $mensaje = '<div class="alert alert-danger">Error al actualizar: ' . $conn->error . '</div>';
        }
    }
    // Después de POST redirigimos a lista (opcional: quitar si quieres ver el mensaje)
    // header("Location: admin_habitaciones.php"); exit;
}

if ($action == 'delete' && $id > 0) {
    $stmt = $conn->prepare("DELETE FROM habitaciones WHERE id_habitacion=?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $mensaje = '<div class="alert alert-success">Habitación eliminada.</div>';
    } else {
        $mensaje = '<div class="alert alert-danger">Error al eliminar.</div>';
    }
}

// Acción para alternar rol de administrador
if (($action == 'make_admin' || $action == 'remove_admin') && $id > 0) {
    if ($action == 'make_admin') {
        $stmt = $conn->prepare("UPDATE usuarios_alborada SET addmin = 1 WHERE id = ?");
    } else {
        $stmt = $conn->prepare("UPDATE usuarios_alborada SET addmin = 0 WHERE id = ?");
    }
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $msg = ($action == 'make_admin') ? 'Usuario ahora es administrador.' : 'Administrador revocado.';
        // redirigir a la pestaña usuarios para ver el cambio y evitar resubmits
        header('Location: admin_habitaciones.php?action=usuarios&msg=' . urlencode('<div class="alert alert-success">' . $msg . '</div>'));
        exit;
    } else {
        $mensaje = '<div class="alert alert-danger">Error al actualizar rol de administrador.</div>';
    }
}

// Obtener habitación para editar
$habitacion = null;
if ($action == 'edit' && $id > 0) {
    $stmt = $conn->prepare("SELECT * FROM habitaciones WHERE id_habitacion=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $habitacion = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Habitaciones - Alborada</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom admin styles (moved to central styles.css) -->
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', 'Roboto', sans-serif;
        }
        .main-content-wrapper {
            padding: 2rem 1.5rem;
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            background: white;
            padding: 1.5rem 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        .page-header h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
            color: #2c3e50;
            letter-spacing: -0.5px;
        }
        .page-header .btn {
            border-radius: 8px;
            font-weight: 600;
            padding: 0.7rem 1.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(52,152,219,0.3);
        }
        .page-header .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(52,152,219,0.4);
        }
        .alert {
            border-radius: 12px;
            border: none;
            padding: 1.2rem 1.5rem;
            font-weight: 500;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
        }
        .alert-info {
            background-color: #e8f4f8;
            color: #0c5460;
            border-left: 4px solid #3498db;
        }
        .alert-success {
            background-color: #e6f7e6;
            color: #155724;
            border-left: 4px solid #27ae60;
        }
        .alert-danger {
            background-color: #f8e6e6;
            color: #721c24;
            border-left: 4px solid #e74c3c;
        }
        .row > .col {
            margin-bottom: 1.5rem;
        }
        .card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card:hover {
            transform: translateY(-12px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.15);
        }
        .card-title {
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }
        .card-body {
            padding: 1.75rem;
        }
        .btn-sm {
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .btn-outline-primary:hover {
            transform: translateY(-2px);
        }
        .btn-outline-danger:hover {
            transform: translateY(-2px);
        }
        .form-control, textarea.form-control {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            background-color: #ffffff;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }
        .form-control:focus, textarea.form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.2rem rgba(52,152,219,0.25);
            background-color: #ffffff;
        }
        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.75rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.75rem;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(52,152,219,0.3);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #2980b9 0%, #1f618d 100%);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(52,152,219,0.4);
        }
    </style>
</head>
<body>

<div class="d-flex">

    <!-- Sidebar -->
    <div class="admin-sidebar col-md-3 col-lg-2 p-3">
        <div class="sidebar-logo-container" style="text-align: center; margin-bottom: 1.5rem;">
            <a href="../index.html">
                <img src="https://alboradahotel.com/wp-content/uploads/2023/09/Alborada-Original-en-MELGAR-hoteles-en-melgar.png" alt="Alborada Hotel" class="sidebar-logo" style="max-height: 80px; width: auto; transition: all 0.3s ease;">
            </a>
        </div>
        <hr class="text-white">
        <?php $current_action = isset($action) ? $action : 'habitaciones'; ?>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_action == 'habitaciones') ? 'active' : ''; ?>" href="admin_habitaciones.php?action=habitaciones"><i class="bi bi-house-door me-2"></i> Habitaciones</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_action == 'reservas') ? 'active' : ''; ?>" href="admin_habitaciones.php?action=reservas"><i class="bi bi-calendar-check me-2"></i> Reservas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_action == 'usuarios') ? 'active' : ''; ?>" href="admin_habitaciones.php?action=usuarios"><i class="bi bi-people me-2"></i> Usuarios</a>
            </li>
            <li class="nav-item mt-4">
                <a class="nav-link text-danger <?php echo ($current_action == 'logout') ? 'active' : ''; ?>" href="#"><i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión</a>
            </li>
        </ul>
    </div>

    <!-- Contenido principal -->
    <div class="col-md-9 ms-sm-auto col-lg-10 main-content-wrapper">

        <div class="page-header">
            <h1 class="h2 mb-0">
                <?php 
                if ($action == 'create') echo "Nueva Habitación";
                elseif ($action == 'edit') echo "Editar Habitación";
                elseif ($action == 'reservas') echo "Reservas";
                elseif ($action == 'usuarios') echo "Usuarios";
                else echo "Gestión de Habitaciones";
                ?>
            </h1>
            <?php if ($action == 'habitaciones'): ?>
                <a href="?action=create" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Nueva Habitación</a>
            <?php endif; ?>
        </div>

        <?php if ($mensaje) echo $mensaje; ?>

        <?php if ($action == 'habitaciones'): ?>

            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php
                $result = $conn->query("SELECT * FROM habitaciones ORDER BY id_habitacion DESC");
                if ($result->num_rows == 0) {
                    echo '<div class="col-12"><div class="alert alert-info">No hay habitaciones registradas aún.</div></div>';
                }
                while ($row = $result->fetch_assoc()):
                ?>
                <div class="col">
                    <div class="card h-100">
                        <?php if ($row['imagen']): ?>
                            <img src="<?php echo htmlspecialchars($row['imagen']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['nombre']); ?>">
                        <?php else: ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height:180px;">
                                <i class="bi bi-image fs-1 text-muted"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['nombre']); ?></h5>
                            <p class="card-text text-muted small"><?php echo nl2br(htmlspecialchars(substr($row['descripcion'], 0, 120))); ?>...</p>
                            <p class="fw-bold text-primary fs-5">$<?php echo number_format($row['precio'], 2); ?></p>
                            <p class="text-muted small mb-1">Disponibles: <?php echo intval($row['habitaciones_disponibles'] ?? 0); ?><?php if(isset($row['max_habitaciones'])) echo ' / '.intval($row['max_habitaciones']); ?></p>
                            <div class="d-flex gap-2 mt-3">
                                <a href="?action=edit&id=<?php echo $row['id_habitacion']; ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i> Editar</a>
                                <a href="?action=delete&id=<?php echo $row['id_habitacion']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Realmente deseas eliminar esta habitación?');"><i class="bi bi-trash"></i> Eliminar</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>

        <?php elseif ($action == 'reservas'): ?>
            <div class="alert alert-info" role="alert">
                <i class="bi bi-info-circle me-2"></i>
                <strong>No hay reservas</strong> - Por el momento no hay reservas registradas en el sistema.
            </div>

        <?php elseif ($action == 'usuarios'): ?>

            <?php
            $result_users = $conn->query("SELECT * FROM usuarios_alborada ORDER BY id DESC");
            if ($result_users && $result_users->num_rows > 0):
            ?>
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Usuarios registrados</h5>
                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Correo</th>
                                        <th>Teléfono</th>
                                        <th>Admin</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php while ($u = $result_users->fetch_assoc()): ?>
                                    <?php
                                        $nombre = htmlspecialchars($u['Nombre'] ?? ($u['fullname'] ?? '—'));
                                        $correo = htmlspecialchars($u['Correo'] ?? ($u['email'] ?? '—'));
                                        $telefono = htmlspecialchars($u['Telefono'] ?? ($u['phone'] ?? '—'));
                                        $isAdmin = intval($u['addmin'] ?? 0);
                                    ?>
                                    <tr>
                                        <td><?php echo $nombre; ?></td>
                                        <td><?php echo $correo; ?></td>
                                        <td><?php echo $telefono; ?></td>
                                        <td><?php echo $isAdmin ? 'Sí' : 'No'; ?></td>
                                        <td>
                                            <?php if (!$isAdmin): ?>
                                                <a href="?action=make_admin&id=<?php echo $u['id']; ?>" class="btn btn-sm btn-outline-primary">Hacer admin</a>
                                            <?php else: ?>
                                                <a href="?action=remove_admin&id=<?php echo $u['id']; ?>" class="btn btn-sm btn-outline-secondary">Quitar admin</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info" role="alert">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>No hay usuarios registrados</strong> - Por el momento no hay usuarios registrados en el sistema.
                </div>
            <?php endif; ?>

        <?php elseif ($action == 'create' || $action == 'edit'): ?>

            <div class="card">
                <div class="card-body p-4">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nombre de la habitación</label>
                            <input type="text" name="nombre" class="form-control" value="<?php echo $habitacion ? htmlspecialchars($habitacion['nombre']) : ''; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="4" required><?php echo $habitacion ? htmlspecialchars($habitacion['descripcion']) : ''; ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Precio por noche (USD)</label>
                            <input type="number" step="0.01" name="precio" class="form-control" value="<?php echo $habitacion ? $habitacion['precio'] : ''; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Habitaciones Disponibles</label>
                            <input type="number" min="0" name="habitaciones_disponibles" class="form-control" value="<?php echo $habitacion ? intval($habitacion['habitaciones_disponibles'] ?? 0) : 0; ?>" required>
                            <div class="form-text">Cantidad de habitaciones de este tipo disponibles para reservar.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Total de habitaciones</label>
                            <input type="number" min="0" name="max_habitaciones" class="form-control" value="<?php echo $habitacion ? intval($habitacion['max_habitaciones'] ?? 0) : 0; ?>" required>
                            <div class="form-text">Número máximo o total de habitaciones de este tipo (se usa para cálculo avanzado).</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Características</label>
                            <textarea name="caracteristicas" class="form-control" rows="3"><?php echo $habitacion ? htmlspecialchars($habitacion['caracteristicas'] ?? '') : ''; ?></textarea>
                            <div class="form-text">Lista breve de características o servicios (separados por comas o saltos).</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Imagen principal</label>
                            <input type="file" name="imagen" class="form-control" accept="image/jpeg,image/png,image/gif">
                            <?php if ($habitacion && $habitacion['imagen']): ?>
                                <div class="mt-3">
                                    <small class="text-muted">Imagen actual:</small><br>
                                    <img src="<?php echo htmlspecialchars($habitacion['imagen']); ?>" alt="Actual" class="img-thumbnail mt-2" style="max-height:180px;">
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-primary btn-lg px-5"><i class="bi bi-save me-2"></i> Guardar Habitación</button>
                            <a href="admin_habitaciones.php" class="btn btn-outline-secondary btn-lg px-5">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>

        <?php endif; ?>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Modal para vista rápida de habitación -->
<div class="modal fade" id="roomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle habitación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <img id="roomModalImg" src="https://via.placeholder.com/600x300?text=Sin+imagen" class="img-fluid mb-3" style="width:100%;height:220px;object-fit:cover;border-radius:8px;">
                <p id="roomModalDesc" class="small text-muted"></p>
                <p id="roomModalPrice" class="fw-bold"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <a href="#" id="roomModalEditBtn" class="btn btn-primary">Editar</a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    var roomModalEl = document.getElementById('roomModal');
    if (!roomModalEl) return;
    roomModalEl.addEventListener('show.bs.modal', function (event) {
        var trigger = event.relatedTarget;
        if (!trigger) return;
        var name = trigger.getAttribute('data-name') || '';
        var desc = trigger.getAttribute('data-desc') || '';
        var price = trigger.getAttribute('data-price') || '';
        var img = trigger.getAttribute('data-img') || '';
        var id = trigger.getAttribute('data-id') || '';
        roomModalEl.querySelector('.modal-title').textContent = name;
        roomModalEl.querySelector('#roomModalDesc').textContent = desc;
        roomModalEl.querySelector('#roomModalPrice').textContent = price ? ('$' + price) : '';
        roomModalEl.querySelector('#roomModalImg').src = img || 'https://via.placeholder.com/600x300?text=Sin+imagen';
        var editBtn = roomModalEl.querySelector('#roomModalEditBtn');
        if (id) editBtn.href = '?action=edit&id=' + id; else editBtn.href = '#';
    });
});
</script>



</body>
</html>

<?php $conn->close(); ?>