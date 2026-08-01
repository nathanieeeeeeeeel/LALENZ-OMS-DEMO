<?php
/*
 * Admin password verification API endpoint.
 * Validates the currently logged-in admin’s password
 * against the stored hash in the database.
 * Used for sensitive actions that require re-authentication
 * (e.g., settings changes, deletions, or security checks).
 * Returns JSON response for frontend validation handling.
*/
session_start();
header('Content-Type: application/json; charset=UTF-8');
require_once 'db_connect.php';

$password = $_POST['password'] ?? '';
if (!$password) {
    http_response_code(400);
    echo json_encode(['status' => 'failed', 'message' => 'Password is required.']);
    exit;
}

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['status' => 'failed', 'message' => 'Not authenticated.']);
    exit;
}

$stmt = $pdo->prepare('SELECT password FROM admins WHERE id = ? LIMIT 1');
$stmt->execute([$_SESSION['admin_id']]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    echo json_encode(['status' => 'verified']);
    exit;
}

echo json_encode(['status' => 'failed', 'message' => 'Incorrect password']);
?>
