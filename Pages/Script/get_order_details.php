<?php
header("Content-Type: application/json");
require_once 'db_connect.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid or missing Order ID"
    ]);
    exit;
}

$orderId = (int)$_GET['id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo json_encode([
            "success" => false,
            "message" => "Order not found"
        ]);
        exit;
    }

    // =========================
    // SAFE ITEMS PARSE
    // =========================
    $items = json_decode($order['order_items'], true);

    if (!is_array($items)) {
        $items = [];
    }

    $subtotal = 0;

    foreach ($items as $item) {
        $qty = (int)($item['qty'] ?? 1);

        // IMPORTANT: support BOTH formats safely
        // - New items may store unit price as `price`
        // - Legacy/updated items may store line totals as `amnt`
        if (isset($item['amnt'])) {
            $lineTotal = (float)$item['amnt'];
        } else {
            $unitPrice = isset($item['price']) ? (float)$item['price'] : 0;
            $lineTotal = $qty * $unitPrice;
        }

        $subtotal += $lineTotal;
    }

    // =========================
    // NORMALIZE OUTPUT
    // =========================
    $order['success'] = true;
    $order['subtotal'] = $subtotal;
    $order['discount'] = (float)($order['discount'] ?? 0);
    $order['delivery_fee'] = (float)($order['delivery_fee'] ?? 0);
    $order['advance_payment'] = (float)($order['advance_payment'] ?? 0);

    echo json_encode($order, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Database error",
        "debug" => $e->getMessage() // remove in production
    ]);
}