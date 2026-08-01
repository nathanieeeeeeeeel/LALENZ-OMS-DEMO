<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../db.php';

$email = $_POST['email'] ?? '';

if (!$email) {
    echo json_encode(['success' => false, 'message' => 'Email is required']);
    exit;
}

// Check if email exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'Email not found']);
    exit;
}

// Generate a secure token
$token = bin2hex(random_bytes(32));
$expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

// Save token in DB
$stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE id = ?");
$stmt->execute([$token, $expiry, $user['id']]);

// TODO: Send email with link
$resetLink = "https://yourdomain.com/reset-password.php?token=$token";

// For now, return the link for testing
echo json_encode(['success' => true, 'message' => 'Reset link sent', 'link' => $resetLink]);