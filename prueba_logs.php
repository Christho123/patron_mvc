<?php
require_once __DIR__ . '/Config/Database.php';

header('Content-Type: text/plain; charset=utf-8');

echo "PRUEBA DE LOGS\n";
echo "==============\n\n";

try {
    $pdo = getConnection();
    echo "Conexion: OK\n\n";

    $summaryQuery = $pdo->query("
        SELECT
            SUM(CASE WHEN action = 'login_success' THEN 1 ELSE 0 END) AS login_success,
            SUM(CASE WHEN action = 'register_success' THEN 1 ELSE 0 END) AS register_success,
            SUM(CASE WHEN action IN ('register_failed', 'register_duplicate', 'register_otp_expired') THEN 1 ELSE 0 END) AS register_failed
        FROM user_logs
    ");

    $summary = $summaryQuery->fetch(PDO::FETCH_ASSOC) ?: [];

    echo "Resumen:\n";
    echo "- Login exitoso: " . (int)($summary['login_success'] ?? 0) . "\n";
    echo "- Registro exitoso: " . (int)($summary['register_success'] ?? 0) . "\n";
    echo "- Registro fallido: " . (int)($summary['register_failed'] ?? 0) . "\n\n";

    $recent = $pdo->query("
        SELECT user_id, action, created_at
        FROM user_logs
        ORDER BY created_at DESC
        LIMIT 10
    ")->fetchAll(PDO::FETCH_ASSOC);

    echo "Ultimos movimientos:\n";
    if (!$recent) {
        echo "- Sin registros\n";
    } else {
        foreach ($recent as $row) {
            echo "- Usuario {$row['user_id']} | {$row['action']} | {$row['created_at']}\n";
        }
    }
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

