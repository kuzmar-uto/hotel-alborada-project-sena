<?php
require_once __DIR__ . '/config/database.php';

$db = new Database();
$conn = $db->getConnection();

// obtener reservas
try {
    $stmt = $conn->prepare('SELECT id, room, price, checkin, checkout, guest_name, adults, children, email, created_at FROM reservas ORDER BY created_at DESC');
    $stmt->execute();
    $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = $e->getMessage();
    $reservas = [];
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Listado de Reservas</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            padding: 16px;
            background: #f6f7f9;
            color: #111
        }

        table {
            border-collapse: collapse;
            width: 100%;
            background: #fff;
            border-radius: 8px;
            overflow: hidden
        }

        th,
        td {
            padding: 10px;
            border-bottom: 1px solid #eee;
            text-align: left;
            font-size: 14px
        }

        th {
            background: #fafafa
        }

        .muted {
            color: #666;
            font-size: 13px
        }

        .container {
            max-width: 1100px;
            margin: 0 auto
        }

        .actions {
            margin: 12px 0
        }

        .btn {
            display: inline-block;
            padding: 8px 12px;
            background: #007bff;
            color: #fff;
            border-radius: 6px;
            text-decoration: none
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Reservas</h2>
        <p class="muted">Lista de reservas registradas en la base de datos.</p>
        <div class="actions">
            <a class="btn" href="/php/list_reservas.php">Refrescar</a>
        </div>

        <?php if (isset($error)): ?>
            <div style="color:crimson">Error al consultar: <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (empty($reservas)): ?>
            <div style="padding:14px;background:#fff;border-radius:8px">No se encuentran .</div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Habitación</th>
                        <th>Precio</th>
                        <th>Entrada</th>
                        <th>Salida</th>
                        <th>Huésped</th>
                        <th>Adultos</th>
                        <th>Niños</th>
                        <th>Email</th>
                        <th>Creada</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservas as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['id']) ?></td>
                            <td><?= htmlspecialchars($r['room']) ?></td>
                            <td><?= htmlspecialchars($r['price']) ?></td>
                            <td><?= htmlspecialchars($r['checkin']) ?></td>
                            <td><?= htmlspecialchars($r['checkout']) ?></td>
                            <td><?= htmlspecialchars($r['guest_name']) ?></td>
                            <td><?= htmlspecialchars($r['adults']) ?></td>
                            <td><?= htmlspecialchars($r['children']) ?></td>
                            <td><?= htmlspecialchars($r['email']) ?></td>
                            <td><?= htmlspecialchars($r['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>

</html>