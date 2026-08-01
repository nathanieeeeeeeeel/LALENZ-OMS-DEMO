/*
|--------------------------------------------------------------------------
| DEPRECATED AUTH SYSTEM
|--------------------------------------------------------------------------
| This file is deprecated and should no longer be used.
|
| It previously handled:
| - Session authentication
| - Remember-me cookie restoration
| - Access control redirect logic
|
| It has been fully replaced by:
| - auth_bootstrap.php (authentication engine / session + cookie restore)
| - auth_guard.php (access control / route protection)
|
| DO NOT USE THIS FILE IN NEW CODE.
| KEPT ONLY FOR BACKWARD COMPATIBILITY OR REFERENCE.
|--------------------------------------------------------------------------
*/
<?php
session_start();
require_once "db_connect.php";

// ==========================
// 1. RESTORE SESSION FROM COOKIE
// ==========================
if (!isset($_SESSION['admin_id']) && isset($_COOKIE['remember_me'])) {

    $token = $_COOKIE['remember_me'];

    // find admin by token
    $stmt = $pdo->prepare("SELECT id, isSuper FROM admins WHERE remember_token = ? LIMIT 1");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['is_superadmin'] = (bool)$user['isSuper'];
    } else {
        // invalid cookie → remove it
        setcookie("remember_me", "", time() - 3600, "/");
    }
}

// ==========================
// 2. FINAL CHECK
// ==========================
$isAdminLoggedIn = !empty($_SESSION['admin_id']);

if (!$isAdminLoggedIn) {
    header("Location: /LALENZ_ORDER_SYSTEM/Pages/admin/login.php");
    exit;
}
?>