<?php
/*
 * Sales and expenses reporting API endpoint for admin dashboard.
 * Generates monthly financial summary including sales, expenses, and net profit/loss per day.
 * Used in Pages/admin/dashboard/sales-report.php for analytics and charts.
 * Returns JSON data for charts and analytics.
 */
header("Content-Type: application/json");
require_once 'db_connect.php'; 
$year = $_GET['year'] ?? null;

if (!$year) {
    echo json_encode(["error" => "Year is required"]);
    exit;
}

/* ================= SALES PER MONTH ================= */
$salesStmt = $pdo->prepare("
    SELECT 
        MONTH(order_datetime) AS month,
        SUM(grand_total) AS total_sales
    FROM orders
    WHERE YEAR(order_datetime) = ?
      AND status != 'Cancelled'
    GROUP BY MONTH(order_datetime)
");
$salesStmt->execute([$year]);
$salesData = $salesStmt->fetchAll(PDO::FETCH_KEY_PAIR); 
// [month => total_sales]

/* ================= EXPENSES PER MONTH ================= */
$expStmt = $pdo->prepare("
    SELECT 
        MONTH(expense_datetime) AS month,
        SUM(amount) AS total_expenses
    FROM expenses
    WHERE YEAR(expense_datetime) = ?
    GROUP BY MONTH(expense_datetime)
");
$expStmt->execute([$year]);
$expenseData = $expStmt->fetchAll(PDO::FETCH_KEY_PAIR); 
// [month => total_expenses]

/* ================= BUILD MONTHLY REPORT ================= */
$report = [];

for ($m = 1; $m <= 12; $m++) {
    $sales = (float)($salesData[$m] ?? 0);
    $expenses = (float)($expenseData[$m] ?? 0);
    $net = $sales - $expenses;

    $report[] = [
        "month" => $m,
        "month_name" => date("F", mktime(0, 0, 0, $m, 1)),
        "year" => (int)$year,
        "sales" => $sales,
        "expenses" => $expenses,
        "net" => $net,
        "is_loss" => $net < 0
    ];
}

echo json_encode($report);
