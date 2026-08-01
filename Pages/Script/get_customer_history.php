<?php
/*
 * (LEGACY FILE - NO LONGER USED ANYWHERE)
 * Customer profile and order history API endpoint.
 * Retrieves customer details and all related orders by customer ID,
 * and returns combined data as JSON for dashboard display.
 */
header("Content-Type: application/json");
$custID = $_GET['id'] ?? 0;
// require_once 'db_connect.php'; // centralized PDO connection

try {
    // Get Customer Profile
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE cust_id = ?");
    $stmt->execute([$custID]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get All Orders for this customer
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE cust_id_ref = ? ORDER BY id DESC");
    $stmt->execute([$custID]);
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "customer" => $customer,
        "history" => $history
    ]);
} catch (Exception $e) { echo json_encode(["status" => "error"]); }
?>
