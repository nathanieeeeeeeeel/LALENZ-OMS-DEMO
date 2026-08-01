<?php
/*
 * Superadmin-only API endpoint for clearing all expense records.
 * Permanently truncates the expenses table and resets its IDs.
 * Returns JSON response indicating success or failure.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header("Content-Type: application/json");
require_once 'db_connect.php'; // Ensure this defines $pdo

// 1. Security Check: Only allow Superadmins to clear data
$isSuperAdmin = isset($_SESSION['is_superadmin']) && $_SESSION['is_superadmin'] === true;

if (!$isSuperAdmin) {
    echo json_encode(["status" => "error", "message" => "Access Denied: Superadmin permissions required to proceed."]);
    exit;
}

try {
    // 2. Clear the expenses table
    // Use TRUNCATE to completely empty the table and reset IDs to 1
    $sql = "TRUNCATE TABLE expenses"; 
    $pdo->exec($sql);

    echo json_encode(["status" => "success", "message" => "All expense data has been cleared successfully!"]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database Error: " . $e->getMessage()]);
}
?>
