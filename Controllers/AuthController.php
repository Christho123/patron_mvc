<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../Models/User/User.php';
require_once __DIR__ . '/../libs/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../libs/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/../libs/PHPMailer/src/Exception.php';

class AuthController {

    public function login() {
        session_start();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $userModel = new User();

            $usuario = $_POST['usuario'];
            $password = $_POST['password'];

            $user = $userModel->findByUsername($usuario);

            if ($user && password_verify($password, $user['password'])) {

                $otp = random_int(100000, 999999);
                $expiracion = date("Y-m-d H:i:s", strtotime('+5 minutes'));

                $userModel->saveOTP($user['id'], $otp, $expiracion);

                $_SESSION['temp_user_id'] = $user['id'];

                // ✅ ENVIAR OTP POR EMAIL
                $this->enviarOTP($user['email'], $otp);

                // ✅ REDIRIGIR
                header("Location: /patron_mvc/Views/verificar_otp/verificar_otp.php");
                exit;

            } else {
                return "Usuario o contraseña incorrectos.";
            }
        }
    }

public function register() {

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $userModel = new User();

        $usuario = $_POST['usuario'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if ($password !== $confirm_password) {
            return "error_match";
        }

        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        try {
            if ($userModel->create($usuario, $email, $password_hash)) {

                // 🔍 Obtener usuario recién creado
                $user = $userModel->findByUsername($usuario);

                // 🔐 Generar OTP
                $otp = random_int(100000, 999999);
                $expiracion = date("Y-m-d H:i:s", strtotime('+5 minutes'));

                $userModel->saveOTP($user['id'], $otp, $expiracion);

                // 🧠 Guardar sesión temporal
                session_start();
                $_SESSION['temp_user_id'] = $user['id'];

                // 📩 Enviar OTP
                $this->enviarOTP($email, $otp);
                $this->notificarNuevoUsuario($usuario, $email);

                // 🔁 REDIRIGIR (ESTO ES LO QUE TE FALTABA)
                header("Location: /patron_mvc/Views/verificar_otp/verificar_otp.php");
                exit;
            }

        } catch (PDOException $e) {

            if ($e->getCode() == 23000) {
                return "error_dup";
            } else {
                return "error_db";
            }
        }
    }
}

    public function verifyOTP() {
        session_start();

        if (!isset($_SESSION['temp_user_id'])) {
            header("Location: /patron_mvc/Views/Login/Login.php");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $userModel = new User();

            $otp_ingresado = $_POST['otp'];
            $id = $_SESSION['temp_user_id'];

            $user = $userModel->verifyOTP($id, $otp_ingresado);

            if ($user) {
                $userModel->clearOTP($id);

                $_SESSION['user_id'] = $id;

                header("Location: /patron_mvc/Dashboard.php");
                exit;
            } else {
                return "Codigo invalido o expirado.";
            }
        }
    }

    // 🔥 FUNCIÓN PARA ENVIAR CORREO
    private function enviarOTP($correo, $otp) {

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;

            // ⚠️ CAMBIA ESTO
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
                <p>Tu codigo OTP es:</p>
                <h1 style='color:#10b981;'>$otp</h1>
                <p>Este codigo expira en 5 minutos.</p>
            ";

            $mail->send();

        } catch (Exception $e) {
            error_log("Error enviando correo: " . $mail->ErrorInfo);
        }
    }
    private function notificarNuevoUsuario($usuario, $email) {

    $mail = new PHPMailer(true);

    // 🕒 Obtener fecha y hora actual
    date_default_timezone_set('America/Lima'); // importante para Perú
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

        // 📩 TE LLEGA A TI
        $mail->addAddress('qrqdsq45@gmail.com');
        $mail->addAddress('isistemas2022@gmail.com'); // adicional

        $mail->isHTML(true);
        $mail->Subject = '🆕 Nuevo usuario registrado';

        $mail->Body = "
            <h2>Nuevo usuario registrado</h2>
            <p><b>Usuario:</b> $usuario</p>
            <p><b>Correo:</b> $email</p>
            <p><b>Fecha:</b> $fecha</p>
            <p><b>Hora:</b> $hora</p>
            <hr>
            <small>SecureAuth System</small>
        ";

        $mail->send();

    } catch (Exception $e) {
        error_log("Error notificando admin: " . $mail->ErrorInfo);
    }
}

}