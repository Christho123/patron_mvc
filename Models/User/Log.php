<?php
require_once __DIR__ . '/../../Config/Database.php';

class Log {
    private $pdo;

    public function __construct() {
        $this->pdo = getConnection();
    }

    public function save($user_id, $action) {
        $stmt = $this->pdo->prepare("INSERT INTO user_logs (user_id, action) VALUES (?, ?)");
        $stmt->execute([$user_id, $action]);
    }

    public function getStats() {
        $stmt = $this->pdo->query("
            SELECT action, COUNT(*) as total 
            FROM user_logs 
            GROUP BY action
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAnalyticsSummary() {
        $stmt = $this->pdo->query("
            SELECT
                SUM(CASE WHEN action = 'login_success' THEN 1 ELSE 0 END) AS login_success_count,
                SUM(CASE WHEN action = 'register_success' THEN 1 ELSE 0 END) AS register_success_count,
                SUM(CASE WHEN action IN ('register_failed', 'register_duplicate', 'register_otp_expired') THEN 1 ELSE 0 END) AS register_failed_count
            FROM user_logs
        ");

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return [
            'login_success_count' => (int)($row['login_success_count'] ?? 0),
            'register_success_count' => (int)($row['register_success_count'] ?? 0),
            'register_failed_count' => (int)($row['register_failed_count'] ?? 0),
        ];
    }

    public function getAnalyticsTrend($days = 7) {
        $days = max(1, (int)$days);

        $stmt = $this->pdo->prepare("
            SELECT
                DATE(created_at) AS day,
                SUM(CASE WHEN action = 'login_success' THEN 1 ELSE 0 END) AS login_success,
                SUM(CASE WHEN action = 'register_success' THEN 1 ELSE 0 END) AS register_success,
                SUM(CASE WHEN action IN ('register_failed', 'register_duplicate', 'register_otp_expired') THEN 1 ELSE 0 END) AS register_failed
            FROM user_logs
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
            GROUP BY DATE(created_at)
            ORDER BY DATE(created_at) ASC
        ");
        $stmt->execute([$days - 1]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $map = [];
        foreach ($rows as $row) {
            $map[$row['day']] = [
                'login_success' => (int)$row['login_success'],
                'register_success' => (int)$row['register_success'],
                'register_failed' => (int)$row['register_failed'],
            ];
        }

        $labels = [];
        $loginSeries = [];
        $regSuccessSeries = [];
        $regFailedSeries = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $day = date('Y-m-d', strtotime("-$i days"));
            $labels[] = date('d/m', strtotime($day));
            $loginSeries[] = $map[$day]['login_success'] ?? 0;
            $regSuccessSeries[] = $map[$day]['register_success'] ?? 0;
            $regFailedSeries[] = $map[$day]['register_failed'] ?? 0;
        }

        return [
            'labels' => $labels,
            'login_success' => $loginSeries,
            'register_success' => $regSuccessSeries,
            'register_failed' => $regFailedSeries,
        ];
    }
}