<?php

header("Content-Type: application/json");
ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once 'db_connect.php';

date_default_timezone_set('Asia/Manila');

try {

    // =========================
    // 1. INPUT
    // =========================
    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true);

    if (!is_array($data)) {
        throw new Exception("Invalid JSON input.");
    }

    $customer = trim($data['customer'] ?? '');
    $items    = $data['items'] ?? [];

    if ($customer === '') throw new Exception("Customer name is required.");
    if (!is_array($items) || count($items) === 0) throw new Exception("Please add at least one item.");

    $address        = trim($data['address'] ?? 'No Address');
    $order_note     = trim($data['notes'] ?? 'No special instructions.');
    $payment_method = trim($data['payment_method'] ?? 'Cash');

    $is_scheduled   = (int)($data['is_scheduled'] ?? 0);
    $scheduled_date = $data['scheduled_date'] ?? null;
    $scheduled_time = $data['scheduled_time'] ?? null;
    $scheduled_datetime = $scheduled_date . " " . $scheduled_time;

    $order_datetime = date('Y-m-d H:i:s');
    $status = "Pending";

    if ($payment_method === '') {
        $payment_method = 'Cash';
    }

    if ($is_scheduled === 1) {
        if (!$scheduled_date || !$scheduled_time) {
            throw new Exception("Scheduled date and time required.");
        }

        $dt = strtotime("$scheduled_date $scheduled_time");
        if ($dt <= time()) {
            throw new Exception("Scheduled time must be in the future.");
        }

        $status = "Scheduled";
    }

    // =========================
    // 2. TRANSACTION START
    // =========================
    $pdo->beginTransaction();

    $normalized = [];
    $grand_total = 0;

    // =========================
    // 3. PROCESS ITEMS (DB TRUSTED)
    // =========================
    $stmtItem = $pdo->prepare("SELECT id, name, price, stock FROM items WHERE id = ? FOR UPDATE");

    foreach ($items as $item) {

        $id  = (int)($item['id'] ?? 0);
        $qty = (int)($item['qty'] ?? 0);

        if ($id <= 0 || $qty <= 0) {
            throw new Exception("Invalid item in order.");
        }

        $stmtItem->execute([$id]);
        $dbItem = $stmtItem->fetch(PDO::FETCH_ASSOC);

        if (!$dbItem) {
            throw new Exception("Item ID $id not found.");
        }

        $stock = (int)$dbItem['stock'];
        $price = (float)$dbItem['price'];
        $name  = $dbItem['name'];

        if ($qty > $stock) {
            throw new Exception("Not enough stock for $name. Available: $stock");
        }

        // deduct stock safely
        $update = $pdo->prepare("
            UPDATE items 
            SET stock = stock - ? 
            WHERE id = ? AND stock >= ?
        ");

        $update->execute([$qty, $id, $qty]);

        if ($update->rowCount() === 0) {
            throw new Exception("Stock conflict for $name.");
        }

        $normalized[] = [
            "id"   => $id,
            "qty"  => $qty,
            "price"=> $price,
            "amnt" => $qty * $price
        ];

        $grand_total += $qty * $price;
    }

    // =========================
    // 4. APPLY DISCOUNTS / FEES
    // =========================
    $discount = (float)($data['discount'] ?? 0);
    $delivery = (float)($data['delivery_fee'] ?? 0);
    $advance  = (float)($data['advance_payment'] ?? 0);

    $grand_total = $grand_total - $discount + $delivery - $advance;

    if ($grand_total < 0) $grand_total = 0;

    // =========================
    // 5. INSERT ORDER
    // =========================
    $items_json = json_encode($normalized);

    $sql = "
        INSERT INTO orders (
            order_datetime,
            customer_name,
            customer_address,
            order_items,
            order_note,
            payment_method,
            grand_total,
            discount,
            delivery_fee,
            advance_payment,
            status,
            is_scheduled,
            scheduled_date,
            scheduled_time,
            scheduled_datetime
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $order_datetime,
        $customer,
        $address,
        $items_json,
        $order_note,
        $payment_method,
        $grand_total,
        $discount,
        $delivery,
        $advance,
        $status,
        $is_scheduled,
        $scheduled_date,
        $scheduled_time,
        $scheduled_datetime
    ]);

    $new_id = $pdo->lastInsertId();

    // =========================
    // 6. COMMIT
    // =========================
    $pdo->commit();

    echo json_encode([
        "status" => "success",
        "message" => "Order #$new_id placed successfully",
        "order_id" => $new_id
    ]);

} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(200);

    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}