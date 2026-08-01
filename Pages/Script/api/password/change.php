<?php
session_start();

// Force all errors to be in JSON
set_error_handler(function($severity, $message, $file, $line) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $message, 'file' => $file, 'line' => $line]);
    exit;
});

ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');

// --- SESSION CHECK ---
$userId = $_SESSION['admin_id'] ?? null;
// if (!$userId || empty($_SESSION['is_superadmin']) || $_SESSION['is_superadmin'] !== true) {
//     http_response_code(403);
//     echo json_encode(['success' => false, 'message' => 'Access Denied: Superadmin required']);
//     exit;
// }

// --- DATABASE CONNECTION ---
require_once __DIR__ . '/../../db_connect.php';
if (!isset($pdo)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// --- READ JSON INPUT ---
$input = json_decode(file_get_contents('php://input'), true);
$currentPassword = trim($input['current_password'] ?? '');
$newPassword     = trim($input['new_password'] ?? '');
$confirmPassword = trim($input['confirm_password'] ?? '');

if (!$currentPassword || !$newPassword || !$confirmPassword) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

if ($newPassword !== $confirmPassword) {
    echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
    exit;
}

// --- FETCH USER PASSWORD ---
$stmt = $pdo->prepare("SELECT password FROM admins WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

if (!password_verify($currentPassword, $user['password'])) {
    echo json_encode(['success' => false, 'message' => 'Current password incorrect']);
    exit;
}

// --- HASH NEW PASSWORD ---
$newHash = password_hash($newPassword, PASSWORD_DEFAULT);

// --- UPDATE PASSWORD ---
$stmt = $pdo->prepare("UPDATE admins SET password = ?, is_default = 0, password_last_changed = NOW() WHERE id = ?");
if ($stmt->execute([$newHash, $userId])) {
    echo json_encode(['success' => true, 'message' => 'Password changed successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update password']);
}

// No closing PHP tag