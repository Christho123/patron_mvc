<?php
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

if (!isset($_SESSION['otp_verified_user_id'])) {
    header("Location: /patron_mvc/Views/Login/Login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escaneo Facial</title>
    <link rel="stylesheet" href="/patron_mvc/Assets/css/FaceScan/FaceScan.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://unpkg.com/face-api.js@0.22.2/dist/face-api.min.js"></script>
</head>
<body>
    <div class="box">
        <h3>Escaneo facial</h3>
        <p class="hint">Completa el segundo factor para ingresar.</p>

        <div class="camera-wrap">
            <video id="video" autoplay muted playsinline></video>
            <canvas id="overlay"></canvas>
        </div>

        <button id="btnVerify" type="button">Verificar rostro e ingresar</button>
    </div>

    <script defer src="/patron_mvc/Assets/js/FaceScan/FaceScan.js"></script>
</body>
</html>
