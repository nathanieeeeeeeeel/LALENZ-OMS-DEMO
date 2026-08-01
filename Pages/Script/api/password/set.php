<?php
session_start();

// Force all errors to return JSON
set_error_handler(function($severity, $message, $file, $line) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $message,
        'file' => $file,
        'line' => $line
    ]);
    exit;
});

ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');

// --- SESSION CHECK ---
$userId = $_SESSION['admin_id'] ?? null;
if (!$userId) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Access Denied: Admin login required'
    ]);
    exit;
}

// --- DATABASE CONNECTION ---
require_once __DIR__ . '/../../db_connect.php';
if (!isset($pdo)) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed'
    ]);
    exit;
}

// --- READ JSON INPUT ---
$input = json_decode(file_get_contents('php://input'), true);
$newPassword     = trim($input['new_password'] ?? '');
$confirmPassword = trim($input['confirm_password'] ?? '');

if (!$newPassword || !$confirmPassword) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

if ($newPassword !== $confirmPassword) {
    echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
    exit;
}

// Optional: enforce password strength
if (strlen($newPassword) < 8) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters']);
    exit;
}

// --- UPDATE PASSWORD ---
$newHash = password_hash($newPassword, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("UPDATE admins SET password = ?, is_default = 0, password_last_changed = NOW() WHERE id = ?");
if ($stmt->execute([$newHash, $userId])) {
    echo json_encode(['success' => true, 'message' => 'Password set successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to set password']);
}