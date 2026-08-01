<?php
/*
 * Database import utility (import_db.php).
 * Allows superadmin to upload and execute SQL backup files.
 * Handles large imports safely by disabling foreign key checks temporarily
 * and executing SQL statements sequentially.
 * Used for restoring database backups in the admin system.
*/
session_start();
require_once "db_connect.php";

// ✅ SECURITY: Only allow superadmin
if (!isset($_SESSION['admin_id']) || empty($_SESSION['is_superadmin']) || $_SESSION['is_superadmin'] !== true) {
    http_response_code(403);
    die("Access Denied: Superadmin permissions required to proceed.");
}

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request.");
}

// Validate file upload
if (!isset($_FILES['sql_file']) || $_FILES['sql_file']['error'] !== UPLOAD_ERR_OK) {
    die("Upload failed.");
}

$file = $_FILES['sql_file']['tmp_name'];

// Validate extension
if (strtolower(pathinfo($_FILES['sql_file']['name'], PATHINFO_EXTENSION)) !== 'sql') {
    die("Invalid file. Only .sql allowed.");
}

try {
    // Increase limits for large imports
    set_time_limit(300);
    ini_set('memory_limit', '512M');

    // Disable foreign key checks
    $pdo->exec("SET FOREIGN_KEY_CHECKS=0;");

    $handle = fopen($file, "r");
    if (!$handle) {
        throw new Exception("Cannot open file.");
    }

    $query = "";

    while (($line = fgets($handle)) !== false) {

        $trimmed = trim($line);

        // Skip empty lines and comments
        if ($trimmed === "" || str_starts_with($trimmed, "--") || str_starts_with($trimmed, "/*")) {
            continue;
        }

        $query .= $line;

        // Execute when query ends with ;
        if (str_ends_with($trimmed, ";")) {
            $pdo->exec($query);
            $query = "";
        }
    }

    fclose($handle);

    // Re-enable FK checks
    $pdo->exec("SET FOREIGN_KEY_CHECKS=1;");

    echo "success";

} catch (Throwable $e) {
    // Always re-enable FK checks even if error
    try {
        $pdo->exec("SET FOREIGN_KEY_CHECKS=1;");
    } catch (Exception $ignore) {}

    echo "error: " . $e->getMessage();
}