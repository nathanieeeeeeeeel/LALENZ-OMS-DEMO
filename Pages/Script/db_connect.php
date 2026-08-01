<?php
/*
 * Central database connection file used by all scripts.
 * Provides a shared PDO instance with consistent settings (error mode, fetch mode, and timezone).
 */
date_default_timezone_set('Asia/Manila');

try {
    $pdo = new PDO(
        "mysql:host=127.0.0.1;dbname=lalenz_db;charset=utf8mb4",
        "root",
        "",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    $pdo->exec("SET time_zone = '+08:00'");
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed"
    ]);
    exit;
}