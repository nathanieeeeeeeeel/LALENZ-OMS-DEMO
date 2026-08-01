<?php
/*
 * save_settings.php
 * Superadmin-only API endpoint for updating system configuration.
 * Handles saving of system preferences (branding, receipt settings, currency, etc.)
 * and optional logo upload (.ico only) to the server.
 * Stores settings as key-value pairs in system_preference_kv table.
 * Used in:
 * - Pages/admin/settings.php
*/

// ----------------------------
// Dynamic system root detection
// ----------------------------
$parts = explode("/", $_SERVER['PHP_SELF']); 
$systemFolder = "/" . $parts[1]; // e.g., /LALENZ_ORDER_SYSTEM

// 1. THIS MUST BE THE FIRST LINE
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header("Content-Type: application/json");
require_once 'db_connect.php'; 


// 2. SECURITY CHECK
if (!isset($_SESSION['admin_id']) || empty($_SESSION['is_superadmin']) || $_SESSION['is_superadmin'] !== true) {
    http_response_code(403);
    echo json_encode([ 
        "status" => "unauthorized", 
        "message" => "Access Denied: Superadmin permissions required to proceed."
    ]);
    exit;
}

// 3. GET DATA
$system_name = trim($_POST['system_name'] ?? '');
$currency_code = strtoupper(trim($_POST['currency_code'] ?? 'PHP'));
$receipt_title = trim($_POST['receipt_title'] ?? 'Lalenz Foodies');
$receipt_subtitle = trim($_POST['receipt_subtitle'] ?? 'Registered as: LALENZ ONLINE SHOP');
$receipt_address = trim($_POST['receipt_address'] ?? '');
$receipt_footer = ($_POST['receipt_footer'] ?? 'Thank you for your purchase!');
$receipt_width = ($_POST['receipt_width']) ?? '58mm';

// 3.5 HANDLE LOGO UPLOAD (ICO ONLY)
if (isset($_FILES['system_logo']) && $_FILES['system_logo']['error'] === 0) {
    $fileTmp = $_FILES['system_logo']['tmp_name'];
    $fileName = $_FILES['system_logo']['name'];
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if ($ext !== 'ico') {
        echo json_encode(["status" => "error", "message" => "Only .ico files are allowed"]);
        exit;
    }

    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . $systemFolder . '/Public/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $targetPath = $uploadDir . 'logo.ico';

    // Overwrite existing logo
    if (file_exists($targetPath)) {
        unlink($targetPath);
    }

    if (!move_uploaded_file($fileTmp, $targetPath)) {
        echo json_encode(["status" => "error", "message" => "Failed to upload logo"]);
        exit;
    }
}

// Save settings as key-value pairs in the DB
try {
    // Prepare an array of settings to update
    $settings = [
        'system_name' => $system_name ?: "Lalenz Foodies",
        'currency_code' => $currency_code,
        'receipt_title' => $receipt_title,
        'receipt_subtitle' => $receipt_subtitle,
        'receipt_address' => $receipt_address,
        'receipt_footer' => $receipt_footer,
        'receipt_width' => $receipt_width
    ];

    // Update each setting
    foreach ($settings as $key => $value) {
        $stmt = $pdo->prepare("INSERT INTO system_preference_kv (preference_key, preference_value) VALUES (?, ?)
            ON DUPLICATE KEY UPDATE preference_value = ?");
        $stmt->execute([$key, $value, $value]);
    }

    echo json_encode(["status" => "success", "message" => "Settings saved successfully!"]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>