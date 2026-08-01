<?php
/*
 * save_expense.php
 * API endpoint for adding new expense records to the system.
 * Validates input (title, amount, etc.) and inserts data into the expenses table.
 * Used for logging expenses in the admin dashboard expense management module.
 */
header("Content-Type: application/json");
require_once 'db_connect.php'; // use centralized PDO connection
date_default_timezone_set('Asia/Manila');

$input = file_get_contents("php://input");
$data = json_decode($input, true);

// 1. Check for 'title' (was 'description')
$title = !empty($data['title']) ? trim($data['title']) : null;
$amount = isset($data['amount']) ? floatval($data['amount']) : 0;

if (!$title) {
    echo json_encode(["status" => "error", "message" => "Title/Description is required."]);
    exit;
}

if ($amount <= 0) {
    echo json_encode(["status" => "error", "message" => "Valid amount is required."]);
    exit;
}

// 2. Assign variables based on your DB: title, details, amount, status, category
$details      = !empty($data['details']) ? trim($data['details']) : ""; 
$category     = !empty($data['category']) ? $data['category'] : "General";
$status       = !empty($data['status']) ? $data['status'] : "Paid";
$expense_datetime = !empty($data['expense_datetime'])
    ? $data['expense_datetime']
    : date("Y-m-d H:i:s");

try {

    // ✅ FIXED SQL: Matches your columns exactly
    $sql = "
    INSERT INTO expenses (
        title,
        details,
        amount,
        status,
        category,
        expense_datetime,
        expense_date,
        expense_time
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $title,
        $details,
        $amount,
        $status,
        $category,
        $expense_datetime,
        date('Y-m-d', strtotime($expense_datetime)),
        date('H:i:s', strtotime($expense_datetime))
    ]);
    
    echo json_encode(["status" => "success", "message" => "Expense logged successfully."]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "DB Error: " . $e->getMessage()]);
}
?>
