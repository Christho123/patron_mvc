<?php

function getConnection() {
    $host = 'localhost';
    $db = 'sistema_login';
    $user = 'root';
    $pass = '123456';

    try {
        $pdo = new PDO(
            "mysql:host=$host;dbname=$db;charset=utf8",
            $user,
            $pass
        );

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $pdo;

    } catch (PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
    }
}