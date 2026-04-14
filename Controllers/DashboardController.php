<?php
require_once __DIR__ . '/../Models/User/Log.php';

class DashboardController {

    public function index() {
        $log = new Log();
        $data = $log->getStats();

        require_once __DIR__ . '/../Views/Dashboard/index.php';
    }
}