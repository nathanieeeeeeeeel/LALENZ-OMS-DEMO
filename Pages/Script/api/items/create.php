<?php
header("Content-Type: application/json");
session_start();

// ----------------------------
// Dynamic system root detection
// ----------------------------
$parts = explode("/", $_SERVER['PHP_SELF']); 
$systemFolder = "/" . $parts[1]; // e.g., /LALENZ_ORDER_SYSTEM

require_once $_SERVER['DOCUMENT_ROOT'] . $systemFolder . '/Pages/Script/db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);

try {
    $stmt = $pdo->prepare("
        INSERT INTO items (name, price, stock, description, low_stock_threshold)
        VALUES (:name, :price, :stock, :description, :threshold)
    ");

    $stmt->execute([
        ":name" => $data["name"],
        ":price" => $data["price"],
        ":stock" => $data["stock"],
        ":description" => $data["description"],
        ":threshold" => $data["low_stock_threshold"]
    ]);

    echo json_encode([
        "status" => "success",
        "id" => $pdo->lastInsertId()
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Insert failed"
    ]);
}