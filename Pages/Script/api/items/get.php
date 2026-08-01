<?php
header("Content-Type: application/json");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// ----------------------------
// Dynamic system root detection
// ----------------------------
$parts = explode("/", $_SERVER['PHP_SELF']); 
$systemFolder = "/" . $parts[1]; // e.g., /LALENZ_ORDER_SYSTEM

require_once $_SERVER['DOCUMENT_ROOT'] . $systemFolder . '/Pages/Script/db_connect.php';

try {
    $stmt = $pdo->prepare("
        SELECT id, name, price, stock, description, low_stock_threshold
        FROM items
        ORDER BY id ASC
    ");

    $stmt->execute();

    echo json_encode([
        "status" => "success",
        "items" => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);

} catch (Exception $e) {
    http_response_code(500);

    echo json_encode([
        "status" => "error",
        "message" => "Failed to fetch items"
    ]);
}