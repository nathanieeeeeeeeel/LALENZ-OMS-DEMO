<?php
header("Content-Type: application/json");
session_start();

// ----------------------------
// Dynamic system root detection
// ----------------------------
$parts = explode("/", $_SERVER['PHP_SELF']); 
$systemFolder = "/" . $parts[1]; // e.g., /LALENZ_ORDER_SYSTEM

require_once $_SERVER['DOCUMENT_ROOT'] . $systemFolder . '/Pages/Script/db_connect.php';

try {

    $data = json_decode(file_get_contents("php://input"), true);

    // ------------------------------------
    // CHECK IF AUTHENTICATED
    // ------------------------------------
    
    // 🔒 Security
    if (!isset($_SESSION['admin_id']) || empty($_SESSION['is_superadmin']) || $_SESSION['is_superadmin'] != 1) {
        http_response_code(403);
        echo json_encode([
            "status" => "error",
            "message" => "Sorry, but only super admins can perform this action.",
            "code" => "unauthorized"
        ]);
        exit;
    }

    // ------------------------------------
    // VALIDATION (must be BEFORE transaction)
    // ------------------------------------
    if (!isset($data["confirm"]) || $data["confirm"] !== "YES_RESET_ITEMS") {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "Confirmation required"
        ]);
        exit;
    }

    // ------------------------------------
    // CHECK IF THERE ARE ITEMS TO CLEAR
    // ------------------------------------
    $stmt = $pdo->query("SELECT COUNT(*) FROM items");
    $itemCount = (int) $stmt->fetchColumn();

    if ($itemCount === 0) {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "Items table is already empty"
        ]);
        exit;
    }

    // ------------------------------------
    // TRANSACTION (only for data changes)
    // ------------------------------------
    $pdo->beginTransaction();

    // Delete all items
    $pdo->exec("DELETE FROM items");

    $pdo->commit();

    // ------------------------------------
    // SCHEMA OPERATION (NOT transactional)
    // ------------------------------------
    // NOTE: Auto-increment reset intentionally removed
    // IDs are treated as permanent unique identifiers,
    // so we preserve the sequence instead of restarting.
    // ------------------------------------
    // $pdo->exec("ALTER TABLE items AUTO_INCREMENT = 1"); // <- removed

    // ------------------------------------
    // SUCCESS RESPONSE
    // ------------------------------------
    echo json_encode([
        "status" => "success",
        "message" => "All items have been reset successfully"
    ]);

} catch (Exception $e) {

    // Safe rollback check
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}