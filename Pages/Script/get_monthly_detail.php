<?php
/*
 * Monthly sales and expenses detailed report API for admin dashboard.
 * Aggregates daily sales, expenses, and net profit/loss for a selected month.
 * Used in Pages/admin/dashboard/sales-report.php for report visualization.
 * Returns JSON formatted data grouped by date.
 */
header("Content-Type: application/json");

// ✅ Set Philippine timezone
date_default_timezone_set('Asia/Manila');

try {

    $pdo = new PDO(
        "mysql:host=127.0.0.1;dbname=lalenz_db;charset=utf8mb4",
        "root",
        "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Optional but recommended
    $pdo->exec("SET time_zone = '+08:00'");

    $month = $_GET['month'] ?? null;
    $year  = $_GET['year'] ?? null;

    if (!$month || !$year) {
        echo json_encode([]);
        exit;
    }

    /* ================= DAILY SALES ================= */
    $salesStmt = $pdo->prepare("
        SELECT 
            DATE(order_datetime) AS date,
            SUM(grand_total) AS sales
        FROM orders
        WHERE MONTH(order_datetime) = ?
          AND YEAR(order_datetime) = ?
          AND status != 'Cancelled'
        GROUP BY DATE(order_datetime)
    ");
    $salesStmt->execute([$month, $year]);
    $salesData = $salesStmt->fetchAll(PDO::FETCH_KEY_PAIR);
    // returns: [date => sales]

    /* ================= DAILY EXPENSES ================= */
    $expStmt = $pdo->prepare("
        SELECT 
            DATE(expense_date) AS date,
            SUM(amount) AS expenses
        FROM expenses
        WHERE MONTH(expense_date) = ?
          AND YEAR(expense_date) = ?
        GROUP BY DATE(expense_date)
    ");
    $expStmt->execute([$month, $year]);
    $expenseData = $expStmt->fetchAll(PDO::FETCH_KEY_PAIR);
    // returns: [date => expenses]

    /* ================= MERGE BOTH ================= */
    $allDates = array_unique(
        array_merge(array_keys($salesData), array_keys($expenseData))
    );

    sort($allDates);

    $result = [];

    foreach ($allDates as $date) {

        $sales = (float)($salesData[$date] ?? 0);
        $expenses = (float)($expenseData[$date] ?? 0);
        $net = $sales - $expenses;

        $result[] = [
            "date" => $date,
            "sales" => $sales,
            "expenses" => $expenses,
            "net" => $net,
            "is_loss" => $net < 0
        ];
    }

    echo json_encode($result);

} catch (Exception $e) {

    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>