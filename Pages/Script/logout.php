<?php
/*
 * Admin logout handler.
 * Clears remember token from database, destroys session data,
 * deletes authentication cookies, and redirects to the homepage.
*/
session_start();
require_once "db_connect.php";

// ==========================
// 1. DELETE TOKEN FROM DB
// ==========================
if (isset($_SESSION['admin_id'])) {
    $stmt = $pdo->prepare("UPDATE admins SET remember_token = NULL WHERE id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
}

// ==========================
// 2. DESTROY SESSION
// ==========================
$_SESSION = [];
session_unset();
session_destroy();

// ==========================
// 3. DELETE COOKIE
// ==========================
setcookie("remember_me", "", time() - 3600, "/");

// ==========================
// 4. REDIRECT
// ==========================
header("Location: ../../");
exit;