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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg-primary: #06090f;
            --card-bg: rgba(15, 23, 42, 0.65);
            --card-border: rgba(255, 255, 255, 0.06);
            --accent: #10b981;
            --accent-hover: #34d399;
            --accent-glow: rgba(16, 185, 129, 0.25);
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --input-bg: rgba(15, 23, 42, 0.5);
            --input-border: rgba(100, 116, 139, 0.3);
            --input-focus: rgba(16, 185, 129, 0.4);
            --error: #f43f5e;
            --font-display: 'Space Grotesk', sans-serif;
            --font-body: 'DM Sans', sans-serif;
        }

        body {
            font-family: var(--font-body);
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        .bg-mesh {
            position: fixed; inset: 0; z-index: 0; overflow: hidden;
        }
        .bg-mesh .orb {
            position: absolute; border-radius: 50%;
            filter: blur(100px); opacity: 0.3;
            animation: orbFloat 24s ease-in-out infinite alternate;
        }
        .bg-mesh .orb-1 {
            width: 500px; height: 500px;
            background: radial-gradient(circle, #10b981 0%, transparent 70%);
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
        }
        .bg-mesh .orb-2 {
            width: 400px; height: 400px;
            background: radial-gradient(circle, #0d9488 0%, transparent 70%);
            top: -10%; left: -5%;
            animation-delay: -10s;
        }
        .bg-mesh .orb-3 {
            width: 350px; height: 350px;
            background: radial-gradient(circle, #065f46 0%, transparent 70%);
            bottom: -10%; right: -5%;
            animation-delay: -18s;
        }
        @keyframes orbFloat {
            0% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -25px) scale(1.05); }
            66% { transform: translate(-25px, 35px) scale(0.96); }
            100% { transform: translate(15px, -10px) scale(1.02); }
        }

        .bg-grid {
            position: fixed; inset: 0; z-index: 1;
            background-image:
                linear-gradient(rgba(255,255,255,0.015) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.015) 1px, transparent 1px);
            background-size: 60px 60px;
        }

        /* === Tarjeta OTP centrada === */
        .otp-wrapper {
            position: relative; z-index: 10;
            width: 100%; max-width: 440px;
            margin: 1.5rem;
            border-radius: 20px; overflow: hidden;
            border: 1px solid var(--card-border);
            background: var(--card-bg);
            backdrop-filter: blur(40px) saturate(1.4);
            -webkit-backdrop-filter: blur(40px) saturate(1.4);
            box-shadow:
                0 0 0 1px rgba(255,255,255,0.03) inset,
                0 32px 64px -12px rgba(0,0,0,0.5),
                0 0 120px -40px var(--accent-glow);
            padding: 3rem 2.5rem;
            text-align: center;
            animation: cardEntry 0.8s cubic-bezier(0.16, 1, 0.3, 1) both;
        }
        @keyframes cardEntry {
            from { opacity: 0; transform: translateY(30px) scale(0.97); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Icono animado */
        .otp-icon-container {
            width: 80px; height: 80px;
            margin: 0 auto 1.75rem;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(16,185,129,0.12), rgba(13,148,136,0.06));
            border: 1.5px solid rgba(16,185,129,0.2);
            display: flex; align-items: center; justify-content: center;
            position: relative;
        }
        .otp-icon-container::after {
            content: '';
            position: absolute; inset: -8px;
            border-radius: 50%;
            border: 1px dashed rgba(16,185,129,0.15);
            animation: spinSlow 20s linear infinite;
        }
        @keyframes spinSlow {
            to { transform: rotate(360deg); }
        }
        .otp-icon-container i {
            font-size: 1.8rem; color: var(--accent);
        }

        .otp-wrapper h2 {
            font-family: var(--font-display);
            font-size: 1.4rem; font-weight: 700;
            letter-spacing: -0.02em; margin-bottom: 0.5rem;
        }
        .otp-wrapper .subtitle {
            font-size: 0.88rem; color: var(--text-secondary);
            line-height: 1.5; margin-bottom: 2rem;
        }

        /* === Cajas individuales del código OTP === */
        .otp-inputs {
            display: flex; gap: 10px; justify-content: center;
            margin-bottom: 1.75rem;
        }
        .otp-inputs input {
            width: 48px; height: 58px;
            text-align: center;
            font-family: var(--font-display);
            font-size: 1.4rem; font-weight: 600;
            background: var(--input-bg);
            border: 1.5px solid var(--input-border);
            border-radius: 12px;
            color: var(--text-primary);
            outline: none;
            transition: border-color 0.25s, box-shadow 0.25s, transform 0.15s;
            caret-color: var(--accent);
        }
        .otp-inputs input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--input-focus);
            transform: translateY(-2px);
        }
        .otp-inputs input.filled {
            border-color: rgba(16,185,129,0.5);
            background: rgba(16,185,129,0.05);
        }

        /* Input oculto real que se envía */
        .hidden-otp { position: absolute; opacity: 0; pointer-events: none; }

        .btn-primary {
            width: 100%; padding: 0.85rem 1.5rem;
            background: linear-gradient(135deg, var(--accent), #0d9488);
            color: #fff; font-family: var(--font-display);
            font-size: 0.92rem; font-weight: 600;
            letter-spacing: 0.01em; border: none;
            border-radius: 10px; cursor: pointer;
            position: relative; overflow: hidden;
            transition: transform 0.2s, box-shadow 0.3s;
        }
        .btn-primary::before {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, transparent 50%);
            opacity: 0; transition: opacity 0.3s;
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 30px rgba(16,185,129,0.35);
        }
        .btn-primary:hover::before { opacity: 1; }
        .btn-primary:active {
            transform: translateY(0);
            box-shadow: 0 4px 16px rgba(16,185,129,0.25);
        }

        /* Temporizador */
        .timer-row {
            margin-top: 1.25rem;
            font-size: 0.8rem; color: var(--text-muted);
        }
        .timer-row i { margin-right: 0.35rem; }
        .timer-row .timer-val {
            color: var(--accent); font-weight: 600;
            font-family: var(--font-display);
        }

        /* Error */
        .error-banner {
            display: flex; align-items: center; gap: 0.6rem;
            padding: 0.75rem 1rem;
            background: rgba(244, 63, 94, 0.08);
            border: 1px solid rgba(244, 63, 94, 0.2);
            border-radius: 10px; margin-bottom: 1.25rem;
            animation: shakeIn 0.4s ease;
            justify-content: center;
        }
        .error-banner i { color: var(--error); font-size: 0.85rem; flex-shrink: 0; }
        .error-banner span { font-size: 0.82rem; color: #fda4af; }
        @keyframes shakeIn {
            0% { transform: translateX(-8px); opacity: 0; }
            25% { transform: translateX(6px); }
            50% { transform: translateX(-4px); }
            75% { transform: translateX(2px); }
            100% { transform: translateX(0); opacity: 1; }
        }

        .back-link {
            display: inline-flex; align-items: center; gap: 0.4rem;
            margin-top: 1.5rem; font-size: 0.82rem;
            color: var(--text-muted); text-decoration: none;
            transition: color 0.2s;
        }
        .back-link:hover { color: var(--text-secondary); }
        .back-link i { font-size: 0.7rem; }

        @media (max-width: 480px) {
            .otp-wrapper { padding: 2.5rem 1.75rem; }
            .otp-inputs input { width: 42px; height: 50px; font-size: 1.2rem; }
            .otp-inputs { gap: 7px; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>
<body>

    <div class="bg-mesh" aria-hidden="true">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>
    <div class="bg-grid" aria-hidden="true"></div>

    <div class="otp-wrapper">
        <div class="otp-icon-container" aria-hidden="true">
            <i class="fas fa-fingerprint"></i>
        </div>

        <h2>Verificacion de Seguridad</h2>
        <p class="subtitle">Ingresa el codigo de 6 digitos que se genero para tu cuenta.</p>

        <?php if(isset($error)): ?>
            <div class="error-banner" role="alert">
                <i class="fas fa-circle-exclamation"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" id="otpForm" autocomplete="off">
            <!-- Input oculto que lleva el valor real -->
            <input type="text" name="otp" id="otpHidden" class="hidden-otp" required maxlength="6">

            <!-- 6 cajas visuales -->
            <div class="otp-inputs" aria-label="Codigo de verificacion">
                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" aria-label="Digito 1" data-idx="0">
                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" aria-label="Digito 2" data-idx="1">
                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" aria-label="Digito 3" data-idx="2">
                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" aria-label="Digito 4" data-idx="3">
                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" aria-label="Digito 5" data-idx="4">
                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" aria-label="Digito 6" data-idx="5">
            </div>

            <button type="submit" class="btn-primary" id="submitBtn">
                Verificar Codigo
            </button>
        </form>

        <div class="timer-row">
            <i class="fas fa-clock"></i>
            El codigo expira en <span class="timer-val" id="countdown">5:00</span>
        </div>

        <a href="index.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Volver al inicio de sesion
        </a>
    </div>

    <script>
        /* === Lógica de cajas OTP === */
        const boxes = document.querySelectorAll('.otp-inputs input');
        const hidden = document.getElementById('otpHidden');

        boxes.forEach((box, idx) => {
            box.addEventListener('input', (e) => {
                /* Solo permitir dígitos */
                e.target.value = e.target.value.replace(/[^0-9]/g, '');
                hidden.value = Array.from(boxes).map(b => b.value).join('');

                /* Marcar como lleno */
                box.classList.toggle('filled', e.target.value !== '');

                /* Auto-avanzar */
                if (e.target.value && idx < boxes.length - 1) {
                    boxes[idx + 1].focus();
                }
            });

            box.addEventListener('keydown', (e) => {
                /* Retroceder con Backspace */
                if (e.key === 'Backspace' && !box.value && idx > 0) {
                    boxes[idx - 1].focus();
                    boxes[idx - 1].value = '';
                    boxes[idx - 1].classList.remove('filled');
                    hidden.value = Array.from(boxes).map(b => b.value).join('');
                }
                /* Enter para enviar */
                if (e.key === 'Enter') {
                    document.getElementById('otpForm').requestSubmit();
                }
            });

            /* Seleccionar todo al enfocar (para pegar) */
            box.addEventListener('focus', () => box.select());

            /* Soporte pegar código completo */
            box.addEventListener('paste', (e) => {
                e.preventDefault();
                const paste = (e.clipboardData.getData('text') || '').replace(/[^0-9]/g, '').slice(0, 6);
                paste.split('').forEach((ch, i) => {
                    if (boxes[i]) {
                        boxes[i].value = ch;
                        boxes[i].classList.add('filled');
                    }
                });
                hidden.value = paste;
                const focusIdx = Math.min(paste.length, boxes.length - 1);
                boxes[focusIdx].focus();
            });
        });

        /* Enfocar la primera caja al cargar */
        boxes[0].focus();

        /* === Temporizador regresivo de 5 minutos === */
        let totalSeconds = 5 * 60;
        const timerEl = document.getElementById('countdown');

        const timerInterval = setInterval(() => {
            totalSeconds--;
            if (totalSeconds <= 0) {
                clearInterval(timerInterval);
                timerEl.textContent = '0:00';
                timerEl.style.color = 'var(--error)';
                document.getElementById('submitBtn').disabled = true;
                document.getElementById('submitBtn').style.opacity = '0.4';
                document.getElementById('submitBtn').style.cursor = 'not-allowed';
                return;
            }
            const m = Math.floor(totalSeconds / 60);
            const s = totalSeconds % 60;
            timerEl.textContent = m + ':' + String(s).padStart(2, '0');

            /* Cambiar color en los últimos 60 segundos */
            if (totalSeconds <= 60) {
                timerEl.style.color = 'var(--error)';
            }
        }, 1000);
    </script>
</body>
</html>