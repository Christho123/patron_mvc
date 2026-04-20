<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 60 * 60 * 24 * 30,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

$password = 'ClaveSegura123!';
$hash = password_hash($password, PASSWORD_BCRYPT);
$verifyOk = password_verify($password, $hash);

$otp = random_int(100000, 999999);
$otpOk = strlen((string)$otp) === 6;

$face1 = [0.1, 0.2, 0.3];
$face2 = [0.1, 0.2, 0.3];
$distance = sqrt(pow($face1[0] - $face2[0], 2) + pow($face1[1] - $face2[1], 2) + pow($face1[2] - $face2[2], 2));
$faceOk = $distance < 0.5;

header('Content-Type: text/plain; charset=utf-8');

$cookieParams = session_get_cookie_params();

echo "PRUEBA DE SEGURIDAD\n";
echo "===================\n\n";

echo "Hash contrasena: " . ($verifyOk ? 'OK' : 'FALLO') . "\n";
echo "OTP de 6 digitos: " . ($otpOk ? 'OK' : 'FALLO') . " ({$otp})\n";
echo "Distancia facial: " . number_format($distance, 4) . " => " . ($faceOk ? 'OK' : 'FALLO') . "\n";
echo "Sesion httponly: " . (!empty($cookieParams['httponly']) ? 'OK' : 'FALLO') . "\n";
echo "Sesion samesite: " . (($cookieParams['samesite'] ?? '') === 'Lax' ? 'OK' : 'FALLO') . "\n";
