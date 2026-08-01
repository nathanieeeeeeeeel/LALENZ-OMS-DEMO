<?php
/*
 * remove_logo.php
 * Superadmin-only endpoint for removing the system logo.
 * Deletes the uploaded logo file and resets branding to default.
 * Used in Pages/admin/settings.php.
*/
session_start();

// ----------------------------
// Dynamic system root detection
// ----------------------------
$parts = explode("/", $_SERVER['PHP_SELF']); 
$systemFolder = "/" . $parts[1]; // e.g., /LALENZ_ORDER_SYSTEM

header('Content-Type: application/json');

$uploadPath = $_SERVER['DOCUMENT_ROOT'] . $systemFolder . '/Public/uploads/logo.ico';

$response = [];

if (file_exists($uploadPath)) {
    if (unlink($uploadPath)) {
        $response['status'] = 'logo_removed';
        $response['message'] = 'Logo has been removed and reset to default.';
    } else {
        $response['status'] = 'error_deleting';
    }
} else {
    $response['status'] = 'already_default';
}

// 2. SECURITY CHECK
if (!isset($_SESSION['admin_id']) || empty($_SESSION['is_superadmin']) || $_SESSION['is_superadmin'] !== true) {
    http_response_code(403);
    echo json_encode([
        "status" => "unauthorized", 
        "message" => "Access Denied: Superadmin permissions required to proceed."
    ]);
    exit;
}

echo json_encode($response);
exit;