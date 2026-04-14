<?php

require_once __DIR__ . '/Controllers/FaceController.php';


// 🔥 obtener ruta
$route = $_GET['route'] ?? null;

// 🔥 GUARDAR ROSTRO
if ($route === 'save-face') {
    $controller = new FaceController();
    $controller->registerFace();
    exit;
}

// 🔥 LOGIN FACIAL
if ($route === 'face-login') {
    $controller = new FaceController();
    $controller->loginWithFace();
    exit;
}

// 🔥 DASHBOARD 👈 AGREGA ESTO
if ($route === 'dashboard') {
    require_once __DIR__ . '/Views/Dashboard/Dashboard.php';
    exit;
}
if ($route === 'dashboard' || $route === 'users') {

    require_once __DIR__ . '/Controllers/AuthController.php';

    $auth = new AuthController();
    $users = $auth->listUsers();

    require_once __DIR__ . '/Views/Dashboard/Dashboard.php';
    exit;
}

if ($route === 'logout') {
    require_once __DIR__ . '/Controllers/AuthController.php';

    $auth = new AuthController();
    $auth->logout();
    exit;
}

if ($route === 'api-users') {
    require_once __DIR__ . '/Controllers/AuthController.php';
    require_once __DIR__ . '/Models/User/User.php';

    $userModel = new User();

    // 🔥 AQUÍ LLAMAS DIRECTO AL MODEL
    $userModel->expireOtps();

    $auth = new AuthController();

    header('Content-Type: application/json');

    echo json_encode($auth->listUsers());
    exit;
};
// 🔥 RUTA POR DEFECTO (LOGIN)
require_once __DIR__ . '/Views/Login/Login.php';