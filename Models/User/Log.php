<?php
require_once __DIR__ . '/../../Config/Database.php';

class Log {

    public function save($user_id, $action) {
        global $pdo;

        $stmt = $pdo->prepare("INSERT INTO user_logs (user_id, action) VALUES (?, ?)");
        $stmt->execute([$user_id, $action]);
    }

    public function getStats() {
        global $pdo;

        $stmt = $pdo->query("
            SELECT action, COUNT(*) as total 
            FROM user_logs 
            GROUP BY action
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}