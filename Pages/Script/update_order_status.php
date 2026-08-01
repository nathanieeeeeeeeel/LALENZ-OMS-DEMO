<?php
/*
 * update_order_status.php
 * API endpoint for updating or deleting orders.
 * Supports both:
 *   - action=...
 *   - status=...
 * Used by:
 *   - dashboard.php
 *   - orders monitor
 *   - kitchen monitor
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

require_once 'db_connect.php';

try {

    // Read JSON body if sent
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);

    // Fallback to normal POST
    if (!$data) {
        $data = $_POST;
    }

    // --------------------------
    // VALIDATE ORDER ID
    // --------------------------
    $id = isset($data["id"]) ? intval($data["id"]) : 0;

    // Accept BOTH "action" and "status"
    $value = "";

    if (!empty($data["action"])) {
        $value = strtolower(trim($data["action"]));
    }

    if (!empty($data["status"])) {
        $value = strtolower(trim($data["status"]));
    }

    if ($id <= 0) {
        throw new Exception("Missing Order ID.");
    }

    if ($value === "") {
        throw new Exception("Missing Order Status.");
    }

    // --------------------------
    // DELETE ORDER
    // --------------------------
    if ($value === "delete") {

        $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode([
            "success" => true,
            "message" => "Order #{$id} deleted successfully."
        ]);

        exit;
    }

    // --------------------------
    // STATUS MAP
    // --------------------------
    $statusMap = [

        // Current workflow
        "pending"           => "Pending",
        "preparing"         => "Preparing",
        "ready"             => "Ready",
        "out_for_delivery"  => "Out For Delivery",
        "completed"         => "Completed",
        "cancelled"         => "Cancelled",
        "scheduled"         => "Scheduled",

        // Backward compatibility
        "out"               => "Out For Delivery",
        "delivered"         => "Completed",
        "done"              => "Completed",
        "serving"           => "Serving"
    ];

    if (!array_key_exists($value, $statusMap)) {
        throw new Exception("Invalid status: {$value}");
    }

    $dbStatus = $statusMap[$value];

    // --------------------------
    // UPDATE ORDER
    // --------------------------
    $stmt = $pdo->prepare("
        UPDATE orders
        SET status = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $dbStatus,
        $id
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Order #{$id} updated successfully.",
        "status" => $dbStatus
    ]);

} catch (Exception $e) {

    http_response_code(200);

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);

    error_log("Order Update Error: " . $e->getMessage());
}