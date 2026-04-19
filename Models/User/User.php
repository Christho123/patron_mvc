<?php
require_once __DIR__ . '/../../Config/Database.php';

class User {

    private $pdo;

    public function __construct() {
        // FIX CLAVE: obtener conexión correctamente
        $this->pdo = getConnection();
    }

    // Buscar usuario por username
    public function findByUsername($usuario) {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE usuario = ?");
        $stmt->execute([$usuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear usuario
    public function create($usuario, $email, $password_hash) {
        $stmt = $this->pdo->prepare("
            INSERT INTO usuarios (usuario, email, password, estado)
            VALUES (?, ?, ?, 2)
        ");
        return $stmt->execute([$usuario, $email, $password_hash]);
    }

    // Guardar OTP
    public function saveOTP($id, $otp, $expiracion) {
        $stmt = $this->pdo->prepare("
            UPDATE usuarios 
            SET otp_code = ?, otp_expiracion = ?, estado = 2
            WHERE id = ?
        ");
        return $stmt->execute([$otp, $expiracion, $id]);
    }

    // Verificar OTP
    public function verifyOTP($id, $otp) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM usuarios 
            WHERE id = ? 
            AND otp_code = ? 
            AND otp_expiracion > NOW()
        ");
        $stmt->execute([$id, $otp]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Limpiar OTP
    public function clearOTP($id) {
        $stmt = $this->pdo->prepare("
            UPDATE usuarios 
            SET otp_code = NULL, otp_expiracion = NULL 
            WHERE id = ?
        ");
        return $stmt->execute([$id]);
    }

    // Listar todos los usuarios
public function getAllUsers() {
    $stmt = $this->pdo->prepare("
        SELECT id, usuario, email, verified, estado, otp_expiracion, created_at
        FROM usuarios
        ORDER BY id DESC
    ");

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function expireOtps() {

    // 1) Limpia rostros de usuarios pendientes ya vencidos para evitar bloqueos FK.
    $deleteFaces = $this->pdo->prepare("
        DELETE FROM user_faces
        WHERE user_id IN (
            SELECT id
            FROM usuarios
            WHERE verified = 0
              AND estado = 2
              AND (
                    (otp_expiracion IS NOT NULL AND otp_expiracion < NOW())
                    OR created_at < NOW() - INTERVAL 5 MINUTE
                  )
        )
    ");
    $deleteFaces->execute();

    // 2) Elimina de usuarios los pendientes vencidos.
    $delete = $this->pdo->prepare("
        DELETE FROM usuarios
        WHERE verified = 0
          AND estado = 2
          AND (
                (otp_expiracion IS NOT NULL AND otp_expiracion < NOW())
                OR created_at < NOW() - INTERVAL 5 MINUTE
              )
    ");
    $delete->execute();
}

public function existsById($id) {
    $stmt = $this->pdo->prepare("SELECT id FROM usuarios WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    return (bool)$stmt->fetch(PDO::FETCH_ASSOC);
}
}