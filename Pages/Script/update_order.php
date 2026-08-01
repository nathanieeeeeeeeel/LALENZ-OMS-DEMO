<?php
header("Content-Type: application/json");
require_once 'db_connect.php';

try {

    $pdo->exec("SET time_zone = '+08:00'");

    $data = json_decode(file_get_contents("php://input"), true);
    if (!$data) throw new Exception("No data received.");

    $order_id        = intval($data['order_id'] ?? 0);
    $customer        = trim($data['customer'] ?? '');
    $address         = trim($data['address'] ?? '');
    $notes           = trim($data['notes'] ?? '');
    $total           = floatval(str_replace(['₱', ',', ' '], '', $data['total'] ?? 0));
    $discount        = floatval(str_replace(['₱', ',', ' '], '', $data['discount'] ?? 0));
    $delivery_fee    = floatval(str_replace(['₱', ',', ' '], '', $data['delivery_fee'] ?? 0));
    $advance_payment = floatval(str_replace(['₱', ',', ' '], '', $data['advance_payment'] ?? 0));
    $is_scheduled    = intval($data['is_scheduled'] ?? 0);
    $s_date          = !empty($data['scheduled_date']) ? $data['scheduled_date'] : null;
    $s_time          = !empty($data['scheduled_time']) ? $data['scheduled_time'] : null;
    $s_datetime      = $s_date . " " . $s_time;
    $payment         = trim($data['payment_method'] ?? '');

    if ($order_id <= 0) throw new Exception("Invalid Order ID.");

    /* =========================
       FORMAT NEW ITEMS
    ========================= */
    $formatted_items = [];

    foreach ($data['items'] ?? [] as $item) {

        $id = intval($item['id'] ?? 0);
        $qty = intval($item['qty'] ?? 0);
        $price = floatval($item['price'] ?? 0);

        if (!$id || $qty <= 0) continue;

        $formatted_items[] = [
            "id" => $id,
            "qty" => $qty,
            "amnt" => $qty * $price,
            "name" => $item['name'] ?? "Item #$id"
        ];
    }

    $items_json = json_encode($formatted_items);

    /* =========================
       GET OLD ORDER (FOR STOCK FIX)
    ========================= */
    $stmt = $pdo->prepare("SELECT order_items FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);
    $oldOrder = $stmt->fetch(PDO::FETCH_ASSOC);

    $oldItems = json_decode($oldOrder['order_items'] ?? '[]', true);

    /* =========================
       MAP FUNCTION
    ========================= */
    function mapItems($items) {
        $map = [];
        foreach ($items as $i) {
            $id = intval($i['id']);
            $qty = intval($i['qty']);
            if (!$id || $qty <= 0) continue;

            $map[$id] = ($map[$id] ?? 0) + $qty;
        }
        return $map;
    }

    $oldMap = mapItems($oldItems);
    $newMap = mapItems($formatted_items);

    /* =========================
       STOCK ADJUSTMENT
    ========================= */
    $allIds = array_unique(array_merge(array_keys($oldMap), array_keys($newMap)));

    foreach ($allIds as $id) {

        $oldQty = $oldMap[$id] ?? 0;
        $newQty = $newMap[$id] ?? 0;

        $diff = $newQty - $oldQty;

        if ($diff === 0) continue;

        if ($diff > 0) {
            // reduce stock
            $stmt = $pdo->prepare("UPDATE items SET stock = stock - ? WHERE id = ?");
            $stmt->execute([$diff, $id]);
        } else {
            // restore stock
            $stmt = $pdo->prepare("UPDATE items SET stock = stock + ? WHERE id = ?");
            $stmt->execute([abs($diff), $id]);
        }
    }

    /* =========================
       UPDATE ORDER
    ========================= */
    $sql = "UPDATE orders SET 
                customer_name   = ?, 
                customer_address = ?, 
                order_items     = ?, 
                order_note      = ?, 
                discount        = ?, 
                delivery_fee    = ?, 
                advance_payment = ?, 
                grand_total     = ?, 
                is_scheduled    = ?, 
                scheduled_datetime = ?,
                scheduled_date  = ?, 
                scheduled_time  = ?,
                payment_method  = ?
            WHERE id = ?";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $customer,
        $address,
        $items_json,
        $notes,
        $discount,
        $delivery_fee,
        $advance_payment,
        $total,
        $is_scheduled,
        $s_datetime,
        $s_date,
        $s_time,
        $payment,
        $order_id
    ]);

    echo json_encode([
        "status" => "success",
        "message" => "Order #$order_id updated successfully."
    ]);

} catch (Exception $e) {

    http_response_code(200);

    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);

    error_log("Order Update Error: " . $e->getMessage());
}