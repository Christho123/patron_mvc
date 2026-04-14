<?php
require_once __DIR__ . '/../../Controllers/AuthController.php';

$auth = new AuthController();
$error = $auth->login();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso — SecureAuth</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/patron_mvc/Assets/css/Login/Login.css">
</head>
<body>

    <!-- Fondo animado -->
    <div class="bg-mesh" aria-hidden="true">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>
    <div class="bg-grid" aria-hidden="true"></div>

    <!-- Tarjeta principal -->
    <div class="auth-wrapper">

        <!-- Panel de marca -->
        <aside class="brand-panel" aria-hidden="true">
            <div class="brand-icon">
                <i class="fas fa-shield-halved"></i>
            </div>
            <h1>SecureAuth</h1>
            <p>Autenticación de dos factores para proteger lo que más importa.</p>
            <ul class="brand-features">
                <li><i class="fas fa-check"></i> Cifrado de extremo a extremo</li>
                <li><i class="fas fa-check"></i> Verificación OTP en tiempo real</li>
                <li><i class="fas fa-check"></i> Cumplimiento de seguridad empresarial</li>
            </ul>
        </aside>

        <!-- Panel del formulario -->
        <main class="form-panel">
            <div class="form-header">
                <h2>Iniciar Sesion</h2>
                <p>Ingresa tus credenciales para continuar</p>
            </div>

            <?php if(isset($error)): ?>
                <div class="error-banner" role="alert">
                    <i class="fas fa-circle-exclamation"></i>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" autocomplete="off">
                <div class="field-group">
                    <label for="usuario">Usuario</label>
                    <div class="input-wrapper">
                        <input type="text" id="usuario" name="usuario" placeholder="nombre.usuario" required>
                        <i class="fas fa-user"></i>
                    </div>
                </div>

                <div class="field-group">
                    <label for="password">Contrasena</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" placeholder="Ingresa tu contrasena" required>
                        <i class="fas fa-lock"></i>
                    </div>
                </div>

                <button type="submit" class="btn-primary">
                    Acceder
                </button>
                <hr style="margin:20px 0;">

                <div style="text-align:center;">
                    <video id="video" width="260" autoplay style="border-radius:10px;"></video>

                    <br><br>

                    <button type="button" class="btn-primary" onclick="loginFace()">
                        <i class="fas fa-face-smile"></i> Iniciar con rostro
                    </button>
                </div>
            </form>

            <div class="form-footer">
                No tienes cuenta? <a href="/patron_mvc/Views/Register/Register.php">Crear cuenta</a>
            </div>
        </main>

    </div>
    <script defer src="/patron_mvc/Assets/js/Login/Login.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://unpkg.com/face-api.js@0.22.2/dist/face-api.min.js"></script>
</body>
</html>
