<?php
header("Content-Type: application/json");
session_start();

// ----------------------------
// Dynamic system root detection
// ----------------------------
$parts = explode("/", $_SERVER['PHP_SELF']); 
$systemFolder = "/" . $parts[1]; // e.g., /LALENZ_ORDER_SYSTEM

require_once $_SERVER['DOCUMENT_ROOT'] . $systemFolder . '/Pages/Script/db_connect.php';

// 🔒 Security
if (!isset($_SESSION['admin_id']) || empty($_SESSION['is_superadmin']) || $_SESSION['is_superadmin'] != 1) {
    http_response_code(403);
    echo json_encode([
        "status" => "error",
        "code" => "UNAUTHORIZED",
        "message" => "Sorry, but only superadmins can perform this action."
    ]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

try {
    $stmt = $pdo->prepare("DELETE FROM items WHERE id = :id");
    $stmt->execute([":id" => $data["id"]]);

    echo json_encode(["status" => "success"]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Delete failed"
    ]);
}