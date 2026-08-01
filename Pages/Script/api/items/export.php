<?php
// ----------------------------
// Dynamic system root detection
// ----------------------------
$parts = explode("/", $_SERVER['PHP_SELF']); 
$systemFolder = "/" . $parts[1]; // e.g., /LALENZ_ORDER_SYSTEM

require_once $_SERVER['DOCUMENT_ROOT'] . $systemFolder . '/Pages/Script/db_connect.php';

header('Content-Type: application/json; charset=utf-8');
header('Content-Disposition: attachment; filename="items_export.json"');

try {
    $stmt = $pdo->query("SELECT * FROM items ORDER BY id ASC");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // IMPORTANT: clean output buffer (prevents corrupted JSON)
    if (ob_get_length()) ob_clean();

    echo json_encode([
        "items" => $items
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);

    echo json_encode([
        "error" => true,
        "message" => $e->getMessage()
    ]);
}
exit;