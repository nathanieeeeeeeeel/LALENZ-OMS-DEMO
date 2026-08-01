<?php
// Pages/Script/auth_guard.php
/*
 * Authentication guard for admin pages.
 * Ensures only logged-in admins can access protected routes.
 * Redirects unauthorized users to the login page.
 */

require_once __DIR__ . "/auth_bootstrap.php";

/**
 * =========================
 * ACCESS CONTROL (GUARD)
 * =========================
 * Blocks access if not logged in, except for public pages.
 */

// List of pages that don't require authentication
$publicPages = [
    'login.php',     // login page
    'index.php',     // public-facing homepage
    'set_password.php' // password setup page (only for default password users)
    // Add more public pages here if needed
];

// Get current script name
$currentPage = basename($_SERVER['PHP_SELF']);

// Skip guard for public pages
if (in_array($currentPage, $publicPages)) {
    return; // allow access without authentication
}

// Redirect to login page if not authenticated
if (empty($_SESSION['admin_id'])) {

    $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];

    header("Location: " . $systemFolder . "/Pages/admin/login.php?redirect_to=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// --- Force password change if default password is used ---
require_once __DIR__ . "/db_connect.php";

// Fetch user info
$stmt = $pdo->prepare("SELECT is_default FROM admins WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$user = $stmt->fetch();

$setPasswordPage = 'set_password.php';

if ($user && !empty($user['is_default'])) {
    // Only redirect if not already on the password setup page
    if ($currentPage !== $setPasswordPage) {
        header("Location: " . $systemFolder . "/Pages/admin/" . $setPasswordPage);
        exit;
    }
}
?>