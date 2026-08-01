<?php
header("Content-Type: application/json");
ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once 'db_connect.php';

try {

    $range = $_GET['range'] ?? '30d';
    $now = new DateTime("now");

    $data = [];

    /* =========================================================
     * 24 HOURS
     * ========================================================= */
    if ($range === "24h") {

        $now->setTime((int)$now->format("H"), 0, 0);

        $start = (clone $now)->modify("-23 hours");
        $end   = (clone $now)->modify("+1 hour");

        $startStr = $start->format("Y-m-d H:i:s");
        $endStr   = $end->format("Y-m-d H:i:s");

        $buckets = [];

        for ($i = 0; $i < 24; $i++) {
            $t = (clone $start)->modify("+$i hours");
            $key = $t->format("Y-m-d H:00:00");

            $buckets[$key] = [
                "date" => $key,
                "sales" => 0,
                "expenses" => 0
            ];
        }

        // SALES
        $stmt = $pdo->prepare("
            SELECT 
                DATE_FORMAT(order_datetime, '%Y-%m-%d %H:00:00') AS hour_key,
                SUM(grand_total) AS total
            FROM orders
            WHERE order_datetime >= ?
            AND order_datetime < ?
            AND status != 'Cancelled'
            GROUP BY hour_key
        ");
        $stmt->execute([$startStr, $endStr]);

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $key = $row['hour_key'] ?? null;

            if ($key && isset($buckets[$key])) {
                $buckets[$key]['sales'] = (float)$row['total'];
            }
        }

        // EXPENSES
        $stmt = $pdo->prepare("
            SELECT 
                DATE_FORMAT(expense_datetime, '%Y-%m-%d %H:00:00') AS hour_key,
                SUM(amount) AS total
            FROM expenses
            WHERE expense_datetime >= ?
            AND expense_datetime < ?
            GROUP BY hour_key
        ");
        $stmt->execute([$startStr, $endStr]);

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $key = $row['hour_key'] ?? null;

            if ($key && isset($buckets[$key])) {
                $buckets[$key]['expenses'] = (float)$row['total'];
            }
        }

        $data = [];

        foreach ($buckets as $row) {
            $row['net'] = $row['sales'] - $row['expenses'];
            $data[] = $row;
        }

    } else {

        $days = match ($range) {
            "7d" => 6,
            "15d" => 14,
            "60d" => 59,
            default => 29
        };

        $startDate = (clone $now)->modify("-$days days")->format("Y-m-d");
        $endDate = $now->format("Y-m-d");

        // SALES
        $stmt = $pdo->prepare("
            SELECT DATE(order_datetime) as d, SUM(grand_total) as sales
            FROM orders
            WHERE DATE(order_datetime) BETWEEN ? AND ?
              AND status != 'Cancelled'
            GROUP BY d
        ");
        $stmt->execute([$startDate, $endDate]);

        $sales = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $sales[$r['d']] = (float)$r['sales'];
        }

        // EXPENSES (FIXED: expense_date ONLY)
        $stmt = $pdo->prepare("
            SELECT DATE(expense_date) as d, SUM(amount) as expenses
            FROM expenses
            WHERE DATE(expense_date) BETWEEN ? AND ?
            GROUP BY d
        ");
        $stmt->execute([$startDate, $endDate]);

        $expenses = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $expenses[$r['d']] = (float)$r['expenses'];
        }

        $current = new DateTime($startDate);
        $end = new DateTime($endDate);

        while ($current <= $end) {

            $d = $current->format("Y-m-d");

            $s = $sales[$d] ?? 0;
            $e = $expenses[$d] ?? 0;

            $data[] = [
                "date" => $d,
                "sales" => $s,
                "expenses" => $e,
                "net" => $s - $e
            ];

            $current->modify("+1 day");
        }
    }

    echo json_encode([
        "status" => "success",
        "data" => $data
    ]);

} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}