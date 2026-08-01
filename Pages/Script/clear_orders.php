<?php
/*
 * Superadmin-only API endpoint for clearing all order records.
 * Truncates the orders table (and optionally related tables) while temporarily disabling foreign key checks.
 * Returns JSON response for success or failure instead of redirecting.
 */
session_start();
require_once "db_connect.php";

// 🔒 Security Check
if (!isset($_SESSION['admin_id']) || empty($_SESSION['is_superadmin']) || $_SESSION['is_superadmin'] !== true) {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Access Denied: Superadmin permissions required to proceed."]);
    exit; 
}

try {
    set_time_limit(60);
    $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
    $pdo->exec("TRUNCATE TABLE orders");
    // $pdo->exec("TRUNCATE TABLE order_items"); // Add this if you have items!
    $pdo->exec("SET FOREIGN_KEY_CHECKS=1");

    // ✅ CHANGE THIS: Instead of header redirect, just echo success
    echo json_encode(["status" => "success", "message" => "All orders cleared successfully."]);
    exit;

} catch (PDOException $e) {
    http_response_code(500);
    // echo "Error: " . $e->getMessage();
    echo json_encode(["status" => "error", "message" => "Failed to clear orders."]);
}
