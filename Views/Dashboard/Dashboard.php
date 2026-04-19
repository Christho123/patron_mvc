<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Profesional</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="/patron_mvc/Assets/css/Dashboard/Dashboard.css">
</head>
<body>

<!-- SIDEBAR -->
<nav class="sidebar">
    <div class="brand">
        <i class="fa-solid fa-layer-group"></i>
        <h2>AdminPanel</h2>
    </div>
    
    <ul class="nav-links">
        <li>
            <a href="/patron_mvc/index.php?route=dashboard" class="nav-link">
                <i class="fa-solid fa-chart-line"></i>
                Análisis
            </a>
        </li>

        <li>
            <a href="/patron_mvc/index.php?route=users" class="nav-link">
                <i class="fa-solid fa-users"></i>
                Usuarios
            </a>
        </li>
        <li>
            <a href="/patron_mvc/index.php?route=logout" class="nav-link" style="color:red;">
                <i class="fa-solid fa-right-from-bracket"></i>
                Cerrar sesión
            </a>
        </li>
    </ul>
</nav>

<!-- MAIN -->
<main class="main-content">

<header>
    <h1>
        <?php echo (($_GET['route'] ?? '') === 'users') ? 'Usuarios' : 'Análisis General'; ?>
    </h1>
</header>

<!-- 🔥 CONTENIDO DINÁMICO -->
<section class="view-container active">

<?php
$route = $_GET['route'] ?? 'dashboard';
$analyticsSummary = $analyticsData['summary'] ?? [
    'login_success_count' => 0,
    'register_success_count' => 0,
    'register_failed_count' => 0
];
$analyticsTrend = $analyticsData['trend'] ?? [
    'labels' => [],
    'login_success' => [],
    'register_success' => [],
    'register_failed' => []
];

if ($route === 'users') {
?>

    <!-- 👥 TABLA DE USUARIOS -->
    <div class="card">
        <table id="usersTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?php echo $u['id']; ?></td>
                        <td><?php echo htmlspecialchars($u['usuario']); ?></td>
                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                        <td>
                        <?php
                        $ahora = time();
                        $expirado = false;

                        if (!empty($u['otp_expiracion'])) {
                            $expirado = strtotime($u['otp_expiracion']) < $ahora;
                        }

                        if ($u['verified'] == 1) {
                            echo "<span style='color:green; font-weight:bold;'>Activo</span>";
                        } else {
                            if ($expirado) {
                                echo "<span style='color:red; font-weight:bold;'>Expirado</span>";
                            } else {
                                echo "<span style='color:orange; font-weight:bold;'>Pendiente</span>";
                            }
                        }
                        ?>
                        </td>
                        <td><?php echo $u['created_at']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php
} else {
?>

    <!-- 📊 ANÁLISIS -->
    <div class="kpi-grid">

        <div class="card">
            <div class="kpi-header">
                <div>
                    <div class="kpi-value"><?php echo (int)$analyticsSummary['login_success_count']; ?></div>
                    <div class="kpi-label">Inicios de sesion</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="kpi-header">
                <div>
                    <div class="kpi-value"><?php echo (int)$analyticsSummary['register_success_count']; ?></div>
                    <div class="kpi-label">Registros exitosos</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="kpi-header">
                <div>
                    <div class="kpi-value"><?php echo (int)$analyticsSummary['register_failed_count']; ?></div>
                    <div class="kpi-label">Registros no exitosos</div>
                </div>
            </div>
        </div>

    </div>

    <div class="card">
        <h3>Grafico analitico</h3>
        <div class="chart-fixed-wrap">
            <canvas id="mainChart" height="220"></canvas>
        </div>
    </div>

<?php } ?>

</section>

</main>

<!-- LIBS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    window.DATA_FROM_PHP = <?php echo json_encode($analyticsTrend ?? [], JSON_UNESCAPED_UNICODE); ?>;
</script>
<script src="/patron_mvc/Assets/js/Dashboard/Dashboard.js"></script>
</body>
</html>