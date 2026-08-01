<?php
$systemFolder = "";

require_once __DIR__ . '/init.php';

// ----------------------------
// Includes
// ----------------------------
require_once $_SERVER['DOCUMENT_ROOT'] . $systemFolder . '/Pages/Script/auth_guard.php';
require_once $_SERVER['DOCUMENT_ROOT'] . $systemFolder . '/Pages/Script/get_system.php';

// ----------------------------
// System info fallbacks
// ----------------------------
$systemName = $settings['system_name'] ?? "Lalenz Foodies";
$systemAddress = $settings['receipt_address'] ?? "(Your Business Address Here)";

// ----------------------------
// Currency
// ----------------------------
$currencyData = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . $systemFolder . "/currencies.json"), true);
$systemCurrency = $settings['currency_code'] ?? "PHP";
$currencySymbol = $currencyData[$systemCurrency]['symbol'] ?? $currencyData[$systemCurrency]['code'] ?? "₱";

// ----------------------------
// Logo
// ----------------------------
$logo = file_exists($_SERVER['DOCUMENT_ROOT'] . $systemFolder . '/Public/uploads/logo.ico')
    ? $systemFolder . '/Public/uploads/logo.ico'
    : $systemFolder . '/Assets/logo.png';

// ----------------------------
// Admin login flag
// ----------------------------
$isAdminLoggedIn = isset($_SESSION['admin_id']);
?>
