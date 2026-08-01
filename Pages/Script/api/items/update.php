<?php
header("Content-Type: application/json");
// ----------------------------
// Dynamic system root detection
// ----------------------------
$parts = explode("/", $_SERVER['PHP_SELF']); 
$systemFolder = "/" . $parts[1]; // e.g., /LALENZ_ORDER_SYSTEM

require_once $_SERVER['DOCUMENT_ROOT'] . $systemFolder . '/Pages/Script/db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);

try {
    $stmt = $pdo->prepare("
        UPDATE items
        SET name = :name,
            price = :price,
            stock = :stock,
            description = :description,
            low_stock_threshold = :threshold
        WHERE id = :id
    ");

    $stmt->execute([
        ":id" => $data["id"],
        ":name" => $data["name"],
        ":price" => $data["price"],
        ":stock" => $data["stock"],
        ":description" => $data["description"],
        ":threshold" => $data["low_stock_threshold"]
    ]);

    echo json_encode(["status" => "success"]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Update failed"
    ]);
}