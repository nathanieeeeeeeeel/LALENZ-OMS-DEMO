<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/db_connect.php";

header("Content-Type: application/json");

// must be logged in
if (!isset($_SESSION['admin_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized"
    ]);
    exit;
}

$adminId = $_SESSION['admin_id'];

// fetch latest login history
$stmt = $pdo->prepare("
    SELECT id, ip_address, device, status, login_time
    FROM login_logs
    WHERE user_id = ?
    ORDER BY login_time DESC
    LIMIT 20
");

$stmt->execute([$adminId]);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "success" => true,
    "data" => $logs
]);