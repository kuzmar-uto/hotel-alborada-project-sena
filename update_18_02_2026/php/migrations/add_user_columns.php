<?php
require_once __DIR__ . '/../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    $dbName = 'alborada';
    $table = 'usuarios_alborada';
    $columns = [
        'Nombre' => "VARCHAR(255) DEFAULT NULL",
        'Telefono' => "VARCHAR(50) DEFAULT NULL"
    ];

    foreach ($columns as $col => $definition) {
        $stmt = $db->prepare("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = :db AND table_name = :table AND column_name = :col");
        $stmt->execute([':db' => $dbName, ':table' => $table, ':col' => $col]);
        $exists = (int) $stmt->fetchColumn();

        if ($exists === 0) {
            echo "Adding column $col...\n";
            $db->exec("ALTER TABLE `$table` ADD COLUMN `$col` $definition");
            echo "Added $col.\n";
        } else {
            echo "Column $col already exists.\n";
        }
    }

    echo "Migration completed.\n";
} catch (PDOException $e) {
    echo "Migration error: " . $e->getMessage() . "\n";
    exit(1);
}
