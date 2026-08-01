<?php
/*
 * Dashboard statistics API endpoint.
 * Returns total sales, total expenses, and net profit/loss,
 * with optional filtering by date.
 * Used in Pages/admin/dashboard.php and Pages/admin/dashboard/expenses/log.php.
 */
header("Content-Type: application/json");
require_once 'db_connect.php'; // use centralized PDO connection

try {
    // Get filter from URL
    $date = $_GET['date'] ?? '';

    // --- 1. Total Sales Query ---
    $salesSql = "SELECT SUM(grand_total) AS total FROM orders WHERE status != 'Cancelled'";
    $salesParams = [];
    if (!empty($date)) {
        $salesSql .= " AND DATE(order_datetime) = ?";
        $salesParams[] = $date;
    }
    $salesStmt = $pdo->prepare($salesSql);
    $salesStmt->execute($salesParams);
    $totalSales = $salesStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // --- 2. Total Expenses Query ---
    $expSql = "SELECT SUM(amount) AS total FROM expenses WHERE 1=1";
    $expParams = [];
    if (!empty($date)) {
        $expSql .= " AND DATE(expense_date) = ?";
        $expParams[] = $date;
    }
    $expStmt = $pdo->prepare($expSql);
    $expStmt->execute($expParams);
    $totalExpenses = $expStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $netAmount = $totalSales - $totalExpenses;

    echo json_encode([
        "status" => "success",
        "sales" => number_format($totalSales, 2),
        "expenses" => number_format($totalExpenses, 2),
        "net" => number_format($netAmount, 2),
        "is_loss" => $netAmount < 0
    ]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
