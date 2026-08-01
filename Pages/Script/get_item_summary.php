<?php
/*
 * Item Summary API for Admin Dashboard
 * Located in: Pages/admin/dashboard/item-summary.php
 *
 * Generates aggregated sales data per item within a given date range.
 * Computes total quantity sold and total revenue from order_items.
 * Returns JSON output for dashboard item summary visualization.
 */
header("Content-Type: application/json");
require_once 'db_connect.php';
date_default_timezone_set('Asia/Manila');

$fromDate = $_GET['from'] ?? date("Y-m-d");
$toDate   = $_GET['to']   ?? date("Y-m-d");

$from = $fromDate . " 00:00:00";
$to   = $toDate   . " 23:59:59";

try {

    $stmt = $pdo->prepare("
        SELECT order_items
        FROM orders
        WHERE order_datetime BETWEEN ? AND ?
    ");

    $stmt->execute([$from, $to]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $summary = [];

    foreach ($orders as $order) {

        $items = json_decode($order['order_items'], true);
        if (!$items) continue;

        foreach ($items as $item) {

            $id  = $item['id'] ?? null;
            $qty = floatval($item['qty'] ?? 1);

            if (!$id) continue;

            // calculate revenue
            $revenue = 0;

            if (isset($item['amnt'])) {
                $revenue = floatval($item['amnt']);
            } elseif (isset($item['price'])) {
                $revenue = floatval($item['price']) * $qty;
            }

            if (!isset($summary[$id])) {
                $summary[$id] = [
                    "id" => $id,
                    "qty" => 0,
                    "revenue" => 0
                ];
            }

            $summary[$id]["qty"] += $qty;
            $summary[$id]["revenue"] += $revenue;
        }
    }

    echo json_encode(array_values($summary));

} catch (Exception $e) {
    echo json_encode([]);
}