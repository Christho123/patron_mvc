<?php
require_once __DIR__ . '/../../Config/Database.php';

class Duplicate {

    public function save($email, $ip) {
        global $pdo;

        $stmt = $pdo->prepare("INSERT INTO duplicate_attempts (email, ip_address) VALUES (?, ?)");
        $stmt->execute([$email, $ip]);
    }
}