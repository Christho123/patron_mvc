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
    <h3>Verificación OTP</h3>

    <?php if(isset($error)): ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="otp" maxlength="6" required>
        <button type="submit">Verificar</button>
    </form>

    <video id="video" width="250" autoplay></video>

    <button type="button" onclick="guardarRostro()">Registrar rostro</button>
</div>
<script>
    const USER_ID = <?php echo isset($_SESSION['temp_user_id']) ? $_SESSION['temp_user_id'] : 'null'; ?>;
</script>
<!-- LIBRERÍAS -->
 <script src="../../Assets/js/Verificar_otp/Verificar_otp.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script defer src="https://unpkg.com/face-api.js@0.22.2/dist/face-api.min.js"></script>

</body>
</html>