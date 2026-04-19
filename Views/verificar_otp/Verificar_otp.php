<?php

require_once __DIR__ . '/../../Controllers/AuthController.php';

$auth = new AuthController();
$error = $auth->verifyOTP();
?>  
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificacion OTP — SecureAuth</title>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="../../Assets/css/Verificar_otp/Verificar_otp.css">
</head>

<body>

<div class="box">       
    <h3>Verificacion OTP</h3>
    <p class="hint">Ingresa el codigo de 6 digitos enviado a tu correo. Expira en 5 minutos.</p>

    <?php if(isset($error)): ?>
        <p class="error-msg"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="POST" onsubmit="return submitOTP();">
        <div class="otp-grid">
            <input type="text" class="otp-input" maxlength="1" inputmode="numeric" autocomplete="one-time-code" required>
            <input type="text" class="otp-input" maxlength="1" inputmode="numeric" required>
            <input type="text" class="otp-input" maxlength="1" inputmode="numeric" required>
            <input type="text" class="otp-input" maxlength="1" inputmode="numeric" required>
            <input type="text" class="otp-input" maxlength="1" inputmode="numeric" required>
            <input type="text" class="otp-input" maxlength="1" inputmode="numeric" required>
        </div>
        <input type="hidden" id="otpHidden" name="otp">
        <button type="submit">Verificar</button>
    </form>
</div>
<script src="../../Assets/js/Verificar_otp/Verificar_otp.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>
</html>