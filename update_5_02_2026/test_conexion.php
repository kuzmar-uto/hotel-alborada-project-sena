<?php
session_start();
header('Content-Type: text/html; charset=utf-8');

require_once 'php/config/database.php';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Conexión - Alborada Hotel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
        }
        .test-result {
            margin: 20px 0;
            padding: 15px;
            border-radius: 5px;
            border-left: 5px solid #ddd;
        }
        .success {
            background: #d4edda;
            border-left-color: #28a745;
            color: #155724;
        }
        .error {
            background: #f8d7da;
            border-left-color: #dc3545;
            color: #721c24;
        }
        .info {
            background: #d1ecf1;
            border-left-color: #17a2b8;
            color: #0c5460;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background: #3d8da5;
            color: white;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background: #3d8da5;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Test de Conexión - Alborada Hotel</h1>
        
        <?php
        // Test 1: Verificar conexión a base de datos
        echo '<div class="test-result info">';
        echo '<strong>Test 1: Conexión a Base de Datos</strong><br>';
        
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            if ($db) {
                echo '<div class="test-result success">';
                echo '✅ Conexión exitosa a la base de datos<br>';
                echo 'Host: <code>localhost</code><br>';
                echo 'Base de datos: <code>alborada</code><br>';
                echo 'Usuario: <code>root</code>';
                echo '</div>';
            }
        } catch (Exception $e) {
            echo '<div class="test-result error">';
            echo '❌ Error de conexión: ' . $e->getMessage();
            echo '</div>';
            die();
        }
        
        // Test 2: Verificar tabla usuarios_alborada
        echo '<div class="test-result info">';
        echo '<strong>Test 2: Tabla usuarios_alborada</strong><br>';
        
        try {
            $query = "SHOW TABLES LIKE 'usuarios_alborada'";
            $stmt = $db->prepare($query);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                echo '<div class="test-result success">';
                echo '✅ Tabla <code>usuarios_alborada</code> existe<br>';
                
                // Obtener estructura de la tabla
                $structQuery = "DESCRIBE usuarios_alborada";
                $structStmt = $db->prepare($structQuery);
                $structStmt->execute();
                $columns = $structStmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo '<table>';
                echo '<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th></tr>';
                foreach ($columns as $col) {
                    echo '<tr>';
                    echo '<td>' . $col['Field'] . '</td>';
                    echo '<td>' . $col['Type'] . '</td>';
                    echo '<td>' . ($col['Null'] === 'YES' ? 'Sí' : 'No') . '</td>';
                    echo '<td>' . $col['Key'] . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
                
                echo '</div>';
            } else {
                echo '<div class="test-result error">';
                echo '❌ Tabla <code>usuarios_alborada</code> NO existe';
                echo '</div>';
            }
        } catch (Exception $e) {
            echo '<div class="test-result error">';
            echo '❌ Error: ' . $e->getMessage();
            echo '</div>';
        }
        
        // Test 3: Contar usuarios registrados
        echo '<div class="test-result info">';
        echo '<strong>Test 3: Usuarios Registrados</strong><br>';
        
        try {
            $query = "SELECT COUNT(*) as total FROM usuarios_alborada";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo '<div class="test-result success">';
            echo '✅ Total de usuarios: <strong>' . $result['total'] . '</strong>';
            echo '</div>';
            
            if ($result['total'] > 0) {
                echo '<div class="test-result info">';
                echo '<strong>Usuarios registrados:</strong><br>';
                $listQuery = "SELECT id, Correo FROM usuarios_alborada";
                $listStmt = $db->prepare($listQuery);
                $listStmt->execute();
                $users = $listStmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo '<table>';
                echo '<tr><th>ID</th><th>Correo</th></tr>';
                foreach ($users as $user) {
                    echo '<tr><td>' . $user['id'] . '</td><td>' . $user['Correo'] . '</td></tr>';
                }
                echo '</table>';
                echo '</div>';
            }
        } catch (Exception $e) {
            echo '<div class="test-result error">';
            echo '❌ Error: ' . $e->getMessage();
            echo '</div>';
        }
        
        // Test 4: Verificar archivos PHP
        echo '<div class="test-result info">';
        echo '<strong>Test 4: Archivos Necesarios</strong><br>';
        
        $files = [
            'php/config/database.php' => 'Configuración de BD',
            'php/auth/register.php' => 'Sistema de Registro',
            'php/auth/login.php' => 'Sistema de Login',
            'cuenta.html' => 'Página de Registro',
            'login.html' => 'Página de Login'
        ];
        
        foreach ($files as $file => $desc) {
            $path = __DIR__ . '/' . $file;
            if (file_exists($path)) {
                echo '<div class="test-result success">✅ ' . $desc . ' (<code>' . $file . '</code>)</div>';
            } else {
                echo '<div class="test-result error">❌ ' . $desc . ' (<code>' . $file . '</code>) - NO ENCONTRADO</div>';
            }
        }
        
        ?>
        
        <div class="test-result info">
            <strong>✨ Resumen:</strong><br>
            <p>Si todos los tests pasaron correctamente (en verde), el sistema está listo para usar:</p>
            <ul>
                <li>🔗 Ir a <a href="cuenta.html" class="button">Página de Registro</a></li>
                <li>🔗 Ir a <a href="login.html" class="button">Página de Login</a></li>
                <li>🔗 Ir a <a href="index.html" class="button">Página Principal</a></li>
            </ul>
        </div>
    </div>
</body>
</html>
