<?php
/*
 * Session bootstrap and authentication guard.
 * Restores admin session using "remember me" cookie if available,
 * and defines global login state flags for admin and superadmin access control.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/db_connect.php";

/**
 * =========================
 * RESTORE SESSION FROM COOKIE
 * =========================
 */
if (empty($_SESSION['admin_id']) && !empty($_COOKIE['remember_me'])) {

    $token = $_COOKIE['remember_me'];

    $stmt = $pdo->prepare("SELECT id, isSuper FROM admins WHERE remember_token = ? LIMIT 1");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['is_superadmin'] = (bool)$user['isSuper'];
    } else {
        setcookie("remember_me", "", time() - 3600, "/");
    }
}

/**
 * =========================
 * GLOBAL FLAGS (like Passport req.user)
 * =========================
 */
$isAdminLoggedIn = !empty($_SESSION['admin_id']);
$isSuperAdminLoggedIn = !empty($_SESSION['is_superadmin']);
?>