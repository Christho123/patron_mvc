<?php
require_once __DIR__ . '/../../Config/Database.php';

class User {

    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    // 🔍 Buscar usuario por username
    public function findByUsername($usuario) {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE usuario = ?");
        $stmt->execute([$usuario]);
        return $stmt->fetch();
    }

    // 🆕 Crear usuario
    public function create($usuario, $email, $password_hash) {
    $stmt = $this->pdo->prepare("INSERT INTO usuarios (usuario, email, password) VALUES (?, ?, ?)");
    return $stmt->execute([$usuario, $email, $password_hash]);
}

    // 🔐 Guardar OTP
    public function saveOTP($id, $otp, $expiracion) {
        $stmt = $this->pdo->prepare("UPDATE usuarios SET otp_code = ?, otp_expiracion = ? WHERE id = ?");
        return $stmt->execute([$otp, $expiracion, $id]);
    }

    // ✅ Verificar OTP
    public function verifyOTP($id, $otp) {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios 
            WHERE id = ? AND otp_code = ? AND otp_expiracion > NOW()");
        $stmt->execute([$id, $otp]);
        return $stmt->fetch();
    }

    // 🧹 Limpiar OTP
    public function clearOTP($id) {
        $stmt = $this->pdo->prepare("UPDATE usuarios SET otp_code = NULL, otp_expiracion = NULL WHERE id = ?");
        return $stmt->execute([$id]);
    }
}