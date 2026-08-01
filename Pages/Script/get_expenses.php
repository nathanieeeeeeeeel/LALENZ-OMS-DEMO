<?php
/*
 * Expenses API endpoint for retrieving expense records with optional filters.
 * Supports filtering by date and status via GET parameters.
 * Returns results as JSON, ordered by expense date and time (latest first).
 *
 * Used in: Pages/admin/dashboard/expenses/log.php
*/

header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 0); // Hide errors from user

require_once 'db_connect.php'; // <-- include the shared DB connection

// Get filter values from the URL (if they exist)
$date = $_GET['date'] ?? '';
$status = $_GET['status'] ?? '';

$query = "
SELECT
    *,
    DATE(expense_datetime) AS expense_date,
    TIME(expense_datetime) AS expense_time
FROM expenses
WHERE 1=1
";

$params = [];

if (!empty($date)) {
    $query .= " AND DATE(expense_datetime) = ?";
    $params[] = $date;
}
if (!empty($status)) {
    $query .= " AND status = ?";
    $params[] = $status;
}

// Order by date and time
$query .= " ORDER BY expense_datetime DESC";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);

    echo json_encode($stmt->fetchAll()); // fetch mode already set in db_connect.php

} catch (PDOException $e) {
    // Optional: log internally
    // error_log("Database error in get_expenses.php: " . $e->getMessage());

    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}