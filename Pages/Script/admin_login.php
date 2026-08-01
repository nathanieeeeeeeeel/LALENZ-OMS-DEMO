<?php
session_start();
require_once "db_connect.php";

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$username = trim($data['username'] ?? '');
$password = trim($data['password'] ?? '');
$remember = !empty($data['remember']);

// ======================
// IP
// ======================
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
if ($ip === '::1') {
    $ip = '127.0.0.1';
}

// ======================
// CHECK LOCK (ONLY ONCE - IMPORTANT)
// ======================
$stmt = $pdo->prepare("
    SELECT failed_attempts, locked_until
    FROM login_attempts
    WHERE ip_address = ?
    LIMIT 1
");
$stmt->execute([$ip]);
$attempt = $stmt->fetch(PDO::FETCH_ASSOC);

if (
    $attempt &&
    $attempt['locked_until'] &&
    strtotime($attempt['locked_until']) > time()
) {

    $remaining = strtotime($attempt['locked_until']) - time();

    echo json_encode([
        "status" => "locked",
        "message" => "Too many login attempts. Please wait.",
        "remaining_seconds" => $remaining
    ]);
    exit;
}

if (
    $attempt &&
    $attempt['locked_until'] &&
    strtotime($attempt['locked_until']) <= time()
) {

    $stmt = $pdo->prepare("
        DELETE FROM login_attempts
        WHERE ip_address = ?
    ");

    $stmt->execute([$ip]);

    $attempt = null;
}

// ======================
// GET USER
// ======================
$stmt = $pdo->prepare("
    SELECT id, password, isSuper
    FROM admins
    WHERE username = ?
    LIMIT 1
");
$stmt->execute([$username]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

$ua = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

// ======================
// SUCCESS LOGIN
// ======================
if ($user && password_verify($password, $user['password'])) {

    session_regenerate_id(true);

    $_SESSION['admin_id'] = $user['id'];
    $_SESSION['is_superadmin'] = (bool)$user['isSuper'];

    // REMEMBER ME
    if ($remember) {
        $token = bin2hex(random_bytes(32));

        $stmt = $pdo->prepare("
            UPDATE admins
            SET remember_token = ?
            WHERE id = ?
        ");
        $stmt->execute([$token, $user['id']]);

        setcookie("remember_me", $token, [
            'expires' => time() + (86400 * 30),
            'path' => '/',
            'httponly' => true,
            'secure' => false,
            'samesite' => 'Lax'
        ]);
    }

    // SUCCESS LOG
    $stmt = $pdo->prepare("
        INSERT INTO login_logs
        (user_id, username, ip_address, device, status, login_time)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");

    $stmt->execute([
        $user['id'],
        $username,
        $ip,
        $ua,
        'success'
    ]);

    // RESET ATTEMPTS
    $stmt = $pdo->prepare("
        DELETE FROM login_attempts
        WHERE ip_address = ?
    ");
    $stmt->execute([$ip]);

    $redirect = $_SESSION['redirect_to'] ?? null;
    unset($_SESSION['redirect_to']);

    echo json_encode([
        "status" => "success",
        "redirect" => $redirect ?: "../../Pages/admin/dashboard.php"
    ]);
    exit;
}

// ======================
// FAILED LOGIN LOG
// ======================
$stmt = $pdo->prepare("
    INSERT INTO login_logs
    (user_id, username, ip_address, device, status, login_time)
    VALUES (?, ?, ?, ?, ?, NOW())
");

$stmt->execute([
    $user['id'] ?? null,
    $username,
    $ip,
    $ua,
    'failed'
]);

// ======================
// UPDATE ATTEMPTS
// ======================
$stmt = $pdo->prepare("
    SELECT failed_attempts
    FROM login_attempts
    WHERE ip_address = ?
    LIMIT 1
");
$stmt->execute([$ip]);

$attempt = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$attempt) {

    $stmt = $pdo->prepare("
        INSERT INTO login_attempts
        (ip_address, failed_attempts, last_attempt)
        VALUES (?, 1, NOW())
    ");
    $stmt->execute([$ip]);

} else {

    $failedAttempts = $attempt['failed_attempts'] + 1;

    if ($failedAttempts >= 5) {

        $stmt = $pdo->prepare("
            UPDATE login_attempts
            SET failed_attempts = ?,
                last_attempt = NOW(),
                locked_until = DATE_ADD(NOW(), INTERVAL 15 MINUTE)
            WHERE ip_address = ?
        ");

        $stmt->execute([$failedAttempts, $ip]);

    } else {

        $stmt = $pdo->prepare("
            UPDATE login_attempts
            SET failed_attempts = ?,
                last_attempt = NOW()
            WHERE ip_address = ?
        ");

        $stmt->execute([$failedAttempts, $ip]);
    }
}

$stmt = $pdo->prepare("
    SELECT locked_until
    FROM login_attempts
    WHERE ip_address = ?
    LIMIT 1
");
$stmt->execute([$ip]);
$attempt = $stmt->fetch(PDO::FETCH_ASSOC);

if (
    $attempt &&
    $attempt['locked_until'] &&
    strtotime($attempt['locked_until']) > time()
) {

    $remaining = strtotime($attempt['locked_until']) - time();

    echo json_encode([
        "status" => "locked",
        "message" => "Too many attempts. Please wait " . gmdate("i:s", $remaining) . ".",
        "remaining_seconds" => $remaining
    ]);
    exit;
}

echo json_encode([
    "status" => "error",
    "message" => "Invalid credentials"
]);
exit;