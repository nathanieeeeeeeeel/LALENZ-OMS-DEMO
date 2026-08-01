<?php
session_start();
require_once "db_connect.php";

header("Content-Type: application/json");

$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

if ($ip === "::1") {
    $ip = "127.0.0.1";
}

$stmt = $pdo->prepare("
    SELECT locked_until
    FROM login_attempts
    WHERE ip_address = ?
    LIMIT 1
");
$stmt->execute([$ip]);

$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (
    $row &&
    $row['locked_until'] &&
    strtotime($row['locked_until']) > time()
) {
    echo json_encode([
        "locked" => true,
        "remaining_seconds" => strtotime($row['locked_until']) - time()
    ]);
} else {
    echo json_encode([
        "locked" => false
    ]);
}