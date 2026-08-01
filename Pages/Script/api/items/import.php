<?php
session_start();

// ----------------------------
// Dynamic system root detection
// ----------------------------
$parts = explode("/", $_SERVER['PHP_SELF']); 
$systemFolder = "/" . $parts[1]; // e.g., /LALENZ_ORDER_SYSTEM

require_once $_SERVER['DOCUMENT_ROOT'] . $systemFolder . '/Pages/Script/db_connect.php';

header('Content-Type: application/json');

// 🔒 Security
if (!isset($_SESSION['admin_id']) || empty($_SESSION['is_superadmin']) || $_SESSION['is_superadmin'] != 1) {
    http_response_code(403);
    echo json_encode([
        "status" => "error",
        "message" => "Unauthorized"
    ]);
    exit;
}

// 🔥 READ JSON BODY
$raw = file_get_contents("php://input");

if (!$raw) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Empty request body"
    ]);
    exit;
}

$data = json_decode($raw, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Invalid JSON: " . json_last_error_msg()
    ]);
    exit;
}

if (!isset($data['items']) || !is_array($data['items'])) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Missing items array"
    ]);
    exit;
}

try {
    // Clear table without resetting auto_increment
    $pdo->exec("DELETE FROM items");

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        INSERT INTO items (id, name, price, description, category, stock, low_stock_threshold)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    foreach ($data['items'] as $item) {
        $stmt->execute([
            $item['id'] ?? null,
            $item['name'] ?? '',
            $item['price'] ?? 0,
            $item['description'] ?? '',
            $item['category'] ?? '',
            $item['stock'] ?? 0,
            $item['low_stock_threshold'] ?? 0
        ]);
    }

    $pdo->commit();

    echo json_encode([
        "status" => "success",
        "message" => "Import successful (items replaced, IDs preserved)"
    ]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}

