<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../Models/User/User.php';
require_once __DIR__ . '/../libs/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../libs/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/../libs/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../Models/User/Duplicate.php';
require_once __DIR__ . '/../Models/User/Log.php';
require_once __DIR__ . '/../Config/Database.php';

class AuthController {

    // 🔥 función para evitar error de sesión duplicada
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

    public function login() {
        $this->startSession();

        if (isset($_SESSION['user_id'])) {
            header("Location: /patron_mvc/index.php?route=dashboard");
            exit;
        }

        #$this->deleteUnverifiedUsers();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $userModel = new User();
            $userModel->expireOtps();

            $usuario = $_POST['usuario'];
            $password = $_POST['password'];

            $user = $userModel->findByUsername($usuario);

            if ($user && password_verify($password, $user['password'])) {

                $otp = random_int(100000, 999999);
                $expiracion = date("Y-m-d H:i:s", strtotime('+5 minutes'));

                $userModel->saveOTP($user['id'], $otp, $expiracion);

                $_SESSION['temp_user_id'] = $user['id'];
                $_SESSION['otp_flow'] = 'login';

                $this->enviarOTP($user['email'], $otp);

                header("Location: /patron_mvc/Views/verificar_otp/Verificar_otp.php");
                exit;

            } else {
                return "Usuario o contraseña incorrectos.";
            }
        }
    }

    public function register() {

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $pdo = getConnection();
            $userModel = new User();
            $userModel->expireOtps();

            $usuario = $_POST['usuario'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            if ($password !== $confirm_password) {
                $this->logAction(null, 'register_failed');
                return "error_match";
            }

            $password_hash = password_hash($password, PASSWORD_BCRYPT);

            // 🔍 VALIDAR DUPLICADO
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? OR usuario = ?");
            $stmt->execute([$email, $usuario]);

            if ($stmt->fetch()) {

                $duplicate = new Duplicate();
                $duplicate->save($email, $_SERVER['REMOTE_ADDR']);
                $this->logAction(null, 'register_duplicate');

                return "error_dup";
            }

            try {
                if ($userModel->create($usuario, $email, $password_hash)) {

                    $user = $userModel->findByUsername($usuario);

                    $otp = random_int(100000, 999999);
                    $expiracion = date("Y-m-d H:i:s", strtotime('+5 minutes'));

                    $userModel->saveOTP($user['id'], $otp, $expiracion);

                    $this->startSession();
                    $_SESSION['temp_user_id'] = $user['id'];
                    $_SESSION['otp_flow'] = 'register';

                    $this->enviarOTP($email, $otp);
                    $this->notificarNuevoUsuario($usuario, $email);

                    header("Location: /patron_mvc/Views/verificar_otp/Verificar_otp.php");
                    exit;
                }

            } catch (PDOException $e) {
                $this->logAction(null, 'register_failed');
                return ($e->getCode() == 23000) ? "error_dup" : "error_db";
            }
        }
    }

    public function verifyOTP() {
        $this->startSession();

        if (!isset($_SESSION['temp_user_id'])) {
            header("Location: /patron_mvc/Views/Login/Login.php");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $userModel = new User();
            $userModel->expireOtps();

            $otp_ingresado = $_POST['otp'];
            $id = $_SESSION['temp_user_id'];

            $user = $userModel->verifyOTP($id, $otp_ingresado);

            if ($user) {

                $pdo = getConnection();

                $stmt = $pdo->prepare("
                    UPDATE usuarios 
                    SET verified = 1,
                        estado = 1,
                        otp_code = NULL,
                        otp_expiracion = NULL
                    WHERE id = ?
                ");
                $stmt->execute([$id]);

                // OTP correcto -> segundo factor facial
                $flow = $_SESSION['otp_flow'] ?? 'login';
                unset($_SESSION['temp_user_id']);
                unset($_SESSION['otp_flow']);

                if ($flow === 'register') {
                    $_SESSION['otp_verified_user_id'] = $id;
                    header("Location: /patron_mvc/Views/FaceScan/FaceScan.php");
                    exit;
                }

                $_SESSION['user_id'] = $id;
                $this->logAction($id, 'login_success');

                header("Location: /patron_mvc/index.php?route=dashboard");
                exit;
            } else {
                // Si ya no existe, fue eliminado por vencimiento del OTP.
                if (!$userModel->existsById($id)) {
                    unset($_SESSION['temp_user_id']);
                    unset($_SESSION['otp_flow']);
                    $this->logAction($id, 'register_otp_expired');
                    return "El codigo OTP expiro (5 minutos). Vuelve a registrarte.";
                }
                return "Codigo invalido o expirado.";
            }
        }
    }

    public function getAnalyticsData() {
        $log = new Log();
        return [
            'summary' => $log->getAnalyticsSummary(),
            'trend' => $log->getAnalyticsTrend(7),
        ];
    }

    private function enviarOTP($correo, $otp) {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'qrqdsq45@gmail.com';
            $mail->Password = 'ifgf hqab djtf afus';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('qrqdsq45@gmail.com', 'SecureAuth');
            $mail->addAddress($correo);

            $mail->isHTML(true);
            $mail->Subject = 'Codigo OTP - SecureAuth';
            $mail->Body = "
                <h2>Verificacion de seguridad</h2>
                <h1 style='color:#10b981;'>$otp</h1>
                <p>Expira en 5 minutos</p>
            ";

            $mail->send();

        } catch (Exception $e) {
            error_log("Error enviando correo: " . $mail->ErrorInfo);
        }
    }

    private function notificarNuevoUsuario($usuario, $email) {
        $mail = new PHPMailer(true);

        date_default_timezone_set('America/Lima');
        $fecha = date("d/m/Y");
        $hora = date("H:i:s");

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'qrqdsq45@gmail.com';
            $mail->Password = 'ifgf hqab djtf afus';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('qrqdsq45@gmail.com', 'SecureAuth');
            $mail->addAddress('qrqdsq45@gmail.com');

            $mail->isHTML(true);
            $mail->Subject = '🆕 Nuevo usuario registrado';
            $mail->Body = "
                <h2>Nuevo usuario registrado</h2>
                <p><b>Usuario:</b> $usuario</p>
                <p><b>Correo:</b> $email</p>
                <p><b>Fecha:</b> $fecha</p>
                <p><b>Hora:</b> $hora</p>
            ";

            $mail->send();

        } catch (Exception $e) {
            error_log("Error notificando admin: " . $mail->ErrorInfo);
        }
    }

    public function deleteUnverifiedUsers() {
        $pdo = getConnection();

        $pdo->exec("
            DELETE FROM user_faces 
            WHERE user_id IN (
                SELECT id FROM usuarios 
                WHERE verified = 0 
                AND created_at < NOW() - INTERVAL 5 MINUTE
            )
        ");

        $stmt = $pdo->prepare("
            DELETE FROM usuarios 
            WHERE verified = 0 
            AND created_at < NOW() - INTERVAL 5 MINUTE
        ");

        $stmt->execute();
    }

    public function listUsers() {

    $userModel = new User();

    // 🔥 limpieza automática antes de listar
    $userModel->expireOtps();

    return $userModel->getAllUsers();
}

    public function logout() {
        $this->startSession();

        session_unset();
        session_destroy();

        header("Location: /patron_mvc/index.php");
        exit;
    }
    
}