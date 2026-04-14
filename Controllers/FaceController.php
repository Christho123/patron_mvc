<?php
require_once __DIR__ . '/../Models/User/Face.php';

class FaceController {

    public function registerFace() {

        // 🔥 SIEMPRE JSON limpio
        header('Content-Type: application/json; charset=utf-8');
        error_reporting(E_ALL);
        ini_set('display_errors', 0);

        try {

            require_once __DIR__ . '/../Config/Database.php';
            $pdo = getConnection();
            // 🔥 FIX IMPORTANTE: asegurar conexión PDO

            // 🔥 leer JSON correctamente
            $rawInput = file_get_contents("php://input");
            $data = json_decode($rawInput, true);

            if (!$data) {
                echo json_encode([
                    "success" => false,
                    "message" => "No llegaron datos o JSON inválido"
                ]);
                return;
            }

            $user_id = $data['user_id'] ?? null;
            $face_data = $data['face_data'] ?? null;

            if (!$user_id) {
                echo json_encode([
                    "success" => false,
                    "message" => "Falta user_id"
                ]);
                return;
            }

            if (!$face_data) {
                echo json_encode([
                    "success" => false,
                    "message" => "Falta face_data"
                ]);
                return;
            }

            // 🔥 IMPORTANTE: evitar doble encode si ya es JSON string
            if (is_array($face_data)) {
                $face_data = json_encode($face_data);
            }

            // 🔥 preparar query
            $stmt = $pdo->prepare("INSERT INTO user_faces (user_id, face_data) VALUES (?, ?)");

            if (!$stmt) {
                echo json_encode([
                    "success" => false,
                    "message" => "Error en prepare SQL"
                ]);
                return;
            }

            $stmt->execute([$user_id, $face_data]);

            echo json_encode([
                "success" => true,
                "message" => "Rostro registrado correctamente"
            ]);

        } catch (Exception $e) {
            echo json_encode([
                "success" => false,
                "message" => "Error server: " . $e->getMessage()
            ]);
        }
    }

    private function calcularDistancia($face1, $face2) {

    $sum = 0;

    for ($i = 0; $i < count($face1); $i++) {
        $sum += pow($face1[$i] - $face2[$i], 2);
    }

    return sqrt($sum);
}

public function loginWithFace() {

    header('Content-Type: application/json; charset=utf-8');

    try {

        require_once __DIR__ . '/../Config/Database.php';
        $pdo = getConnection();

        $rawInput = file_get_contents("php://input");
        $data = json_decode($rawInput, true);

        $inputFace = $data['face_data'] ?? null;

        if (!$inputFace) {
            echo json_encode([
                "status" => "error",
                "message" => "No se recibió rostro"
            ]);
            return;
        }

        // 🔥 traer todos los rostros registrados
        $stmt = $pdo->prepare("SELECT user_id, face_data FROM user_faces");
        $stmt->execute();
        $faces = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($faces as $face) {

            $storedFace = json_decode($face['face_data'], true);

            // 🔥 calcular distancia
            $distance = $this->calcularDistancia($storedFace, $inputFace);

            if ($distance < 0.5) {

                session_start();
                $_SESSION['user_id'] = $face['user_id'];

                echo json_encode([
                    "status" => "success",
                    "distance" => $distance
                ]);
                return;
            }
        }

        echo json_encode([
            "status" => "fail"
        ]);

    } catch (Exception $e) {
        echo json_encode([
            "status" => "error",
            "message" => $e->getMessage()
        ]);
    }
}
    
}