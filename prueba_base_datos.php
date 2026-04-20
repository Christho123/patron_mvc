<?php
require_once __DIR__ . '/Config/Database.php';

header('Content-Type: text/plain; charset=utf-8');

echo "PRUEBA DE BASE DE DATOS\n";
echo "========================\n\n";

try {
    $pdo = getConnection();
    echo "Conexion: OK\n";

    $tables = ['usuarios', 'user_faces', 'user_logs', 'duplicate_attempts'];

    foreach ($tables as $table) {
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        $exists = (bool)$stmt->fetchColumn();

        echo "- Tabla {$table}: " . ($exists ? 'existe' : 'no existe') . "\n";

        if ($exists) {
            $countStmt = $pdo->query("SELECT COUNT(*) FROM {$table}");
            $count = (int)$countStmt->fetchColumn();
            echo "  Registros: {$count}\n";
        }
    }
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

