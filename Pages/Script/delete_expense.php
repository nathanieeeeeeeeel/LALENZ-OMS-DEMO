<?php
/*
 * Deletes a single expense record by ID.
 * Returns JSON response indicating success or failure of the operation.
 */
header('Content-Type: application/json');
require_once 'db_connect.php'; // use centralized PDO connection

try {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id <= 0) {
        throw new Exception("Invalid ID provided.");
    }

    $stmt = $pdo->prepare("DELETE FROM expenses WHERE id = ?");
    if ($stmt->execute([$id])) {
        echo json_encode(['status' => 'success', 'message' => 'Expense deleted successfully.']);
    } else {
        throw new Exception("Failed to delete expense.");
    }

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}