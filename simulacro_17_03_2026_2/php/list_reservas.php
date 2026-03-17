<?php
require_once __DIR__ . '/config/database.php';

$db = new Database();
$conn = $db->getConnection();

// obtener reservas
try {
    // garantizamos que existan las columnas en tablas antiguas
    $alterSqls = [
        "ALTER TABLE reservas ADD COLUMN IF NOT EXISTS id_habitacion VARCHAR(255) NOT NULL",
        "ALTER TABLE reservas ADD COLUMN IF NOT EXISTS fecha_entrada DATE NOT NULL",
        "ALTER TABLE reservas ADD COLUMN IF NOT EXISTS fecha_salida DATE NOT NULL",
        "ALTER TABLE reservas ADD COLUMN IF NOT EXISTS nombre_completo VARCHAR(255) NOT NULL",
        "ALTER TABLE reservas ADD COLUMN IF NOT EXISTS adultos INT DEFAULT 0",
        "ALTER TABLE reservas ADD COLUMN IF NOT EXISTS ninos INT DEFAULT 0",
        "ALTER TABLE reservas ADD COLUMN IF NOT EXISTS email VARCHAR(255) NOT NULL",
        "ALTER TABLE reservas ADD COLUMN IF NOT EXISTS fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP"
    ];
    foreach ($alterSqls as $a) {
        try { $conn->exec($a); } catch (PDOException $ignored) {}
    }

    // select the Spanish-named reservation columns
    $stmt = $conn->prepare('SELECT id, id_habitacion, fecha_entrada, fecha_salida, nombre_completo, adultos, ninos, email, fecha_registro FROM reservas ORDER BY fecha_registro DESC');
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
                        <th>ID Habitación</th>
                        <th>Entrada</th>
                        <th>Salida</th>
                        <th>Nombre completo</th>
                        <th>Adultos</th>
                        <th>Niños</th>
                        <th>Email</th>
                        <th>Registro</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservas as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['id']) ?></td>
                            <td><?= htmlspecialchars($r['id_habitacion']) ?></td>
                            <td><?= htmlspecialchars($r['fecha_entrada']) ?></td>
                            <td><?= htmlspecialchars($r['fecha_salida']) ?></td>
                            <td><?= htmlspecialchars($r['nombre_completo']) ?></td>
                            <td><?= htmlspecialchars($r['adultos']) ?></td>
                            <td><?= htmlspecialchars($r['ninos']) ?></td>
                            <td><?= htmlspecialchars($r['email']) ?></td>
                            <td><?= htmlspecialchars($r['fecha_registro']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>

</html>