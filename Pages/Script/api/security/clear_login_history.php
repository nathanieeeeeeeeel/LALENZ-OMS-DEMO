<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../../db_connect.php';

try {

    if (!isset($_SESSION['admin_id'])) {
        http_response_code(403);
        echo json_encode([
            "success" => false,
            "message" => "Unauthorized"
        ]);
        exit;
    }

    $userId = $_SESSION['admin_id'] ?? null;

    if (!$userId) {
        throw new Exception("Missing session user ID");
    }

    if (!isset($pdo)) {
        throw new Exception("PDO not initialized");
    }

    $stmt = $pdo->prepare("
        SELECT * FROM login_logs 
        WHERE user_id = :user_id
    ");
    $stmt->execute([":user_id" => $userId]);
    $loginLogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!$loginLogs) {
        echo json_encode([
            "success" => false,
            "message" => "No active sessions found"
        ]);
        exit;
    }

    $stmt = $pdo->prepare("
        DELETE FROM login_logs 
        WHERE user_id = :user_id
    ");

    $stmt->execute([":user_id" => $userId]);

    echo json_encode([
        "success" => true,
        "message" => "Sessions cleared"
    ]);
    exit;

} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Server error",
        "debug" => $e->getMessage()
    ]);

    exit;
}