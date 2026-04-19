<?php
require_once __DIR__ . '/../Models/User/Face.php';
require_once __DIR__ . '/../Models/User/Log.php';

class FaceController {
    private function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.gc_maxlifetime', (string)(60 * 60 * 24 * 30));
            session_set_cookie_params([
                'lifetime' => 60 * 60 * 24 * 30,
                'path' => '/',
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            session_start();
        }
    }

    private function logAction($userId, $action) {
        try {
            $log = new Log();
            $log->save($userId, $action);
        } catch (Exception $e) {
            error_log("No se pudo guardar log: " . $e->getMessage());
        }
    }

    public function registerFace() {

        // SIEMPRE JSON limpio
        header('Content-Type: application/json; charset=utf-8');
        error_reporting(E_ALL);
        ini_set('display_errors', 0);

        try {

            require_once __DIR__ . '/../Config/Database.php';
            $pdo = getConnection();
            // FIX IMPORTANTE: asegurar conexión PDO

            // leer JSON correctamente
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

            // IMPORTANTE: evitar doble encode si ya es JSON string
            if (is_array($face_data)) {
                $face_data = json_encode($face_data);
            }

            // preparar query
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

        // traer todos los rostros registrados
        $stmt = $pdo->prepare("SELECT user_id, face_data FROM user_faces");
        $stmt->execute();
        $faces = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($faces as $face) {

            $storedFace = json_decode($face['face_data'], true);

            // calcular distancia
            $distance = $this->calcularDistancia($storedFace, $inputFace);

            if ($distance < 0.5) {

                $this->startSession();
                $_SESSION['user_id'] = $face['user_id'];
                $this->logAction((int)$face['user_id'], 'login_success');

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

public function verifyFaceAfterOtp() {
    header('Content-Type: application/json; charset=utf-8');
    $this->startSession();

    if (!isset($_SESSION['otp_verified_user_id'])) {
        echo json_encode([
            "status" => "error",
            "message" => "Sesion OTP no valida"
        ]);
        return;
    }

    try {
        require_once __DIR__ . '/../Config/Database.php';
        $pdo = getConnection();

        $rawInput = file_get_contents("php://input");
        $data = json_decode($rawInput, true);
        $inputFace = $data['face_data'] ?? null;

        if (!$inputFace) {
            echo json_encode([
                "status" => "error",
                "message" => "No se recibio rostro"
            ]);
            return;
        }

        $userId = (int)$_SESSION['otp_verified_user_id'];

        $stmt = $pdo->prepare("SELECT face_data FROM user_faces WHERE user_id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si no tiene rostro, se registra en este paso.
        if (!$row) {
            $insert = $pdo->prepare("INSERT INTO user_faces (user_id, face_data) VALUES (?, ?)");
            $insert->execute([$userId, json_encode($inputFace)]);

            $_SESSION['user_id'] = $userId;
            unset($_SESSION['otp_verified_user_id']);
            $this->logAction($userId, 'register_success');

            echo json_encode([
                "status" => "success",
                "mode" => "registered"
            ]);
            return;
        }

        $storedFace = json_decode($row['face_data'], true);
        if (!is_array($storedFace) || count($storedFace) !== count($inputFace)) {
            echo json_encode([
                "status" => "error",
                "message" => "Rostro almacenado invalido"
            ]);
            return;
        }

        $distance = $this->calcularDistancia($storedFace, $inputFace);

        if ($distance < 0.5) {
            $_SESSION['user_id'] = $userId;
            unset($_SESSION['otp_verified_user_id']);
            $this->logAction($userId, 'register_success');

            echo json_encode([
                "status" => "success",
                "mode" => "verified",
                "distance" => $distance
            ]);
            return;
        }

        echo json_encode([
            "status" => "fail",
            "distance" => $distance
        ]);
    } catch (Exception $e) {
        echo json_encode([
            "status" => "error",
            "message" => $e->getMessage()
        ]);
    }
}
    
}