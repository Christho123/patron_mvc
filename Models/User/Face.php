<?php
require_once __DIR__ . '/../../Config/Database.php';

class Face {

    public function saveFace($user_id, $face_data) {
        global $pdo;

        $stmt = $pdo->prepare("INSERT INTO user_faces (user_id, face_data) VALUES (?, ?)");
        return $stmt->execute([$user_id, json_encode($face_data)]);
    }

    public function getFaceByUser($user_id) {
        global $pdo;

        $stmt = $pdo->prepare("SELECT * FROM user_faces WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}