<?php
require_once __DIR__ . '/../../Controllers/AuthController.php';

$auth = new AuthController();
$mensaje = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mensaje = $auth->register();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro — SecureAuth</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../Assets/css/Register/Register.css">
</head>
<body>

    <div class="bg-mesh" aria-hidden="true">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>
    <div class="bg-grid" aria-hidden="true"></div>

    <div class="auth-wrapper">

        <aside class="brand-panel" aria-hidden="true">
            <div class="brand-icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <h1>SecureAuth</h1>
            <p>Crea tu cuenta y protege tu acceso con autenticacion de dos factores.</p>
            <div class="brand-stats">
                <div class="stat-item">
                    <div class="stat-num">256</div>
                    <div class="stat-label">Bit Cifrado</div>
                </div>
                <div class="stat-item">
                    <div class="stat-num">2FA</div>
                    <div class="stat-label">Verificacion</div>
                </div>
                <div class="stat-item">
                    <div class="stat-num">99.9%</div>
                    <div class="stat-label">Uptime</div>
                </div>
                <div class="stat-item">
                    <div class="stat-num">0</div>
                    <div class="stat-label">Brechas</div>
                </div>
            </div>
        </aside>

        <main class="form-panel">
            <div class="form-header">
                <h2>Crear Cuenta</h2>
                <p>Completa los campos para registrarte en el sistema</p>
            </div>

            <?php
            if ($mensaje === "error_match") {
                echo '<div class="msg-banner error"><i class="fas fa-circle-exclamation"></i><span>Las contrasenas no coinciden.</span></div>';
            } elseif ($mensaje === "error_dup") {
                echo '<div class="msg-banner error"><i class="fas fa-circle-exclamation"></i><span>El nombre de usuario ya existe.</span></div>';
            } elseif ($mensaje === "error_db") {
                echo '<div class="msg-banner error"><i class="fas fa-circle-exclamation"></i><span>Error interno del servidor.</span></div>';
            } elseif ($mensaje === "success") {
                echo '<div class="msg-banner success"><i class="fas fa-circle-check"></i><span>Usuario registrado con exito. <a href="index.php">Inicia sesion aqui</a></span></div>';
            }
            ?>

            <form method="POST" autocomplete="off">
                <div class="field-group">
                    <label for="usuario">Usuario</label>
                    <div class="input-wrapper">
                        <input type="text" id="usuario" name="usuario" placeholder="Elige un nombre de usuario" required>
                        <i class="fas fa-at"></i>
                    </div>
                </div>

                <div class="field-group">
                    <label for="email">Correo</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" placeholder="Tu correo Gmail" required>
                        <i class="fas fa-envelope"></i>
                    </div>
                </div>

                <div class="field-group">
                    <label for="password">Contrasena</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" placeholder="Minimo 6 caracteres" required>
                        <progress id="strength-bar" max="4"></progress>
                        <script src="/Assets/js/Register/passwordStrength.js"></script>
                        <i class="fas fa-lock"></i>
                    </div>
                    <div class="password-strength" id="strengthBars">
                        <div class="bar"></div>
                        <div class="bar"></div>
                        <div class="bar"></div>
                        <div class="bar"></div>
                    </div>
                </div>

                <div class="field-group">
                    <label for="confirm_password">Confirmar Contrasena</label>
                    <div class="input-wrapper">
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Repite tu contrasena" required>
                        <i class="fas fa-lock"></i>
                    </div>
                </div>

                <button type="submit" class="btn-primary">
                    Crear Cuenta
                </button>
                    
            </form>

            <div class="form-footer">
                Ya tienes cuenta? <a href="/patron_mvc/Views/Login/Login.php">Iniciar sesión</a>
            </div>
        </main>

    </div>

    <script src="../../Assets/js/Register/Register.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>