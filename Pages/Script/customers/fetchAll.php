<?php
/*
 * Customer Book API
 * Located in: Pages/Script/customers/fetchAll.php
 *
 * Fetches unique customers from the orders table.
 * Used for repeat customer lookup during order creation.
 * Aggregates total orders and retrieves the latest order date.
 * Returns JSON output for Customer Book modal.
 */

header("Content-Type: application/json");
// ----------------------------
// Dynamic system root detection
// ----------------------------
$parts = explode("/", $_SERVER['PHP_SELF']); 
$systemFolder = "/" . $parts[1]; // e.g., /LALENZ_ORDER_SYSTEM

require_once $_SERVER['DOCUMENT_ROOT'] . $systemFolder . '/Pages/Script/db_connect.php';

date_default_timezone_set('Asia/Manila');

try {

    $stmt = $pdo->prepare("
        SELECT
            customer_name,
            customer_address,
            COUNT(*) AS total_orders,
            MAX(order_datetime) AS last_order
        FROM orders
        WHERE customer_name IS NOT NULL
        AND customer_name != ''
        GROUP BY
            customer_name,
            customer_address
        ORDER BY last_order DESC
    ");

    $stmt->execute();

    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($customers);

} catch (Exception $e) {

    echo json_encode([]);

}