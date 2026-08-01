<?php
/*
 * System-wide data wipe API endpoint (superadmin only).
 * Verifies superadmin authentication and password before execution.
 * Permanently deletes all records from key tables (orders, expenses, system preferences)
 * and resets items.json to an empty state.
 * Used for full system reset/cleanup operations.
 * Returns JSON response for frontend confirmation handling.
 * Used in Pages/admin/settings.php for system maintenance actions.
*/
session_start();
header("Content-Type: application/json; charset=UTF-8");

require_once 'db_connect.php'; // PDO connection

// ✅ SECURITY: Only allow superadmin
if (!isset($_SESSION['admin_id']) || empty($_SESSION['is_superadmin']) || $_SESSION['is_superadmin'] !== true) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Access Denied: Superadmin permissions required to proceed.'
    ]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);
$password = $input['password'] ?? null;

if (!$password) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Password required.'
    ]);
    exit;
}

// Get logged-in admin
$adminId = $_SESSION['admin_id'];

$stmt = $pdo->prepare("
    SELECT password, isSuper 
    FROM admins 
    WHERE id = ? LIMIT 1
");
$stmt->execute([$adminId]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// Must be superadmin
if (!$admin || (int)$admin['isSuper'] !== 1) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Superadmin required.'
    ]);
    exit;
}

// Verify password
if (!password_verify($password, $admin['password'])) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid password.'
    ]);
    exit;
}

try {
    // Define tables to wipe
    $tables = ['expenses', 'orders', 'system_preference', "items"];

    // Start transaction
    $pdo->beginTransaction();

    foreach ($tables as $table) {
        $pdo->exec("DELETE FROM `$table`");
    }

    // Commit transaction
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'All system data wiped successfully!'
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to wipe data: ' . $e->getMessage()
    ]);

    error_log("Wipe Data Error: " . $e->getMessage());
}