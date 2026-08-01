<?php
/*
 * update_expense.php
 * API endpoint for updating existing expense records.
 * Validates input data (ID, title, amount, etc.) and updates the corresponding record in the expenses table.
 * Returns JSON response indicating success or failure of the operation.
 * Used in Pages/admin/dashboard/expenses/log.php for customer order searching.
*/
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(0);

require_once 'db_connect.php'; // centralized PDO connection

try {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data) {
        throw new Exception("Invalid JSON payload.");
    }

    $id       = intval($data['id'] ?? 0);
    $title    = trim($data['title'] ?? '');
    $details  = trim($data['details'] ?? '');
    $amount   = floatval($data['amount'] ?? 0);
    $status   = trim($data['status'] ?? 'Paid');
    $category = trim($data['category'] ?? '');

    if ($id <= 0 || empty($title)) {
        throw new Exception("Missing required fields: ID or Title.");
    }

    $sql = "UPDATE expenses 
            SET title = ?, details = ?, amount = ?, status = ?, category = ? 
            WHERE id = ?";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([$title, $details, $amount, $status, $category, $id])) {
        echo json_encode([
            'status' => 'success',
            'message' => "Expense record #$id has been updated."
        ]);
    } else {
        throw new Exception("Failed to update expense.");
    }

} catch (Exception $e) {
    http_response_code(200);
    echo json_encode([
        'status' => 'error',
        'message' => "Server Error: " . $e->getMessage()
    ]);
}